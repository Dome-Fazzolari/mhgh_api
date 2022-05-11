<?php
    session_start();
    if(isset($_POST['user_id'])){
        session_unset();
        session_destroy();
        http_response_code(204);
    }else{
        http_response_code(400);
        exit(json_encode(['error'=>'Nessuna sessione rilevata']));
    }
?>