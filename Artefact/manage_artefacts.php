<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artefacts</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 20px; font-weight: bold; color: #212529; }
        .table-container { margin-top: 30px; max-width: 98%; margin-left: auto; margin-right: auto; }
        .modal { color: #000; }
    </style>
</head>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<body>
<?php include '../header.php'; ?>

<!-- Toast for when you update your artefact-->
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Artefact updated successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
  // Automatically dismiss after 3 seconds, or user can close manually
  setTimeout(function() {
    var toastEl = document.getElementById('updateToast');
    if (toastEl) {
      var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.hide();
    }
  }, 3000);
</script>
<?php endif; ?>

<!-- Toast for when you delete an artefact as confirmation-->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Artefact deleted successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
  // Automatically dismiss after 3 seconds, or user can close manually
  setTimeout(function() {
    var toastEl = document.getElementById('updateToast');
    if (toastEl) {
      var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.hide();
    }
  }, 3000);
</script>
<?php endif; ?>
<div class="table-container container">
    <h1>Manage Artefacts</h1>
    <?php
    include '../connection.php';

    // Fetch earthquake list (id + country + date)
    $earthquakes_sql = "SELECT id, country, date FROM earthquakes";
    $earthquakes_result = sqlsrv_query($conn, $earthquakes_sql);
    $earthquakes = [];
    if ($earthquakes_result && sqlsrv_has_rows($earthquakes_result)) {
        while ($row = sqlsrv_fetch_array($earthquakes_result, SQLSRV_FETCH_ASSOC)) {
            if ($row["date"] instanceof DateTime) {
                $formattedDate = $row["date"]->format('Y-m-d');
            } else {
                $formattedDate = $row["date"];
            }
            $earthquakes[$row["id"]] = $row["country"] . " - " . $formattedDate;
        }
    }

    // Fetch pallet list (id only)
    $pallet_sql = "SELECT id FROM pallets";
    $pallet_res = sqlsrv_query($conn, $pallet_sql);
    $pallets = [];
    if ($pallet_res && sqlsrv_has_rows($pallet_res)) {
        while ($row = sqlsrv_fetch_array($pallet_res, SQLSRV_FETCH_ASSOC)) {
            $pallets[] = $row["id"];
        }
    }

    // Fetch artefact list
    $sql = "SELECT * FROM artefacts";
    $result = sqlsrv_query($conn, $sql);
    if ($result === false) {
        die('<div class="alert alert-danger">Error fetching data from the database.</div>');
    }
    if (sqlsrv_has_rows($result)) {
        echo "<table class='table table-dark table-striped table-hover align-middle'>";
        echo "<thead><tr>
            <th>ID</th>
            <th>Earthquake</th>
            <th>Type</th>
            <th>Timestamp</th>
            <th>Shelving Location</th>
            <th>Pallet ID</th>
            <th>Required</th>
            <th>Actions</th>
        </tr></thead><tbody>";
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $id = $row["id"] ?? "";
            $earthquake_id = $row["earthquake_id"] ?? "";
            $type = $row["type"] ?? "";
            $timestamp = ($row["time_stamp"] instanceof DateTime) ? $row["time_stamp"]->format('Y-m-d H:i:s') : ($row["time_stamp"] ?? "");
            $shelving_loc = $row["shelving_loc"] ?? "";
            $pallet_id = $row["pallet_id"] ?? "N/A";
            $required = $row["required"] ?? "";

            // Table row
            echo "<tr>
                <td>".htmlspecialchars($id)."</td>
                <td>" . (isset($earthquakes[$earthquake_id]) ? htmlspecialchars($earthquakes[$earthquake_id]) : "N/A") . "</td>
                <td>".htmlspecialchars($type)."</td>
                <td>".htmlspecialchars($timestamp)."</td>
                <td>".htmlspecialchars($shelving_loc)."</td>
                <td>".htmlspecialchars($pallet_id)."</td>
                <td>".htmlspecialchars($required)."</td>
                <td>
                  <button 
                    class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#editModal$id'>
                    Edit
                  </button>
                  <form id='deleteForm$id' action='process_delete_artefact.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='".htmlspecialchars($id)."'>
                        <button type='button'
                            class='btn btn-danger btn-sm mb-1'
                            data-bs-toggle='modal'
                            data-bs-target='#confirmDeleteModal$id'>
                            Delete
                        </button>
                    </form>
                </td>
            </tr>";
            ?>

            <!-- Confirm Delete Modal -->
            <div class="modal fade" id="confirmDeleteModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="confirmDeleteLabel<?php echo $id; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel<?php echo $id; ?>">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete artefact <b>#<?php echo htmlspecialchars($id); ?></b>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger"
                        onclick="document.getElementById('deleteForm<?php echo $id; ?>').submit();">
                        Yes, Delete
                    </button>
                </div>
                </div>
            </div>
            </div>
            <?php

            // ----- EDIT MODAL -----
            ?>
            <div class="modal fade" id="editModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $id; ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="process_edit_artefact.php" method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel<?php echo $id; ?>">Edit Artefact #<?php echo $id; ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        <!-- Earthquake -->
                        <div class="mb-3">
                            <label for="earthquake_id_<?php echo $id; ?>" class="form-label">Earthquake</label>
                            <select id="earthquake_id_<?php echo $id; ?>" name="earthquake_id" class="form-select" required>
                                <?php
                                foreach ($earthquakes as $eq_id => $eq_label) {
                                    echo "<option value='" . htmlspecialchars($eq_id) . "'";
                                    if ($earthquake_id == $eq_id) echo " selected";
                                    echo ">" . htmlspecialchars($eq_label) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type_<?php echo $id; ?>" class="form-label">Type:</label>
                            <select id="type_<?php echo $id; ?>" name="type" class="form-select" required>
                                <option value="solidified lava" <?php if($type==="solidified lava") echo "selected"; ?>>Solidified Lava</option>
                                <option value="foreign debris" <?php if($type==="foreign debris") echo "selected"; ?>>Foreign Debris</option>
                                <option value="ash sample" <?php if($type==="ash sample") echo "selected"; ?>>Ash Sample</option>
                                <option value="ground soil" <?php if($type==="ground soil") echo "selected"; ?>>Ground Soil</option>
                            </select>
                        </div>
                        <!-- Shelving Location -->
                        <div class="mb-3">
                            <label for="shelving_loc_<?php echo $id; ?>" class="form-label">Shelving Location:</label>
                            <select id="shelving_loc_<?php echo $id; ?>" name="shelving_loc" class="form-select" required>
                                <?php
                                foreach (range('A', 'L') as $char) {
                                    echo "<option value='$char'";
                                    if($shelving_loc==$char) echo " selected";
                                    echo ">$char</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Pallet ID -->
                        <div class="mb-3">
                            <label for="pallet_id_<?php echo $id; ?>" class="form-label">Pallet ID:</label>
                            <select id="pallet_id_<?php echo $id; ?>" name="pallet_id" class="form-select">
                                <option value="">No pallet</option>
                                <?php
                                foreach ($pallets as $pID) {
                                    echo "<option value='" . htmlspecialchars($pID) . "'";
                                    if ($pallet_id == $pID) echo " selected";
                                    echo ">$pID</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Required -->
                        <div class="mb-3">
                          <label for="required_<?php echo $id; ?>" class="form-label">Required:</label>
                          <span 
                              data-bs-toggle="tooltip" 
                              data-bs-placement="top" 
                              title="Cannot modify the required field. It can only be changed when adding artefact to the shop"
                              style="display:inline-block;width:100%;">
                              <select id="required_<?php echo $id; ?>" name="required" class="form-select" disabled aria-disabled="true" style="pointer-events: none;">
                                  <option value="Yes" <?php if($required=="Yes") echo "selected"; ?>>Yes</option>
                                  <option value="No" <?php if($required=="No") echo "selected"; ?>>No</option>
                              </select>
                          </span>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-warning">Save Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <?php
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning text-center'>No artefacts found.</div>";
    }
    sqlsrv_close($conn);
    ?>
</div>
<!-- Bootstrap JS Bundle (for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>