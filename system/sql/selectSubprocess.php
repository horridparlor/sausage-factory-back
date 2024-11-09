<?php

const SUBPROCESS_EXISTS_SQL = <<<SQL
    SELECT :comparedValue, "Subprocess" entityType
    FROM DUAL
    WHERE NOT EXISTS (
        SELECT id
        FROM subprocess
        WHERE id = :comparedValue
    )
SQL;
