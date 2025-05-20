<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
$account_username = $_SESSION['account_name'];
$account_type = $_SESSION['account_type'];

$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) die(print_r(sqlsrv_errors(), true));

// Load all users
$sql = "SELECT id, username, account_type, manager_username FROM registered_accounts";
$stmt = sqlsrv_query($conn, $sql);
$users = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $users[$row['username']] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'type' => $row['account_type'],
        'manager_username' => $row['manager_username']
    ];
}
// Who is a manager?
$managed_users = [];
foreach ($users as $u) {
    if ($u['manager_username']) $managed_users[$u['manager_username']][] = $u['username'];
}
function is_team_managed_by($user, $manager, $users) {
    while ($users[$user]['manager_username']) {
        if ($users[$user]['manager_username'] == $manager) return true;
        $user = $users[$user]['manager_username'];
    }
    return false;
}
function get_manager_chain($users, $username) {
    $chain = [];
    while ($username && isset($users[$username]) && $users[$username]['manager_username']) {
        $mname = $users[$username]['manager_username'];
        if (isset($users[$mname])) {
            $chain[] = $users[$mname];
            $username = $mname;
        } else break;
    }
    return array_reverse($chain);
}
function render_tree($user, $users, $highlight_username) {
    $is_me = ($user['username'] == $highlight_username);
    $li_class = $is_me ? 'fw-bold text-success' : '';
    $role = ucfirst(str_replace("_", " ", $user['type']));
    echo "<li>";
    echo "<span class=\"$li_class\">" . htmlspecialchars($user['username']) . " <small class='badge bg-secondary ms-2'>$role</small></span>";
    if (!empty($user['children'])) {
        echo "<ul>";
        foreach ($user['children'] as $child) {
            render_tree($child, $users, $highlight_username);
        }
        echo "</ul>";
    }
    echo "</li>";
}

// --- Handle move user POST (minimal: update DB, no replacement-controls here for demo) ---
$edit_error = $edit_success = '';
$move_user_val = $_POST['move_user'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move_user'], $_POST['new_manager']) && $_POST['move_user'] && $_POST['new_manager'] !== null) {
    $move_user = $_POST['move_user'];
    $new_manager = $_POST['new_manager'] ?: NULL;
    $move_type = $users[$move_user]['type'] ?? null;
    $target_type = $users[$new_manager]['type'] ?? null;

    // Permission enforcement
    $allowed = false;
    if ($account_type === 'admin') {
        if ($move_type === 'senior_scientist' && $target_type === 'admin') $allowed = true;
        if ($move_type === 'junior_scientist' && $target_type === 'senior_scientist') $allowed = true;
        if ($move_type === 'guest' && $target_type === 'junior_scientist') $allowed = true;
    } elseif ($account_type === 'senior_scientist') {
        if ($move_type === 'junior_scientist' && $target_type === 'senior_scientist'
            && is_team_managed_by($move_user, $account_username, $users)) $allowed = true;
        if ($move_type === 'guest' && $target_type === 'junior_scientist'
            && is_team_managed_by($move_user, $account_username, $users)) $allowed = true;
    } elseif ($account_type === 'junior_scientist') {
        // Juniors can only assign managed guests -> juniors
        if ($move_type === 'guest' && $target_type === 'junior_scientist'
            && is_team_managed_by($move_user, $account_username, $users)) $allowed = true;
    }

    // Self-manager check
    if ($move_user === $new_manager) { $allowed = false; $edit_error = "A user can't be their own manager."; }
    // Execute
    if ($allowed) {
        $stmt = sqlsrv_query($conn, "UPDATE registered_accounts SET manager_username = ? WHERE username = ?", [$new_manager, $move_user]);
        if ($stmt === false) { $edit_error = "Database error: couldn't update manager."; }
        else $edit_success = "Moved user ".htmlspecialchars($move_user)." under ".htmlspecialchars($new_manager);
        // reload in-page user array
        $stmt = sqlsrv_query($conn, $sql);
        $users = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $users[$row['username']] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'type' => $row['account_type'],
                'manager_username' => $row['manager_username']
            ];
        }
    } elseif (!$edit_error) {
        $edit_error = "Invalid move: not allowed.";
    }
}

// Tree - you can add your own tree logic (here it's simple top-level)
$tree = [];
foreach ($users as $u) {
    if ($u['manager_username'] === null && $u['type'] != 'admin')
        $tree[] = $u;
}
// --- Table/list prep for search/table ---
$displayed_users = [];
foreach ($users as $u) {
    $is_manager = array_key_exists($u['username'], $managed_users);
    $displayed_users[] = $u + ['is_manager' => $is_manager];
}
usort($displayed_users, fn($a,$b)=>strcasecmp($a['username'],$b['username']));
$account_types_in_table = array_unique(array_map(fn($u)=>$u['type'],$displayed_users));
sort($account_types_in_table);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Team Structure / Edit Team</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    #userTable tbody { display:block; max-height:246px; overflow-y:auto;}
    #userTable thead, #userTable tbody tr { display:table; width:100%; table-layout:fixed;}
    .tree ul { padding-left: 1.5em; list-style-type: none; position: relative; }
    .tree ul ul { margin-left: 1.2em; border-left: 1px solid #bbc2cc; }
    .tree li { margin: .5em 0; position: relative; }
    .tree li:before { content: ''; position: absolute; top: 1.1em; left: -1.3em; width: 1em; border-top: 1px solid #bbc2cc; }
    .tree li:last-child:before { border-left: 1px solid #bbc2cc; }
    .tree .fw-bold { background: #e8ffea;}
    .tree-toggle-btn { cursor:pointer; margin-right:4px;}
    .section-desc {color:#687283;}
    .toast-container { position: fixed; top:1rem; right: 1rem; z-index: 1080; }
  </style>
</head>
<body>
<?php include "../header.php"; ?>

<div class="toast-container p-3">
  <?php if ($edit_success): ?>
    <div id="ts-toast" class="toast show text-bg-success">
      <div class="d-flex">
        <div class="toast-body fw-semibold"><i class="bi bi-check-circle-fill"></i> <?= $edit_success ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php elseif ($edit_error): ?>
    <div id="ts-toast" class="toast show text-bg-danger">
      <div class="d-flex">
        <div class="toast-body fw-semibold"><i class="bi bi-x-circle-fill"></i> <?= $edit_error ?></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="container my-4">
  <h1 class="mb-2">Team Structure / Edit Team</h1>
  <div class="mb-4 section-desc">
  Only seniors may manage juniors. Only juniors may manage guests. Admins may re-assign anyone (except admins).
  </div>

  <form class="card card-body mb-4" method="post" id="move-user-form" autocomplete="off">
    <h5 class="mb-3">Move User</h5>
    <div class="row">
      <div class="col-md-5 mb-2">
        <label for="move_user" class="form-label">Select User to Move</label>
        <input class="form-control mb-1" id="moveUserSearch" onkeyup="filterSelect('move_user', this.value)" placeholder="Search user...">
        <select class="form-select" name="move_user" id="move_user" size="6" required onchange="updateManagerList()">
          <?php
          $moveable_users = [];
          foreach ($users as $u) {
            if ($account_type === 'admin' && $u['type'] !== 'admin' && $u['username'] !== $account_username) $moveable_users[] = $u;
            elseif ($account_type === 'senior_scientist' &&
                ($u['type'] === 'junior_scientist' || $u['type'] === 'guest') &&
                is_team_managed_by($u['username'], $account_username, $users)) $moveable_users[] = $u;
            elseif ($account_type === 'junior_scientist' &&
                $u['type'] === 'guest' && is_team_managed_by($u['username'], $account_username, $users)) $moveable_users[] = $u;
          }
          usort($moveable_users, fn($a,$b)=>strcasecmp($a['username'],$b['username']));
          foreach ($moveable_users as $u) {
            $selected = ($move_user_val == $u['username']) ? 'selected' : '';
            echo "<option value=\"{$u['username']}\" $selected>{$u['username']} ({$u['type']})</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-5 mb-2">
        <label for="new_manager" class="form-label">New Manager</label>
        <input class="form-control mb-1" id="managerSearch" onkeyup="filterSelect('new_manager', this.value)" placeholder="Search manager...">
        <select class="form-select mt-2" name="new_manager" id="new_manager" size="6" required>
            <option value="">None</option>
            <!-- JS will populate dynamically -->
        </select>
      </div>
    </div>
    <button class="btn btn-warning mt-3 w-100" type="submit">Move User</button>
  </form>

  <div class="card card-body mb-4">
    <h5 class="mb-3">Search Users / View Managers</h5>
    <div class="row g-2 mb-3">
      <div class="col">
        <input class="form-control" id="userSearch" oninput="filterUserTable()" placeholder="Search usernames...">
      </div>
      <div class="col-auto">
        <select class="form-select" id="typeFilter" onchange="filterUserTable()">
          <option value="">All Types</option>
          <?php foreach ($account_types_in_table as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst(str_replace("_"," ", htmlspecialchars($type))) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <select class="form-select" id="managerFilter" onchange="filterUserTable()">
          <option value="">All Users</option>
          <option value="manager">Managers Only</option>
          <option value="nonmanager">Non-managers Only</option>
        </select>
      </div>
    </div>
    <div class="table-responsive" style="max-height: 320px;">
      <table class="table table-sm table-bordered mb-0" id="userTable">
        <thead>
          <tr><th>Username</th><th>Account Type</th><th>Manager</th></tr>
        </thead>
        <tbody>
          <?php foreach ($displayed_users as $u): ?>
          <tr data-username="<?= htmlspecialchars($u['username']) ?>" data-type="<?= htmlspecialchars($u['type']) ?>" data-manager="<?= $u['is_manager'] ? '1':'0' ?>">
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['type']) ?></td>
            <td><?= htmlspecialchars($u['manager_username'] ?? "") ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <!-- Basic tree rendering as before ... -->
  <div class="tree card card-body mt-4"><h5>Team Hierarchy</h5><ul>
    <?php foreach($tree as $branch) render_tree($branch, $users, $account_username); ?>
  </ul></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let toast = document.getElementById('ts-toast');
    if (toast) setTimeout(function() { (new bootstrap.Toast(toast)).hide(); }, 3000);

    document.querySelectorAll('.tree ul').forEach(ul=> ul.style.display = 'none');
    for (let li of document.querySelectorAll('.tree li')) {
      let ul = li.querySelector('ul');
      if (ul) {
        let btn = document.createElement('span');
        btn.className = "tree-toggle-btn";
        btn.innerHTML = '<i class="bi bi-caret-right-fill"></i>';
        btn.onclick = function(e) {
            e.stopPropagation();
            if (ul.style.display === 'none') {
                ul.style.display = 'block';
                btn.innerHTML = '<i class="bi bi-caret-down-fill"></i>';
            } else {
                ul.style.display = 'none';
                btn.innerHTML = '<i class="bi bi-caret-right-fill"></i>';
            }
        };
        li.insertBefore(btn, li.firstChild);
        if(li.querySelector('.fw-bold')) {
            ul.style.display = 'block';
            btn.innerHTML = '<i class="bi bi-caret-down-fill"></i>';
        }
      }
    }
    updateManagerList();
});

function updateManagerList() {
    var moveUser = document.getElementById('move_user').value;
    var users = <?php echo json_encode($users); ?>;
    var type = users[moveUser] && users[moveUser].type;
    var managerSelect = document.getElementById('new_manager');
    // Remove old options
    for (let i = managerSelect.options.length-1; i > 0; i--) managerSelect.remove(i);
    var options = [];
    if (type === 'senior_scientist') {
        options = Object.values(users).filter(u=>u.type==='admin');
    } else if (type === 'junior_scientist') {
        options = Object.values(users).filter(u=>u.type==='senior_scientist');
    } else if (type === 'guest') {
        options = Object.values(users).filter(u=>u.type==='junior_scientist');
    }
    options.sort((a,b)=>a.username.localeCompare(b.username,'en',{sensitivity:'base'}));
    options.forEach(function(u) {
        if (u.username === moveUser) return;
        managerSelect.options[managerSelect.options.length] = new Option(u.username+' ('+u.type+')', u.username);
    });
}
function filterUserTable() {
    let usernameVal = document.getElementById('userSearch').value.toLowerCase();
    let typeVal = document.getElementById('typeFilter').value;
    let managerVal = document.getElementById('managerFilter').value;
    let rows = document.querySelectorAll('#userTable tbody tr');
    rows.forEach(function(row) {
        let user = row.getAttribute('data-username').toLowerCase();
        let type = row.getAttribute('data-type');
        let isMgr = row.getAttribute('data-manager') === "1";
        let usernameMatch = user.indexOf(usernameVal) > -1;
        let typeMatch = (!typeVal || type===typeVal);
        let managerMatch = (
            !managerVal ||
            (managerVal==="manager" && isMgr) ||
            (managerVal==="nonmanager" && !isMgr)
        );
        row.style.display = (usernameMatch && typeMatch && managerMatch) ? '' : 'none';
    });
}
function filterSelect(selectId, filterValue) {
    let select = document.getElementById(selectId);
    let filter = filterValue.toLowerCase();
    for (let option of select.options) {
        option.style.display = (option.text.toLowerCase().indexOf(filter) > -1 || option.value === "") ? "" : "none";
    }
}
</script>
</body>
</html>