<?php
    include_once '../../config/Constants.php';
    include_once '../../config/core.php';
    include_once '../../config/Token.php';
    include_once '../../config/Database.php';
    include_once '../../model/Users.php';
    
    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Post);
    header(Constants::$Headers.','.Constants::$Method_Post.','.Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $user = new Users($conn);

    $data = json_decode(file_get_contents('php://input'));
    $token = isset($data->token) ? $data->token : die();
    $decode = Token::DecodeToken($token,$key,$alg);
    if($decode == null) return;

    $user->id = $decode->data->id;
    $user->firstname = $data->firstname;
    $user->lastname = $data->lastname;
    $user->email = $data->email;
    $user->password = $data->password;

    $results = $user->Update();
    try {
        if($results->execute()){
            $jwt = array(
                "iss" =>$iss,
                "aud" =>$aud,
                "iat" =>$iat,
                "nbf" =>$nbf,
                "key" =>$key
            );
            $user_data = array(
                "id"        => $user->id,
                "firstname" => $user->firstname,
                "lastname"  => $user->lastname,
                "email"     => $user->email
            );
            $token = Token::EncodeToken($jwt,$user_data);
            if($token == null) return;
            echo json_encode(array('message'=>'User Info Updated','errors'=>$user->errors,'token'=>$token));
        }

    } catch (Exception $e) {
        http_response_code(400);
        $user->errors['Mysql'] = $e->getMessage();
        echo json_encode(array('message'=>'Update Action Failed','errors'=>$user->errors));
    }