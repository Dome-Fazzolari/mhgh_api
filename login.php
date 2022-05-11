<?php
    session_start();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){  //Verifico che la richiesta si post
        if(isset($_POST["user_id"])){
            http_response_code(200);
            $risposta = ['success' => 'login effettuato'];
            exit(json_encode($risposta));
        }else {
            require("config.php");
            $connessioneDB = new mysqli(SERVER, USER, PASSWORD, DATABASE);
            if ($connessioneDB->errno) {
                $risposta = ['error' => 'Errore di connessione al database'];
                http_response_code(500);
                exit (json_encode($risposta));
            }

            //query da rifare
            $dichiarazioneLogin = $connessioneDB->prepare("SELECT CU.password_salt as 'password_salt',CU.password_hash as 'password_hash',U.username as 'username' FROM credenziali_utenti CU JOIN utente U on CU.id = U.related_user_id WHERE email = ?");
            $dichiarazioneLogin->bind("s", $_POST["email"]);
            $dichiarazioneLogin->execute();
            $risposteLogin = $dichiarazioneLogin->get_result();

            if ($risposteLogin->errno) {
                $risposta = ['error' => 'Errore della query'];
                http_response_code(500);
                exit (json_encode($risposta));
            }

            if ($risposteLogin->num_rows > 0) {
                while ($riga = $risposteLogin->fetch_assoc()) {
                    $options = ['salt' => $riga['password_salt']];
                    if (password_hash($_POST["password"], PASSWORD_DEFAULT, $options) == $riga['password_hash']) {
                        http_response_code(200);
                        $_SESSION["user_id"] = $riga["user_id"];
                        $risposta = ['success' => 'login effettuato'];
                        exit(json_encode($risposta));
                    }
                }
            }
        }
    }else{
        /*
         * Mi autoinsulto in caso la richiesta sia sbagliata (es.faccio post invece di GET)
         */
        $risposta = ['error' => 'Metodo di richiesta sbagliato'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }
?>