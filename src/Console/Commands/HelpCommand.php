<?php

namespace App\Console\Commands;

class HelpCommand extends Command
{
    public function handle(array $args): void
    {
        echo "\n🤖 PhpTeleBot CLI Tool v2.0\n\n";
        echo "📋 Available commands:\n\n";
        
        $commands = [
            'Model & Migration:' => [
                'make:model <name>' => 'Create a new model class',
                'make:migration <name>' => 'Create a new migration file',
            ],
            'Database:' => [
                'migrate' => 'Run pending migrations',
                'migrate:rollback [steps]' => 'Rollback migrations',
                'migrate:status' => 'Show migration status',
                'migrate:fresh' => 'Drop all tables and re-run migrations',
            ],
            'Bot:' => [
                'bot:serve' => 'Start bot in long polling mode',
            ],
            'Utility:' => [
                'help' => 'Show this help message',
            ]
        ];
        
        foreach ($commands as $category => $cmds) {
            echo "  📂 {$category}\n";
            foreach ($cmds as $cmd => $desc) {
                echo "    {$cmd} - {$desc}\n";
            }
            echo "\n";
        }
        
        echo "📖 Examples:\n";
        echo "  php phptelebot make:model Product\n";
        echo "  php phptelebot make:migration create_products_table\n";
        echo "  php phptelebot migrate\n";
        echo "  php phptelebot bot:serve\n\n";
    }
}
