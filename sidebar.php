<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Assumes a session variable for account type (edit if your naming differs)
$account_type = $_SESSION['account_type'] ?? 'guest';

// Detect current path for highlighting
$current_path = $_SERVER['REQUEST_URI'];

// Helper: returns 'active' if path matches
function nav_active($paths) {
    global $current_path;
    foreach ((array)$paths as $path) {
        if (stripos($current_path, $path) !== false) return 'active';
    }
    return '';
}
?>
<!-- SIDEBAR -->
<div class="sidebar">
    <ul class="sidebar-nav">
        <?php if ($account_type === 'admin') : ?>
        <li class="<?= nav_active(['accountNew']) ?>"><a href="/Account_Managment/accountNew.php"><img
                    src="/assets/icons/account.svg">Account</a></li>
        <li class="<?= nav_active(['Earthquake']) ?>"><a href="/Earthquake/manage_earthquakesNew.php"><img
                    src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li class="<?= nav_active(['Observatories']) ?>"><a href="/Observatories/manage_observatoriesNew.php"><img
                    src="/assets/icons/observatory.svg">Observatories</a></li>
        <li class="<?= nav_active(['admin']) ?>"><a href="/Admin/collected_artefacts.php"><img
                    src="/assets/icons/warehouse.svg">Warehouse</a></li>
        <li class="<?= nav_active(['Pallet']) ?>"><a href="/Pallet/manage_palletsNew.php"><img
                    src="/assets/icons/box.svg">Pallets</a></li>
        <li class="<?= nav_active(['manage_artefacts']) ?>"><a href="/Artefact/manage_artefactsNew.php"><img
                    src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li class="<?= nav_active(['team']) ?>"><a href="/Account_Managment/team_structure.php"><img
                    src="/assets/icons/team.svg">Team</a></li>
        <li class="<?= nav_active(['shelves']) ?>"><a href="/Shelves/ViewShelves.php"><img
                    src="/assets/icons/shelves.svg">View stock</a></li>
        <?php elseif($account_type === 'senior_scientist'): ?>
        <li class="<?= nav_active(['accountNew']) ?>"><a href="/Account_Managment/accountNew.php"><img
                    src="/assets/icons/account.svg">Account</a></li>
        <li class="<?= nav_active(['Earthquake']) ?>"><a href="/Earthquake/manage_earthquakesNew.php"><img
                    src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li class="<?= nav_active(['Observatories']) ?>"><a href="/Observatories/manage_observatoriesNew.php"><img
                    src="/assets/icons/observatory.svg">Observatories</a></li>
        <li class="<?= nav_active(['Pallet']) ?>"><a href="/Pallet/manage_palletsNew.php"><img
                    src="/assets/icons/box.svg">Pallets</a></li>
        <li class="<?= nav_active(['Artefact']) ?>"><a href="/Artefact/manage_artefactsNew.php"><img
                    src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li class="<?= nav_active(['team']) ?>"><a href="/Account_Managment/team_structure.php"><img
                    src="/assets/icons/team.svg">Team</a></li>
        <li class="<?= nav_active(['shelves']) ?>"><a href="/Shelves/ViewShelves.php"><img
                    src="/assets/icons/shelves.svg">View stock</a></li>
        <?php elseif($account_type === 'junior_scientist'): ?>
        <li class="<?= nav_active(['accountNew']) ?>"><a href="/Account_Managment/accountNew.php"><img
                    src="/assets/icons/account.svg">Account</a></li>
        <li class="<?= nav_active(['Earthquake']) ?>"><a href="/Earthquake/manage_earthquakesNew.php"><img
                    src="/assets/icons/quake.svg">Earthquakes</a></li>
        <li class="<?= nav_active(['Observatories']) ?>"><a href="/Observatories/manage_observatoriesNew.php"><img
                    src="/assets/icons/observatory.svg">Observatories</a></li>
        <li class="<?= nav_active(['Artefact']) ?>"><a href="/Artefact/manage_artefactsNew.php"><img
                    src="/assets/icons/artifact.svg">Artifacts</a></li>
        <li class="<?= nav_active(['shelves']) ?>"><a href="/Shelves/ViewShelves.php"><img
                    src="/assets/icons/shelves.svg">View stock</a></li>
        <?php elseif ($account_type === 'guest') : ?>
        <li class="<?= nav_active(['accountNew']) ?>"><a href="/Account_Managment/accountNew.php"><img
                    src="/assets/icons/account.svg">Account</a></li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-logout">
        <a href="/sign-in/logout.php"><img src="/assets/icons/logout.svg">Log out</a>
    </div>
</div>