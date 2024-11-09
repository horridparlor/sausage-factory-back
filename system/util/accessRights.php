<?php

use system\StandardType;
use system\SqlComparison;


function getUserAccessRightsMissingParams(): array {
    $roleExistsSql = <<<SQL
        SELECT :comparedValue
        FROM DUAL
        WHERE NOT EXISTS (
            SELECT id
            FROM userRole
            WHERE id = :comparedValue
        )
    SQL;
    return array(
        'param' => 'roleId',
        'type' => StandardType::ID,
        'exists' => new SqlComparison($roleExistsSql),
        'option' => getAccessRightsMissingParams()
    );
}

function getAccessRightsMissingParams(): array {
    return array(
        'param' => 'accessRights',
        'children' => array(
            array(
                'param' => 'isSuperAdmin',
                'type' => StandardType::BOOLEAN,
                'forbidden' => true
            )
        )
    );
}
