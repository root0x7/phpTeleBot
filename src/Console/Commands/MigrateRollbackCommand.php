<?php

namespace Root0x7\Console\Commands;

use Root0x7\Database\Migrator;

class MigrateRollbackCommand extends Command
{
    public function handle(array $args): void
    {
        $steps = isset($args[0]) ? (int)$args[0] : 1;
        
        $this->info("Rolling back {$steps} migration batch(es)...");
        
        $migrator = new Migrator();
        
        try {
            $rolledBack = $migrator->rollback($steps);
            
            if (empty($rolledBack)) {
                $this->info("Nothing to rollback.");
                return;
            }
            
            foreach ($rolledBack as $migration) {
                $this->success("Rolled back: {$migration}");
            }
            
        } catch (\Exception $e) {
            $this->error("Rollback failed: " . $e->getMessage());
        }
    }
}
