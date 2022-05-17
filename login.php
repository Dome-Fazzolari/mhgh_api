<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){  //Verifico che la richiesta si post
        if(isset($_SESSION["user_id"])){
            http_response_code(200);
            $risposta = ['status' => 'success'];
            exit(json_encode($risposta));
        }else {
            require("config.php");
            $connessioneDB = new mysqli(SERVER, UTENTE, PASSWORD, DATABASE);
            if ($connessioneDB->errno) {
                $risposta = ['status' => 'error','error' => 'Errore di connessione al database'];
                http_response_code(500);
                exit (json_encode($risposta));
            }
            $dichiarazioneLogin = $connessioneDB->prepare("select id,password_hash from credenziali_utenti where email = ? ");
            $email = $_POST["email"];
            $dichiarazioneLogin->bind_param("s", $email);

            $dichiarazioneLogin->execute();
            $risposteLogin = $dichiarazioneLogin->get_result();
            if ($risposteLogin->num_rows > 0) {
                while ($riga = $risposteLogin->fetch_assoc()) {
                    $options = ['salt' => $riga['password_salt']];
                    if (password_verify($_POST["password"],$riga["password_hash"])) {
                        http_response_code(200);
                        $_SESSION['user_id'] = $riga["id"];
                        $risposta = ['status' => 'success','user_id'=>$riga["id"]];
                        exit(json_encode($risposta));
                    }else{
                        http_response_code(200);
                        $risposta = ['status' => 'failed','reason'=>'email_or_password_wrong'];
                        exit(json_encode($risposta));
                    }
                }
            }else{
                http_response_code(200);
                $risposta = ['status' => 'failed','reason'=>'email_or_password_wrong'];
                exit(json_encode($risposta));
            }
        }
    }else{
        /*
         * Mi autoinsulto in caso la richiesta sia sbagliata (es.faccio POST invece di GET)
         */
        $risposta = ['error' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }
?>