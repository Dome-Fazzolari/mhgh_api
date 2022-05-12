<?php
    if($_SERVER["REQUEST_METHOD"] != 'POST'){
        $risposta = ['status' => 'failed','reason'=>'bad_request_method'];
        http_response_code(400);                        //Bad request status code
        exit (json_encode($risposta));
    }

    require('config.php');
    $connessioneDB = new mysqli(SERVER,USER,PASSWORD,DATABASE);
?>