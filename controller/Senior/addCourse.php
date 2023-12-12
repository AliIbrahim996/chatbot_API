<?php

require_once "../../config/Database.php";

require_once "../../Models/Course.php";

$database = new Database();

$conn = $database->connect();

$c = new Course($conn);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->cName)) {

    echo $c->add($data->cName);
} else {

    http_response_code(401);

    return json_encode(

        array("message" => "check your data!")

    );
}
