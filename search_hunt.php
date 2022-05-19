<?php
    if(isset($_COOKIE['PHPSESSID'])){
        session_id($_COOKIE['PHPSESSID']);
    }
    session_start();
    /*
     * Variabili di cui ho bisogno:
     * -hunt_preferences
     * -orario_libero_inizio/fine
     * -hr(facoltativo)
     */

    if(!isset($_SESSION['user_id'])){
        $risposta = ['status'=>'failed','reason'=>'you_are_not_logged_in'];
        http_response_code(200);
        exit(json_encode($risposta));
    }

    require('config.php');
    $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['status' => 'error','error' => 'db_connection_error'];
        http_response_code(500);
        exit (json_encode($risposta));
    }

        $preferenze_caccia = $_GET['preferenze_caccia'];
    $hr = $_GET['HR'];
    $orario  = intval(date('H'));

    /*
     * Preferenze di caccia:
     * -ffn = for fun,quindi casual gameplay come il grind dei materiali di un mostro cazzeggiando
     * -trh = try hard, quindi per quelli che vogliono essere perfetti per qualunque aspetto della caccia
     * -srh = search help, quei giocatori che hanno bisogno di aiuto per procedere nel gioco oppure fanno fatica con determinate caccie
     * -gvh = give help, coloro che sono disposti ad aiutare i giocatori in difficoltà
     */
    $dichiarazioneRicerca = $connessioneDB->prepare("
            SELECT username,arma_preferita,HR,fk_credenziali 
            FROM utente 
            WHERE preferenze_caccia = ? AND HR>? AND (?>orario_libero_inizio OR ?<orario_libero_fine)");
    $dichiarazioneRicerca->bind_param("siii",$preferenze_caccia,$hr,$orario,$orario);
    $dichiarazioneRicerca->execute();
    $risultatiRIcerca = $dichiarazioneRicerca->get_result();
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