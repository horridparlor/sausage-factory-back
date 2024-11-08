<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");

function getStatus(Database $database): string
{
    $sql = <<<SQL

    SQL;
    $warnings = $database->query($sql);
    return $database->responseSuccess(array(
        'countOfWarnings' => sizeof($warnings),
        'warnings' => $warnings
    ));
}


$database = new Database();
$database->handleRequest('getStatus');
