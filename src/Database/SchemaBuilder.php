<?php 
namespace App\Database;


class SchemaBuilder
{
    private string $table;
    private array $columns = [];
    private array $indexes = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[] = "{$name} INTEGER PRIMARY KEY AUTOINCREMENT";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "{$name} VARCHAR({$length})";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "{$name} INTEGER";
        return $this;
    }

    public function bigInteger(string $name): self
    {
        $this->columns[] = "{$name} BIGINT";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "{$name} BOOLEAN DEFAULT 0";
        return $this;
    }

    public function timestamp(string $name): self
    {
        $this->columns[] = "{$name} TIMESTAMP";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    public function json(string $name): self
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function index(string $column): self
    {
        $this->indexes[] = "CREATE INDEX idx_{$this->table}_{$column} ON {$this->table} ({$column})";
        return $this;
    }

    public function unique(string $column): self
    {
        $this->indexes[] = "CREATE UNIQUE INDEX unique_{$this->table}_{$column} ON {$this->table} ({$column})";
        return $this;
    }

    public function toSql(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (\n";
        $sql .= "    " . implode(",\n    ", $this->columns);
        $sql .= "\n)";

        foreach ($this->indexes as $index) {
            $sql .= ";\n" . $index;
        }

        return $sql;
    }
}