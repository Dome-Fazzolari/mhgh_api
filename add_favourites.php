<?php
    if(isset($_COOKIE['PHPSESSID'])){
        session_id($_COOKIE['PHPSESSID']);
    }
    session_start();
    if($_SERVER['REQUEST_METHOD']!=='GET'){
        $risposta = ['error' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }

    if(!isset($_SESSION['user_id'])){
        http_response_code(200);
        exit(json_encode(['status'=>'failed','reason'=>'you_are_not_logged_in']));
    }

    if($_SESSION['user_id'] == $_GET['add_user_id']){
        http_response_code(200);
        exit(json_encode(['status'=>'failed','reason'=>'youre_not_narcissus_asshole']));
    }

    require("config.php");
    $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['status' => 'error','error' => 'Errore di connessione al database'];
        http_response_code(500);
        exit (json_encode($risposta));
    }

    $dichiarazioneAggiunta = $connessioneDB->prepare("INSERT INTO utenti_preferiti(fk_utente,utente_salvato) VALUES(?,?)");
    $dichiarazioneAggiunta->bind_param("ii",$_SESSION['user_id'],$_GET['add_user_id']);
    try{
        $dichiarazioneAggiunta->execute();
    }catch(Exception $e){
        http_response_code(200);
        echo $e->getMessage();
        exit(json_encode(['status'=>'failed','reason'=>'added_user_doesnt_exists']));
    }
    http_response_code(200);
    exit(json_encode(['status'=>'success']));

