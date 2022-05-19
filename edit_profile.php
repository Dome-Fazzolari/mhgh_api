<?php
    if(isset($_COOKIE['PHPSESSID'])){
        session_id($_COOKIE['PHPSESSID']);
    }
    session_start();
    if($_SERVER["REQUEST_METHOD"] != 'POST'){
        $risposta = ['status' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }
    if(!isset($_SESSION["user_id"])){
        http_response_code(200);
        exit(json_encode(['status'=>'failed','reason'=>'you_have_to_login_first']));
    }

    require('config.php');
    $connessioneDB = new mysqli(SERVER,UTENTE,PASSWORD,DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['error' => 'db_connection_error'];
        http_response_code(500);
        exit (json_encode($risposta));
    }

    $username = $_POST["username"];
    $sesso = $_POST["sesso"];
    $discord_data = $_POST["discord_data"];
    $bio_personale = $_POST["bio_personale"];
    $link_propic = $_POST["link_propic"];
    $arma_preferita = $_POST["arma_preferita"];
    $preferenze_caccia = $_POST["preferenze_caccia"];
    $orario_libero_inizio = $_POST["orario_libero_inizio"];
    $orario_libero_fine = $_POST["orario_libero_fine"];
    $HR = $_POST["HR"];
    $piattaforma = $_POST["piattaforma"];
    $user_id = $_SESSION["user_id"];

    $dichiarazioneModifica = $connessioneDB->prepare("UPDATE utente 
    SET 
        username = ?,
        sesso = ?,
        discord_data = ?,
        bio_personale = ?,
        link_propic = ?,
        arma_preferita = ?,
        preferenze_caccia = ?,
        orario_libero_inizio = ?,
        orario_libero_fine = ?,
        HR = ?,
        piattaforma = ?
    WHERE fk_credenziali = ?");
    $dichiarazioneModifica->bind_param("sssssssiiisi",$username,
        $sesso,
        $discord_data,
        $bio_personale,
        $link_propic,
        $arma_preferita,
        $preferenze_caccia,
        $orario_libero_inizio,
        $orario_libero_fine,
        $HR,
        $piattaforma,
        $user_id);

    try{
        $dichiarazioneModifica->execute();
    }catch (Exception $e){
        echo $e->getMessage();
        exit();
    }

    http_response_code(200);
    $risposta = ['status' => 'success'];
    exit(json_encode($risposta));
?>