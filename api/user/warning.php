<?php

use system\Database;
use system\AccessBlock;
use system\StandardType;
use system\SqlComparison;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/AccessBlock.php");
include("../../system/sql/selectWarning.php");
include("../../system/sql/selectWarningType.php");
include("../../system/sql/selectSubprocess.php");

function postWarning(Database $database): string
{
    $requiredParams = array(
        array(
            'param' => 'warningTypeId',
            'type' => StandardType::ID,
            'exists' => new SqlComparison(WARNING_TYPE_EXISTS_SQL)
        ),
        array(
            'param' => 'subprocessId',
            'type' => StandardType::ID,
            'exists' => new SqlComparison(SUBPROCESS_EXISTS_SQL)
        )
    );
    $missingParam = AccessBlock::findMissingParam($requiredParams, $database);
    if ($missingParam) {
        return $database->responseBadRequest($missingParam);
    }
    $sql = <<<SQL
        INSERT INTO warning (warningTypeId, subprocessId)
        VALUES (:warningTypeId, :subprocessId)
    SQL;
    $warnings = $database->query($sql);
    return $database->responseSuccess(array(
        'countOfWarnings' => sizeof($warnings),
        'warnings' => $warnings
    ));
}


$database = new Database();
$database->handleRequest(null, 'postWarning');
