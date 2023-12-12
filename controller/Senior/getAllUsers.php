<?php
error_reporting(E_ALL);
ini_set('display_errors','on');

require_once "../config/Database.php";
require_once "../Models/User.php";

$data = json_decode(file_get_contents("php://input"));
$database = new Database();
$conn = $database->connect();
$u = new User($conn);
$sId = "";
if(!empty($data->sId))
    $sId = $data->sId;
echo $data->role;
echo $u->getAll($data->role,$sId);