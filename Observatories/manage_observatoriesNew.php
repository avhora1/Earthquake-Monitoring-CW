<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';


// Query the observatories
$sql = "SELECT * FROM observatories";
$result = sqlsrv_query($conn, $sql);
$observatories = [];
if ($result && sqlsrv_has_rows($result)) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        // Format date if datetime
        if (isset($row["est_date"]) && $row["est_date"] instanceof DateTime) {
            $row["est_date"] = $row["est_date"]->format("y.m.d");
        }
        $observatories[] = $row;
    }
}
sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Observatories | Quake</title>
    <link rel="stylesheet" href="../assets/css/quake.css">
    <?php include '../headerNew.php'?>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <ul class="sidebar-nav">
            <li><a href="#"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
            <li class="active"><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
            <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
            <li><a href="#"><img src="/assets/icons/box.svg">Pallets</a></li>
            <li><a href="#"><img src="/assets/icons/artifact.svg">Artifacts</a></li>
            <li><a href="#"><img src="/assets/icons/shop.svg">Shop</a></li>
            <li><a href="#"><img src="/assets/icons/team.svg">Team</a></li>
            <li><a href="#"><img src="/assets/icons/account.svg">Account</a></li>
        </ul>
        <div class="sidebar-logout">
            <a href="/logout.php"><img src="/assets/icons/logout.svg"> Log out</a>
        </div>
    </aside>

    <!-- MAIN PANELS -->
    <main class="main-content">
        <!-- Manage Observatories Table Panel -->
        <div class="glass-panel manage-panel">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h2 style="margin-bottom:0;">Manage Observatories</h2>
                <div style="display: flex; align-items:center; gap:13px;"></div>
            </div>

            <table class="observatories-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Est. Date</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($observatories)): ?>
                    <?php foreach($observatories as $obs): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($obs['id']); ?></td>
                        <td><?php echo htmlspecialchars($obs['name']); ?></td>
                        <td><?php echo htmlspecialchars($obs['est_date']); ?></td>
                        <td><?php echo htmlspecialchars($obs['latitude']); ?></td>
                        <td><?php echo htmlspecialchars($obs['longitude']); ?></td>
                        <td>
                        <form action="process_delete_observatory.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id" value="<?=$obs['id']?>">
                            <button type="submit" class="delete-btn" title="Delete"><img src="/assets/icons/rubbish.svg" alt="Delete" style="height:1.1em;vertical-align:middle;margin:0;padding:0;"></button>
                        </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php for($i=0;$i<max(0,6-count($observatories));$i++): ?>
                        <tr class="empty-row"><td colspan="6"></td></tr>
                    <?php endfor; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;color:orange;">No observatories found.</td></tr>
                    <?php for($i=0;$i<6;$i++): ?>
                        <tr class="empty-row"><td colspan="6"></td></tr>
                    <?php endfor; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Side Panels (Add form + image)-->
        <div class="side-panels">
            <!-- Add Observatory Form Panel -->
            <div class="side-panel add-panel">
                <h3>Add Observatory</h3>
                <form class="form-inline" method="POST" action="process_form.php">
                    <div>
                        <label for="name">Name</label>
                        <input name="name" id="name" type="text" required autocomplete="off">
                    </div>
                    <div class="q-field"><label for="date">Est. Date</label>
                        <input type="date" name="est_date" id="est_date" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="q-field"><label for="latitude">Latitude ≤|90|</label>
                        <input type="number" name="latitude" id="latitude" step="0.0001" min="-90" max="90">
                    </div>
                    <div class="q-field"><label for="longitude">Longitude ≤|180|</label>
                        <input type="number" name="longitude" id="longitude" step="0.0001" min="-180" max="180">
                    </div>
                    <div class="input-group">
                        <button type="submit" class="add-btn">Add</button>
                        <button type="reset" class="delete-btn" title="Clear"><img src="/assets/icons/rubbish.svg" alt="Clear" style="height:1.2em;"></button>
                </form>
            </div>
        </div>
    </main>
    <script>
    // Toggle delete button disabled/enabled
    document.getElementById('edit-switch').addEventListener('change', function(){
        const enabled = this.checked;
        document.querySelectorAll('.delete-btn[data-table-delete]').forEach(btn => {
            btn.disabled = !enabled;
            if(!enabled) btn.classList.add('disabled')
            else btn.classList.remove('disabled')
        });
    });
    </script>
<div id="delete-modal" class="q-modal-backdrop" style="display:none">
  <div class="q-modal">
    <h3>Confirm Delete</h3>
    <p style="font-size:1.12em; color:#fff; margin:18px 0 30px 0;">Are you sure you want to delete this observatory?</p>
    <div class="q-modal-actions">
      <button type="button" class="add-btn" id="cancel-delete">Cancel</button>
      <button type="button" class="delete-btn" id="confirm-delete">Delete</button>
    </div>
  </div>
</div>
<script>
let formToDelete = null;

// Intercept submit for all delete artefact forms
document.querySelectorAll('form[action="process_delete_observatory.php"]').forEach(function(form){
  form.addEventListener('submit', function(e){
    e.preventDefault();
    formToDelete = form;
    document.getElementById('delete-modal').style.display = 'flex';
  });
});
document.getElementById('cancel-delete').onclick = function() {
  document.getElementById('delete-modal').style.display = 'none';
  formToDelete = null;
};
document.getElementById('confirm-delete').onclick = function() {
  if(formToDelete) formToDelete.submit();
  document.getElementById('delete-modal').style.display = 'none';
  formToDelete = null;
};
</script>
</body>
</html>