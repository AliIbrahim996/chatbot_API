<?php

error_reporting(E_ALL);

ini_set('display_errors', 'on');

require_once "../Init/QuestionInit.php";

header("Access-Control-Allow-Methods: POST");

$data = QuestionInit::getData();

$q = QuestionInit::Question();

if (!empty($data->query)) {

    echo $q->ask($data->query);
} else {

    http_response_code(403);

    echo json_encode(array(

        "message" => "Check your data!",

        "flag" => 0

    ));
}
