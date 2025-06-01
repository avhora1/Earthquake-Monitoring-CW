<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

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
    <?php include '../headerNew.php';?>
    <link rel="stylesheet" href="../assets/css/quake.css">
    <meta charset="UTF-8">
    <title>Manage Earthquakes | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Urbanist:700,600,400|Roboto:400,500,700&display=swap" rel="stylesheet">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <li<?php if(strpos($_SERVER['REQUEST_URI'], 'earthquakes')!==false) echo ' class="active"'; ?>><a href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
        <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li><a href="../Pallet/manage_palletsNew.php"><img src="/assets/icons/box.svg">Pallets</a></li>
        <li><a href="/Artefact/manage_artefactsNew.php"><img src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li><a href="/shop/shop.php"><img src="/assets/icons/shop.svg">Shop</a></li>
        <li><a href="#"><img src="/assets/icons/team.svg">Team</a></li>
        <li><a href="../Account_Managment/accountNew.php"><img src="/assets/icons/account.svg">Account</a></li>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><img src="/assets/icons/logout.svg">Log out</a>
    </div>
</div>

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
                                <img src="/assets/icons/rubbish.svg" alt="Delete" style="height:1.1em;vertical-align:middle;margin:0;padding:0;">
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7" style="text-align:center;color:#eee;">No earthquakes found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- SIDE PANELS -->
    <div class="side-panel add-earthquake">
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
            <input type="date" name="date" id="date" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/yyyy" required>
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
</body>
</html>