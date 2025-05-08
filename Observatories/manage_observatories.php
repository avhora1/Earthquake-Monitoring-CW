<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Observatories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 20px; font-weight: bold; color: #212529; }
        .table-container { margin-top: 30px; max-width: 98%; margin-left: auto; margin-right: auto; }
        .modal { color: #000; }
    </style>
</head>
<body>
<?php include '../header.php'; ?>

<!-- Toast for successful edit -->
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Observatory updated successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
// Timeout for toast
setTimeout(function() {
  var toastEl = document.getElementById('updateToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 3000);
</script>
<?php endif; ?>

<!-- Toast when observatory is deleted-->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="deleteToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Observatory deleted successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
setTimeout(function() {
  var toastEl = document.getElementById('deleteToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 3000);
</script>
<?php endif; ?>

<!-- Toast when there is a foreign key violation -->
<?php if (isset($_GET['fkviolation'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="errorToast" class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Cannot delete observatory: there are earthquakes or other records linked to it. Remove/reassign them first!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
setTimeout(function() {
  var toastEl = document.getElementById('errorToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 5000);
</script>
<?php endif; ?>

<div class="table-container container">
    <h1>Manage Observatories</h1>
    <?php
    include '../connection.php';

    $sql = "SELECT * FROM observatories";
    $result = sqlsrv_query($conn, $sql);

    if (sqlsrv_has_rows($result)) {
        echo "<table class='table table-dark table-striped table-bordered table-hover'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Establishment Date</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Actions</th>
            </tr>";
        echo "</thead><tbody>";

        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $id = $row['id'];
            $name = $row['name'];
            $est_date = $row['est_date'] instanceof DateTime ? $row['est_date']->format('Y-m-d') : $row['est_date'];
            $latitude = $row['latitude'];
            $longitude = $row['longitude'];

            echo "<tr>";
            echo "<td>" . htmlspecialchars($id) . "</td>";
            echo "<td>" . htmlspecialchars($name) . "</td>";
            echo "<td>" . htmlspecialchars($est_date) . "</td>";
            echo "<td>" . htmlspecialchars($latitude) . "</td>";
            echo "<td>" . htmlspecialchars($longitude) . "</td>";
            echo "<td>
                <button class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#editModal$id'>Edit</button>
                <form id='deleteForm$id' action='process_delete_observatory.php' method='POST' style='display:inline;'>
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
                        Are you sure you want to delete observatory <b>#<?php echo htmlspecialchars($id); ?></b>?
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
                    <form action="process_edit_observatory.php" method="POST" onsubmit="return validateObservatoryEdit(this)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?php echo $id; ?>">Edit Observatory #<?php echo $id; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                            <div class="mb-3">
                                <label for="name_<?php echo $id; ?>" class="form-label">Name:</label>
                                <input type="text" id="name_<?php echo $id; ?>" name="name" class="form-control" required value="<?php echo htmlspecialchars($name); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="est_date_<?php echo $id; ?>" class="form-label">Establishment Date:</label>
                                <input type="date" id="est_date_<?php echo $id; ?>" name="est_date" class="form-control" required value="<?php echo htmlspecialchars($est_date); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="latitude_<?php echo $id; ?>" class="form-label">Latitude:</label>
                                <input type="number" step="0.000001" id="latitude_<?php echo $id; ?>" name="latitude" class="form-control" required value="<?php echo htmlspecialchars($latitude); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="longitude_<?php echo $id; ?>" class="form-label">Longitude:</label>
                                <input type="number" step="0.000001" id="longitude_<?php echo $id; ?>" name="longitude" class="form-control" required value="<?php echo htmlspecialchars($longitude); ?>">
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
        echo "<div class='alert alert-warning' role='alert'>No observatories found.</div>";
    }
    sqlsrv_close($conn);
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>