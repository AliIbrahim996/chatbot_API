<?php

error_reporting(E_ALL);

ini_set('display_errors', 'on');

require_once "../Init/QuestionInit.php";

header("Access-Control-Allow-Methods: POST");

$q = QuestionInit::Question();

echo $q->getQuestions();
