<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
include '../headerNew.php';


// --- Fetch observatories (if needed for forms) ---
$obs_sql = "SELECT id, name FROM observatories";
$obs_result = sqlsrv_query($conn, $obs_sql);
$observatories = [];
if ($obs_result && sqlsrv_has_rows($obs_result)) {
    while ($obs_row = sqlsrv_fetch_array($obs_result, SQLSRV_FETCH_ASSOC)) {
        $observatories[$obs_row["id"]] = $obs_row["name"];
    }
}

$sql = "SELECT id, country, magnitude, type, date, time FROM earthquakes";
$result = sqlsrv_query($conn, $sql);
$earthquakes = [];
if($result && sqlsrv_has_rows($result)) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $earthquakes[] = $row;
    }
}

// Fetch for Add Artifact form (if needed)
$all_earthquakes_list = $earthquakes;

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/quake.css">
    <meta charset="UTF-8">
    <title>Manage Earthquakes | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<style>
    header {
        position: relative;
    }
    .main-content {
    margin-left: 320px;
    margin-top: 0;
    display: flex;
    align-items: flex-start;
    gap: 38px;
    max-width: 1440px;
    width: 100%;
    box-sizing: border-box;
    justify-content: flex-start;
    padding-bottom: 36px;
}
.glass-panel.manage-panel {
    flex: 1 1 0;
    min-width: 1000px;
    margin-right: 0;
    padding: 34px 38px 33px 38px;
    border-radius: 22px;
    box-shadow: 0 0 34px #090e206e;
    background: linear-gradient(113deg, rgba(40,44,64,0.93), rgba(22,26,38,0.96) 90%);
}
.glass-panel.add-panel {
    flex: 0 0 400px;
    min-width: 320px;
    max-width: 425px;
    margin-right: 0;
    padding: 35px 32px 37px 32px;
    border-radius: 22px;
    box-shadow: 0 0 34px #090e206e;
    background: linear-gradient(113deg,rgba(60,66,92,.94),rgba(27,33,52,.97) 93%);
}
@media (max-width: 1200px) {
    .main-content { flex-direction: column; margin-left: 0; gap: 26px; max-width: 99vw;}
    .glass-panel.manage-panel, .glass-panel.add-panel { max-width: 99vw; }
}
table { width: 100%; }
</style>

<body>

    <!-- MAIN PANEL -->
    <div class="main-content">
        <div class="glass-panel manage-panel">
            <h2>Manage Earthquakes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Country</th>
                        <th>Magnitude</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($earthquakes)): foreach ($earthquakes as $row): 
                $id = $row["id"];
                $country = $row["country"];
                $magnitude = $row["magnitude"];
                $type = $row["type"];
                $date = $row["date"] instanceof DateTime ? $row["date"]->format('d.m.y') : $row["date"];
                $time = $row["time"] instanceof DateTime ? $row["time"]->format('H:i') : $row["time"];
            ?>
                    <tr>
                        <td><?=htmlspecialchars($id)?></td>
                        <td><?=htmlspecialchars($country)?></td>
                        <td><?=htmlspecialchars($magnitude)?></td>
                        <td><?=htmlspecialchars($date)?></td>
                        <td><?=htmlspecialchars($time)?></td>
                        <td><?=htmlspecialchars(ucfirst($type))?></td>
                        <td>
                            <form action="process_delete_earthquake.php" method="POST" style="margin:0;">
                                <input type="hidden" name="id" value="<?=$id?>">
                                <button type="submit" class="delete-btn" title="Delete">
                                    <img src="/assets/icons/rubbish.svg" alt="Delete"
                                        style="height:1.1em;vertical-align:middle;margin:0;padding:0;">
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#eee;">No earthquakes found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- SIDE PANELS -->
        <div class="glass-panel add-panel">
            <h1>Add Earthquake</h1>
            <form method="POST" action="process_earthquake.php" autocomplete="off">
                <div class="q-field"><label for="country">Country</label>
                    <input type="text" name="country" id="country" required autocomplete="off">
                </div>
                <div class="q-field"><label for="magnitude">Magnitude</label>
                    <input type="number" name="magnitude" id="magnitude" step="0.1" min="0.1" max="10" required>
                </div>
                <div class="q-field"><label for="type">Type</label>
                    <select name="type" id="type" required>
                        <option value="">Select type...</option>
                        <option value="tectonic">Tectonic</option>
                        <option value="volcanic">Volcanic</option>
                        <option value="collapse">Collapse</option>
                        <option value="explosion">Explosion</option>
                    </select>
                </div>
                <div class="q-field"><label for="date">Date dd/mm/yyyy</label>
                    <input type="date" name="date" id="date" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy"
                        required>
                </div>
                <div class="q-field"><label for="time">Time --:--</label>
                    <input type="time" name="time" id="time" pattern="\d{2}:\d{2}" placeholder="hh:mm" required>
                </div>
                <div class="q-field"><label for="latitude">Latitude ≤|90|</label>
                    <input type="number" name="latitude" id="latitude" step="0.0001" min="-90" max="90">
                </div>
                <div class="q-field"><label for="longitude">Longitude ≤|180|</label>
                    <input type="number" name="longitude" id="longitude" step="0.0001" min="-180" max="180">
                </div>
                <div class="q-field"><label for="observatory_id">Observatory</label>
                    <select name="observatory_id" id="observatory_id" required>
                        <option value="">Select...</option>
                        <?php foreach ($observatories as $obs_id=>$obs_name): ?>
                        <option value="<?=$obs_id?>"><?=htmlspecialchars($obs_name)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <button type="submit" class="add-btn" style="min-width:90px;">Add</button>
                    <button type="reset" class="delete-btn" title="Clear">
                        <img src="/assets/icons/rubbish.svg" alt="Clear" style="height:1.6em;">
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Toggle delete button disabled/enabled
    document.getElementById('edit-switch').addEventListener('change', function() {
        const enabled = this.checked;
        document.querySelectorAll('.delete-btn[data-table-delete]').forEach(btn => {
            btn.disabled = !enabled;
            if (!enabled) btn.classList.add('disabled')
            else btn.classList.remove('disabled')
        });
    });
    </script>
    <div id="delete-modal" class="q-modal-backdrop" style="display:none">
        <div class="q-modal">
            <h3>Confirm Delete</h3>
            <p style="font-size:1.12em; color:#fff; margin:18px 0 30px 0;">Are you sure you want to delete this
                earthquake?</p>
            <div class="q-modal-actions">
                <button type="button" class="add-btn" id="cancel-delete">Cancel</button>
                <button type="button" class="delete-btn" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
    <script>
    let formToDelete = null;

    // Intercept submit for all delete artefact forms
    document.querySelectorAll('form[action="process_delete_earthquake.php"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
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
        if (formToDelete) formToDelete.submit();
        document.getElementById('delete-modal').style.display = 'none';
        formToDelete = null;
    };
    </script>
</body>

</html>