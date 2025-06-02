<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// --- Fetch earthquake and artefact data ---
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
    <meta charset="UTF-8">
    <title>Manage Artifacts | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="../assets/css/quake.css">
    <?php include '../headerNew.php';?>
    <style>
    th,
    td {
        text-align: center;
        vertical-align: middle;
    }

    .add-shop-btn {
        background: linear-gradient(90deg, #ff9100, #ffbe3d 91%);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 1.1em;
        font-size: 1.02em;
        font-family: inherit;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 0 10px #ffa70060;
        transition: background .15s, color .15s, box-shadow .22s;
        margin-left: 2px;
    }

    .add-shop-btn:hover {
        background: linear-gradient(90deg, #ffbe3d, #ff9100 91%);
        color: #fff;
        box-shadow: 0 0 20px #ff910099;
    }

    /* Modal Backdrop */
    .q-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(20, 20, 35, 0.70);
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }

    .q-modal {
        background: linear-gradient(135deg, rgba(38, 40, 64, 0.97) 0%, rgba(16, 23, 34, 0.89) 100%);
        color: #fff;
        padding: 2.7em 2.5em 2.4em 2.5em;
        border-radius: 1.3em;
        min-width: 300px;
        min-height: 140px;
        max-width: 97vw;
        box-shadow: 0 12px 44px #000a;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .q-modal h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 11px;
    }

    .q-modal-actions {
        display: flex;
        gap: 2em;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
    }

    .q-modal input[type="number"] {
        font-size: 1.18em;
        padding: 11px;
        border-radius: 7px;
        border: 1.5px solid #e4e2f4;
        margin-top: 15px;
        outline: none;
        width: 89%;
    }

    .add-btn,
    .delete-btn {
        font-size: 1.13em;
        font-weight: bold;
        padding: 7px 20px;
        border-radius: 11px;
        cursor: pointer;
        border: none;
    }

    .add-btn {
        background: linear-gradient(90deg, #ff9100, #ffbe3d 91%);
        color: #fff;
        box-shadow: 0 2px 18px #e3a54721;
        transition: background .16s;
    }

    .add-btn:hover {
        background: linear-gradient(90deg, #ffbe3d, #ff9100 91%);
    }

    .delete-btn {
        background: #ff4040;
        color: #fff;
        box-shadow: 0 2px 10px #ff404033;
    }

    .delete-btn:hover {
        background: #ee2222;
    }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
        <ul class="sidebar-nav">
            <li<?php if(strpos($_SERVER['REQUEST_URI'], 'earthquakes')!==false) echo ' class="active"'; ?>><a
                    href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
                <li><a href="/Observatories/manage_observatoriesNew.php"><img
                            src="/assets/icons/observatory.svg">Observatories</a></li>
                <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
                <li><a href="/Pallet/manage_palletsNew.php"><img src="/assets/icons/box.svg">Pallets</a></li>
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
                        <th>Add to Shop</th>
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
                        <td><?= isset($earthquakes[$earthquake_id]) ? explode(' - ', htmlspecialchars($earthquakes[$earthquake_id]))[0] : "N/A"; ?>
                        </td>
                        <td><?=htmlspecialchars(ucwords($type))?></td>
                        <td><?=htmlspecialchars($timestamp)?></td>
                        <td><?=htmlspecialchars($required)?></td>
                        <td>
                            <?php if ($required !== "No"): ?>
                            <button type="button" class="add-shop-btn" data-id="<?=htmlspecialchars($id)?>">Add to
                                Shop</button>
                            <?php else: ?>
                            <span style="color:#666;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="process_delete_artefact.php" method="POST" style="margin:0;">
                                <input type="hidden" name="id" value="<?=$id?>">
                                <button type="submit" class="delete-btn" title="Delete"><img
                                        src="/assets/icons/rubbish.svg" alt="Delete"
                                        style="height:1.1em;vertical-align:middle;margin:0;padding:0;"></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#eee;">No artefacts found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- ADD PANEL -->
        <div class="glass-panel add-panel">
            <h3>Add Artifact</h3>
            <form method="POST" action="process_artefact.php">
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
                    <button type="submit" class="add-btn">Add</button>
                    <button type="reset" class="delete-btn" title="Clear"><img src="/assets/icons/rubbish.svg"
                            alt="Clear" style="height:1.2em;"></button>
                </div>
            </form>
        </div>
    </div>
    <div class="stat-box"><strong><?=number_format($quake_count)?></strong> <span>Earthquakes Logged</span></div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="q-modal-backdrop">
        <div class="q-modal">
            <h3>Confirm Delete</h3>
            <p style="font-size:1.12em; color:#fff; margin:18px 0 30px 0;">Are you sure you want to delete this
                artefact?</p>
            <div class="q-modal-actions">
                <button type="button" class="add-btn" id="cancel-delete">Cancel</button>
                <button type="button" class="delete-btn" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>

    <!-- Add To Shop Modal -->
    <div id="shop-modal" class="q-modal-backdrop">
        <div class="q-modal">
            <h3>Add To Shop</h3>
            <form id="add-shop-form" method="POST" action="add_to_shop_process.php" autocomplete="off">
                <input type="hidden" name="artifact_id" id="shop-modal-artifact-id" value="">
                <label style="font-size:1.15em; color:#fff;">Price:
                    <input type="number" step="0.01" min="0" name="price" id="shop-modal-price" required
                        style="margin-top:12px;padding:9px 14px;width:92%;border-radius:9px;border:none;font-size:1.18em;">
                </label>
                <div class="q-modal-actions" style="margin-top:2em">
                    <button type="button" class="add-btn" id="cancel-shop">Cancel</button>
                    <button type="submit" class="add-btn">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // ----- Delete Modal Logic -----
    let formToDelete = null;
    document.querySelectorAll('form[action="process_delete_artefact.php"]').forEach(function(form) {
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

    // ----- Add To Shop Modal -----
    let currentArtefactId = null;
    document.querySelectorAll('.add-shop-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentArtefactId = this.getAttribute('data-id');
            document.getElementById('shop-modal-artifact-id').value = currentArtefactId;
            document.getElementById('shop-modal').style.display = 'flex';
            document.getElementById('shop-modal-price').value = '';
            document.getElementById('shop-modal-price').focus();
        });
    });
    document.getElementById('cancel-shop').onclick = function() {
        document.getElementById('shop-modal').style.display = 'none';
        document.getElementById('add-shop-form').reset();
    };
    document.getElementById('add-shop-form').onsubmit = function() {
        // Optionally validate price here
        document.getElementById('shop-modal').style.display = 'none';
        return true; // Allow form submission
    };
    </script>
</body>

</html>