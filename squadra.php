<?php
$connection = Connect("calcetto_db");
$squadraId = isset($_GET['id']) ? $_GET['id'] : null;

$squadra = null;

if ($squadraId) {
    $squadraInfo = GetSquadraInfos($connection, $squadraId, null);
    $squadra = $squadraInfo[$squadraId];
}

if($squadra) {
    $girone = GetGironeInfos($connection, $squadra['id_girone']);
    $squadreGirone = GetSquadraInfos($connection, null, $squadra['id_girone']) ;
    usort($squadreGirone, function($a, $b) {
        if ($a['punti'] != $b['punti']) {
            return $b['punti'] - $a['punti'];
        }
        return $b['differenza_reti'] - $a['differenza_reti'];
    });

    $partite = GetGames($connection, $squadraId);
}

CloseConnection($connection);
?>

<?php if($squadra): ?>
    <h1>Oriani League</h1>

    <div class="navbar">
        <a href="?p=home" class="<?= (isset($_GET['p']) && $_GET['p'] === 'home')  ? 'active' : '' ?>">Home</a>
        <a href="?p=squadre" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'squadre' || $_GET['p'] === 'squadra'))  ? 'active' : '' ?>">Squadre</a>
        <a href="?p=gironi" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'gironi' || $_GET['p'] === 'girone'))  ? 'active' : '' ?>">Gironi</a>
    </div>
    <div style="margin:20px 0 0 20px;">
        <a href="index.php?p=squadre"
           style="display:inline-block;padding:8px 16px;background:#2e6bf9;color:white;text-decoration:none;border-radius:8px;font-weight:bold;transition:0.2s ease;"
           onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">⟵ Torna indietro</a>
    </div>
    <h2 style="text-align:center;"><?php echo $squadra['nome']; ?></h2>
    <div style="text-align:center;">
        <img src="images/logo-placeholder.png" alt="<?php echo $squadra['nome']; ?> Logo" style="width:150px; height:150px;">
    </div>
    <h2 style="text-align:center; margin-top:20px;">Informazioni Squadra</h2>
    <p style="margin-top: 20px; text-align: center; font-size:16px;">Girone <?php echo $girone[$squadra['id_girone']]['nome']?>: </p>
    <div style="display:flex; gap:40px; max-width:1200px; margin:0 auto;">
        <div style="flex:1;">
            <h2 style="text-align:center;">Classifica Girone</h2>
            <table style="width:100%; border-collapse: collapse; text-align: center;">
                <tr style="background:#2e6bf9; color:white;">
                    <th>Pos</th>
                    <th>Squadra</th>
                    <th>Punti</th>
                    <th>Differenza Reti</th>
                </tr>
                <?php $pos=1; foreach($squadreGirone as $s): ?>
                    <tr style="background: <?php echo ($s['id_squadra']==$squadra['id_squadra'])?'#f0f8ff':'white'; ?>;">
                        <td><?php echo $pos++; ?></td>
                        <td><?php echo $s['nome']; ?></td>
                        <td><?php echo $s['punti']; ?></td>
                        <td><?php echo $s['differenza_reti']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="flex:1;">
            <h2 style="text-align:center;">Partite</h2>
            <table style="width:100%; border-collapse: collapse; text-align:center;">
                <tr style="background:#2e6bf9; color:white;">
                    <th>Squadra 1</th>
                    <th>Gol</th>
                    <th>-</th>
                    <th>Gol</th>
                    <th>Squadra 2</th>
                    <th>Data</th>
                </tr>
                <?php foreach($partite as $g):
                    $played = ($g['gol1'] !== null && $g['gol2'] !== null);
                    $won = $played && (($g['team1'] == $squadra['nome'] && $g['gol1'] > $g['gol2']) ||
                                    ($g['team2'] == $squadra['nome'] && $g['gol2'] > $g['gol1']));
                    $draw = $played && $g['gol1'] == $g['gol2'];
                    $loss = $played && !$won && !$draw;

                    if (!$played)
                        $bg = 'white';
                    elseif ($won)
                        $bg = '#d4edda';
                    elseif ($draw)
                        $bg = '#fff3b3';
                    else
                        $bg = '#f8d7da';
                    ?>
                    <tr style="background: <?php echo $bg; ?>;">
                        <td><?php echo $g['team1']; ?></td>
                        <td><?php echo isset($g['gol1']) ? $g['gol1'] : ''; ?></td>
                        <td>-</td>
                        <td><?php echo isset($g['gol2']) ? $g['gol2'] : ''; ?></td>
                        <td><?php echo $g['team2']; ?></td>
                        <td><?php echo $g['orario']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
<?php else: ?>
    <h2 style="text-align:center; color:red;">Squadra non trovata!</h2>
<?php endif; ?>