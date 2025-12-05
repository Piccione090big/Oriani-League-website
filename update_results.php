<?php
session_start();
if(!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php?p=admin");
    exit;
}

if(isset($_POST['update'])) {
    $id = intval($_POST['id_partita']);
    $gol1 = intval($_POST['gol1']);
    $gol2 = intval($_POST['gol2']);

    $conn = Connect("calcetto_db");

    $sql = "UPDATE partita SET gol_squadra1 = $gol1, gol_squadra2 = $gol2 WHERE id_partita = $id";
    mysqli_query($conn, $sql);

    CloseConnection($conn);
}

header("Location: index.php?p=admin");
exit;
