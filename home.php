<?php
    if(isset($_COOKIE['PHPSESSID'])){
        session_id($_COOKIE['PHPSESSID']);
    }
    session_start();

    if(!isset($_SESSION["user_id"])){
        http_response_code(200);
        exit(json_encode(['status'=>'failed','reason'=>'you_have_to_login_first']));
    }

    $mese = 60 *60 * 24 * 7 * 4; //secondi in un mese
    setcookie('PHPSESSID',session_id(),time()+$mese);

    require('config.php');
    $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['status' => 'error','error' => 'db_connection_error'];
        http_response_code(500);
        exit (json_encode($risposta));
    }

    $dichiarazioneHome = $connessioneDB->prepare("
            select utente.username as username,utente.arma_preferita as arma_preferita,utente.fk_credenziali as fk_credenziali
            from utenti_preferiti join utente on utente.fk_credenziali = utenti_preferiti.utente_salvato
            where utenti_preferiti.fk_utente = ?");

    $dichiarazioneHome->bind_param("i",$_SESSION['user_id']);
    $dichiarazioneHome->execute();
    $risultatiRIcerca = $dichiarazioneHome->get_result();

    if ($risultatiRIcerca->num_rows<1){
        http_response_code(200);
        exit(json_encode(['status'=>'success','results'=>'none']));
    }
    $utenti = [];
    while($riga = $risultatiRIcerca->fetch_assoc()){
        $utente = [
            'username'=>$riga['username'],
            'arma_preferita'=>$riga['arma_preferita'],
            'fk_credenziali'=>$riga['fk_credenziali']
        ];
        array_push($utenti,$utente);
    }

    http_response_code(200);
    exit(json_encode(['status'=>'success','results'=>$utenti]));