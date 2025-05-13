<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pallets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 20px; font-weight: bold; color: #212529; }
        .table-container { margin-top: 30px; max-width: 98%; margin-left: auto; margin-right: auto; }
        .modal { color: #000; }
        .artefact-table { margin: 0; background: #222; }
        .artefact-table th, .artefact-table td { padding: 0.3rem 0.6rem; font-size: 0.92rem;}
    </style>
</head>
<body>
<?php include '../header.php'; ?>

<!-- Toasts for successful pallet edit -->
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="updateToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Pallet updated successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
setTimeout(function() {
  var toastEl = document.getElementById('updateToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 3000);
</script>
<?php endif; ?>

<!-- Deleted pallet toast-->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="deleteToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Pallet deleted successfully!
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

<!-- FK assoc toast-->
<?php if (isset($_GET['fkviolation'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="errorToast" class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Cannot delete pallet: there are artefacts linked to this pallet. Remove them first!
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

<!-- Deleted artefact toast-->
<?php if (isset($_GET['deleted_artefact']) && $_GET['deleted_artefact'] == 1): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="deleteArtefactAssocToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Artefact deleted successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
setTimeout(function() {
  var toastEl = document.getElementById('deleteArtefactAssocToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 3000);
</script>
<?php endif; ?>

<!-- Shop assoc violation -->
<?php if (isset($_GET['shopviolation'])): ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="shopErrorToast" class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Cannot delete artefact: This artefact is currently in the shop. Remove it from the shop first!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
setTimeout(function() {
  var toastEl = document.getElementById('shopErrorToast');
  if (toastEl) {
    var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.hide();
  }
}, 5000);
</script>
<?php endif; ?>

<div class="table-container container">
    <h1>Manage Pallets</h1>
    <?php
    include '../connection.php';

    $sql = "SELECT * FROM pallets";
    $result = sqlsrv_query($conn, $sql);

    if (sqlsrv_has_rows($result)) {
        echo "<table class='table table-dark table-striped table-bordered table-hover'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>
                <th>ID</th>
                <th>Pallet Size</th>
                <th>Arrival Date</th>
                <th>Artefacts On Pallet</th>
                <th>Actions</th>
            </tr>";
        echo "</thead><tbody>";

        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $id = $row['id'];
            $pallet_size = $row['pallet_size'];
            $arrival_date = $row['arrival_date'] instanceof DateTime ? $row['arrival_date']->format('Y-m-d H:i:s') : $row['arrival_date'];

            echo "<tr>";
            echo "<td>" . htmlspecialchars($id) . "</td>";
            echo "<td>" . htmlspecialchars(ucfirst($pallet_size)) . "</td>";
            echo "<td>" . htmlspecialchars($arrival_date) . "</td>";

            // --- List artefacts on this pallet ---
            echo "<td>";
            $asql = "SELECT * FROM artefacts WHERE pallet_id = ?";
            $ares = sqlsrv_query($conn, $asql, [$id]);
            if (sqlsrv_has_rows($ares)) {
                echo "<table class='artefact-table table table-bordered table-hover table-sm table-dark'><thead>
                        <tr>
                          <th>ID</th>
                          <th>Type</th>
                          <th>Remove</th>
                        </tr>
                    </thead><tbody>";
                while ($arow = sqlsrv_fetch_array($ares, SQLSRV_FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($arow['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($arow['type']) . "</td>";
                    echo "<td>
                        <form action='process_delete_artefact.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='".htmlspecialchars($arow['id'])."'>
                            <button type='submit' class='btn btn-danger btn-xs btn-sm'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<span class='text-success'>No artefacts</span>";
            }
            echo "</td>";

            // --- Pallet Actions column ---
            echo "<td>
                <button class='btn btn-warning btn-sm mb-1' data-bs-toggle='modal' data-bs-target='#editModal$id'>Edit</button>
                <form id='deleteForm$id' action='process_delete_pallet.php' method='POST' style='display:inline;'>
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
                        Are you sure you want to delete pallet <b>#<?php echo htmlspecialchars($id); ?></b>?
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
                    <form action="process_edit_pallet.php" method="POST" onsubmit="return validateEditPallet(this)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?php echo $id; ?>">Edit Pallet #<?php echo $id; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                            <div class="mb-3">
                                <label for="pallet_size_<?php echo $id; ?>" class="form-label">Pallet Size:</label>
                                <select id="pallet_size_<?php echo $id; ?>" name="pallet_size" class="form-select" required>
                                    <option value="half" <?php if($pallet_size==='half') echo "selected"; ?>>Half</option>
                                    <option value="full" <?php if($pallet_size==='full') echo "selected"; ?>>Full</option>
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
        echo "<div class='alert alert-warning' role='alert'>No pallets found.</div>";
    }
    sqlsrv_close($conn);
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>