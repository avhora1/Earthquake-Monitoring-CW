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
    <meta charset="UTF-8">
    <title>Manage Earthquakes | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Urbanist:700,600,400|Roboto:400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/quake.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <li<?php if(strpos($_SERVER['REQUEST_URI'], 'earthquakes')!==false) echo ' class="active"'; ?>><a href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
        <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li><a href="#"><img src="/assets/icons/box.svg">Pallets</a></li>
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
    <div class="side-panels">
        <div class="side-panel">
            <h3 style="margin-bottom:18px;">Add Pallet</h3>
            <div class="pallet-btns">
                <button class="pallet-btn-half" onclick="location.href='add_pallet.php?type=half'">Half</button>
                <button class="pallet-btn-full" onclick="location.href='add_pallet.php?type=full'">Full</button>
            </div>
        </div>
        <div class="side-panel add-panel">
            <h3>Add Artifact</h3>
            <form method="POST" action="process_add_artefact.php">
                <label>Earthquake
                    <select name="earthquake_id" required>
                        <option value="">Select...</option>
                        <?php foreach ($all_earthquakes_list as $eq_row) {
                            $eq_id = $eq_row['id'];
                            $eq_country = htmlspecialchars($eq_row['country']);
                            $eq_date = ($eq_row['date'] instanceof DateTime) ? $eq_row['date']->format('d.m.y') : $eq_row['date'];
                            echo "<option value='$eq_id'>{$eq_country} - {$eq_date}</option>";
                        } ?>
                    </select>
                </label>
                <label>Type
                    <select name="type" required>
                        <option value="">Pick type</option>
                        <option value="solidified lava">Solidified Lava</option>
                        <option value="foreign debris">Foreign Debris</option>
                        <option value="ash sample">Ash Sample</option>
                        <option value="ground soil">Ground Soil</option>
                    </select>
                </label>
                <label>Shelf
                    <select name="shelving_loc" required>
                        <?php foreach (range('A', 'L') as $char) {
                            echo "<option value='$char'>$char</option>";
                        } ?>
                    </select>
                </label>
                <label>Description
                    <textarea name="description" maxlength="255"></textarea>
                </label>
                <div class="input-group">
                    <label for="add_shop_switch">Add to shop</label>
                    <label class="switch">
                        <input name="required" type="checkbox" id="add_shop_switch" value="Yes">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="input-group">
                    <button type="submit" class="add-btn">Add</button>
                    <button type="reset" class="delete-btn" title="Clear"><img src="/assets/icons/rubbish.svg" alt="Clear" style="height:1.2em;"></button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>