<?php

require_once "../../config/Database.php";

require_once "../../Models/Course.php";

$database = new Database();

$conn = $database->connect();

$c = new Course($conn);

echo $c->get();
