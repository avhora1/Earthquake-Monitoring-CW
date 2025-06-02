<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
$account_username = $_SESSION['account_name'];
$account_type = $_SESSION['account_type'];

// Load users
include '../connection.php';
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
$managed_users = [];
foreach ($users as $u) {
    if ($u['manager_username']) $managed_users[$u['manager_username']][] = $u['username'];
}
// Helper: does $user belong in any branch under $manager ?
function is_team_managed_by($user, $manager, $users) {
    while ($users[$user]['manager_username']) {
        if ($users[$user]['manager_username'] == $manager) return true;
        $user = $users[$user]['manager_username'];
    }
    return false;
}
// Handle move user POST
$edit_error = $edit_success = '';
$move_user_val = $_POST['move_user'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move_user'], $_POST['new_manager']) && $_POST['move_user'] && $_POST['new_manager'] !== null) {
    $move_user = $_POST['move_user'];
    $new_manager = $_POST['new_manager'] ?: NULL;
    $move_type = $users[$move_user]['type'] ?? null;
    $target_type = $new_manager ? ($users[$new_manager]['type'] ?? null) : null;

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
        if ($move_type === 'guest' && $target_type === 'junior_scientist'
            && is_team_managed_by($move_user, $account_username, $users)) $allowed = true;
    }
    if ($move_user === $new_manager) { $allowed = false; $edit_error = "A user can't be their own manager."; }
    if ($allowed) {
        $stmt = sqlsrv_query($conn, "UPDATE registered_accounts SET manager_username = ? WHERE username = ?", [$new_manager, $move_user]);
        if ($stmt === false) { $edit_error = "Database error: couldn't update manager."; }
        else $edit_success = "Moved user ".htmlspecialchars($move_user)." under ".htmlspecialchars($new_manager);
        // reload users
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
        $managed_users = [];
        foreach ($users as $u) {
            if ($u['manager_username']) $managed_users[$u['manager_username']][] = $u['username'];
        }
    } elseif (!$edit_error) {
        $edit_error = "Invalid move: not allowed.";
    }
}
// Team tree
function get_tree($users, $parent=null) {
    $tree = [];
    foreach ($users as $u) {
        if ($u['manager_username'] == $parent) {
            $children = get_tree($users, $u['username']);
            if ($children) $u['children'] = $children;
            $tree[] = $u;
        }
    }
    return $tree;
}
function render_tree($tree, $highlight_username) {
    foreach($tree as $user) {
        $is_me = ($user['username'] == $highlight_username);
        $li_class = $is_me ? 'fw-bold quake-highlight' : '';
        $role = ucfirst(str_replace("_", " ", $user['type']));
        echo "<li>";
        echo "<span class=\"$li_class\">" . htmlspecialchars($user['username']) . " <small class='badge-typetag'>$role</small></span>";
        if (!empty($user['children'])) {
            echo "<ul>";
            render_tree($user['children'], $highlight_username);
            echo "</ul>";
        }
        echo "</li>";
    }
}
$tree = get_tree($users, null);
$displayed_users = [];
foreach ($users as $u) {
    $is_manager = array_key_exists($u['username'], $managed_users);
    $displayed_users[] = $u + ['is_manager' => $is_manager];
}
usort($displayed_users, fn($a,$b)=>strcasecmp($a['username'],$b['username']));
$account_types_in_table = array_unique(array_map(fn($u)=>$u['type'],$displayed_users));
sort($account_types_in_table);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../headerNew.php';?>
    <meta charset="utf-8">
    <title>Team Structure | Quake</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/quake.css">
    <style>
    /* Panels & glass styling for consistency */
    .team-manage-panel,
    .glass-panel,
    .team-card-panel,
    .tree.card,
    .team-top-panel {
        background: linear-gradient(113deg,rgba(40,44,64,.92),rgba(22,26,38,.98) 90%);
        border-radius: 22px;
        box-shadow: 0 0 34px #090e206e;
        color: #fff;
        padding: 36px 38px 30px 38px;
        margin-bottom: 32px;
    }
    .main-content {
        margin-top: 88px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Headings and panels */
    .team-top-panel h2,
    .team-manage-panel h2,
    .glass-panel h2,
    h3 {
        color: #fff;
        font-family: Urbanist, Arial, sans-serif;
        font-weight: 900;
        letter-spacing: .01em;
        margin-bottom: 7px;
    }

    /* Description/subtext */
    .section-desc {
        color: #bfc7df;
        margin-bottom: 22px;
        font-size: 1.09em;
    }

    /* Table styling */
    table, #userTable {
        color: #fff;
        background: transparent;
        border-radius: 10px;
        overflow: hidden;
    }
    #userTable th,
    .table th {
        color: #ffd351 !important;
        background: none;
        font-weight: 800;
        border-bottom: 2px solid #353745;
        font-size: 1.12em;
    }
    #userTable td,
    .table td {
        border-bottom: 1px solid #31324a;
        background: none;
        font-size: 1.05em;
    }

    /* Tree view styling */
    .tree ul, .tree li, .tree {
        color: #fff;
    }

    .badge-typetag {
        color: #fff !important;
        background: #343956 !important;
        border-radius: 7px;
        padding: 2px 9px;
        font-size: 0.97em;
        font-weight: 700;
        margin-left: 10px;
    }

    /* Links/breadcrumb/action style */
    a, a:visited, a:hover, a:active {
        color: #ff9100;
        text-decoration: none;
        font-weight: 600;
    }
    a.add-btn, .add-btn {
        color: #222 !important;
        background: linear-gradient(90deg, #ff9100, #ffbe3d 80%);
        border-radius: 999px;
        padding: 9px 38px;
        font-weight: 700;
        box-shadow: 0 0 10px #fa8c16bb;
        border: none;
        transition: background .2s, box-shadow .2s;
        margin-top: 17px;
        text-align: center;
        display: inline-block;
    }
    a.add-btn:hover, .add-btn:hover {
        background: linear-gradient(90deg,#ffbe3d,#ff9100 90%) !important;
        box-shadow: 0 0 30px #fa8c16d0;
        color: #161616 !important;
    }

    /* Inputs and selects */
    .form-label, label { color: #ffe8a2; font-weight:600; }
    .form-control, .form-select, input[type="text"], input[type="password"], select {
        background: #1c202e;
        color: #fff;
        border:1.5px solid #3d414d;
        font-size: 1.07em;
        border-radius:8px;
        padding: 9px 15px;
    }
    .form-control:focus, .form-select:focus, input[type="text"]:focus, input[type="password"]:focus, select:focus {
        border-color: #ff9100;
        outline: none;
    }
    /* Placeholder styling (for search fields) */
    .form-control::placeholder, .search-bar::placeholder, input[type="text"]::placeholder {
        color: #bfc7dfcc;
        opacity: 1;
    }

    /* Success and error messages */
    .success-msg { color:#22ff74; font-weight:600; }
    .error-msg { color:#ff4a4a; font-weight:600; }

    /* Hide link underline for nav/btn links */
    .sidebar-nav a, .navbar a, a.add-btn, .add-btn {
        text-decoration: none !important;
    }

    /* Responsiveness for main content */
    @media (max-width:1100px) {
        .main-content {margin-left: 0;}
        .team-manage-panel, .glass-panel {padding:15px 3vw;}
    }
    @media (max-width:1000px) {
        .main-content, .team-manage-panel, .glass-panel {padding:10px 1.5vw;}
    }
    select, select option {
    color: #fff !important;
    background: #1c202e !important;
    }
    </style>
</head>
<body>
<!-- SIDEBAR -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <li><a href="/Earthquake/manage_earthquakesNew.php"><img src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li><a href="#"><img src="/assets/icons/observatory.svg">Observatories</a></li>
        <li><a href="#"><img src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li><a href="#"><img src="/assets/icons/box.svg">Pallets</a></li>
        <li><a href="../Artefact/manage_artefactsNew.php"><img src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li><a href="/shop/shop.php"><img src="/assets/icons/shop.svg">Shop</a></li>
        <li class="active"><a href="#"><img src="/assets/icons/team.svg">Team</a></li>
        <li><a href="/account/manage_account.php"><img src="/assets/icons/account.svg">Account</a></li>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><img src="/assets/icons/logout.svg">Log out</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="team-manage-panel">
        <h2>Team Structure</h2>
        <div class="section-desc">
            Manage stakeholders at all access levels.<br>
            <b>Admins</b> have visibility and control over all users. 
            <b>Senior Scientists</b> lead teams of Juniors. 
            <b>Junior Scientists</b> may manage Guests. 
            <br/>All Scientists use platform features such as earthquake and observatory cataloguing. 
        </div>

        <!-- Move User Form -->
        <form class="account-actions-form" method="post" autocomplete="off" style="max-width:800px;margin-bottom:30px;">
            <h3 style="font-size:1.17em;font-weight:700;margin-bottom:9px;">Move User</h3>
            <?php if ($edit_error): ?>
                <div class="error-msg"><?= $edit_error ?></div>
            <?php elseif ($edit_success): ?>
                <div class="success-msg"><?= $edit_success ?></div>
            <?php endif; ?>
            <div style="display:flex; flex-wrap:wrap; gap:33px;">
                <div style="flex:1; min-width:185px;">
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
                <div style="flex:1; min-width:185px; display: flex; flex-direction:column; max-width:300px;">
                  <input class="form-control mb-1" id="managerSearch" onkeyup="filterSelect('new_manager', this.value)" placeholder="Search manager...">
                  <select class="form-select mt-2" name="new_manager" id="new_manager" size="6" required>
                      <option value="">None</option>
                      <!-- JS fills -->
                  </select>
              </div>
            </div>
            <button class="add-btn" type="submit" style="margin-top:20px;">Move User</button>
        </form>

        <!-- User Table/Search -->
        <div class="glass-panel" style="max-width:770px;">
            <h3 style="font-size:1.11em;font-weight:700;">Search & Filter Users</h3>
            <div style="display:flex;flex-wrap:wrap; gap:20px;margin-bottom:12px;">
                <input class="form-control" id="userSearch" oninput="filterUserTable()" placeholder="Search usernames..." style="flex:1">
                <select class="form-select" id="typeFilter" onchange="filterUserTable()" style="max-width:180px;">
                    <option value="">All Types</option>
                    <?php foreach ($account_types_in_table as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= ucfirst(str_replace("_"," ", htmlspecialchars($type))) ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select" id="managerFilter" onchange="filterUserTable()" style="max-width:180px;">
                    <option value="">All Users</option>
                    <option value="manager">Managers Only</option>
                    <option value="nonmanager">Non-managers Only</option>
                </select>
            </div>
            <div style="overflow:auto; max-height:330px;">
                <table id="userTable">
                    <thead>
                        <tr><th>Username</th><th>Account Type</th><th>Reports To</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($displayed_users as $u): ?>
                        <tr data-username="<?= htmlspecialchars($u['username']) ?>" data-type="<?= htmlspecialchars($u['type']) ?>" data-manager="<?= $u['is_manager'] ? '1':'0' ?>">
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', htmlspecialchars($u['type']))) ?></td>
                            <td><?= htmlspecialchars($u['manager_username'] ?? "") ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
                        </div>

        <a href="/Account_Managment/accountNew.php" style="display:block; margin-top:35px;color:#ff9100;font-weight:600;">&larr; Back to Manage Account</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Expand to show your branch in tree
    document.querySelectorAll('.tree ul').forEach(ul=> ul.style.display = 'none');
    for (let li of document.querySelectorAll('.tree li')) {
      let ul = li.querySelector('ul');
      if (ul) {
        let btn = document.createElement('span');
        btn.className = "tree-toggle-btn";
        btn.innerHTML = '&#9654;';
        btn.onclick = function(e) {
            e.stopPropagation();
            if (ul.style.display === 'none') {
                ul.style.display = 'block';
                btn.innerHTML = '&#9660;';
            } else {
                ul.style.display = 'none';
                btn.innerHTML = '&#9654;';
            }
        };
        li.insertBefore(btn, li.firstChild);
        if(li.querySelector('.quake-highlight')) {
            ul.style.display = 'block';
            btn.innerHTML = '&#9660;';
        }
      }
    }
    updateManagerList();
});
// Manager dropdown
function updateManagerList() {
    var moveUser = document.getElementById('move_user').value;
    var users = <?php echo json_encode($users); ?>;
    var type = users[moveUser] && users[moveUser].type;
    var managerSelect = document.getElementById('new_manager');
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