<?php

require_once "../../config/Database.php";

require_once "../../Models/Reference.php";

$database = new Database();

$conn = $database->connect();

$r = new Reference($conn);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->rName)) {

    echo $r->add($data->rName);
} else {

    http_response_code(401);

    return json_encode(

        array("message" => "check your data!")
    );
}
