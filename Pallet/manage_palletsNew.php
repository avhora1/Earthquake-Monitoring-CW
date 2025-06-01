<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// Fetch pallets & artefacts for table
$sql = "SELECT * FROM pallets";
$result = sqlsrv_query($conn, $sql);
$pallets = [];
if($result && sqlsrv_has_rows($result)) {
    while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $pallets[] = $row;
    }
}
// For Add Artifact panel
$eq_sql = "SELECT id, country, date FROM earthquakes";
$eq_res = sqlsrv_query($conn, $eq_sql);
$all_earthquakes_list = [];
if($eq_res && sqlsrv_has_rows($eq_res)) {
    while($r = sqlsrv_fetch_array($eq_res, SQLSRV_FETCH_ASSOC)) {
        $all_earthquakes_list[] = $r;
    }
}
sqlsrv_close($conn);
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
        .main-content { margin-left: 330px; margin-top: 110px; display: flex; flex-direction: row; gap: 34px; }
        .manage-panel { flex: 2.5; min-width: 660px; background: none; padding: 0; }
        .manage-panel h2 { font-size: 2.1rem; font-weight: 800; margin: 0 0 12px 0; color: #fff; }
        .panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;}
        .quake-table-wrap { background: linear-gradient(113deg,rgba(40,44,64,0.88),rgba(22,26,38,.94) 92%); border-radius: 20px; box-shadow: 0 0 34px #090e206e; padding: 14px 20px 8px 20px; min-width: 610px;}
        table { width: 100%; margin-top: 2px;}
        th, td { text-align: center; vertical-align: middle;}
        th { color: #fff; font-weight: 700; font-size: 1.07em; border-bottom: 2px solid #323446; padding-bottom: 7px;}
        td { color: #fff; border-bottom: 1px solid #212435; font-size: 1.13em;}
        .delete-btn { background: linear-gradient(110deg, #fd3816 60%, #f35564); border: none; border-radius: 999px; min-width: 38px; min-height: 38px; color: #fff; box-shadow: 0 0 12px #ff3329a0; font-size: 1.13em; cursor: pointer; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
        .delete-btn:disabled { opacity: 0.45; cursor: not-allowed; filter: grayscale(1); box-shadow: none;}
        .editable-field[disabled], .editable-field[readonly] { background: transparent; border: none; color: #fff;}
        .editable-field[disabled]:not(select), .editable-field[readonly] { border-bottom: 1.5px solid #3d414d;}
        .editable-field:not([disabled]) { background: #22263c; border-bottom: 1.5px solid #ff9100;}
        .side-panels { min-width: 300px; display: flex; flex-direction: column; gap: 30px; width: 350px; }
        .side-panel { background: linear-gradient(113deg,rgba(40,44,64,.91),rgba(22,26,38,.95) 90%); border-radius: 20px; box-shadow: 0 0 34px #090e206e; padding: 29px 24px 19px 24px; margin-bottom: 7px;}
        .side-panel h3 { margin: 0 0 17px 0;font-size:1.27em;font-weight:700;}
        .pallet-btns { display: flex; gap:18px; margin-top:9px; justify-content:center;}
        .pallet-btn-half, .pallet-btn-full { font-size: 1.1em; }
        .add-shop-row { display: flex; flex-direction: row; justify-content: flex-start; align-items: center; gap: 12px; margin-bottom: 15px;}
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <li><a href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
        <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li class="active"><a href="#"><img src="/assets/icons/box.svg">Pallets</a></li>
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
        <div class="panel-header">
            <h2>Manage Pallets</h2>
            <!-- Enable Editing Toggle -->
            <div class="add-shop-row" style="margin-bottom:0;">
                <label for="enableEditSwitch" style="color:#ffbe3d;font-weight:700;cursor:pointer;min-width:108px;text-align:right;">Enable Editing</label>
                <label class="switch" style="margin-bottom:0;">
                    <input type="checkbox" id="enableEditSwitch" onchange="toggleEditing()">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        <div class="quake-table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Pallet Size</th>
                    <th>Arrival Date</th>
                    <th>Artefacts</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($pallets)): foreach ($pallets as $row):
                    $id = $row["id"];
                    $size = $row["pallet_size"];
                    $arrival = ($row["arrival_date"] instanceof DateTime) ? $row["arrival_date"]->format('d.m.y H:i') : $row["arrival_date"];
                ?>
                <tr>
                    <td>
                        <input type="text" value="<?=htmlspecialchars($id)?>" disabled readonly class="editable-field" style="width:54px;text-align:center;">
                    </td>
                    <td>
                        <select disabled class="editable-field" name="pallet_size_<?=$id?>" style="min-width:100px;">
                            <option value="half" <?=$size==='half'?'selected':''?>>Half</option>
                            <option value="full" <?=$size==='full'?'selected':''?>>Full</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" value="<?=htmlspecialchars($arrival)?>" disabled class="editable-field" style="width:120px;text-align:center;">
                    </td>
                    <td>
                        <?php
                        // Show artefacts on this pallet
                        include '../connection.php';
                        $asql = "SELECT id, type FROM artefacts WHERE pallet_id=?";
                        $ares = sqlsrv_query($conn, $asql, [$id]);
                        if($ares && sqlsrv_has_rows($ares)){
                            echo '<table class="artefact-table" style="margin:0 auto;background:transparent;width:98%;font-size:0.95em;"><thead><tr><th>ID</th><th>Type</th><th>Remove</th></tr></thead><tbody>';
                            while($arow = sqlsrv_fetch_array($ares, SQLSRV_FETCH_ASSOC)){
                                ?>
                                <tr>
                                    <td><?=htmlspecialchars($arow['id']);?></td>
                                    <td><?=htmlspecialchars($arow['type']);?></td>
                                    <td>
                                        <form action="process_delete_artefact.php" method="POST" style="display:inline">
                                            <input type="hidden" name="id" value="<?=htmlspecialchars($arow['id'])?>">
                                            <button type="submit" class="delete-btn inner-delete-btn" disabled style="opacity:.45;pointer-events:none;" title="Delete">
                                                <img src="/assets/icons/rubbish.svg" alt="Delete" style="height:1.1em;">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            echo "</tbody></table>";
                        } else {
                            echo '<span class="text-success">No artefacts</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <form action="process_delete_pallet.php" method="POST" style="display:inline">
                            <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>">
                            <button type="submit" class="delete-btn delete-main-btn" disabled style="opacity:.45;pointer-events:none;" title="Delete">
                                <img src="/assets/icons/rubbish.svg" alt="Delete" style="height:1.23em;">
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center;color:#bbb;">No pallets found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIGHT SIDE PANELS -->
<div class="side-panels">
    <div class="side-panel">
        <h1>Add Pallet</h1>
        <div class="pallet-btns">
            <button type="button" class="pallet-btn-half" onclick="showAddArtifact('half')">Half</button>
            <button type="button" class="pallet-btn-full" onclick="showAddArtifact('full')">Full</button>
        </div>
    </div>
    <div class="side-panel add-panel" id="addArtifactPanel" style="display:none;">
        <h1>Add Artifact</h1>
        <form method="POST" action="process_add_pallet_with_artifact.php">
            <input type="hidden" name="pallet_size" id="hiddenPalletSize" value="">
            <!-- All your artefact fields -->
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
                <textarea name="description" maxlength="255" style="padding:10px 14px; margin-bottom:15px;"></textarea>
            </label>
            <div class="input-group add-shop-row">
                <label for="add_shop_switch" style="margin-bottom:0;">Add to shop</label>
                <label class="switch">
                    <input name="required" type="checkbox" id="add_shop_switch" value="Yes">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="input-group" style="justify-content:center;">
                <button type="submit" class="add-btn">Add</button>
                <button type="button" class="delete-btn" onclick="hideAddArtifact()" title="Cancel"><img src="/assets/icons/rubbish.svg" alt="Cancel" style="height:1.2em;"></button>
            </div>
        </form>
    </div>
</div>
<script>
function toggleEditing() {
    const enabled = document.getElementById('enableEditSwitch').checked;
    // Inputs/selects for edit
    document.querySelectorAll('.editable-field').forEach(e=>{
        if(enabled) {
            e.removeAttribute('disabled');
            e.removeAttribute('readonly');
            e.style.background='#2c2f3b';
            e.style.borderBottom='1.5px solid #ff9100';
        } else {
            e.setAttribute('disabled',true);
            e.setAttribute('readonly',true);
            e.style.background='transparent';
            e.style.borderBottom='1.5px solid #3d414d';
        }
    });
    // Main delete buttons
    document.querySelectorAll('.delete-main-btn').forEach(btn=>{
        btn.disabled = !enabled;
        btn.style.opacity = enabled ? "1" : ".45";
        btn.style.pointerEvents = enabled ? "auto" : "none";
    });
    // Inner delete buttons (artefact tables)
    document.querySelectorAll('.inner-delete-btn').forEach(btn=>{
        btn.disabled = !enabled;
        btn.style.opacity = enabled ? "1" : ".45";
        btn.style.pointerEvents = enabled ? "auto" : "none";
    });
    }
    function showAddArtifact(size) {
        document.getElementById('addArtifactPanel').style.display = 'block';
        document.getElementById('hiddenPalletSize').value = size;
    }
    function hideAddArtifact() {
        document.getElementById('addArtifactPanel').style.display = 'none';
        document.getElementById('hiddenPalletSize').value = '';
    }
</script>
</body>
</html>