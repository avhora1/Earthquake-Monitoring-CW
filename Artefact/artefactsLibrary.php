<?php
    //Incrementing the shelf capacity by 1
    function increment_shelf_capacity($conn, $shelving_loc){
        $sql = "UPDATE shelves SET capacity = capacity + 1 WHERE shelf = ?;";
        $params = array($shelving_loc);
        return sqlsrv_query($conn, $sql, $params);
    }
    //decrementing the shelf capacity by 1
    function decrement_shelf_capacity($conn, $shelving_loc){
        $sql = "UPDATE shelves SET capacity = capacity - 1 WHERE shelf = ? AND capacity > 0;";
        $params = array($shelving_loc);
        return sqlsrv_query($conn, $sql, $params);
    }
    //Adding an artefact into the database
    function add_new_artefact($conn, $earthquake_id, $type, $shelving_loc, $datetime_variable, $time_stamp, $description){
        $sql = "INSERT INTO artefacts (earthquake_id, type, time_stamp, shelving_loc, description) 
            VALUES (?, ?, CONVERT(DATETIME, ?, 120), ?, ?);"; // Need to explicitly convert the datetime so SQL can interpret the generated time stamp, doesn't work otherwise
        $params = array($earthquake_id, $type, $time_stamp, $shelving_loc, $description);
        decrement_shelf_capacity($conn, $shelving_loc);
        return sqlsrv_query($conn, $sql, $params);
    }
    //Deleting an artefact from the database
    function delete_artefact($conn, $id){
        //finding the shelving location 
        $shelving_loc = null;
        //delete artefact from dependent tables
        $sql1 = "DELETE FROM stock_list WHERE artifact_id = ?;";
        $params1 = array($id);
        $stmt1 = sqlsrv_query($conn, $sql1, $params1);
        //delete artefact
        $sql2 = "DELETE FROM artefacts WHERE id = ?;";
        //incrementing shelf location
        $sql3 = "SELECT shelving_loc FROM artefacts WHERE id = ?;";
        $params3 = array($id);
        $stmt3 = sqlsrv_query($conn, $sql3, $params3);
        if ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
            $shelving_loc = $row['shelving_loc'];
        }
        sqlsrv_free_stmt($stmt3);
        if ($shelving_loc !== null) {
            increment_shelf_capacity($conn, $shelving_loc);
        }
        //executing the deletion of the artefact
        $stmt2 = sqlsrv_query($conn, $sql2, $params1);
        return $stmt2; //returning so that any html error checks can be done outside the function. 
    }
    //Updating the details of an artefact
    function update_artefact($conn, $id, $earthquake_id, $type, $shelving_loc, $pallet_id, $description){
        //do the error checks before calling this function
        //incrementing the shelf it used to be in.
        $old_shelf = null; 
        $sql1 = "SELECT shelving_loc FROM artefacts WHERE id = ?;";
        $params1 = array($id);
        $stmt1 = sqlsrv_query($conn, $sql1, $params1);
        if ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $old_shelf = $row['shelving_loc'];
        }
        sqlsrv_free_stmt($stmt1);
        // Only change capacities if the shelf changes
        if ($old_shelf !== null && $old_shelf !== $shelving_loc) {
            increment_shelf_capacity($conn, $old_shelf);
            decrement_shelf_capacity($conn, $shelving_loc);
        }
        //updating the details of the artefact
        $sql = "UPDATE artefacts SET
            earthquake_id = ?,
            type = ?,
            shelving_loc = ?,
            pallet_id = ?,
            description = ?
        WHERE id = ?";
        $params = [
        $earthquake_id,
        $type,
        $shelving_loc, 
        $pallet_id,
        $description,
        $id
        ];
        $stmt = sqlsrv_query($conn, $sql, $params);
        return $stmt; //do error checks and outputs outside the function.
    }
    //Optional filtering of artefacts
    function get_artefacts($conn, $filters = [], $order_by = "time_stamp", $direction = "DESC") {
        $sql = "SELECT * FROM artefacts";
        $where = [];
        $params = [];
    
        // Build WHERE clauses based on $filters associative array
        if (isset($filters['type'])) {
            $where[] = "type = ?";
            $params[] = $filters['type'];
        }
        if (isset($filters['shelving_loc'])) {
            $where[] = "shelving_loc = ?";
            $params[] = $filters['shelving_loc'];
        }
        if (isset($filters['earthquake_id'])) {
            $where[] = "earthquake_id = ?";
            $params[] = $filters['earthquake_id'];
        }
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $where[] = "time_stamp BETWEEN ? AND ?";
            $params[] = $filters['date_from'];
            $params[] = $filters['date_to'];
        } elseif (isset($filters['date_from'])) {
            $where[] = "time_stamp >= ?";
            $params[] = $filters['date_from'];
        } elseif (isset($filters['date_to'])) {
            $where[] = "time_stamp <= ?";
            $params[] = $filters['date_to'];
        }
    
        // Assemble the WHERE clause
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
    
        // Only allow sorting by known columns for security
        $allowed_order_columns = ['time_stamp','type','shelving_loc','earthquake_id','id'];
        $allowed_directions = ['ASC','DESC'];
        $order_by = in_array($order_by, $allowed_order_columns) ? $order_by : "time_stamp";
        $direction = in_array(strtoupper($direction), $allowed_directions) ? strtoupper($direction) : "DESC";
        $sql .= " ORDER BY $order_by $direction";
    
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            return false;
        }
    
        $results = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $results[] = $row;
        }
        sqlsrv_free_stmt($stmt);
        return $results;
    }
?>
