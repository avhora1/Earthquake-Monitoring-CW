<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// --- Fetch earthquake and artefact data, as before ---
$earthquakes_sql = "SELECT id, country, date FROM earthquakes";
$earthquakes_result = sqlsrv_query($conn, $earthquakes_sql);
$earthquakes = [];
if ($earthquakes_result && sqlsrv_has_rows($earthquakes_result)) {
    while ($row = sqlsrv_fetch_array($earthquakes_result, SQLSRV_FETCH_ASSOC)) {
        $formattedDate = $row["date"] instanceof DateTime ? $row["date"]->format('Y-m-d') : $row["date"];
        $earthquakes[$row["id"]] = $row["country"] . " - " . $formattedDate;
    }
}

$sql = "SELECT * FROM artefacts";
$result = sqlsrv_query($conn, $sql);
$artefacts = [];
if($result && sqlsrv_has_rows($result)) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $artefacts[] = $row;
    }
}

// Get total number of earthquakes for stat box
$eq_count_res = sqlsrv_query($conn, "SELECT COUNT(*) AS cnt FROM earthquakes");
$eq_count_row = $eq_count_res ? sqlsrv_fetch_array($eq_count_res, SQLSRV_FETCH_ASSOC) : ['cnt'=>0];
$quake_count = $eq_count_row['cnt'];

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../headerNew.php';?>
    <meta charset="UTF-8">
    <title>Manage Artifacts | Quake</title>
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
        <li class="active"><a href="#"><img src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li><a href="/shop/shop.php"><img src="/assets/icons/shop.svg">Shop</a></li>
        <li><a href="#"><img src="/assets/icons/team.svg">Team</a></li>
        <li><a href="../Account_Management/accountNew.php"><img src="/assets/icons/account.svg">Account</a></li>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><img src="/assets/icons/logout.svg">Log out</a>
    </div>
</div>

<!-- MAIN CONTENT AREA -->
<div class="main-content">
    <!-- MANAGE PANEL -->
    <div class="glass-panel manage-panel">
        <h2>Manage Artifacts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Required</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($artefacts)): foreach ($artefacts as $row): 
                $id = $row["id"] ?? "";
                $earthquake_id = $row["earthquake_id"] ?? "";
                $type = $row["type"] ?? "";
                $timestamp = ($row["time_stamp"] instanceof DateTime) ? $row["time_stamp"]->format('d.m.y') : ($row["time_stamp"] ?? "");
                $shelving_loc = $row["shelving_loc"] ?? "N/A";
                $required = $row["required"] ?? "";
            ?>
                <tr>
                    <td><?=htmlspecialchars($id)?></td>
                    <td><?= isset($earthquakes[$earthquake_id]) ? explode(' - ', htmlspecialchars($earthquakes[$earthquake_id]))[0] : "N/A"; ?></td>
                    <td><?=htmlspecialchars(ucwords($type))?></td>
                    <td><?=htmlspecialchars($timestamp)?></td>
                    <td><?=htmlspecialchars($required)?></td>
                    <td>
                        <form action="process_delete_artefact.php" method="POST" style="margin:0;">
                            <input type="hidden" name="id" value="<?=$id?>">
                            <button type="submit" class="delete-btn" title="Delete"><img src="/assets/icons/rubbish.svg" alt="Delete" style="height:1.1em;vertical-align:middle;margin:0;padding:0;"></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="6" style="text-align:center;color:#eee;">No artefacts found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    <style>
    /* Center all cells */
    th, td {
    text-align: center;
    vertical-align: middle;
    }
    </style>
</div>

    <!-- ADD PANEL -->
    <div class="glass-panel add-panel">
        <h3>Add Artifact</h3>
        <form method="POST" action="process_add_artefact.php">
            <label>Earthquake
                <select name="earthquake_id" required>
                    <option value="">Select...</option>
                    <?php foreach ($earthquakes as $eq_id => $eq_label) :
                        echo "<option value='$eq_id'>" . htmlspecialchars($eq_label) . "</option>";
                    endforeach; ?>
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
<div class="stat-box"><strong><?=number_format($quake_count)?></strong> <span>Earthquakes Logged</span></div>
</body>
</html>