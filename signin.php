<?php
    if($_SERVER["REQUEST_METHOD"] === 'POST'){
        require('config.php');

        $username = $_POST["username"];
        $email = $_POST["email"];
        $password_utente = $_POST["password"];

        $hash = password_hash($password_utente, PASSWORD_DEFAULT);


        $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
        if ($connessioneDB->errno) {
            $risposta = ['error' => 'Errore di connessione al database'];
            http_response_code(500);
            exit (json_encode($risposta));
        }



        $dichiarazioneInserimento = $connessioneDB->prepare("INSERT INTO credenziali_utenti(email,password_hash) VALUES (?,?)");
        $dichiarazioneInserimento->bind_param("ss", $email,$hash);
        echo "connesso al db<br>";
        try{
            $dichiarazioneInserimento->execute();
        }catch(Exception $e){
            http_response_code(500);
            exit (json_encode(['status' => 'error','error'=>'user_already_exists']));
        }

        echo "\ninserito e verificato utente doppio";

        $dichiarazione = $connessioneDB->prepare("SELECT id FROM credenziali_utenti WHERE email = ?");
        $dichiarazione->bind_param("s",$email);
        $dichiarazione->execute();

        $risultati_query = $dichiarazione->get_result();
        $riga_utente = $risultati_query->fetch_assoc();
        $user_id = $riga_utente["id"];

        $dichiarazione = $connessioneDB->prepare("INSERT INTO utente(username,fk_credenziali) VALUES(?,?)");
        $dichiarazione->bind_param("si",$username,$user_id);
        $dichiarazione->execute();
        $_SESSION["user_id"] = $user_id;
        http_send_status(200);
        $risposta = ['status'=>'success'];
        $mese = 60 *60 * 24 * 7 * 4; //secondi in un mese
        setcookie('PHPSESSID',session_id(),time()+$mese);
        exit(json_encode($risposta));

    }else{
        $risposta = ['error' => 'Metodo di richiesta sbagliato'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }