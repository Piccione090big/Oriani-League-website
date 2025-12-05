<?php
$connection = Connect("calcetto_db");

session_start();
if (isset($_POST['login'])) {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'password') {
        $_SESSION['admin_logged'] = true;
    } else {
        $error = "Username o password errati.";
    }
}

if (!isset($_SESSION['admin_logged'])) {
    ?>
    <h2>Login Admin</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="password" placeholder="Password"><br>
        <input type="submit" name="login" value="Login">
    </form>
    <?php
    exit();
}

$partite = GetGames($connection, -1);

$editId = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$editPartita = null;
if ($editId) {
    foreach($partite as $p) {
        if ($p['id_partita'] == $editId) {
            $editPartita = $p;
            break;
        }
    }
}

if (isset($_POST['save'])) {
    $id = intval($_POST['id_partita']);
    $gol1 = intval($_POST['gol1']);
    $gol2 = intval($_POST['gol2']);

    mysqli_query($connection, "UPDATE partita SET gol_squadra1=$gol1, gol_squadra2=$gol2 WHERE id_partita=$id");

    mysqli_query($connection, "DELETE FROM marcatore WHERE id_partita=$id");

    if(isset($_POST['marcatori']) && is_array($_POST['marcatori'])) {
        foreach($_POST['marcatori'] as $id_calciatore => $gol_fatti) {
            $gol_fatti = intval($gol_fatti);
            if ($gol_fatti > 0) {
                mysqli_query($connection, "INSERT INTO marcatore (id_partita, id_calciatore, gol_segnati) VALUES ($id, $id_calciatore, $gol_fatti)");
            }
        }

    }
    echo "<p style='color:green;'>Partita aggiornata!</p>";
}

if (!$editPartita):
    ?>
    <h2>Partite</h2>
    <a href="index.php?p=home" style="display:inline-block; margin-bottom:10px; padding:5px 10px; background:#ccc; border-radius:5px; text-decoration:none;">&larr; Go Back</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Squadra 1</th>
            <th>Squadra 2</th>
            <th>Gol 1</th>
            <th>Gol 2</th>
            <th>Data</th>
            <th>Azioni</th>
        </tr>
        <?php
        $index = 1;
        foreach($partite as $p):?>
            <tr>
                <td><?php echo $p['id_partita']; ?></td>
                <td><?php echo $p['team1']; ?></td>
                <td><?php echo $p['team2']; ?></td>
                <td><?php echo $p['gol1']; ?></td>
                <td><?php echo $p['gol2']; ?></td>
                <td><?php echo $p['orario']; ?></td>
                <td><a href="?p=admin&edit=<?php echo $p['id_partita']; ?>">Modifica</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <h2>Modifica Partita #<?php echo $editPartita['id_partita']; ?></h2>
    <a href="index.php?p=admin" style="display:inline-block; margin-bottom:10px; padding:5px 10px; background:#ccc; border-radius:5px; text-decoration:none;">&larr; Go Back</a>
    <form method="post">
        <input type="hidden" name="id_partita" value="<?php echo $editPartita['id_partita']; ?>">
        Gol <?php echo $editPartita['team1']; ?>: <input type="number" name="gol1" value="<?php echo $editPartita['gol1']; ?>"><br>
        Gol <?php echo $editPartita['team2']; ?>: <input type="number" name="gol2" value="<?php echo $editPartita['gol2']; ?>"><br>

        <h3>Marcatori</h3>

        <?php
        $query = "SELECT * FROM calciatore WHERE id_squadra IN (
            SELECT id_squadra FROM squadra WHERE nome IN ('".$editPartita['team1']."','".$editPartita['team2']."')
         ) ORDER BY id_squadra";
        $res = mysqli_query($connection, $query);

        $golPartita = [];
        $golRes = mysqli_query($connection, "SELECT id_calciatore, gol_segnati FROM marcatore WHERE id_partita = ".$editPartita['id_partita']);
        while($g = mysqli_fetch_assoc($golRes)) {
            $golPartita[$g['id_calciatore']] = $g['gol_segnati'];
        }

        $squadraCorrente = 0;
        while($c = mysqli_fetch_assoc($res)) {
            if ($c['id_squadra'] != $squadraCorrente) {
                if ($squadraCorrente != 0) echo "</div>";
                $squadraCorrente = $c['id_squadra'];
                $nomeSquadra = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM squadra WHERE id_squadra = $squadraCorrente"));
                echo "<div style='margin-bottom:20px; padding:10px; border:1px solid #ccc; border-radius:5px;'>";
                echo "<strong>".$nomeSquadra['nome']."</strong><br>";
            }
            $golFatti = isset($golPartita[$c['id_calciatore']]) ? $golPartita[$c['id_calciatore']] : 0;
            echo "<label>"
                . $c['nome'] . ": "
                . "<input type='number' name='marcatori[".$c['id_calciatore']."]' value='".$golFatti."' min='0' style='width:50px;'>"
                . "</label><br>";
        }
        echo "</div>";
        ?>


        <input type="submit" name="save" value="Salva">
    </form>
<?php endif; ?>

<?php CloseConnection($connection); ?>