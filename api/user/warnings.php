<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/sql/selectWarning.php");

function listWarnings(Database $database): string
{
    $sql = SELECT_WARNING;
    $warnings = $database->query($sql);
    return $database->responseSuccess(array(
        'countOfWarnings' => sizeof($warnings),
        'warnings' => $warnings
    ));
}

function clearWarnings(Database $database): string
{
    $sql = <<<SQL
        DELETE
        FROM warning
        WHERE id > 0
    SQL;
    $warnings = $database->query($sql);
    return $database->responseSuccess(array(
        'message' => 'All deleted'
    ));
}


$database = new Database();
$database->handleRequest('listWarnings', null, null, 'clearWarnings');
