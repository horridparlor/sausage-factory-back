<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/sql/selectWarningType.php");

function listWarningTypes(Database $database): string
{
    $sql = SELECT_WARNING_TYPE_SQL;
    $warningTypes = $database->query($sql);
    return $database->responseSuccess(array(
        'countOfWarningTypes' => sizeof($warningTypes),
        'warningTypes' => $warningTypes
    ));
}


$database = new Database();
$database->handleRequest('listWarningTypes');
