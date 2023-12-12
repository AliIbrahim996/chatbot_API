<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once "../Init/UserInit.php";
header("Access-Control-Allow-Methods: POST");
$data = UserInit::getData();
$user = UserInit::getUser();
if (!empty($data->email) && !empty($data->password)) {
    echo $user->userLogIn($data->email, $data->password);
} else {
    http_response_code(403);
    echo json_encode(array(
        "message" => "Check your data!",
        "flag" => 0
    ));
}
