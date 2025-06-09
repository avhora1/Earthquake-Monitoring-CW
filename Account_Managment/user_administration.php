<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || $_SESSION['account_type'] !== 'admin') {
    header('Location: /Sign-in/signin.php');
    exit();
}
include '../headerNew.php';
include '../sidebar.php'; // Sidebar should be fixed at the left as in your design

$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Change type + delete logic as before...
$type_message = ''; $del_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_type_user'], $_POST['change_type_type'])) {
    $user = $_POST['change_type_user'];
    $new_type = $_POST['change_type_type'];
    $valid_types = ['guest','junior_scientist','senior_scientist','admin'];
    if ($user === $_SESSION['account_name']) {
        $type_message = "<span style='color:#ff9100;'>You cannot change your own account type.</span>";
    } elseif (in_array($new_type, $valid_types)) {
        $sql = "UPDATE registered_accounts SET account_type=? WHERE username=?";
        $stmt = sqlsrv_query($conn, $sql, [$new_type, $user]);
        if ($stmt) $type_message = "Account type for <b>$user</b> updated to <b>$new_type</b>.";
        else $type_message = "<span style='color:#ff4444;'>Failed to change account type for $user!</span>";
    }
}
if (isset($_POST['delete_user_confirmed'])) {
    $user = $_POST['delete_user_confirmed'];
    if ($user === $_SESSION['account_name']) {
        $del_message = "<span style='color:#ff9100;'>You cannot delete your own account.</span>";
    } else {
        $sql = "DELETE FROM registered_accounts WHERE username = ?";
        $stmt = sqlsrv_query($conn, $sql, [$user]);
        if ($stmt) $del_message = "User <b>$user</b> deleted.";
        else $del_message = "<span style='color:#ff4444;'>Deletion failed for $user!</span>";
    }
}
$sql = "SELECT username, email, account_type, manager_username FROM registered_accounts";
$stmt = sqlsrv_query($conn, $sql);
$list = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { $list[] = $row; }
usort($list, fn($a, $b) => strcasecmp($a['username'], $b['username']));
$allUsernames = array_column($list, "username");
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
    background: radial-gradient(78.82% 50% at 50% 50%, #000525 0%, #13172c 100%);
    color: #fff;
    min-height: 100vh;
}
.admin-glass-wrap {
    padding-left: 255px; /* sidebar width + margin */
    min-height: 100vh;
}
.admin-glass-box {
    background: linear-gradient(153deg,rgba(255,255,255,0.11),rgba(255,255,255,0.00) 107%);
    border: 2px solid rgba(255,255,255,0.09); backdrop-filter: blur(0.9vh);
    border-radius: 2rem; max-width: 1500px; margin: 100px auto 0 auto;
    box-shadow: 0 8px 55px 0 rgb(10 18 44 / 32%);
    color: #fff;
    padding: 42px 48px 52px 48px;
}
@media (max-width: 1200px) {
    .admin-glass-box { max-width:98vw; padding:18px; margin-left: 20px; }
    .admin-glass-wrap { padding-left:0;}
}
h2.admin-heading {
    font-size:2.33rem;
    font-weight:900;
    color:#fff;
    letter-spacing:.02em;
    margin-bottom: 16px;
    display:flex;
    align-items:center;
    gap:12px;
}
hr.admin-sep { border:none; border-top:2px solid #fff2; margin:12px 0 28px 0;}
label, .form-label {color:#fff;}
.autocomplete-list {
  max-height: 240px; overflow-y: auto; position: absolute; z-index: 1050; width: 100%;
  background: #23243a; border: 2px solid #2a2b36; border-radius: 11px; color:#fff; font-weight:400;
}
.autocomplete-list .list-group-item {cursor: pointer; color:#fff; background:none;}
.autocomplete-list .list-group-item:hover, .autocomplete-list .active {background: #ff9100; color: #fff;}
.user-modal-info dt { width:120px; color:#ff9100;}
.user-modal-info dd { margin-bottom:0.5em;}
#user-searchbox { background:#23243a; color:#fff; border-radius:8px; border:none; }
.toast-container { position: fixed; top:1rem; right: 1rem; z-index: 1080; }
.btn-outline-primary, .btn-outline-danger {
  border-radius: 1.2rem;
  font-weight: 700;
  padding: 9px 20px 8px 20px;
  border: 2px solid #ff9100;
  color: #ff9100;
  background: none;
  margin-right: 9px;
}
.btn-outline-primary:hover, .btn-outline-danger:hover {
    background: #ff9100;
    color: #13172c;
}
.btn-close-white { filter: invert(1); }
</style>
</head>

<body>
<div class="admin-glass-wrap">
<div class="admin-glass-box shadow">
    <h2 class="admin-heading mb-2"><i class="bi bi-people-fill"></i>User Administration</h2>
    <hr class="admin-sep">
    <p style="color:#d1d3ea;font-size:1.17rem;margin-bottom:18px;font-weight:500;">
        Search, view, edit, change account type or delete users below.<br>
        <span style="color:#ffbb00;">Only administrators are authorized for these functions.</span>
    </p>
    <?php if ($type_message): ?><div class="alert alert-info"><?= $type_message ?></div><?php endif; ?>
    <?php if ($del_message): ?><div class="alert alert-info"><?= $del_message ?></div><?php endif; ?>
    <div class="mb-4 position-relative" style="margin-bottom:36px;">
        <label for="user-searchbox" class="form-label" style="color:#fff;font-size:1.18rem;font-weight:700;">Find User</label>
        <input id="user-searchbox" class="form-control" style="background:#23243a;color:#fff;padding:12px 2px;border-radius:10px;border:none;" autocomplete="off" placeholder="Type username or email..." onfocus="showUserList()" oninput="filterUserList()">
        <div id="user-autocomplete" class="autocomplete-list list-group d-none"></div>
    </div>
</div>
</div>
<!-- Toast (for additional messages, if needed) -->
<div class="toast-container p-3 position-fixed">
  <?php if ($type_message): ?>
    <div id="useradm-toast" class="toast show text-bg-success"><div class="d-flex"><div class="toast-body"><?= $type_message ?></div><button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast"></button></div></div>
  <?php elseif ($del_message): ?>
    <div id="useradm-toast" class="toast show text-bg-danger"><div class="d-flex"><div class="toast-body"><?= $del_message ?></div><button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast"></button></div></div>
  <?php endif; ?>
</div>
<!-- User Info Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content" style="background:#222532;">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel" style="color:#fff;"><i class="bi bi-person"></i> User Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body user-modal-info">
        <dl class="row mb-0" id="modal-user-fields"></dl>
        <div id="type-change-wrap" class="mb-3 mt-4">
          <form method="post" id="editTypeForm">
            <input type="hidden" name="change_type_user" id="change_type_user" value="">
            <label class="form-label" style="color:#ff9100;">Change Account Type</label>
            <select class="form-select" name="change_type_type" id="change_type_type" style="background:#23243a;color:#fff;">
              <option value="guest">Guest</option>
              <option value="junior_scientist">Junior Scientist</option>
              <option value="senior_scientist">Senior Scientist</option>
              <option value="admin">Admin</option>
            </select>
            <button class="btn btn-outline-primary mt-2" type="submit"><i class="bi bi-save"></i> Update Account Type</button>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <form method="post" id="deleteUserForm" style="display:inline;">
          <input type="hidden" name="delete_user_confirmed" id="delete_user_confirmed" value="">
          <button type="button" class="btn btn-outline-danger me-auto" id="deleteBtn"><i class="bi bi-trash"></i> Delete User</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="delConfirmModal1" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content" style="background:#222532;">
    <div class="modal-header"><h5 class="modal-title" style="color:#fff;">Are you sure?</h5></div>
    <div class="modal-body" style="color:#fff;"><p>Are you sure you want to <b>delete</b> this account?</p></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button class="btn btn-danger" id="delConfirmBtn1">Yes, continue</button>
    </div>
  </div></div>
</div>
<div class="modal fade" id="delConfirmModal2" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content" style="background:#222532;">
    <div class="modal-header"><h5 class="modal-title" style="color:#fff;">Please Confirm</h5></div>
    <div class="modal-body" style="color:#fff;"><p>This action is <b>irreversible</b>. Do you want to proceed and delete this account?</p></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-bs-dismiss="modal">No</button>
      <button class="btn btn-danger" id="delConfirmBtn2">Delete Account</button>
    </div>
  </div></div>
</div>
<script>
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
function showUserList() {
    filterUserList();
    acList.classList.remove('d-none');
}
function filterUserList() {
    let val = searchBox.value.toLowerCase();
    let filtered = allUsers.filter(u => u.toLowerCase().includes(val)).sort((a,b)=>a.localeCompare(b,"en",{sensitivity:"base"}));
    acList.innerHTML = "";
    if (!val) {
        acList.classList.add('d-none');
        return;
    }
    filtered.forEach(u => {
        let item = document.createElement('div');
        item.className = "list-group-item list-group-item-action py-2";
        item.innerHTML = `<i class="bi bi-person-circle"></i> <b>${u}</b>`;
        item.onclick = function() { showUserModal(u); };
        acList.appendChild(item);
    });
    acList.classList.toggle('d-none', filtered.length == 0);
}
document.addEventListener('click', function(e){
    if (!acList.contains(e.target) && e.target!==searchBox) acList.classList.add('d-none');
});
function showUserModal(username) {
    let user = usersDetail[username];
    if (!user) return;
    let fields = document.getElementById('modal-user-fields');
    fields.innerHTML =
    `<dt class="col-sm-5" style="color:#bbb;">Username</dt><dd class="col-sm-7" style="color:#fff;">${user.username}</dd>`+
    `<dt class="col-sm-5" style="color:#bbb;">Email</dt><dd class="col-sm-7" style="color:#fff;">${user.email}</dd>`+
    `<dt class="col-sm-5" style="color:#bbb;">Type</dt><dd class="col-sm-7 text-capitalize" style="color:#fff;">${user.account_type.replaceAll("_"," ")}</dd>`+
    `<dt class="col-sm-5" style="color:#bbb;">Manager</dt><dd class="col-sm-7" style="color:#fff;">${user.manager_username || '-'}</dd>`;
    document.getElementById('change_type_user').value = user.username;
    document.getElementById('change_type_type').value = user.account_type;
    document.getElementById('delete_user_confirmed').value = user.username;
    if (user.username === adminUsername) {
        document.getElementById('type-change-wrap').classList.add('d-none');
        document.getElementById('deleteBtn').style.display = 'none';
    } else {
        document.getElementById('type-change-wrap').classList.remove('d-none');
        document.getElementById('deleteBtn').style.display = '';
    }
    var modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
}
document.getElementById('deleteBtn').onclick = function() {
    var conf1 = new bootstrap.Modal(document.getElementById('delConfirmModal1'));
    conf1.show();
    document.getElementById('delConfirmBtn1').onclick = function() {
        conf1.hide();
        var conf2 = new bootstrap.Modal(document.getElementById('delConfirmModal2'));
        conf2.show();
        document.getElementById('delConfirmBtn2').onclick = function() {
            conf2.hide();
            document.getElementById('deleteUserForm').submit();
        };
    };
};
document.addEventListener("DOMContentLoaded", function() {
  var toast = document.getElementById('useradm-toast');
  if (toast) setTimeout(function() { (new bootstrap.Toast(toast)).hide(); }, 3000);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>