<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../config/core.php';
    include_once '../../config/Token.php';
    include_once '../../model/Users.php';

    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Post);
    header(Constants::$Headers.','.Constants::$Method_Post.','.Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $user = new Users($conn);
    $data = json_decode(file_get_contents('php://input'));
    $user->email =  isset($data->email) ? $data->email : die();
    $email_check = $user->EmailExist();

    if($email_check && password_verify($data->password,$user->password)){
        $jwt = array(
            "iss" =>$iss,
            "aud" =>$aud,
            "iat" =>$iat,
            "nbf" =>$nbf,
            "key" =>$key
        );
        $user_data = array(
            "id"        => $user->id,
            "firstname" =>$user->firstname,
            "lastname"  => $user->lastname,
            "email"     =>$user->email
        );

        $token = Token::EncodeToken($jwt,$user_data);
        if($token == null) return;
        http_response_code(200);
        echo json_encode(array('message'=>'Login Successful','token'=>$token));

    }else{
        http_response_code(401);
        echo json_encode(array('message'=>'Login Failed'));
    }
