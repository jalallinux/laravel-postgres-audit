<?php

return [

    /**
     * Config AuditLog then run `pg-audit:setup` command.
     * Command drop AuditLog table and overwrite logging() trigger function.
     */


    /**
     * Target database connection
     */
    'connection' => 'pgsql',


    /**
     * Audit log table name
     */
    'table_name' => 'audit_logs',


    /**
     * Target tables for log
     * Set ['*'] to log for all tables
     * Split tables with comma
     */
    'target_tables' => '*',


    /**
     * Except tables for log
     * Except tables has more priority than target tables
     * Split tables with comma
     */
    'except_tables' => 'migrations, password_resets, personal_access_tokens, jobs, failed_jobs, notifications',


    /**
     * Ignore database user activity
     * Example: postgres, forge
     * NULL for logging all users
     * Split tables with comma
     */
    'except_users' => null,


    /**
     * Primary columns
     * Example: id, uuid, slug
     */
    'primary_columns' => 'uuid',


    /**
     * Primary columns type
     * Example: bigint, uuid, varchar
     */
    'primary_columns_type' => 'uuid',


    /**
     * Operations to log
     * Only support INSERT, DELETE, UPDATE
     */
    'operations' => 'INSERT, DELETE, UPDATE'
];
