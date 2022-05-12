<?php
    session_start();
    if($_SERVER["REQUEST_METHOD"] === 'POST'){
        require('config.php');

        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $connessioneDB = new mysqli(SERVER, USER, PASSWORD, DATABASE);
        if ($connessioneDB->errno) {
            $risposta = ['error' => 'Errore di connessione al database'];
            http_response_code(500);
            exit (json_encode($risposta));
        }

        $dichiarazioneLogin = $connessioneDB->prepare("SELECT email FROM credenziali_utenti WHERE email = ?");
        $dichiarazioneLogin->bind_param("s", $email);
        $dichiarazioneLogin->execute();
        $risposteLogin = $dichiarazioneLogin->get_result();

        if ($risposteLogin->errno) {
            $risposta = ['error' => 'Errore della query'];
            http_response_code(500);
            exit (json_encode($risposta));
        }

        if($risposteLogin->num_rows > 0){
            $risposta = ['status' => 'failed','reason'=>'user_already_exists'];
            http_response_code(200);
            exit (json_encode($risposta));
        }
        $dichiarazione = $connessioneDB->prepare("INSERT INTO credenziali_utenti(email,password_salt,password_hash) VALUES(?,?,?)");
        $password_salt = mcrypt_create_iv(5, MCRYPT_DEV_URANDOM);
        $password_hash = password_hash(password,PASSWORD_DEFAULT,['salt'=>$password_salt]);
        $dichiarazione->bind_param("sss",$email,$password_salt,$password_hash);
        $dichiarazione->execute();
        if($dichiarazione->errno){
            http_send_status(500);
            $risposta = ['status'=>'error','error'=>'query_error'];
            exit(json_encode($risposta));
        }

        $dichiarazione = $connessioneDB->prepare("SELECT id FROM credenziali_utenti WHERE email = ?");
        $dichiarazione->bind_param("s",$email);
        $dichiarazione->execute();
        if($dichiarazione->errno){
            http_send_status(500);
            $risposta = ['status'=>'error','error'=>'query_error'];
            exit(json_encode($risposta));
        }
        $risultati_query = $dichiarazione->get_result();
        $riga_utente = $risultati_query->fetch_assoc();
        $user_id = $riga_utente["id"];

        $dichiarazione = $connessioneDB->prepare("INSERT INTO utente(username,sesso,bio_personale,link_propic,discord_data,relate_user_id) VALUES(?,'none','','','',?)");
        $dichiarazione->bind_param("s",$username,$user_id);
        $dichiarazione->execute();
        if($dichiarazione->errno){
            http_send_status(500);
            $risposta = ['status'=>'error','error'=>'query_error'];
            exit(json_encode($risposta));
        }
        http_send_status(200);
        $risposta = ['status'=>'success'];
        exit(json_encode($risposta));
    }else{
        $risposta = ['error' => 'Metodo di richiesta sbagliato'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }