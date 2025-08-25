<?php

namespace App\Console\Commands;

use App\Database\Migrator;

class MigrateStatusCommand extends Command
{
    public function handle(array $args): void
    {
        $migrator = new Migrator();
        
        try {
            $migrator->createMigrationsTable();
            $allMigrations = $migrator->getAllMigrations();
            $runMigrations = $migrator->getRunMigrations();
            
            $this->info("Migration Status:\n");
            
            foreach ($allMigrations as $migration) {
                $status = in_array($migration, $runMigrations) ? '✅ Migrated' : '⏳ Pending';
                echo "  {$status}  {$migration}\n";
            }
            
        } catch (\Exception $e) {
            $this->error("Failed to get migration status: " . $e->getMessage());
        }
    }
}