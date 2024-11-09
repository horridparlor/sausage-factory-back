<?php

use system\Database;

header('Content-Type: application/json');

include("../../system/Database.php");
include("../../system/entity/User.php");

function authenticate(Database $database): string
{
    $username = $database->getStringParam('username');
    $password = $database->getStringParam('password');
    $sql = <<<SQL
        SELECT user.id, passwordHash,
        CASE
            WHEN role.id IS NOT NULL THEN role.accessRights
            ELSE IFNULL(user.accessRights, "{}")
        END AS accessRights
        FROM user
        LEFT JOIN userRole role
            ON role.id = user.roleId
        WHERE username = :username
    SQL;
    $replacements = array(
        'username' => ['value' => $username, 'type' => PDO::PARAM_STR],
    );
    $user = $database->query($sql, $replacements);
    if (!sizeof($user)) {
        return $database->responseUnauthorized(array(
            'error' => "Incorrect username or password",
        ));
    }
    $realHash = $user[0]['passwordHash'];
    if (!$realHash || !password_verify($password, $realHash)) {
        return $database->responseUnauthorized(array(
            'error' => "Incorrect username or password",
        ));
    }

    $token = bin2hex(random_bytes(16));
    $expiration = time() + 24 * 3600;
    $expirationDate = date('Y-m-d H:i:s', $expiration);
    $user = $database->findUser($user[0]['id']);
    if (!$user) {
        return $database->responseNotFound(array(
            'error' => 'User not found',
        ));
    }
    $sql = <<<SQL
        DELETE FROM authToken
        WHERE userId = :userId;
        INSERT INTO authToken ( 
            userId,
            token,
            expiration
        ) VALUES (
            :userId,
            :token,
            :expiration
        )
    SQL;
    $replacements = array(
        'userId' => ['value' => $user->getId(), 'type' => PDO::PARAM_STR],
        'token' => ['value' => $token, 'type' => PDO::PARAM_STR],
        'expiration' => ['value' => $expirationDate, 'type' => PDO::PARAM_STR],
    );
    $database->query($sql, $replacements);

    return $database->responseSuccess(array(
        'authToken' => $token,
        'userId' => $user->getId(),
        'username' => $username,
        'firstname' => $user->getFirstname(),
        'lastname' => $user->getLastname(),
        'accessRights' => json_encode($user->getAccessRights(), true),
        'subprocessId' => $user->getSubprocessId(),
    ));
}

$database = new Database();
$database->handleRequest(null, 'authenticate');

