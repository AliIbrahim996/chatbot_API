<?php

require_once "../../config/Database.php";

require_once "../../Models/Reference.php";

$database = new Database();

$conn = $database->connect();

$r = new Reference($conn);

echo $r->get();
