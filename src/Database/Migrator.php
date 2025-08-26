<?php

namespace Root0x7\Database;

use Exception;

class Migrator
{
    private string $migrationsPath = 'app/Database/Migrations';
    private string $migrationsTable = 'migrations';
    
    public function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        Connection::query($sql);
    }
    
    public function getAllMigrations(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }
        
        $files = scandir($this->migrationsPath);
        $migrations = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $migrations[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        
        sort($migrations);
        return $migrations;
    }
    
    public function getRunMigrations(): array
    {
        try {
            $stmt = Connection::query("SELECT migration FROM {$this->migrationsTable} ORDER BY id");
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getPendingMigrations(): array
    {
        $allMigrations = $this->getAllMigrations();
        $runMigrations = $this->getRunMigrations();
        
        return array_diff($allMigrations, $runMigrations);
    }
    
    public function runMigration(string $migration): void
    {
        $migrationFile = "{$this->migrationsPath}/{$migration}.php";
        
        if (!file_exists($migrationFile)) {
            throw new Exception("Migration file not found: {$migrationFile}");
        }
        
        require_once $migrationFile;
        
        $className = $this->getMigrationClassName($migration);
        
        if (!class_exists($className)) {
            throw new Exception("Migration class {$className} not found");
        }
        
        $migrationInstance = new $className();
        
        Connection::beginTransaction();
        
        try {
            $migrationInstance->up();
            
            // Record migration
            $batch = $this->getNextBatchNumber();
            Connection::query(
                "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)",
                [$migration, $batch]
            );
            
            Connection::commit();
            
        } catch (Exception $e) {
            Connection::rollback();
            throw $e;
        }
    }
    
    public function rollback(int $steps = 1): array
    {
        $rolledBack = [];
        
        for ($i = 0; $i < $steps; $i++) {
            $batch = $this->getLastBatch();
            
            if ($batch === null) {
                break;
            }
            
            $migrations = $this->getMigrationsByBatch($batch);
            
            foreach (array_reverse($migrations) as $migration) {
                $this->rollbackMigration($migration);
                $rolledBack[] = $migration;
            }
        }
        
        return $rolledBack;
    }
    
    private function rollbackMigration(string $migration): void
    {
        $migrationFile = "{$this->migrationsPath}/{$migration}.php";
        
        if (!file_exists($migrationFile)) {
            throw new Exception("Migration file not found: {$migrationFile}");
        }
        
        require_once $migrationFile;
        
        $className = $this->getMigrationClassName($migration);
        $migrationInstance = new $className();
        
        Connection::beginTransaction();
        
        try {
            $migrationInstance->down();
            
            // Remove migration record
            Connection::query(
                "DELETE FROM {$this->migrationsTable} WHERE migration = ?",
                [$migration]
            );
            
            Connection::commit();
            
        } catch (Exception $e) {
            Connection::rollback();
            throw $e;
        }
    }
    
    public function dropAllTables(): void
    {
        $tables = $this->getAllTables();
        
        foreach ($tables as $table) {
            Connection::query("DROP TABLE IF EXISTS {$table}");
        }
    }
    
    private function getAllTables(): array
    {
        $stmt = Connection::query("SELECT name FROM sqlite_master WHERE type='table'");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    private function getMigrationClassName(string $migration): string
    {
        // Remove timestamp prefix
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $migration);
        
        // Convert to PascalCase
        $words = explode('_', $name);
        return implode('', array_map('ucfirst', $words));
    }
    
    private function getNextBatchNumber(): int
    {
        $stmt = Connection::query("SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}");
        $result = $stmt->fetch();
        
        return ($result['max_batch'] ?? 0) + 1;
    }
    
    private function getLastBatch(): ?int
    {
        $stmt = Connection::query("SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}");
        $result = $stmt->fetch();
        
        return $result['max_batch'] ?? null;
    }
    
    private function getMigrationsByBatch(int $batch): array
    {
        $stmt = Connection::query(
            "SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id",
            [$batch]
        );
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}