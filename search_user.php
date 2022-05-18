<?php
    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        $risposta = ['error' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }

    require('config.php');
    $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['status' => 'error','error' => 'db_connection_error'];
        http_response_code(500);
        exit (json_encode($risposta));
    }

    $dichiarazioneRicerca = $connessioneDB->prepare('SELECT username,fk_credenziali FROM utente WHERE username like ?');
    $usernameRicercato = '%'.$_GET['user_searched'].'%';
    $dichiarazioneRicerca->bind_param("s",$usernameRicercato);
    $dichiarazioneRicerca->execute();
    $risultatiRicerca = $dichiarazioneRicerca->get_result();
    if ($risultatiRicerca->num_rows<1){
        $risposta = ['status'=>'success','results'=>'none'];
        http_response_code(200);
        exit(json_encode($risposta));
    }

    $utenti = [];

    while($riga = $risultatiRicerca->fetch_assoc()){
        $utente = ['username'=>$riga['username'],'user_id'=>$riga['fk_credenziali']];
        array_push($utenti,$utente);
    }

    $risposta = ['status'=>'success','results'=>$utenti];
    http_response_code(200);
    exit(json_encode($risposta));
