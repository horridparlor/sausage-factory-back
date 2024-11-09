<?php

const WARNING_TYPE_EXISTS_SQL = <<<SQL
    SELECT :comparedValue, "Warning type" entityType
    FROM DUAL
    WHERE NOT EXISTS (
        SELECT id
        FROM warningType
        WHERE id = :comparedValue
    )
SQL;

const SELECT_WARNING_TYPE_SQL = <<<SQL
    SELECT id, name
    FROM warningType
SQL;

