<?php
    //Queries for artefacts!!

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
    function add_new_artefact($conn, $earthquake_id, $type, $shelving_loc, $datetime_variable, $time_stamp, $description, $pallet_id = null){
        $sql = "INSERT INTO artefacts (earthquake_id, type, time_stamp, shelving_loc, description, pallet_id) 
            VALUES (?, ?, CONVERT(DATETIME, ?, 120), ?, ?, ?);"; // Need to explicitly convert the datetime so SQL can interpret the generated time stamp, doesn't work otherwise
        $params = array($earthquake_id, $type, $time_stamp, $shelving_loc, $description, $pallet_id);
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

    //Queries for pallets!!!!
    //Adding a new pallet
    function add_pallets($conn, $pallet_size, $arrival_date){ 
        $sql = "INSERT INTO pallets (pallet_size, arrival_date) VALUES (?, CONVERT(DATETIME, ?, 120));";
        $params = array($pallet_size, $arrival_date);
        return sqlsrv_query($conn, $sql, $params);
    }
    //retrieving the id of the last added pallet
    function retrieve_last_pallet_ID($conn){
        //returns an ID
        $sql = "SELECT ident_current('pallets') AS last_pallet_id;";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row === false || !isset($row['last_pallet_id'])) {
            die("No pallet ID was retrieved.");
        }
        $last_pallet_id = $row['last_pallet_id'];
        sqlsrv_free_stmt($stmt);
        //return the ID of the last pallet added
        return $last_pallet_id;
    }
    //Updating pallet - normally only edits the pallet_size
    function update_pallet($conn, $id, $pallet_size){
        $sql = "UPDATE pallets SET pallet_size = ? WHERE id = ?;";
        $params = array($pallet_size, $id);
        return sqlsrv_query($conn, $sql, $params);
    }
    //deleting pallets and subsequent artefacts
    function delete_pallet($conn, $id){
        //begin by deleting all artefacts linked to pallet
        $sqlArtefacts = "SELECT id FROM artefacts WHERE pallet_id = ?;";
        $params = array($id);
        $stmtArtefacts = sqlsrv_query($conn, $sqlArtefacts, $params);
        if($stmtArtefacts === false) return false; 
        while ($row = sqlsrv_fetch_array($stmtArtefacts, SQLSRV_FETCH_ASSOC)) {
            $artefact_ids[] = $row['id'];
        }
        if(!empty($artefact_ids)){
            foreach($artefact_ids as $artefact_id){
                delete_artefact($conn, $artefact_id);
            }
        }
        //now delete the pallet from the list
        $sql = "DELETE FROM pallets WHERE id = ?;";
        return sqlsrv_query($conn, $sql, $params);
    }

    //Queries for shelves: this is for the sliding feature
    //Two things we need: 1. Collect capacity of shelves, and have the number of items in each shelf.
    //Getting Shelf Capacity
    function get_shelf_capacity($conn, $shelving_loc) {
        $params = array($shelving_loc);
        $sql = "SELECT capacity FROM shelves WHERE shelf = ?;";
        $stmt = sqlsrv_query($conn, $sql, $params);
    
        if ($stmt === false) {
            // SQL query failed
            return false;
        }
    
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
        sqlsrv_free_stmt($stmt);
    
        if ($row && isset($row['capacity'])) {
            return $row['capacity'];
        } else {
            // shelf not found, or no capacity column
            return null;
        }
    }
    //Getting artefacts in desired shelf -? returns array.
    function get_artefacts_by_shelf($conn, $shelving_loc) {
        $sql = "SELECT * FROM artefacts WHERE shelving_loc = ?;";
        $params = array($shelving_loc);
        $stmt = sqlsrv_query($conn, $sql, $params);
    
        if ($stmt === false) {
            // Query failed
            return false;
        }
    
        $artefacts = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $artefacts[] = $row; // Each $row is an associative array of one artefact's fields
        }
    
        sqlsrv_free_stmt($stmt);
        return $artefacts;
    }

    //Earthquake and Observatory filtering and management.!!!!!!
    //Adding Observatories
    function add_observatory($conn, $name, $est_date, $latitude, $longitude){
        $sql = "INSERT INTO observatories (name, est_date, latitude, longitude) VALUES (?,?,?,?);";
        $params = array($name, $est_date, $latitude, $longitude);
        $stmt = sqlsrv_query($conn, $sql, $params);
        return $stmt;
    }
    //Deleting Observatories
    function delete_observatory($conn, $id){
        $params = array($id);
        //delete dependent earthquakes then delete observatory.
        $sqlEarthquakes = "SELECT id FROM earthquakes WHERE observatory_id = ?;";
        $stmtEarthquakes = sqlsrv_query($conn, $sqlEarthquakes, $params);
        if($stmtEarthquakes === false) return false; 
        while ($row = sqlsrv_fetch_array( $stmtEarthquakes, SQLSRV_FETCH_ASSOC)) {
            $earthquake_ids[] = $row['id'];
        }
        if(!empty($earthquake_ids)){
            foreach($earthquake_ids as $earthquake_id){
                delete_earthquake($conn, $earthquake_id);
            }
        }
        //free statement
        sqlsrv_free_stmt($stmtEarthquakes);
        //delete observatory from table. 
        $sql2 = "DELETE FROM observatories WHERE id = ?;";
        sqlsrv_query($conn, $sql2, $params);
    }
    //Editing Observatories
    function edit_observatory($conn, $name, $est_date, $latitude, $longitude, $id){ 
        $sql = "UPDATE observatories SET name=?, est_date=?, latitude=?, longitude=? WHERE id=?";
        $params = [$name, $est_date, $latitude, $longitude, $id];
        return sqlsrv_query($conn, $sql, $params);
    }
    //adding earthquakes
    function add_earthquake($conn, $type, $magnitude, $country, $date, $time, $latitude, $longitude, $observatory_id, $user_id) {
        // 1. Validate observatory existence
        $check_sql = "SELECT COUNT(*) as cnt FROM observatories WHERE id = ?";
        $check_stmt = sqlsrv_query($conn, $check_sql, [intval($observatory_id)]);
        if ($check_stmt === false) {
            return "Database error while checking observatory.";
        }
        $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
        if (!$row || $row['cnt'] == 0) {
            sqlsrv_free_stmt($check_stmt);
            return "The selected observatory does not exist.";
        }
        sqlsrv_free_stmt($check_stmt);

        // 2. Compute the next country-specific earthquake counter
        $id_sql = "SELECT MAX(country_id) as max_id FROM earthquakes WHERE country = ?";
        $id_stmt = sqlsrv_query($conn, $id_sql, [$country]);
        if ($id_stmt === false) {
            return "Database error while checking max country_id.";
        }
        $id_row = sqlsrv_fetch_array($id_stmt, SQLSRV_FETCH_ASSOC);
        $country_id = ($id_row['max_id'] ?? 0) + 1;
        sqlsrv_free_stmt($id_stmt);

        // 3. Generate the earthquake id depending on type
        $type_lower = strtolower($type);
        $prefix = '';
        switch($type_lower) {
            case 'collapse':   $prefix = 'EC'; break;
            case 'tectonic':   $prefix = 'ET'; break;
            case 'volcanic':   $prefix = 'EV'; break;
            case 'explosion':  $prefix = 'EE'; break;
            default:           $prefix = 'EQ'; // fallback or you can return an error
        }
        $id = $prefix . '-' . $magnitude . '-' . $country . '-' . str_pad($country_id, 5, '0', STR_PAD_LEFT);

        // 4. Insert the earthquake
        $sql = "INSERT INTO earthquakes (id, country, country_id, magnitude, type, date, time, latitude, longitude, observatory_id, user_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $id, $country, $country_id, $magnitude, $type, $date, $time,
            $latitude, $longitude, $observatory_id, $user_id
        ];
        $stmt = sqlsrv_query($conn, $sql, $params);
        return $stmt; 
    }
    //Deleting earthquakes
    function delete_earthquake($conn, $id) {
        // 1. Find all artefacts linked to this earthquake
        $sqlArtefacts = "SELECT id FROM artefacts WHERE earthquake_id = ?;";
        $params = array($id);
        $stmtArtefacts = sqlsrv_query($conn, $sqlArtefacts, $params);
    
        if ($stmtArtefacts === false) {
            return false;
        }
    
        $artefact_ids = [];
        while ($row = sqlsrv_fetch_array($stmtArtefacts, SQLSRV_FETCH_ASSOC)) {
            $artefact_ids[] = $row['id'];
        }
        sqlsrv_free_stmt($stmtArtefacts);
    
        // 2. Delete each artefact
        if (!empty($artefact_ids)) {
            foreach ($artefact_ids as $artefact_id) {
                if (!delete_artefact($conn, $artefact_id)) {
                    // If deleting an artefact fails, stop and return false
                    return false;
                }
            }
        }
    
        // 3. Now delete the earthquake itself
        $sql = "DELETE FROM earthquakes WHERE id = ?;";
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            return false;
        }
        sqlsrv_free_stmt($stmt);
        return true;
    }
    //Editing earthquakes
    function edit_earthquake($conn, $id, $country, $magnitude, $type, $date, $time, $latitude, $longitude, $observatory_id) {
        // 1. Fetch the old values
        $sql = "SELECT * FROM earthquakes WHERE id = ?";
        $stmt = sqlsrv_query($conn, $sql, [$id]);
        if ($stmt === false) {
            return false;  // Query failed!
        }
        $old = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        sqlsrv_free_stmt($stmt);
    
        if(!$old) return false; // No row found
    
        // 2. See if we need to regenerate
        $regen_id = false;
        if (
            $country != $old['country'] ||
            $magnitude != $old['magnitude'] ||
            $type != $old['type']
        ) {
            $regen_id = true;
        }
    
        $new_id = $id;
        $country_id = $old['country_id'];
    
        if ($regen_id) {
            // Recompute country_id if country changed
            if ($country != $old['country']) {
                $max_sql = "SELECT MAX(country_id) as max_id FROM earthquakes WHERE country = ?";
                $max_stmt = sqlsrv_query($conn, $max_sql, [$country]);
                if ($max_stmt === false) return false;
                $max_row = sqlsrv_fetch_array($max_stmt, SQLSRV_FETCH_ASSOC);
                $country_id = ($max_row['max_id'] ?? 0) + 1;
                sqlsrv_free_stmt($max_stmt);
            }
            // Regenerate the earthquake ID
            $type_map = [
                'collapse'  => 'EC',
                'tectonic'  => 'ET',
                'volcanic'  => 'EV',
                'explosion' => 'EE'
            ];
            $prefix = isset($type_map[strtolower($type)]) ? $type_map[strtolower($type)] : 'EQ';
            $new_id = $prefix . '-' . $magnitude . '-' . $country . '-' . str_pad($country_id, 5, '0', STR_PAD_LEFT);
    
            // Update artefacts that point to the old earthquake id
            $sql_update_artefacts = "UPDATE artefacts SET earthquake_id = ? WHERE earthquake_id = ?";
            sqlsrv_query($conn, $sql_update_artefacts, [$new_id, $id]);
        }
    
        // 3. Update the earthquake record
        $sql_update = "UPDATE earthquakes SET
            id = ?,
            country = ?,
            country_id = ?,
            magnitude = ?,
            type = ?,
            date = ?,
            time = ?,
            latitude = ?,
            longitude = ?,
            observatory_id = ?
            WHERE id = ?";
        $params_update = [
            $new_id,
            $country,
            $country_id,
            $magnitude,
            $type,
            $date,
            $time,
            $latitude,
            $longitude,
            $observatory_id,
            $id
        ];
        $update_stmt = sqlsrv_query($conn, $sql_update, $params_update);
    
        if ($update_stmt === false) {
            return false;
        }
        return $update_stmt;
    }
    
    //Filtering earthquakes and observatories.
    function filter_earthquakes($conn, $min_year = null, $max_year = null, $min_magnitude = null, $max_magnitude = null, $types = [], $observatories = [], $countries = []) {
        $conditions = [];
        $params = [];
        
        // Year (assumes 'date' is in YYYY-MM-DD or similar format)
        if ($min_year !== null) {
            $conditions[] = "YEAR(date) >= ?";
            $params[] = $min_year;
        }
        if ($max_year !== null) {
            $conditions[] = "YEAR(date) <= ?";
            $params[] = $max_year;
        }
        
        // Magnitude
        if ($min_magnitude !== null) {
            $conditions[] = "magnitude >= ?";
            $params[] = $min_magnitude;
        }
        if ($max_magnitude !== null) {
            $conditions[] = "magnitude <= ?";
            $params[] = $max_magnitude;
        }
    
        // Types (IN clause)
        if (!empty($types)) {
            $placeholders = implode(',', array_fill(0, count($types), '?'));
            $conditions[] = "type IN ($placeholders)";
            $params = array_merge($params, $types);
        }
        // Observatories (IN clause)
        if (!empty($observatories)) {
            $placeholders = implode(',', array_fill(0, count($observatories), '?'));
            $conditions[] = "observatory_id IN ($placeholders)";
            $params = array_merge($params, $observatories);
        }
        // Countries (IN clause)
        if (!empty($countries)) {
            $placeholders = implode(',', array_fill(0, count($countries), '?'));
            $conditions[] = "country IN ($placeholders)";
            $params = array_merge($params, $countries);
        }
    
        $where = '';
        if (count($conditions) > 0) {
            $where = ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql = "SELECT * FROM earthquakes$where;";
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            return false;
        }
        $rows = [];
        while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
            $rows[] = $row;
        }
        sqlsrv_free_stmt($stmt);
        return $rows;
    }

?>
