<?php
    if($_SERVER["REQUEST_METHOD"] !== 'GET'){
        $risposta = ['error' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }

    require('config.php');
    $connessioneDB = new mysqli(SERVER,UTENTE,PASSWORD,DATABASE);
    if ($connessioneDB->errno) {
        $risposta = ['status' => 'error','error' => 'db_connection_error'];
        http_response_code(500);
        exit (json_encode($risposta));
    }
    $dichiarazioneUtente = $connessioneDB->prepare("select username,discord_data,bio_personale,link_propic,arma_preferita,preferenze_caccia,orario_libero_inizio,orario_libero_fine,HR,piattaforma from utente where fk_credenziali = ?");
    $dichiarazioneUtente->bind_param("i",$_GET["user_id"]);
    $dichiarazioneUtente->execute();
    $risultatiUtenti = $dichiarazioneUtente->get_result();
    if($risultatiUtenti->num_rows < 1){
        $risposta = ['status' => 'success','result'=>'none'];
        http_response_code(200);
        exit (json_encode($risposta));
    }
    $riga = $risultatiUtenti->fetch_assoc();
    $utente = [
        'username'=>$riga['username'],
        'discord_data'=>$riga['discord_data'],
        'bio_personale'=>$riga['bio_personale'],
        'link_propic'=>$riga['link_propic'],
        'arma_preferita'=>$riga['arma_preferita'],
        'preferenze_caccia'=>$riga['preferenze_caccia'],
        'orario_libero_inizio'=>$riga['orario_libero_inizio'],
        'orario_libero_fine'=>$riga['orario_libero_fine'],
        'HR'=>$riga['HR'],
        'piattaforma'=>$riga['piattaforma']
    ];
    $risposta = ['status' => 'success','result'=>$utente];
    http_response_code(200);
    exit (json_encode($risposta));
?>