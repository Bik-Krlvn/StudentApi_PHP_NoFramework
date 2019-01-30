<?php
    include_once '../../config/Constants.php';
    include_once '../../config/core.php';
    include_once '../../config/Token.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Post);
    header(Constants::$Headers.','.Constants::$Method_Post.','.Constants::$ContentType);

    $data = json_decode(file_get_contents('php://input'));
    $token = isset($data->token) ? $data->token : die();


    if($token){
        $decode = Token::DecodeToken($token,$key,$alg);
        if ($decode == null) return;
        http_response_code(200);
        echo json_encode(array('message'=>'Access Granted','data'=>$decode->data));
       
    }else{
        http_response_code(401);
        echo json_encode(array('message'=>'Validation Failed'));
    }
