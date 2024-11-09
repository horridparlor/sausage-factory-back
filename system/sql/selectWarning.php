<?php

const SELECT_WARNING = <<<SQL
    SELECT
        warning.id,
        warningType.id warningTypeId,
        warningType.name warningTypeName,
        subprocess.id subprocessId,
        subprocess.name subprocessName,
        warning.createdAt
    FROM warning
    JOIN warningType
        ON warningType.id = warning.warningTypeId
    JOIN subprocess
        ON subprocess.id = warning.subprocessId
SQL;
