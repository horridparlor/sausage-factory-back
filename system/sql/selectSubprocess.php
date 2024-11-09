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

const SELECT_SUBPROCESS_SQL = <<<SQL
    SELECT id, name, code
    FROM subprocess
SQL;
