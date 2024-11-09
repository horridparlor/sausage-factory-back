<?php

use system\Database;
use system\AccessBlock;
use system\StandardType;
use system\SqlComparison;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/AccessBlock.php");
include("../../system/sql/selectUser.php");
include("../../system/sql/selectSubprocess.php");
include("../../system/util/accessRights.php");


function postUser(Database $database): string
{
    $requiredParams = array(
        array(
            'param' => 'username',
            'type' => StandardType::STRING,
            'unique' => new SqlComparison(NEW_USERNAME_SQL)
        ),
        array(
            'param' => 'password',
            'type' => StandardType::STRING
        ),
        array(
            'param' => 'firstname',
            'type' => StandardType::STRING
        ),
        array(
            'param' => 'lastname',
            'type' => StandardType::STRING
        ),
        getUserAccessRightsMissingParams(),
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

    $username = $database->getStringParam('username');
    $password = $database->getStringParam('password');
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $firstname = $database->getStringParam('firstname');
    $lastname = $database->getStringParam('lastname');
    $roleId = $database->getIntParam('roleId');
    $accessRights = $database->getObjectParam('accessRights');
    $subprocessId = $database->getIntParam('subprocessId');

    $sql = <<<SQL
        INSERT INTO user (
            username,
            passwordHash,
            firstname,
            lastname, 
            roleId,
            accessRights,
            subprocessId
        ) VALUES (
            :username,
            :passwordHash, 
            :firstname,
            :lastname, 
            :roleId,
            :accessRights,
            :subprocessId
        )
    SQL;
    $replacements = array(
        'username' => ['value' => $username, 'type' => PDO::PARAM_STR],
        'passwordHash' => ['value' => $passwordHash, 'type' => PDO::PARAM_STR],
        'firstname' => ['value' => $firstname, 'type' => PDO::PARAM_STR],
        'lastname' => ['value' => $lastname, 'type' => PDO::PARAM_STR],
        'roleId' => ['value' => $roleId, 'type' => PDO::PARAM_INT],
        'accessRights' => ['value' => json_encode($accessRights), 'type' => PDO::PARAM_STR],
        'subprocessId' => ['value' => $subprocessId, 'type' => PDO::PARAM_INT],
    );
    $database->query($sql, $replacements);
    $result = $database->query('SELECT LAST_INSERT_ID() userId;');

    return $database->responseSuccess(array(
        'userId' => $result[0]['userId']
    ));
}

$database = new Database();
$database->handleRequest(null, 'postUser');
