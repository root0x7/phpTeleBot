<?php

namespace App\Database;

class Schema
{
    public static function create(string $table, callable $callback): void
    {
        $builder = new SchemaBuilder($table);
        $callback($builder);
        
        $sql = $builder->toSql();
        Connection::query($sql);
    }

    public static function dropIfExists(string $table): void
    {
        $sql = "DROP TABLE IF EXISTS {$table}";
        Connection::query($sql);
    }
}
