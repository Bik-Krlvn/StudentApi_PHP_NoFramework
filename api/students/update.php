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
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $auth = Token::Authenticate($token,$key,$alg);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    if($auth){
        if($std->Update()){
            echo json_encode(array('message'=>'Student Record Updated','errors'=>$std->errors));
        }else{
            echo json_encode(array('message'=>'Update Action Failed','errors'=>$std->errors));
        }
    }else{
        echo json_encode(array('message'=>'Authentication is Required For This Action','errors'=>$std->errors));
    }