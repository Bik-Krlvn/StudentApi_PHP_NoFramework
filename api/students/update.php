<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../config/Token.php';
    include_once '../../config/core.php';
    include_once '../../model/Students.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Put);
    header(Constants::$Headers.','.Constants::$Method_Put.','.Constants::$ContentType);
    
    $headers = apache_request_headers();
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    $remove_bearer = explode("Bearer ",$jwt);
    $token = $remove_bearer[1];
    $auth = Token::Authenticate($token,$key,$alg);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    if($auth){
        $results = $std->Update();
        try {
            //code...
            if($results->execute()){
                echo json_encode(array('message'=>'Student Record Updated','errors'=>$std->errors));
            }
        } catch (Exception $e) {
            //throw $th;
            http_response_code(400);
            $std->errors['Mysql'] = $e->getMessage();
            echo json_encode(array('message'=>'Update Action Failed','errors'=>$std->errors));
        }
    }else{
        http_response_code(401);
        echo json_encode(array('message'=>'Authentication is Required For This Action','errors'=>$std->errors));
    }