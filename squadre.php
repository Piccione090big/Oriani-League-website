<?php
$connection = Connect("calcetto_db");

$teams = GetSquadraInfos($connection, -1, null);

CloseConnection($connection);

?>
<h1>Oriani League</h1>

<div class="navbar">
    <a href="?p=home" class="<?= (isset($_GET['p']) && $_GET['p'] === 'home')  ? 'active' : '' ?>">Home</a>
    <a href="?p=squadre" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'squadre' || $_GET['p'] === 'squadra'))  ? 'active' : '' ?>">Squadre</a>
    <a href="?p=gironi" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'gironi' || $_GET['p'] === 'girone'))  ? 'active' : '' ?>">Gironi</a>
</div>
<h2 style="text-align: center;">Squadre</h2>

<div class="teams-container">
    <?php foreach($teams as $team): ?>
        <a href="?p=squadra&id=<?php echo $team['id_squadra']; ?>" class="team-card">
            <div class="team-logo">
                <img src="./images/logo-placeholder.png" alt="<?php echo $team['nome']; ?> Logo">
            </div>
            <div class="team-name"><?php echo $team['nome']; ?></div>
        </a>
    <?php endforeach; ?>
</div>