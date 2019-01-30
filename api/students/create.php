<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../model/Students.php';

    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Post);
    header(Constants::$Headers.','.Constants::$Method_Post.','.Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);

    if($std->Create()){
        $student_arr = array();
        $student_arr['message'] = 'Student Created';
        $student_arr['student id'] = $std->id;
        echo json_encode($student_arr);
    }else{
        echo json_encode(array('message'=>'Create Action Failed','errors'=>$std->errors));
    }
