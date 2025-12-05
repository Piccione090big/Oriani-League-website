<?php
function Connect($dbName){
    $dbServername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    try {
        return mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);

    }
    catch (Exception $e) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
}

function GetGironeInfos($conn, $gironeIndex) {
    if($gironeIndex == -1)
        $result = mysqli_query($conn, "SELECT * FROM girone");
    else if($gironeIndex != null)
        $result = mysqli_query($conn, "SELECT * FROM girone WHERE id_girone = $gironeIndex");
    else
        return null;

    $retArray = array();

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $retArray[$row['id_girone']] = $row;
        }
    }

    return $retArray;
}

function GetSquadraInfos($conn, $squadraIndex, $gironeIndex) {
    if($squadraIndex == null && $gironeIndex != null)
        $result = mysqli_query($conn, "SELECT * FROM squadra WHERE id_girone = $gironeIndex");
    else if($squadraIndex != null && $squadraIndex != -1)
        $result = mysqli_query($conn, "SELECT * FROM squadra WHERE id_squadra = $squadraIndex");
    else if($squadraIndex == -1)
        $result = mysqli_query($conn, "SELECT * FROM squadra");
    else
        return null;

    $retArray = array();

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $retArray[$row['id_squadra']] = $row;
        }
    }

    return $retArray;
}

function GetGames($conn, $squadraIndex) {
    if($squadraIndex == -1 || $squadraIndex == null)
        $result = mysqli_query($conn, "SELECT * FROM partita");
    else
        $result = mysqli_query($conn, "SELECT * FROM partita WHERE id_squadra1 = $squadraIndex OR id_squadra2 = $squadraIndex");

    $retArray = array();

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $id1 = $row["id_squadra1"];
            $id2 = $row["id_squadra2"];
            $name1 = mysqli_query($conn, "SELECT nome FROM squadra WHERE id_squadra = $id1");
            $name2 = mysqli_query($conn, "SELECT nome FROM squadra WHERE id_squadra = $id2");
            if($name1 != null && $name2 != null) {
                $nameRow1 = mysqli_fetch_assoc($name1);
                $nameRow2 = mysqli_fetch_assoc($name2);

                $retArray[$row['id_partita']] = array(
                    'id_partita' => $row['id_partita'],
                    'orario' => $row['orario'],
                    'team1'  => $nameRow1['nome'],
                    'team2'  => $nameRow2['nome'],
                    'gol1'   => $row['gol_squadra1'],
                    'gol2'   => $row['gol_squadra2']
                );
            }
        }
    }

    return $retArray;
}

function GetMarcatoriGironi($conn) {
    $query = "
        SELECT 
            c.nome AS giocatore, 
            s.nome AS squadra, 
            g.nome AS girone,
            SUM(m.gol_segnati) AS totale_gol
        FROM marcatore m
        JOIN calciatore c ON c.id_calciatore = m.id_calciatore
        JOIN squadra s ON s.id_squadra = c.id_squadra
        JOIN girone g ON s.id_girone = g.id_girone
        GROUP BY c.id_calciatore, giocatore, squadra, girone
        ORDER BY totale_gol DESC
    ";

    $result = mysqli_query($conn, $query);
    $marcatori = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $marcatori[] = $row;
        }
    }

    return $marcatori;
}



function GetMarcatoriGirone($conn, $gironeId) {
    $query = "
        SELECT c.nome AS giocatore, s.nome AS squadra, SUM(m.gol_segnati) AS totale_gol
        FROM marcatore m
        JOIN calciatore c ON c.id_calciatore = m.id_calciatore
        JOIN squadra s ON s.id_squadra = c.id_squadra
        WHERE s.id_girone = $gironeId
        GROUP BY c.id_calciatore, giocatore, squadra
        ORDER BY totale_gol DESC
    ";

    $result = mysqli_query($conn, $query);
    $marcatori = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $marcatori[] = $row;
    }

    return $marcatori;
}

function CloseConnection($conn) {
        mysqli_close($conn);
}