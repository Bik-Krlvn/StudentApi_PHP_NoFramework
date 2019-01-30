<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Token.php';
    include_once '../../config/core.php';
    include_once '../../config/Database.php';
    include_once '../../model/Students.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    $std->id = isset($_GET['id']) ? $_GET['id'] : die();

    $headers = apache_request_headers();
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    $remove_bearer = explode("Bearer ",$jwt);
    $token = $remove_bearer[1];
    $auth = Token::Authenticate($token,$key,$alg);
    
    if($auth){
        $std->id = htmlspecialchars_decode(strip_tags($std->id));

        if($std->Delete()){
            echo json_encode(array('message'=>'Student Record Deleted','errors'=>$std->errors));
        }else{
            echo json_encode(array('message'=>'Delete Action Failed','errors'=>$std->errors));
        }
    }else{
        http_response_code(401);
        echo json_encode(array('message'=>'Authentication is Required For This Action'));
    }