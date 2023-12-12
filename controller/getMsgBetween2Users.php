<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
require_once "../config/Database.php";
require_once "../Models/Chat.php";

$database = new Database();
$conn = $database->connect();
$c = new Chat($conn);
$data = json_decode(file_get_contents("php://input"));
echo $c->getMsg2($data->u1, $data->u2);
