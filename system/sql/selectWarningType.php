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
