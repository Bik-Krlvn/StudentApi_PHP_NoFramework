<?php
    include_once '../../config/Constants.php';
    include_once '../../config/Database.php';
    include_once '../../model/Users.php';
    header(Constants::$Origin);
    header(Constants::$ContentType);
    header(Constants::$Method_Post);
    header(Constants::$Headers.','.Constants::$Method_Post.','.Constants::$ContentType);

    $database = new Database();
    $conn = $database->Connect();
    $user = new Users($conn);
    $user_arr = array();
    
    if ($user->Create()){
        $user_arr['message'] = 'User Created';
        $user_arr['user id'] = $user->id;
        $user_arr['errors']  = $user->errors;
        echo json_encode($user_arr);
    }else{
        $user_arr['message'] = 'Create Action Failed';
        $user_arr['errors']  = $user->errors;
        echo json_encode($user_arr);
    }