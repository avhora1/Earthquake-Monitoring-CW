<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || $_SESSION['account_type'] !== 'admin') {
    header('Location: /Sign-in/signin.php');
    exit();
}
include '../header.php';

$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Type change
$type_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_type_user'], $_POST['change_type_type'])) {
    $user = $_POST['change_type_user'];
    $new_type = $_POST['change_type_type'];
    $valid_types = ['guest','junior_scientist','senior_scientist','admin'];
    if ($user === $_SESSION['account_name']) {
        $type_message = "<span class='text-danger'>You cannot change your own account type.</span>";
    } elseif (in_array($new_type, $valid_types)) {
        $sql = "UPDATE registered_accounts SET account_type=? WHERE username=?";
        $stmt = sqlsrv_query($conn, $sql, [$new_type, $user]);
        if ($stmt) $type_message = "Account type for <b>$user</b> successfully updated to <b>$new_type</b>.";
        else $type_message = "<span class='text-danger'>Failed to change account type for $user!</span>";
    }
}

// Delete logic
$del_message = '';
if (isset($_POST['delete_user_confirmed'])) {
    $user = $_POST['delete_user_confirmed'];
    if ($user === $_SESSION['account_name']) {
        $del_message = "<span class='text-danger'>You cannot delete your own account.</span>";
    } else {
        $sql = "DELETE FROM registered_accounts WHERE username = ?";
        $stmt = sqlsrv_query($conn, $sql, [$user]);
        if ($stmt) $del_message = "User <b>$user</b> deleted.";
        else $del_message = "<span class='text-danger'>Deletion failed for $user!</span>";
    }
}

// Get all users detail & sorted usernames for JS/auto-complete/dropdown
$sql = "SELECT username, email, account_type, manager_username FROM registered_accounts";
$stmt = sqlsrv_query($conn, $sql);
$list = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { $list[] = $row; }
usort($list, fn($a,$b)=>strcasecmp($a['username'],$b['username']));
$allUsernames = array_column($list, "username");
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="utf-8">
<title>User Administration</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.autocomplete-list {max-height: 260px; overflow-y: auto; position: absolute; z-index: 1050; width: 100%; background: #fff; border: 1px solid #ddd; border-radius: 10px;}
.autocomplete-list .list-group-item {cursor: pointer;}
.autocomplete-list .list-group-item:hover, .autocomplete-list .active {background: #87ceeb; color: #000;}
.user-modal-info dt { width:120px; }
.user-modal-info dd { margin-bottom:0.5em; }
.form-user-admin { max-width: 500px; margin:2rem auto; }
.toast-container { position: fixed; top:1rem; right: 1rem; z-index: 1080; }
</style>
</head>
<body>
<div class="container form-user-admin">
    <h2 class="mb-3 text-center">User Administration</h2>
    <p class="mb-3 text-center text-muted">
        Search, view, edit, change account type or delete users below.<br>
        Only administrators are authorized for these functions.
    </p>
    <?php if ($type_message): ?><div class="alert alert-info"><?= $type_message ?></div><?php endif; ?>
    <?php if ($del_message): ?><div class="alert alert-info"><?= $del_message ?></div><?php endif; ?>
    <div class="mb-4 position-relative">
        <label for="user-searchbox" class="form-label">Find User</label>
        <input id="user-searchbox" class="form-control" autocomplete="off" placeholder="Type username or email..." onfocus="showUserList()" oninput="filterUserList()">
        <div id="user-autocomplete" class="autocomplete-list list-group d-none"></div>
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
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel"><i class="bi bi-person"></i> User Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body user-modal-info">
        <dl class="row mb-0" id="modal-user-fields"></dl>
        <div id="type-change-wrap" class="mb-3 mt-4">
          <form method="post" id="editTypeForm">
            <input type="hidden" name="change_type_user" id="change_type_user" value="">
            <label class="form-label">Change Account Type</label>
            <select class="form-select" name="change_type_type" id="change_type_type">
              <option value="guest">Guest</option>
              <option value="junior_scientist">Junior Scientist</option>
              <option value="senior_scientist">Senior Scientist</option>
              <option value="admin">Admin</option>
            </select>
            <button class="btn btn-outline-primary mt-2" type="submit">Update Account Type</button>
          </form>
        </div>
      </div>
      <div class="modal-footer">
        <form method="post" id="deleteUserForm" style="display:inline;">
          <input type="hidden" name="delete_user_confirmed" id="delete_user_confirmed" value="">
          <button type="button" class="btn btn-outline-danger me-auto" id="deleteBtn">Delete User</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- First Confirm Delete Modal -->
<div class="modal fade" id="delConfirmModal1" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Are you sure?</h5></div>
    <div class="modal-body"><p>Are you sure you want to <b>delete</b> this account?</p></div>
    <div class="modal-footer">
      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button class="btn btn-danger" id="delConfirmBtn1">Yes, continue</button>
    </div>
  </div></div>
</div>
<!-- Second Confirm Delete Modal -->
<div class="modal fade" id="delConfirmModal2" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Please Confirm</h5></div>
    <div class="modal-body"><p>This action is <b>irreversible</b>. Do you want to proceed and delete this account?</p></div>
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
// Hide popdown when blur outside
document.addEventListener('click', function(e){
    if (!acList.contains(e.target) && e.target!==searchBox) acList.classList.add('d-none');
});

// Show user info modal on click
function showUserModal(username) {
    let user = usersDetail[username];
    if (!user) return;
    let fields = document.getElementById('modal-user-fields');
    fields.innerHTML =
        `<dt class="col-sm-5">Username</dt><dd class="col-sm-7">${user.username}</dd>`+
        `<dt class="col-sm-5">Email</dt><dd class="col-sm-7">${user.email}</dd>`+
        `<dt class="col-sm-5">Type</dt><dd class="col-sm-7 text-capitalize">${user.account_type.replaceAll("_"," ")}</dd>`+
        `<dt class="col-sm-5">Manager</dt><dd class="col-sm-7">${user.manager_username || '-'}</dd>`;
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

// Double confirmation delete logic
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