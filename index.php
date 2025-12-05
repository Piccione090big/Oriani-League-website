<?php
include_once "database.php";

//Comandatore FC -3/4
//Brighingham -1/2
//Barciolona tutti
//Cocco FC -4/5
//Paguri -4/5
//KFC

//aaron amborsi marcaore sol# fc

//partita 14
$connection = Connect("calcetto_db");

$games = GetGames($connection, null);
$marcatori = GetMarcatoriGironi($connection);

CloseConnection($connection);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Oriani League</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content">
    <?php
    $page = isset($_GET['p']) ? $_GET['p'] : 'home';

    switch($page) {
        case 'home':
    ?>
        <h1>Oriani League</h1>

        <div class="navbar">
            <a href="?p=home" class="<?= (isset($_GET['p']) && $_GET['p'] === 'home')  ? 'active' : '' ?>">Home</a>
            <a href="?p=squadre" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'squadre' || $_GET['p'] === 'squadra'))  ? 'active' : '' ?>">Squadre</a>
            <a href="?p=gironi" class="<?= (isset($_GET['p']) && ($_GET['p'] === 'gironi' || $_GET['p'] === 'girone'))  ? 'active' : '' ?>">Gironi</a>
        </div>
        <div style="display: flex; gap: 40px; max-width: 1200px; margin: 20px auto;">

            <div style="flex: 1; text-align: center;">
                <h2 style="text-align: center;">Partite</h2>
                <div style="margin-bottom: 20px;">
                    <button id="hideShow" onclick="HideShow()">Nascondi partite già giocate</button>
                </div>

                <table class="games-table">
                    <tr>
                        <th>Squadra 1</th>
                        <th>Gol</th>
                        <th></th>
                        <th>Gol</th>
                        <th>Squadra 2</th>
                        <th>Data</th>
                    </tr>

                    <?php foreach($games as $g):
                        $played = ($g['gol1'] !== null && $g['gol2'] !== null);
                        $class = $played ? "played" : "not-played";
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $g['team1']; ?></td>
                            <td><?php echo $g['gol1']; ?></td>
                            <td>-</td>
                            <td><?php echo $g['gol2']; ?></td>
                            <td><?php echo $g['team2']; ?></td>
                            <td><?php echo substr($g['orario'], 0, 16); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div style="flex: 1; text-align: center;">
                <h2>Marcatori</h2>
                <div style="margin-bottom: 20px;">
                    <button id="top20Btn" onclick="showTop20()">Mostra Top 20</button>
                </div>

                <table id="scorersTable" class="games-table" style="margin-top: 20px;">
                    <tr>
                        <th>Giocatore</th>
                        <th>Squadra</th>
                        <th>Girone</th>
                        <th>Gol</th>
                    </tr>

                    <?php
                    foreach($marcatori as $m):
                        ?>
                        <tr>
                            <td><?php echo $m['giocatore']; ?></td>
                            <td><?php echo $m['squadra']; ?></td>
                            <td><?php echo $m['girone']; ?></td>
                            <td><?php echo $m['totale_gol']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

        </div>
    <?php
            break;
        case 'gironi':
            include "gironi.php";
            break;
        case 'squadre':
            include "squadre.php";
            break;
        case 'squadra':
            include "squadra.php";
            break;
        case 'girone':
            include "girone.php";
            break;
        case 'admin':
            include 'admin.php';
            break;
        default:
            header("Location: index.php?p=home");
            break;
    }
    ?>
</div>

<script>
    function HideShow() {
        const btn = document.getElementById("hideShow");
        const rows = document.querySelectorAll("tr.played");

        const hide = "Nascondi partite già giocate";
        const show = "Mostra partite già giocate";

        const hideGames = btn.innerHTML === hide;

        rows.forEach(r => {
            if (hideGames) r.classList.add("hidden");
            else r.classList.remove("hidden");
        });

        btn.innerHTML = hideGames ? show : hide;
    }

    let showingTop20 = false;
    function showTop20() {
        const btn = document.getElementById('top20Btn');
        const table = document.getElementById('scorersTable');
        const rows = Array.from(table.querySelectorAll('tr')).slice(1);

        if (!showingTop20) {
            rows.forEach((r, i) => r.style.display = i < 20 ? '' : 'none');
            document.getElementById('top20Btn').innerText = 'Mostra tutti';
        } else {
            rows.forEach(r => r.style.display = '');
            document.getElementById('top20Btn').innerText = 'Mostra Top 20';
        }

        showingTop20 = !showingTop20;
    }
</script>

<footer>
    <div style="max-width: 1200px; margin: 0 auto;">
        <p>Oriani League &copy; 2025 | Fatto da Savogpt</p>
        <p>Email: nun me scassa u cazz | Tel: 104 676 7104</p>
        <p>
            <a href="#" style="color:white; text-decoration:underline; margin:0 5px;">Privacy</a> |
            <a href="#" style="color:white; text-decoration:underline; margin:0 5px;">Termini</a>
        </p>
    </div>
</footer>
</body>
</html>
