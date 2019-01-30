<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../model/Students.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    $std->id = isset($_GET['id']) ? $_GET['id'] : die();
    $std->id = htmlspecialchars_decode(strip_tags($std->id));

    if($std->Delete()){
        echo json_encode(array('message'=>'Student Record Deleted','errors'=>$std->errors));
    }else{
        echo json_encode(array('message'=>'Delete Action Failed','errors'=>$std->errors));
    }