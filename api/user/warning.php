<?php

use system\Database;
use system\AccessBlock;
use system\StandardType;
use system\SqlComparison;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/AccessBlock.php");
include("../../system/entity/User.php");
include("../../system/sql/selectWarning.php");
include("../../system/sql/selectWarningType.php");
include("../../system/sql/selectSubprocess.php");

function postWarning(Database $database): string
{
    $user = $database->getUser();
    if (!$user) {
        return $database->responseUnauthorized();
    }
    $requiredParams = array(
        array(
            'param' => 'warningTypeId',
            'type' => StandardType::ID,
            'exists' => new SqlComparison(WARNING_TYPE_EXISTS_SQL)
        )
    );
    $missingParam = AccessBlock::findMissingParam($requiredParams, $database);
    if ($missingParam) {
        return $database->responseBadRequest($missingParam);
    }
    $warningTypeId = $database->getIntParam('warningTypeId');
    $sql = <<<SQL
        INSERT INTO warning (warningTypeId, subprocessId)
        VALUES (:warningTypeId, :subprocessId)
    SQL;
    $replacements = array(
        'warningTypeId' => ['value' => $warningTypeId, 'type' => PDO::PARAM_INT],
        'subprocessId' => ['value' => $user->getSubprocessId(), 'type' => PDO::PARAM_INT]
    );
    $database->query($sql, $replacements);
    $warningId = $database->getInsertId();
    $sql = SELECT_WARNING_SQL . <<<SQL
        WHERE warning.id = :warningId
    SQL;
    $replacements = array(
        'warningId' => ['value' => $warningId, 'type' => PDO::PARAM_INT],
    );
    $warning = $database->query($sql, $replacements)[0];
    return $database->responseSuccess($warning);
}


$database = new Database();
$database->handleRequest(null, 'postWarning');
