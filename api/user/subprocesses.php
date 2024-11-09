<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/sql/selectSubprocess.php");

function listSubprocesses(Database $database): string
{
    $sql = SELECT_SUBPROCESS_SQL;
    $subprocesses = $database->query($sql);
    return $database->responseSuccess(array(
        'countOfSubprocesses' => sizeof($subprocesses),
        'subprocesses' => $subprocesses
    ));
}


$database = new Database();
$database->handleRequest('listSubprocesses');
