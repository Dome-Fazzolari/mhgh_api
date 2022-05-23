<?php
    if(isset($_COOKIE['PHPSESSID'])){
        session_id($_COOKIE['PHPSESSID']);
    }
    session_start();

    if(!isset($_SESSION['user_id'])){
        $risposta = ['status'=>'failed','reason'=>'you_are_not_logged_in'];
        http_response_code(200);
        exit(json_encode($risposta));
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        $risposta = ['error' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }

    $target_dir = __DIR__.'/uploads';
    $target_file = $target_dir .DIRECTORY_SEPARATOR. strval($_SESSION['user_id']) . ".".pathinfo($_FILES['propic']['name'],PATHINFO_EXTENSION);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    echo $target_file . '<br>'. $_FILES['propic']['tmp_name'];
    $check = getimagesize($_FILES['propic']['tmp_name']);
    if($check == false){
        $risposta = ['status'=>'failed','reason'=>'file_is_not_image'];
        http_response_code(415);
        exit(json_encode($risposta));
    }

    if($_FILES['propic']['size'] > 40000){
        $risposta = ['status'=>'failed','reson'=>'file_too_large'];
        http_response_code(415);
        exit(json_encode($risposta));
    }
    if(copy($_FILES["propic"]["tmp_name"],$target_file)){
        require('config.php');
        $connessioneDB = new mysqli(SERVER,UTENTE,PASSWORD,DATABASE);
        if ($connessioneDB->errno) {
            $risposta = ['status' => 'error','error' => 'db_connection_error'];
            http_response_code(500);
            exit (json_encode($risposta));
        }

        $dichiarazioneImmagine = $connessioneDB->prepare("UPDATE utente SET link_propic = ? WHERE fk_credenziali = ".$_SESSION['user_id']);
        $dichiarazioneImmagine->bind_param("s",$target_file);
        try{
            $dichiarazioneImmagine->execute();
        }catch (Exception $e){
            $risposta = ['status'=>'failed','reason'=>$e->getMessage()];
            http_response_code(200);
            exit(json_encode($risposta));
        }
        $risposta = ['status'=>'success'];
        http_response_code(200);
        exit(json_encode($risposta));
    }
