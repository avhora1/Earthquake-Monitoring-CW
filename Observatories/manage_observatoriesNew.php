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
                            <button type="button" class="delete-btn" data-table-delete disabled>
                                <img src="/assets/delete.svg" alt="Del" style="width:16px;height:16px;">
                            </button>
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
                <form class="form-inline" method="POST" action="add_observatory.php">
                    <div>
                        <label for="name">Name</label>
                        <input name="name" id="name" type="text" required autocomplete="off">
                    </div>
                    <div>
                        <label for="date">Est. Date</label>
                        <input name="date" id="date" type="text" placeholder="YY.MM.DD" required autocomplete="off">
                    </div>
                    <div>
                        <label for="lat">Latitude ≤|90|</label>
                        <input name="lat" id="lat" type="number" step="any" min="-90" max="90" required>
                    </div>
                    <div>
                        <label for="lng">Longitude ≤|90|</label>
                        <input name="lng" id="lng" type="number" step="any" min="-90" max="90" required>
                    </div>
                    <div class="pallet-btns" style="justify-content:flex-start;margin-top:18px;">
                        <button type="submit" class="add-btn">Add</button>
                        <button type="reset" title="Clear" class="delete-btn" style="padding:8px 18px;"><img src="/assets/delete.svg" alt="" style="width:19px;height:19px;filter:none;"></button>
                    </div>
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
</body>
</html>