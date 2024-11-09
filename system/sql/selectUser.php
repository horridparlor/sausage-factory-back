<?php

const USER_COLUMNS_TO_DECODE = [
    'accessRights'
];

const NEW_USERNAME_SQL = <<<SQL
    SELECT :comparedValue
    FROM user
    WHERE username = :comparedValue
SQL;

const UNIQUE_USERNAME_SQL = <<<SQL
    SELECT :comparedValue
    FROM user
    WHERE username = :comparedValue
    AND NOT id = :userId
SQL;
