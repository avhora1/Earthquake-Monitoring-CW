<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || $_SESSION['account_type'] !== 'admin') {
    header('Location: /Sign-in/signin.php');
    exit();
}
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
include '../headerNew.php';

$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

$type_message = $del_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_type_user'], $_POST['change_type_type'])) {
    $user = $_POST['change_type_user'];
    $new_type = $_POST['change_type_type'];
    $valid_types = ['guest','junior_scientist','senior_scientist','admin'];
    if ($user === $_SESSION['account_name']) {
        $type_message = "<span style='color:#ff9100;'>You cannot change your own account type.</span>";
    } elseif (in_array($new_type, $valid_types)) {
        $sql = "UPDATE registered_accounts SET account_type=? WHERE username=?";
        $stmt = sqlsrv_query($conn, $sql, [$new_type, $user]);
        $type_message = $stmt
            ? "Account type for <b>$user</b> updated to <b>$new_type</b>."
            : "<span style='color:#ff4444;'>Failed to change account type for $user!</span>";
    }
}
if (isset($_POST['delete_user_confirmed'])) {
    $user = $_POST['delete_user_confirmed'];
    if ($user === $_SESSION['account_name']) {
        $del_message = "<span style='color:#ff9100;'>You cannot delete your own account.</span>";
    } else {
        $sql = "DELETE FROM registered_accounts WHERE username = ?";
        $stmt = sqlsrv_query($conn, $sql, [$user]);
        $del_message = $stmt
            ? "User <b>$user</b> deleted."
            : "<span style='color:#ff4444;'>Deletion failed for $user!</span>";
    }
}
$sql = "SELECT username, email, account_type, manager_username FROM registered_accounts";
$stmt = sqlsrv_query($conn, $sql);
$list = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) $list[] = $row;
usort($list, fn($a, $b) => strcasecmp($a['username'], $b['username']));
$allUsernames = array_column($list,"username");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>User Administration</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: radial-gradient(78.82% 50% at 50% 50%, #000525 0%, #192132 100%);
    color: #fff;
    min-height: 100vh;
    font-family: 'Roboto', Arial, sans-serif;
}
.main-content {
    margin-left: 240px; /* width of sidebar */
    padding-top: 84px;  /* height of header */
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.glass-panel {
    margin: 0 auto;
    background: linear-gradient(143deg, rgba(255,255,255,0.095), rgba(255,255,255,0) 107%);
    border: 2px solid rgba(255,255,255,0.08);
    box-shadow: 0 8px 55px 0 rgb(10, 18, 44, .15);
    backdrop-filter: blur(13px);
    border-radius: 2.45rem;
    max-width: 890px;
    width: 100%;
    padding: 50px 60px 54px 60px;
}
@media (max-width: 1300px) {
    .main-content { margin-left: 0; padding-top: 60px; }
    .glass-panel { max-width:98vw; padding: 16px 2vw 21px 2vw; }
}
h2.admin-heading {
    font-size:2.5rem;font-weight:900;color:#fff;margin-bottom:10px;display:flex;align-items:center;gap:13px;
}
hr.admin-sep { border:none; border-top:2px solid #fff2; margin:18px 0 36px 0;}
label, .form-label {color:#fff;}
.autocomplete-list {
    max-height: 220px; overflow-y: auto; position: absolute; z-index: 1050; width: 100%; background:#23243a; border: 2px solid #2a2b36; border-radius: 11px; color:#fff; font-weight:400;
}
.autocomplete-list .list-group-item {cursor:pointer; color:#fff; background:none;}
.autocomplete-list .list-group-item:hover, .autocomplete-list .active {background: #ff9100; color: #fff;}
.user-modal-info dt { width:120px; color: #bbb; font-weight:700;}
.user-modal-info dd { margin-bottom:.4em; color:#fff;}
#user-searchbox { background:#23243a; color:#fff; border-radius:10px; border:none;}
.toast-container { position: fixed; top:1rem; right: 1rem; z-index: 1080;}
.btn-outline-primary, .btn-outline-danger { border-radius:1.2rem; font-weight:700; padding:10px 20px 10px 20px; border:2px solid #ff9100; color:#ff9100; background:none; margin-right:9px;}
.btn-outline-primary:hover, .btn-outline-danger:hover { background: #ff9100; color: #181826;}
.btn-close-white { filter: invert(1);}
</style>
</head>
<body>
<div class="main-content">
  <div class="glass-panel shadow">
    <h2 class="admin-heading"><i class="bi bi-people-fill"></i> User Administration</h2>
    <hr class="admin-sep">
    <p style="color:#d1d3ea;font-size:1.17rem;margin-bottom:1.5em;font-weight:500;">
        Search, view, edit, change account type or delete users below.<br>
        <span style="color:#ffbb00;">Only administrators are authorized for these functions.</span>
    </p>
    <?php if ($type_message): ?><div class="alert alert-info"><?= $type_message ?></div><?php endif; ?>
    <?php if ($del_message): ?><div class="alert alert-info"><?= $del_message ?></div><?php endif; ?>
    <div class="mb-4 position-relative" style="margin-bottom:36px;">
        <label for="user-searchbox" class="form-label" style="font-size:1.18rem;font-weight:700;">Find User</label>
        <input id="user-searchbox" class="form-control" style="background:#23243a;color:#fff;padding:12px 2px;border-radius:10px;border:none;" autocomplete="off" placeholder="Type username or email..." onfocus="showUserList()" oninput="filterUserList()">
        <div id="user-autocomplete" class="autocomplete-list list-group d-none"></div>
    </div>
    <!-- (Paste your user results table, modals, JS etc from your functional code) -->
  </div>
</div>
<div class="toast-container p-3 position-fixed">
  <?php if ($type_message): ?>
    <div id="useradm-toast" class="toast show text-bg-success"><div class="d-flex"><div class="toast-body"><?= $type_message ?></div><button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast"></button></div></div>
  <?php elseif ($del_message): ?>
    <div id="useradm-toast" class="toast show text-bg-danger"><div class="d-flex"><div class="toast-body"><?= $del_message ?></div><button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast"></button></div></div>
  <?php endif; ?>
</div>
<!-- User Info Modal, JS as before… -->
<script>
// ... put your user search/modals JS from your working code ...
let allUsers = [];
let usersDetail = {};
<?php
usort($list, fn($a,$b)=>strcasecmp($a['username'],$b['username']));
echo "allUsers = ".json_encode(array_column($list,"username")).";\n";
echo "usersDetail = ".json_encode(array_column($list,null,"username")).";\n";
?>
const adminUsername = <?= json_encode($_SESSION['account_name']); ?>;
const searchBox = document.getElementById('user-searchbox');
const acList = document.getElementById('user-autocomplete');
searchBox.addEventListener('input', filterUserList);
searchBox.addEventListener('focus', showUserList);
function showUserList() { filterUserList(); acList.classList.remove('d-none'); }
function filterUserList() {
    let val = searchBox.value.toLowerCase();
    let filtered = allUsers.filter(u => u.toLowerCase().includes(val)).sort((a,b)=>a.localeCompare(b,"en",{sensitivity:"base"}));
    acList.innerHTML = "";
    if (!val) { acList.classList.add('d-none'); return; }
    filtered.forEach(u => {
        let item = document.createElement('div');
        item.className = "list-group-item list-group-item-action py-2";
        item.innerHTML = `<i class="bi bi-person-circle"></i> <b>${u}</b>`;
        item.onclick = function() { showUserModal(u); };
        acList.appendChild(item);
    });
    acList.classList.toggle('d-none', filtered.length == 0);
}
// ...rest of modal JS and modals...
document.addEventListener("DOMContentLoaded", function() {
  var toast = document.getElementById('useradm-toast');
  if (toast) setTimeout(function() { (new bootstrap.Toast(toast)).hide(); }, 3000);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>