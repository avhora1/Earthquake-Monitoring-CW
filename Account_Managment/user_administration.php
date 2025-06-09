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
<link rel="stylesheet" href="../assets/css/quake.css">
<meta charset="utf-8">
<title>User Administration</title>
<style>
    header {
        position: relative;
    }
.glass-panel.manage-panel {
    min-width: 780px;
    max-width: 1100px;
    margin-right: 0;
    padding: 34px 38px 33px 38px;
    border-radius: 22px;
    box-shadow: 0 0 34px #090e206e;
    background: linear-gradient(113deg, rgba(40,44,64,0.93), rgba(22,26,38,0.96) 90%);
}
h2.admin-heading {
    font-size: 2.3rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 13px;
}
hr.admin-sep {
    border: none;
    border-top:2px solid #fff2;
    margin:18px 0 36px 0;
}
.info-text { color: #bbb; font-size: 1.13rem; margin-bottom: 13px;}
.info-warning {color: #ff9100; font-weight: 700;}
.userlist-autocomplete {
    max-height: 220px;
    overflow-y: auto;
    position: absolute;
    top: 75px;
    left: 0;
    width: 100%;
    background: #23243a;
    border: 2px solid #2a2b36;
    border-radius: 11px;
    color: #fff;
    font-weight: 400;
    z-index: 51;
    display: none;
    font-size: 1.07em;
    box-shadow: none;
    border: none;
}
.userlist-autocomplete.active { display: block; }
.userlist-autocomplete .item {
    padding: 9px 16px;
    cursor: pointer;
    border-bottom: 1px solid #22253b;
}
.userlist-autocomplete .item:last-child {border-bottom: none;}
.userlist-autocomplete .item:hover, .userlist-autocomplete .item.active {
    background: #ff9100;
    color: #23243a;
}
.form-row { position:relative; }
.form-label { display: block; color: #fff; font-weight: 500;margin-bottom: .25em;}
.form-control {
    display:block;
    width:100%;
    font-size:1.06em;
    background: #22243a;
    color:#eee;
    border:1.5px solid #333542;
    border-radius: 10px;
    padding: 11px 14px;
}
.form-control:focus { outline:2px solid #ff9100; border-color:#ff9100;}
.select-account {margin-top:8px;}
.panel-message {
    margin: 0 0 20px 0;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 1.08em;
}
.panel-message.success { background: #213227; color: #6fcf97;}
.panel-message.warning { background: #232439; color: #ff9100; }
.panel-message.error { background: #331818; color: #ff4444;}
.panel-message.info { background: #1d263a; color: #84befa;}
.modal { display:none; position:fixed; left:0; top:0; right:0; bottom:0; background:rgba(21,22,34,.92);align-items:center;justify-content:center;z-index:9999;}
.modal.active {display:flex;}
.modal-dialog {
    background: linear-gradient(113deg, #22253b 60%, #181b2a 100%);
    border-radius: 17px;
    width: 98%;
    max-width: 370px;
    box-shadow: 0 0 30px #090e206a;
    padding: 36px 30px 26px 30px;
    color: #fafafc;
    position: relative;
    z-index: 101;
}
.modal-title { font-size:1.34em;font-weight:700;}
.modal-close-btn {
    position: absolute; right: 14px; top: 13px; background:none; border:none; color: #ccc; font-size: 1.4em; cursor:pointer;}
.modal-actions {display:flex;justify-content:flex-end;gap:14px;margin-top:2em;}
.btn {
    display:inline-block;
    border-radius: 16px;
    background: none;
    color: #ffa034;
    border:2px solid #ffa034;
    padding: 9px 23px;
    font-weight: 600;
    font-size: 1.07em;
    transition: .16s;
    margin-right: 5px;
    cursor:pointer;
}
.btn:hover,.btn:focus{ background: #ffa034; color: #181826;}
.btn.btn-danger { border-color:#ff4444; color:#ff4444;}
.btn.btn-danger:hover{background:#ff4444;color:white;}
</style>
</head>
<body>
<div class="main-content">
<div class="glass-panel manage-panel">
    <h2 class="admin-heading">User Administration</h2>
    <hr class="admin-step"/>
    <div class="info-text">Search, view, edit, change account type or delete users below.</div>
    <div class="info-warning">Only administrators are authorized for these functions.</div>

    <?php if ($type_message): ?><div class="panel-message info"><?= $type_message ?></div><?php endif; ?>
    <?php if ($del_message): ?><div class="panel-message info"><?= $del_message ?></div><?php endif; ?>
  
    <div class="form-row" style="position:relative;max-width:500px;">
        <label for="user-searchbox" class="form-label">Find User</label>
        <input id="user-searchbox" class="form-control" autocomplete="off" placeholder="Type username or email..." onfocus="showUserList()" oninput="filterUserList()">
        <div id="user-autocomplete" class="userlist-autocomplete"></div>
    </div>
</div>

<!-- User Info Modal -->
<div id="userModal" class="modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="hideModal('userModal')">&times;</button>
    <div class="modal-title" id="userModalLabel">User Account</div>
    <dl id="modal-user-fields"></dl>
    <div id="type-change-wrap" class="form-row" style="margin-top:20px;">
        <form method="post" id="editTypeForm" style="display:flex;align-items:center;gap:18px;">
            <input type="hidden" name="change_type_user" id="change_type_user" value="">
            <label for="change_type_type" class="form-label" style="margin-bottom:0;">Change Type</label>
            <select class="form-control select-account" name="change_type_type" id="change_type_type">
                <option value="guest">Guest</option>
                <option value="junior_scientist">Junior Scientist</option>
                <option value="senior_scientist">Senior Scientist</option>
                <option value="admin">Admin</option>
            </select>
            <button class="btn" type="submit">Update</button>
        </form>
    </div>
    <div class="modal-actions">
        <form method="post" id="deleteUserForm" style="display:inline;">
            <input type="hidden" name="delete_user_confirmed" id="delete_user_confirmed" value="">
            <button type="button" class="btn btn-danger" id="deleteBtn">Delete User</button>
        </form>
        <button class="btn" type="button" onclick="hideModal('userModal')">Close</button>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div id="delConfirmModal1" class="modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="hideModal('delConfirmModal1')">&times;</button>
    <div class="modal-title">Are you sure?</div>
    <div style="padding:18px 0 6px 0;">Are you sure you want to <b>delete</b> this account?</div>
    <div class="modal-actions">
        <button class="btn" onclick="hideModal('delConfirmModal1')">Cancel</button>
        <button class="btn btn-danger" id="delConfirmBtn1">Yes, continue</button>
    </div>
  </div>
</div>

<!-- Delete Confirm Final Modal -->
<div id="delConfirmModal2" class="modal">
  <div class="modal-dialog">
    <button class="modal-close-btn" onclick="hideModal('delConfirmModal2')">&times;</button>
       <div class="modal-title">Please Confirm</div>
          <div style="padding:18px 0 6px 0;">This action is <b>irreversible</b>. Do you want to proceed and delete this account?</div>
          <div class="modal-actions">
              <button class="btn" onclick="hideModal('delConfirmModal2')">No</button>
              <button class="btn btn-danger" id="delConfirmBtn2">Delete Account</button>
          </div>
        </div>
      </div>
  </div>
</div>

<script>
let allUsers = [];
let usersDetail = {};
<?php
usort($list, fn($a, $b)=>strcasecmp($a['username'], $b['username']));
echo "allUsers = ".json_encode(array_column($list,"username")).";\n";
echo "usersDetail = ".json_encode(array_column($list,null,"username")).";\n";
?>
const adminUsername = <?= json_encode($_SESSION['account_name']); ?>;
const searchBox = document.getElementById('user-searchbox');
const acList = document.getElementById('user-autocomplete');
// --- Autocomplete logic ---
searchBox.addEventListener('input', filterUserList);
searchBox.addEventListener('focus', showUserList);
function showUserList() {
    filterUserList();
    acList.classList.add('active');
}
function filterUserList() {
    let val = searchBox.value.toLowerCase();
    acList.innerHTML = "";
    if (!val) { acList.classList.remove('active'); return; }
    let filtered = allUsers.filter(u => u.toLowerCase().includes(val));
    filtered.forEach(u => {
        let item = document.createElement('div');
        item.className = "item";
        item.innerHTML = `<b>${u}</b>`;
        item.onclick = () => showUserModal(u);
        acList.appendChild(item);
    });
    acList.classList.toggle('active', filtered.length > 0);
}
document.addEventListener('click', function(e){
    if (!acList.contains(e.target) && e.target!==searchBox) acList.classList.remove('active');
});
// --- Modal logic ---
function showModal(id){ document.getElementById(id).classList.add('active'); }
function hideModal(id){ document.getElementById(id).classList.remove('active'); }
function showUserModal(username) {
    let user = usersDetail[username];
    if (!user) return;
    let fields = document.getElementById('modal-user-fields');
    fields.innerHTML =
        `<dt>Username</dt><dd>${user.username}</dd>`+
        `<dt>Email</dt><dd>${user.email}</dd>`+
        `<dt>Type</dt><dd>${user.account_type.replaceAll("_"," ")}</dd>`+
        `<dt>Manager</dt><dd>${user.manager_username || '-'}</dd>`;
    document.getElementById('change_type_user').value = user.username;
    document.getElementById('change_type_type').value = user.account_type;
    document.getElementById('delete_user_confirmed').value = user.username;
    if (user.username === adminUsername) {
        document.getElementById('type-change-wrap').style.display = 'none';
        document.getElementById('deleteBtn').style.display = 'none';
    } else {
        document.getElementById('type-change-wrap').style.display = '';
        document.getElementById('deleteBtn').style.display = '';
    }
    showModal('userModal');
}
// --- Delete double confirm logic ---
document.getElementById('deleteBtn').onclick = function() {
    showModal('delConfirmModal1');
    document.getElementById('delConfirmBtn1').onclick = function() {
        hideModal('delConfirmModal1');
        showModal('delConfirmModal2');
        document.getElementById('delConfirmBtn2').onclick = function() {
            hideModal('delConfirmModal2');
            document.getElementById('deleteUserForm').submit();
        };
    };
};
</script>
</body>
</html>