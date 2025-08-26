<?php

namespace Root0x7\Database;

use Exception;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected array $attributes = [];
    protected array $original = [];
    protected bool $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }


    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function find($id): ?static
    {
        $table = static::getTable();
        $primaryKey = static::$primaryKey;
        
        $stmt = Connection::query(
            "SELECT * FROM {$table} WHERE {$primaryKey} = ? LIMIT 1",
            [$id]
        );
        
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }
        
        $model = new static($data);
        $model->exists = true;
        $model->original = $data;
        
        return $model;
    }

    public static function where(string $column, $operator = null, $value = null): QueryBuilder
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        return (new QueryBuilder(static::class))
            ->where($column, $operator, $value);
    }

    public static function all(): array
    {
        return (new QueryBuilder(static::class))->get();
    }

    public static function first(): ?static
    {
        return (new QueryBuilder(static::class))->first();
    }

    public static function count(): int
    {
        return (new QueryBuilder(static::class))->count();
    }


    public function save(): bool
    {
        if ($this->exists) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }
        
        $table = static::getTable();
        $primaryKey = static::$primaryKey;
        $id = $this->getAttribute($primaryKey);
        
        Connection::query(
            "DELETE FROM {$table} WHERE {$primaryKey} = ?",
            [$id]
        );
        
        $this->exists = false;
        return true;
    }

    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }


    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }


    protected function insert(): bool
    {
        $table = static::getTable();
        $attributes = $this->attributes;
        
        if (!isset($attributes['created_at'])) {
            $attributes['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($attributes['updated_at'])) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = array_keys($attributes);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        Connection::query($sql, array_values($attributes));
        
        $this->attributes[static::$primaryKey] = Connection::lastInsertId();
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->original = $this->attributes;
        $this->exists = true;
        
        return true;
    }

    protected function update(): bool
    {
        $table = static::getTable();
        $primaryKey = static::$primaryKey;
        $attributes = $this->attributes;
        
        $attributes['updated_at'] = date('Y-m-d H:i:s');
        
        $id = $attributes[$primaryKey];
        unset($attributes[$primaryKey]);
        
        $columns = array_keys($attributes);
        $setClause = implode(', ', array_map(fn($col) => "{$col} = ?", $columns));
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$primaryKey} = ?";
        $values = array_merge(array_values($attributes), [$id]);
        
        Connection::query($sql, $values);
        
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->original = $this->attributes;
        
        return true;
    }

    protected static function getTable(): string
    {
        if (empty(static::$table)) {
            $className = (new \ReflectionClass(static::class))->getShortName();
            static::$table = strtolower(preg_replace('/([A-Z])/', '_$1', $className)) . 's';
            static::$table = ltrim(static::$table, '_');
        }
        
        return static::$table;
    }
}