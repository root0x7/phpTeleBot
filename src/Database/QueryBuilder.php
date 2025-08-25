<?php

namespace App\Database;

class QueryBuilder
{
    private string $modelClass;
    private array $wheres = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $orderBy = [];

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $column, $operator = null, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $this->wheres[] = [$column, $operator, $value];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [$column, $direction];
        return $this;
    }

    public function get(): array
    {
        [$sql, $bindings] = $this->toSql();
        $stmt = Connection::query($sql, $bindings);
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $model = new $this->modelClass($row);
            $model->exists = true;
            $model->original = $row;
            $results[] = $model;
        }
        
        return $results;
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $table = $this->modelClass::getTable();
        [$whereClause, $bindings] = $this->buildWhereClause();
        
        $sql = "SELECT COUNT(*) as count FROM {$table}{$whereClause}";
        $stmt = Connection::query($sql, $bindings);
        
        return (int) $stmt->fetch()['count'];
    }

    private function toSql(): array
    {
        $table = $this->modelClass::getTable();
        [$whereClause, $bindings] = $this->buildWhereClause();
        
        $sql = "SELECT * FROM {$table}{$whereClause}";
        
        if (!empty($this->orderBy)) {
            $orderClauses = array_map(
                fn($order) => "{$order[0]} {$order[1]}", 
                $this->orderBy
            );
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return [$sql, $bindings];
    }

    private function buildWhereClause(): array
    {
        if (empty($this->wheres)) {
            return ['', []];
        }
        
        $conditions = [];
        $bindings = [];
        
        foreach ($this->wheres as $where) {
            [$column, $operator, $value] = $where;
            $conditions[] = "{$column} {$operator} ?";
            $bindings[] = $value;
        }
        
        $whereClause = ' WHERE ' . implode(' AND ', $conditions);
        
        return [$whereClause, $bindings];
    }
}
