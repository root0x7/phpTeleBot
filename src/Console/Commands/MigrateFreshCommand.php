<?php

namespace App\Console\Commands;

use App\Database\Migrator;

class MigrateFreshCommand extends Command
{
    public function handle(array $args): void
    {
        if (!$this->confirm("This will drop all tables and re-run migrations. Are you sure?")) {
            $this->info("Operation cancelled.");
            return;
        }
        
        $migrator = new Migrator();
        
        try {
            $this->info("Dropping all tables...");
            $migrator->dropAllTables();
            $this->success("All tables dropped!");
            
            $this->info("Running migrations...");
            $migrator->createMigrationsTable();
            $migrations = $migrator->getAllMigrations();
            
            foreach ($migrations as $migration) {
                $this->info("Migrating: {$migration}");
                $migrator->runMigration($migration);
                $this->success("Migrated: {$migration}");
            }
            
            $this->success("Fresh migration completed!");
            
        } catch (\Exception $e) {
            $this->error("Fresh migration failed: " . $e->getMessage());
        }
    }
}