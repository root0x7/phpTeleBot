<?php

namespace Root0x7\Console;

use Exception;

class Application
{
    private array $commands = [];
    
    public function __construct()
    {
        $this->registerCommands();
    }
    
    public function run(): void
    {
        global $argv;
        
        if (!isset($argv[1])) {
            $this->showHelp();
            return;
        }
        
        $commandName = $argv[1];
        
        if (!isset($this->commands[$commandName])) {
            echo "❌ Command '{$commandName}' not found.\n";
            $this->showHelp();
            return;
        }
        
        $commandClass = $this->commands[$commandName];
        $command = new $commandClass();
        
        try {
            $command->handle(array_slice($argv, 2));
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
    
    private function registerCommands(): void
    {
        $this->commands = [
            'make:model' => Commands\MakeModelCommand::class,
            'make:migration' => Commands\MakeMigrationCommand::class,
            'migrate' => Commands\MigrateCommand::class,
            'migrate:rollback' => Commands\MigrateRollbackCommand::class,
            'migrate:status' => Commands\MigrateStatusCommand::class,
            'migrate:fresh' => Commands\MigrateFreshCommand::class,
            'bot:serve' => Commands\BotServeCommand::class,
            'help' => Commands\HelpCommand::class,
        ];
    }
    
    private function showHelp(): void
    {
        echo "\n🤖 PhpTeleBot CLI Tool\n\n";
        echo "Available commands:\n";
        echo "  make:model <name>          Create a new model\n";
        echo "  make:migration <name>      Create a new migration\n";
        echo "  migrate                    Run pending migrations\n";
        echo "  migrate:rollback           Rollback last migration batch\n";
        echo "  migrate:status             Show migration status\n";
        echo "  migrate:fresh              Drop all tables and re-run migrations\n";
        echo "  bot:serve                  Start bot in long polling mode\n";
        echo "  help                       Show this help message\n\n";
    }
}