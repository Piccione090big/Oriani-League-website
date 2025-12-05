<?php
$connection = Connect("calcetto_db");

$gironeId = isset($_GET['id']) ? intval($_GET['id']) : null;

$girone = null;
$squadre = [];
$partite = [];

if ($gironeId) {
    $gironeInfo = GetGironeInfos($connection, $gironeId);
    $girone = $gironeInfo[$gironeId];

    $squadre = GetSquadraInfos($connection, null, $gironeId);

    usort($squadre, function($a, $b) {
        if ($a['punti'] != $b['punti']) {
            return $b['punti'] - $a['punti'];
        }
        return $b['differenza_reti'] - $a['differenza_reti'];
    });

    $allGames = GetGames($connection, -1);
    foreach ($allGames as $g) {
        foreach ($squadre as $s) {
            if ($s['nome'] == $g['team1'] || $s['nome'] == $g['team2']) {
                $partite[] = $g;
                break;
            }
        }
    }
}

$marcatori = GetMarcatoriGirone($connection, $gironeId);

CloseConnection($connection);
?>

<?php if ($girone): ?>
    <h1>Oriani League</h1>

    <div class="navbar">
        <a href="?p=home" class="<?= (isset($_GET['p']) && $_GET['p'] === 'home')  ? 'active' : '' ?>">Home</a>
        <a href="?p=squadre" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'squadre' || $_GET['p'] === 'squadra'))  ? 'active' : '' ?>">Squadre</a>
        <a href="?p=gironi" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'gironi' || $_GET['p'] === 'girone'))  ? 'active' : '' ?>">Gironi</a>
    </div>
    <div style="margin:20px 0 0 20px;">
        <a href="index.php?p=gironi" style="display:inline-block;padding:8px 16px;background:#2e6bf9;color:white;text-decoration:none;border-radius:8px;font-weight:bold;transition:0.2s ease;"
           onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">⟵ Torna indietro</a>
    </div>
    <h2 style="text-align:center;"><?php echo $girone['nome']; ?></h2>

    <div style="display:flex; gap:40px; max-width:1200px; margin:0 auto;">

        <div style="flex:1;">
            <h2 style="text-align:center;">Classifica</h2>
            <table style="width:100%; border-collapse: collapse; text-align:center;">
                <tr style="background:#2e6bf9; color:white;">
                    <th>Pos</th>
                    <th>Squadra</th>
                    <th>Punti</th>
                    <th>Diff</th>
                </tr>

                <?php $pos = 1; ?>
                <?php foreach ($squadre as $s): ?>
                    <tr>
                        <td><?php echo $pos++; ?></td>
                        <td>
                            <a href="?p=squadra&id=<?php echo $s['id_squadra']; ?>" style="text-decoration:none; color:black;">
                                <?php echo $s['nome']; ?>
                            </a>
                        </td>
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

                <?php foreach ($partite as $g): ?>
                    <tr style="background: white;">
                        <td><?php echo $g['team1']; ?></td>
                        <td><?php echo $g['gol1']; ?></td>
                        <td>-</td>
                        <td><?php echo $g['gol2']; ?></td>
                        <td><?php echo $g['team2']; ?></td>
                        <td><?php echo $g['orario']; ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>
        </div>

    </div>
    <div style="max-width:800px; margin:40px auto;">
        <h2 style="text-align:center;">Classifica Marcatori</h2>

        <table style="width:100%; border-collapse: collapse; text-align:center;">
            <tr style="background:#2e6bf9; color:white;">
                <th>Giocatore</th>
                <th>Squadra</th>
                <th>Gol</th>
            </tr>
            <?php foreach ($marcatori as $m): ?>
                <tr>
                    <td><?php echo $m['giocatore']; ?></td>
                    <td><?php echo $m['squadra']; ?></td>
                    <td><?php echo $m['totale_gol']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>


<?php else: ?>
    <h2 style="text-align:center; color:red;">Girone non trovato.</h2>
<?php endif; ?>