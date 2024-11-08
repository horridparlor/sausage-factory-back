<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");

function getStatus(Database $database): string
{
    return $database->responseSuccess(array(
       'data' => 222
    ));
}


$database = new Database();
$database->handleRequest('getStatus');
