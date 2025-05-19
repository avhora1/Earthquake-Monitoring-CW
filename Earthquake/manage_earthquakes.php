<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Earthquakes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 20px; font-weight: bold; color: #212529; }
        .table-container { margin-top: 30px; max-width: 98%; margin-left: auto; margin-right: auto; }
        .modal { color: #000; }
    </style>
</head>
<body>
<?php if (isset($_GET['fkviolation'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Cannot delete earthquake: there are artefacts or other records linked to it. Remove/reassign them first!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
  setTimeout(function() {
    var toastEl = document.querySelector('.toast');
    if (toastEl) {
      var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.hide();
    }
  }, 5000);
</script>
<?php endif; ?>
<?php include '../header.php'; ?>
<!-- Toast for when you update your artefact-->
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Earthquake updated successfully!
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
        Earthquake deleted successfully!
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
    <h1>Manage Earthquakes</h1>
    <?php
    include '../connection.php';

    // Fetch observatories for the dropdown
    $obs_sql = "SELECT id, name FROM observatories";
    $obs_result = sqlsrv_query($conn, $obs_sql);
    $observatories = [];
    if ($obs_result && sqlsrv_has_rows($obs_result)) {
        while ($obs_row = sqlsrv_fetch_array($obs_result, SQLSRV_FETCH_ASSOC)) {
            $observatories[$obs_row["id"]] = $obs_row["name"];
        }
    }

    $sql = "SELECT e.id, e.country, e.magnitude, e.type, e.date, e.time, e.latitude, e.longitude, e.observatory_id, o.name AS observatory_name
            FROM earthquakes e
            LEFT JOIN observatories o ON e.observatory_id = o.id";
    $result = sqlsrv_query($conn, $sql);

    if (sqlsrv_has_rows($result)) {
        echo "<table class='table table-dark table-striped table-bordered table-hover'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>
                <th>ID</th>
                <th>Country</th>
                <th>Magnitude</th>
                <th>Type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Observatory</th>
                <th>Actions</th>
            </tr>";
        echo "</thead><tbody>";

        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $id = $row["id"];
            $country = $row["country"];
            $magnitude = $row["magnitude"];
            $type = $row["type"];
            $date = $row["date"] instanceof DateTime ? $row["date"]->format('Y-m-d') : $row["date"];
            $time = $row["time"] instanceof DateTime ? $row["time"]->format('H:i:s') : $row["time"];
            $latitude = $row["latitude"];
            $longitude = $row["longitude"];
            $observatory_id = $row["observatory_id"];
            $observatory_name = $row["observatory_name"];

            echo "<tr>";
            echo "<td>" . htmlspecialchars($id) . "</td>";
            echo "<td>" . htmlspecialchars($country) . "</td>";
            echo "<td>" . htmlspecialchars($magnitude) . "</td>";
            echo "<td>" . htmlspecialchars($type) . "</td>";
            echo "<td>" . htmlspecialchars($date) . "</td>";
            echo "<td>" . htmlspecialchars($time) . "</td>";
            echo "<td>" . htmlspecialchars($latitude) . "</td>";
            echo "<td>" . htmlspecialchars($longitude) . "</td>";
            echo "<td>" . htmlspecialchars($observatory_name) . "</td>";
            echo "<td>
                <button class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#editModal$id'>Edit</button>
                <form id='deleteForm$id' action='process_delete_earthquake.php' method='POST' style='display:inline;'>
                    <input type='hidden' name='id' value='".htmlspecialchars($id)."'>
                    <button type='button' class='btn btn-danger btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#confirmDeleteModal$id'>Delete</button>
                </form>
            </td>";
            echo "</tr>";

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
                        Are you sure you want to delete earthquake <b>#<?php echo htmlspecialchars($id); ?></b>?
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

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $id; ?>" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <form action="process_edit_earthquake.php" method="POST" onsubmit="return validateEditEarthquake(this)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?php echo $id; ?>">Edit Earthquake #<?php echo $id; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                            <!-- Country -->
                            <div class="mb-3">
                                <label for="country_<?php echo $id; ?>" class="form-label">Country:</label>
                                <input type="text" id="country_<?php echo $id; ?>" name="country" class="form-control" required
                                    value="<?php echo htmlspecialchars($country); ?>">
                            </div>
                            <!-- Magnitude -->
                            <div class="mb-3">
                                <label for="magnitude_<?php echo $id; ?>" class="form-label">Magnitude:</label>
                                <input type="number" step="0.1" id="magnitude_<?php echo $id; ?>" name="magnitude" class="form-control" required
                                    value="<?php echo htmlspecialchars($magnitude); ?>">
                            </div>
                            <!-- Type -->
                            <div class="mb-3">
                                <label for="type_<?php echo $id; ?>" class="form-label">Type:</label>
                                <select id="type_<?php echo $id; ?>" name="type" class="form-select" required>
                                    <option value="tectonic" <?php if($type==='tectonic') echo "selected"; ?>>Tectonic</option>
                                    <option value="volcanic" <?php if($type==='volcanic') echo "selected"; ?>>Volcanic</option>
                                    <option value="collapse" <?php if($type==='collapse') echo "selected"; ?>>Collapse</option>
                                    <option value="explosion" <?php if($type==='explosion') echo "selected"; ?>>Explosion</option>
                                </select>
                            </div>
                            <!-- Date -->
                            <div class="mb-3">
                                <label for="date_<?php echo $id; ?>" class="form-label">Date:</label>
                                <input type="date" id="date_<?php echo $id; ?>" name="date" class="form-control" required
                                    value="<?php echo htmlspecialchars($date); ?>">
                            </div>
                            <!-- Time -->
                            <div class="mb-3">
                                <label for="time_<?php echo $id; ?>" class="form-label">Time:</label>
                                <input type="time" id="time_<?php echo $id; ?>" name="time" class="form-control" required
                                    value="<?php echo htmlspecialchars($time); ?>">
                            </div>
                            <!-- Latitude -->
                            <div class="mb-3">
                                <label for="latitude_<?php echo $id; ?>" class="form-label">Latitude:</label>
                                <input type="number" step="0.000001" id="latitude_<?php echo $id; ?>" name="latitude" class="form-control" required
                                    value="<?php echo htmlspecialchars($latitude); ?>">
                            </div>
                            <!-- Longitude -->
                            <div class="mb-3">
                                <label for="longitude_<?php echo $id; ?>" class="form-label">Longitude:</label>
                                <input type="number" step="0.000001" id="longitude_<?php echo $id; ?>" name="longitude" class="form-control" required
                                    value="<?php echo htmlspecialchars($longitude); ?>">
                            </div>
                            <!-- Observatory -->
                            <div class="mb-3">
                                <label for="observatory_id_<?php echo $id; ?>" class="form-label">Observatory:</label>
                                <select id="observatory_id_<?php echo $id; ?>" name="observatory_id" class="form-select" required>
                                    <?php
                                    foreach ($observatories as $oid => $oname) {
                                        echo "<option value='" . htmlspecialchars($oid) . "'";
                                        if ($observatory_id == $oid) echo " selected";
                                        echo ">" . htmlspecialchars($oname) . "</option>";
                                    }
                                    ?>
                                </select>
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
        echo "<div class='alert alert-warning' role='alert'>No earthquakes found.</div>";
    }
    sqlsrv_close($conn);
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>