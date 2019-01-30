<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../model/Students.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Put);
    header(Constants::$Headers.','.Constants::$Method_Put.','.Constants::$Headers);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    if($std->Update()){
        echo json_encode(array('message'=>'Student Record Updated','errors'=>$std->errors));
    }else{
        echo json_encode(array('message'=>'Update Action Failed','errors'=>$std->errors));
    }