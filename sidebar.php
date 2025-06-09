<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$account_type = $_SESSION['account_type'] ?? 'guest';
$current_path = $_SERVER['REQUEST_URI'];
if (!function_exists('nav_active')) {
    function nav_active($paths) {
        global $current_path;
        foreach ((array)$paths as $path) {
            if (stripos($current_path, $path) !== false) return 'active';
        }
        return '';
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
.sidebar {
  position: fixed;
  top: 64px; /* Match your header height */
  left: 0; width: 232px;
  height: calc(100vh - 64px);
  background: rgba(24,25,40,0.93);
  box-shadow: 2px 0 22px #0004;
  z-index: 900;
  display: flex; flex-direction: column;
  overflow-y:auto;
}
.sidebar-nav {
  margin: 0; padding: 0; list-style: none;
  display: flex; flex-direction: column;
  gap: 7px;
  margin-top: 28px;
}
.sidebar-nav li {
  border-radius: 10px;
  margin: 0 10px;
}
.sidebar-nav li a {
  display: flex; align-items: center; gap: 13px;
  padding: 13px 22px 13px 20px; color: #fff; font-weight: 500;
  font-size: 1.09rem; background: none; border-radius: 10px;
  transition: background 0.13s, color 0.17s; text-decoration: none;
}
.sidebar-nav li a.active,
.sidebar-nav li a:hover {
  background: linear-gradient(90deg, #24263a 70%, #222835 100%);
  color: #ff9100;
}
.sidebar-nav li a .bi { font-size: 1.19rem; }
.sidebar-nav .user-admin-link { color: #fff; font-weight: 500; }
.sidebar-nav .user-admin-link.active,
.sidebar-nav .user-admin-link:hover { background: #222835; color: #ff9100; }
.sidebar-logout {
  margin-top: auto; margin-bottom: 15px; padding: 2px 0;
}
.sidebar-logout a {
  display: flex; align-items: center; gap: 11px;
  padding: 13px 24px 13px 24px; color: #fff; font-size:1.05rem;
  background: none; border-radius: 10px; text-decoration: none; margin: 0 10px;
  transition: background .13s, color .16s;
}
.sidebar-logout a:hover { background: #23273d; color: #ff9100; }
</style>
<div class="sidebar">
    <ul class="sidebar-nav">
        <?php if ($account_type === 'admin') : ?>
        <li><a class="<?= nav_active(['accountNew']) ?>" href="/Account_Managment/accountNew.php"><i class="bi bi-person"></i> Account</a></li>
        <li><a class="<?= nav_active(['Earthquake']) ?>" href="/Earthquake/manage_earthquakesNew.php"><i class="bi bi-activity"></i> Earthquakes</a></li>
        <li><a class="<?= nav_active(['Observatories']) ?>" href="/Observatories/manage_observatoriesNew.php"><i class="bi bi-building"></i> Observatories</a></li>
        <li><a class="<?= nav_active(['collected']) ?>" href="/Admin/collected_artefacts.php"><i class="bi bi-archive"></i> Warehouse</a></li>
        <li><a class="<?= nav_active(['Pallet']) ?>" href="/Pallet/manage_palletsNew.php"><i class="bi bi-box"></i> Pallets</a></li>
        <li><a class="<?= nav_active(['manage_artefacts']) ?>" href="/Artefact/manage_artefactsNew.php"><i class="bi bi-droplet-half"></i> Artifacts</a></li>
        <li><a class="<?= nav_active(['team']) ?>" href="/Account_Managment/team_structure.php"><i class="bi bi-people"></i> Team</a></li>
        <li><a class="<?= nav_active(['shelves']) ?>" href="/Shelves/ViewShelves.php"><i class="bi bi-list"></i> View stock</a></li>
        <li><a class="<?= nav_active(['user']) ?>" href="/Account_Managment/user_administration.php"><i class="bi bi-gear"></i> User administration</a></li>
        <?php elseif($account_type === 'senior_scientist'): ?>
        <li><a class="<?= nav_active(['accountNew']) ?>" href="/Account_Managment/accountNew.php"><i class="bi bi-person"></i> Account</a></li>
        <li><a class="<?= nav_active(['Earthquake']) ?>" href="/Earthquake/manage_earthquakesNew.php"><i class="bi bi-activity"></i> Earthquakes</a></li>
        <li><a class="<?= nav_active(['Observatories']) ?>" href="/Observatories/manage_observatoriesNew.php"><i class="bi bi-building"></i> Observatories</a></li>
        <li><a class="<?= nav_active(['Pallet']) ?>" href="/Pallet/manage_palletsNew.php"><i class="bi bi-box"></i> Pallets</a></li>
        <li><a class="<?= nav_active(['Artefact']) ?>" href="/Artefact/manage_artefactsNew.php"><i class="bi bi-droplet-half"></i> Artifacts</a></li>
        <li><a class="<?= nav_active(['team']) ?>" href="/Account_Managment/team_structure.php"><i class="bi bi-people"></i> Team</a></li>
        <li><a class="<?= nav_active(['shelves']) ?>" href="/Shelves/ViewShelves.php"><i class="bi bi-list"></i> View stock</a></li>
        <?php elseif($account_type === 'junior_scientist'): ?>
        <li><a class="<?= nav_active(['accountNew']) ?>" href="/Account_Managment/accountNew.php"><i class="bi bi-person"></i> Account</a></li>
        <li><a class="<?= nav_active(['Earthquake']) ?>" href="/Earthquake/manage_earthquakesNew.php"><i class="bi bi-activity"></i> Earthquakes</a></li>
        <li><a class="<?= nav_active(['Observatories']) ?>" href="/Observatories/manage_observatoriesNew.php"><i class="bi bi-building"></i> Observatories</a></li>
        <li><a class="<?= nav_active(['Artefact']) ?>" href="/Artefact/manage_artefactsNew.php"><i class="bi bi-droplet-half"></i> Artifacts</a></li>
        <li><a class="<?= nav_active(['shelves']) ?>" href="/Shelves/ViewShelves.php"><i class="bi bi-list"></i> View stock</a></li>
        <?php elseif ($account_type === 'guest') : ?>
        <li><a class="<?= nav_active(['accountNew']) ?>" href="/Account_Managment/accountNew.php"><i class="bi bi-person"></i> Account</a></li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><i class="bi bi-box-arrow-left"></i> Log out</a>
    </div>
</div>