<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
include '../headerNew.php';

// --- Handle artefact collection/delete
$delete_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collected_id'])) {
    $collected_id = intval($_POST['collected_id']);
    // Get the artifact_id before deleting
    $lookup = sqlsrv_query($conn, "SELECT artifact_id FROM stock_list WHERE id = ?", [$collected_id]);
    $row = sqlsrv_fetch_array($lookup, SQLSRV_FETCH_ASSOC);
    if ($row && isset($row['artifact_id'])) {
        $artifact_id = $row['artifact_id'];
        // Delete from stock_list
        $ok1 = sqlsrv_query($conn, "DELETE FROM stock_list WHERE id = ?", [$collected_id]);
        // Delete from artefacts table
        $ok2 = sqlsrv_query($conn, "DELETE FROM artefacts WHERE id = ?", [$artifact_id]);
        if ($ok1 && $ok2) {
            $delete_msg = "Artefact #$artifact_id collected & deleted!";
        } else {
            $delete_msg = "Error deleting artefact.";
        }
    } else {
        $delete_msg = "Artefact not found in stock_list.";
    }
}

// --- Get all unavailable artefacts
$sql = "SELECT 
            s.id AS stock_id, 
            s.artifact_id, 
            s.price, 
            a.type, 
            a.time_stamp, 
            a.shelving_loc, 
            a.required
        FROM stock_list s
        JOIN artefacts a ON s.artifact_id = a.id
        WHERE s.availability = 'No'
        ORDER BY s.artifact_id DESC";
$result = sqlsrv_query($conn, $sql);

$artefacts = [];
if ($result && sqlsrv_has_rows($result)) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $artefacts[] = $row;
    }
}
sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/quake.css">
    <meta charset="UTF-8">
    <title>Collected Artefacts | Quake</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style>
        .collected-btn {
            background: linear-gradient(90deg, #30ff82, #b4ff8c 85%);
            color: #232333;
            font-weight: 700;
            font-size: 1.07em;
            border: none;
            border-radius: 999px;
            padding: 8px 26px;
            cursor: pointer;
            box-shadow: 0 0 13px #44ffae8a;
            outline: none;
            transition: background 0.2s, box-shadow 0.17s;
            margin: 0 auto;
            display: block;
            letter-spacing: 0.04em;
        }
        .collected-btn:hover,
        .collected-btn:focus {
            background: linear-gradient(90deg, #28ed6d 15%, #43e38b 95%);
            box-shadow: 0 0 22px #1ed14e;
            color: #162a16;
        }
        .q-modal-backdrop {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(11, 14, 34, 0.55);
        z-index: 2000;
        display: flex; align-items: center; justify-content: center;
        transition: opacity 0.2s;
        }
        .q-modal {
        background: linear-gradient(113deg, rgba(44,48,69,.98), rgba(21,23,38,.98) 90%);
        border-radius: 22px;
        box-shadow: 0 4px 46px #181e3380;
        padding: 36px 32px 28px 32px;
        max-width: 350px;
        min-width: 220px;
        font-family: Urbanist, Arial, sans-serif;
        text-align: center;
        border: 1.5px solid #181a2b;
        }
        .q-modal h3 {
        font-size: 1.5em;
        font-weight: 900;
        color: #fff;
        margin: 0 0 12px 0;
        letter-spacing: .01em;
        }
        .q-modal-actions {
        display: flex; gap: 18px; justify-content: center; margin-top:14px;
        }
        .q-modal .collected-btn {
        min-width: 90px; padding: 10px 32px; font-size:1.14em;
        }
        .q-modal .delete-btn {
        background: radial-gradient(ellipse at 67% 25%, #ff4a4a 62%, #fc5d1f 120%);
        color: #fff;
        border-radius: 12px;
        padding: 10px 28px;
        font-size:1.13em;
        min-width:90px;
        font-weight:700;
        border:none;
        box-shadow:0 0 8px #ff4a4ace;
        }
        .q-modal .delete-btn:hover {
        background: radial-gradient(ellipse at 67% 25%, #fd2b2b 62%, #fc5d1f 140%);
        }
        header {
            position: relative;
        }
        .main-content {
            margin-top: 0px;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="glass-panel manage-panel">
        <h2>Artefacts Marked as Unavailable</h2>
        <p>View and collect artefacts that are no longer available for sale or use. Collecting will remove all records from the system.</p>
        <?php if ($delete_msg): ?>
            <div class="success-msg" style="margin: 16px 0;"><?= htmlspecialchars($delete_msg) ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Stock ID</th>
                    <th>Artefact ID</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Shelf</th>
                    <th>Required</th>
                    <th>Collected?</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($artefacts)): ?>
                <?php $i = 1; foreach ($artefacts as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= htmlspecialchars($row['stock_id']) ?></td>
                        <td><?= htmlspecialchars($row['artifact_id']) ?></td>
                        <td>€<?= number_format($row['price'], 2) ?></td>
                        <td><?php 
                            if (isset($row['time_stamp']) && $row['time_stamp'] instanceof DateTime) {
                                echo $row['time_stamp']->format('d.m.y H:i');
                            } else {
                                echo htmlspecialchars($row['time_stamp']);
                            }
                        ?></td>
                        <td><?= htmlspecialchars($row['shelving_loc']) ?></td>
                        <td><?= htmlspecialchars($row['required']) ?></td>
                        <td>
                        <form method="post" class="collected-form" data-id="<?=$row['stock_id']?>" data-artifact="<?=$row['artifact_id']?>" style="margin:0;">
                            <input type="hidden" name="collected_id" value="<?= $row['stock_id']; ?>">
                            <button type="button" class="collected-btn js-collect-btn">
                                Yes
                            </button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;color:#eee;">No unavailable artefacts found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Backdrop & Window -->
<div class="q-modal-backdrop" id="collectConfirmModal" style="display:none;">
  <div class="q-modal" style="max-width:340px;">
    <h3>Collect Artefact?</h3>
    <div id="modal-artifact-message" style="margin-bottom:19px; color:#fff; font-weight:500;font-size:1.06em;"></div>
    <div class="q-modal-actions">
      <button class="collected-btn" id="modal-confirm-btn" style="min-width:95px;">Yes</button>
      <button class="delete-btn" id="modal-cancel-btn" style="min-width:95px;">Cancel</button>
    </div>
    <form method="post" id="modal-real-form" style="display:none;"></form>
  </div>
</div>
<script>
document.querySelectorAll('.js-collect-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
        // Get form and artifact info
        let form = btn.closest('.collected-form');
        let artefactId = form.getAttribute('data-artifact') || '?';
        // Show modal
        document.getElementById('collectConfirmModal').style.display = 'flex';
        document.getElementById('modal-artifact-message').innerHTML =
          "Are you sure you want to collect (delete)<br>artefact <b>#"+artefactId+"</b> from ALL records?";
        // Remember the form to submit
        window._modalConfirmedForm = form;
    });
});
document.getElementById('modal-cancel-btn').onclick = function(){
    document.getElementById('collectConfirmModal').style.display = 'none';
    window._modalConfirmedForm = null;
};
document.getElementById('collectConfirmModal').onclick = function(e){
    if(e.target === this) this.style.display = 'none';
};
// On confirm, submit actual form
document.getElementById('modal-confirm-btn').onclick = function(){
    if(window._modalConfirmedForm) {
        window._modalConfirmedForm.submit();
        document.getElementById('collectConfirmModal').style.display = 'none';
    }
};
</script>
</body>
</html>