<?php

namespace App\Console\Commands;

use App\Database\Migrator;

class MigrateCommand extends Command
{
    public function handle(array $args): void
    {
        $this->info("Running migrations...");
        
        $migrator = new Migrator();
        
        try {
            $migrator->createMigrationsTable();
            $migrations = $migrator->getPendingMigrations();
            
            if (empty($migrations)) {
                $this->info("No pending migrations.");
                return;
            }
            
            foreach ($migrations as $migration) {
                $this->info("Migrating: {$migration}");
                $migrator->runMigration($migration);
                $this->success("Migrated: {$migration}");
            }
            
            $this->success("All migrations completed!");
            
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
        }
    }
}