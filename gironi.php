<?php
$connection = Connect("calcetto_db");

$gironi = GetGironeInfos($connection, -1);

$gironiData = [];

foreach ($gironi as $g) {
    $id = $g['id_girone'];

    $squadre = GetSquadraInfos($connection, null, $id);

    usort($squadre, function($a, $b) {
        if ($a['punti'] != $b['punti']) return $b['punti'] - $a['punti'];
        return $b['differenza_reti'] - $a['differenza_reti'];
    });

    $gironiData[] = [
        "id" => $id,
        "nome" => $g['nome'],
        "classifica" => $squadre
    ];
}

CloseConnection($connection);
?>
<h1>Oriani League</h1>

<div class="navbar">
    <a href="?p=home" class="<?= (isset($_GET['p']) && $_GET['p'] === 'home')  ? 'active' : '' ?>">Home</a>
    <a href="?p=squadre" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'squadre' || $_GET['p'] === 'squadra'))  ? 'active' : '' ?>">Squadre</a>
    <a href="?p=gironi" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'gironi' || $_GET['p'] === 'girone'))  ? 'active' : '' ?>">Gironi</a>
</div>
<h2 style="text-align: center;">Gironi</h2>

<div class="teams-container">
    <?php foreach($gironiData as $g): ?>
        <a href="?p=girone&id=<?php echo $g['id']; ?>" class="team-card" style="height:auto; padding:15px;">
            <div class="team-name" style="font-size:22px; margin-bottom:10px;">
                <?php echo $g['nome']; ?>
            </div>

            <table style="width:100%; font-size:14px; text-align:center; border-collapse: collapse;">
                <tr style="font-weight:bold;">
                    <td>#</td>
                    <td>Squadra</td>
                    <td>P</td>
                </tr>

                <?php
                $pos = 1;
                foreach(array_slice($g['classifica'], 0, 4) as $s): ?>
                    <tr>
                        <td><?php echo $pos++; ?></td>
                        <td><?php echo $s['nome']; ?></td>
                        <td><?php echo $s['punti']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </a>
    <?php endforeach; ?>
</div>
