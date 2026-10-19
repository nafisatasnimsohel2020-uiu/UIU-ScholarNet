<?php

if (!function_exists('ensure_document_lock_schema')) {
    function ensure_document_lock_schema(): void
    {
        static $ensured = false;

        if ($ensured) {
            return;
        }

        global $conn;

        if (!$conn) {
            return;
        }

        $columns = [
            'locked_by' => "ALTER TABLE documents ADD COLUMN locked_by INT NULL DEFAULT NULL AFTER last_edited_by",
            'locked_at' => "ALTER TABLE documents ADD COLUMN locked_at TIMESTAMP NULL DEFAULT NULL AFTER locked_by",
        ];

        foreach ($columns as $column => $alterSql) {
            $safeColumn = $conn->real_escape_string($column);
            $check = $conn->query("SHOW COLUMNS FROM documents LIKE '{$safeColumn}'");

            if ($check && $check->num_rows === 0) {
                @$conn->query($alterSql);
            }
        }

        $ensured = true;
    }
}
