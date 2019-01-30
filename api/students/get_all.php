<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../config/core.php';
    include_once '../../config/Token.php';
    include_once '../../model/Students.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $std = new Students($conn);
    $token = isset($_GET['token']) ? $_GET['token'] : '';
    $auth = Token::Authenticate($token,$key,$alg);

   if($auth){
        $result = $std->Get();
        $result->execute();
        $count = $result->rowCount();
        $std->errors = $result->error;
        if($count == 0) {echo json_encode(array('message'=>'No Student Records Available')); return;}

        $student_arr = array();
        $student_arr['message'] = "Student Records";
        $student_arr['count'] = $count;
        $student_arr['errors'] = $std->errors;
        $student_arr['students'] = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            # code...
            extract($row);
            $student_item = array(
                "id"         => $id,
                "firstname"  =>$firstname,
                "lastname"   =>$lastname,
                "email"      =>$email,
                "created_at" =>$created_at
            );
            array_push($student_arr['students'],$student_item);
        }
        echo json_encode($student_arr);
   }else{
        http_response_code(401);
        echo json_encode(array('message'=>'Authentication is required For This Action'));   
    }




