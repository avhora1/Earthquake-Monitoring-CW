<?php
$new_pallet_id = isset($_GET['new_pallet_id']) ? intval($_GET['new_pallet_id']) : null;
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
// For "Add Artefact" - you may want a list of earthquakes from your DB:
$all_earthquakes_list = [];
$eq_res = sqlsrv_query($conn, "SELECT id,country,date FROM earthquakes");
if ($eq_res && sqlsrv_has_rows($eq_res)) {
    while($r = sqlsrv_fetch_array($eq_res, SQLSRV_FETCH_ASSOC)) {
        $all_earthquakes_list[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../headerNew.php';?>
    <meta charset="UTF-8">
    <title>Manage Pallets | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/quake.css">
    <style>
        th, td { vertical-align: middle!important; }
        .artefact-list { font-family:monospace; line-height:1.7; padding:0; margin:0;}
        .main-content { display:flex; align-items:flex-start;}
        .glass-panel.manage-panel { flex:2; min-width:0; }
        .side-panel.add-panel { 
            <?php if (!$new_pallet_id): ?>display:none;<?php endif; ?>
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="glass-panel manage-panel">
        <h2>Manage Pallets</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Size</th>
                    <th>Arrival Date</th>
                    <th>Artefact IDs</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT * FROM pallets";
            $result = sqlsrv_query($conn, $sql);
            if($result === false) {
                echo "<tr><td colspan='4' style='color:#fff; background:#a00;'>";
                echo "SQLSRV query failed:<br>";
                foreach(sqlsrv_errors() as $err) {
                    echo htmlspecialchars($err['message']) . "<br>";
                }
                echo "</td></tr>";
            }
            elseif(sqlsrv_has_rows($result)) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $pid = $row['id'] ?? "N/A";
                    $pallet_size = $row['pallet_size'] ?? "N/A";
                    $arrival_date = $row["arrival_date"] instanceof DateTime
                        ? $row["arrival_date"]->format('d.m.y H:i')
                        : ($row["arrival_date"] ?? "N/A");
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pid) . "</td>";
                    echo "<td>" . htmlspecialchars(ucfirst($pallet_size)) . "</td>";
                    echo "<td>" . htmlspecialchars($arrival_date) . "</td>";
                    echo "<td><div class='artefact-list'>";
                    // List artefact IDs
                    $ares = sqlsrv_query($conn, "SELECT id FROM artefacts WHERE pallet_id = ?", [$pid]);
                    $ids = [];
                    if($ares && sqlsrv_has_rows($ares)) {
                        while ($arow = sqlsrv_fetch_array($ares, SQLSRV_FETCH_ASSOC)) {
                            $ids[] = (int)$arow['id'];
                        }
                        echo $ids ? implode(', ', $ids) : "<span style='color:#6fd;'>None</span>";
                    } else {
                        echo "<span style='color:#6fd;'>None</span>";
                    }
                    echo "</div></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;color:#ccc;'>No pallets found</td></tr>";
            }
            sqlsrv_close($conn);
            ?>
            </tbody>
        </table>
    </div>
    <div class="side-panels">
        <div class="side-panel">
            <h3 style="margin-bottom:18px;">Add Pallet</h3>
            <form method="POST" action="process_pallet.php">
                <div class="pallet-btns">
                    <button type="submit" name="pallet_size" value="half" class="pallet-btn-half">Half</button>
                    <button type="submit" name="pallet_size" value="full" class="pallet-btn-full">Full</button>
                </div>
            </form>
        </div>
        <div class="side-panel add-panel" id="addArtefactPanel">
            <h3>Add Artefact</h3>
            <form method="POST" action="process_artefact_to_pallet.php">
                <label>Pallet ID
                    <input type="text" name="pallet_id" value="<?=htmlspecialchars($new_pallet_id)?>" readonly>
                </label>
                <label>Earthquake
                    <select name="earthquake_id" required>
                        <option value="">Select...</option>
                        <?php foreach ($all_earthquakes_list as $eq_row) {
                            $eq_id = $eq_row['id'];
                            $eq_date = ($eq_row['date'] instanceof DateTime) ? $eq_row['date']->format('d.m.y') : $eq_row['date'];
                            echo "<option value='$eq_id'>{$eq_id} - {$eq_date}</option>";
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
                    <button type="button" onclick="window.location='../Pallet/manage_palletsNew.php'" class="delete-btn" title="Clear"><img src="/assets/icons/rubbish.svg" alt="Clear" style="height:1.2em;"></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Hide Add Artefact panel if new_pallet_id is not set
    <?php if (!$new_pallet_id): ?>
        document.getElementById('addArtefactPanel').style.display = 'none';
    <?php endif; ?>
</script>
</body>
</html>