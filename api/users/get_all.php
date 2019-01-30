<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../model/Users.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $user = new Users($conn);
    $results = $user->Get();
    $results->execute();
    $user->errors = $results->error;
    $num = $results->rowCount();
    $user_arr = array();

    if($num == 0){
        http_response_code(404);
        $user_arr['message'] = 'No Users Available';
        $user_arr['error']   = $user->errors;
        echo json_encode($user_arr);
        return;
    }

    while($row = $results->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $user_arr['message'] = 'Available Users';
        $user_arr['count']   = $num;
        $user_arr['users']   = array();

        $user_item = array(
            "id"         => $id,
            "firstname"  => $firstname,
            "lastname"   =>$lastname,
            "email"      =>$email,
            "created_at" => $created_at
        );
        array_push($user_arr['users'],$user_item);
    }
    http_response_code(200);
    echo json_encode($user_arr);

