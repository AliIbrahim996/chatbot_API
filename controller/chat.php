<?php

require_once "../config/Database.php";

require_once "../Models/Chat.php";


$database = new Database();

$conn = $database->connect();

$c = new Chat($conn);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->sId) && !empty($data->rId)) {

    echo $c->chat($data);

} else {

    http_response_code(400);

    echo json_encode(array("message" => "check your data"));

}