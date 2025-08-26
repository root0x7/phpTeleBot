<?php

namespace App\Console\Commands;

class MakeMigrationCommand extends Command
{
    public function handle(array $args): void
    {
        if (empty($args[0])) {
            $this->error("Migration name is required!");
            $this->info("Usage: php phptelebot make:migration <migration_name>");
            return;
        }
        
        $migrationName = $args[0];
        $timestamp = date('Y_m_d_His');
        $className = $this->formatClassName($migrationName);
        $fileName = "{$timestamp}_{$migrationName}.php";
        $migrationPath = "database/migrations/{$fileName}";
        
        // Create migrations directory if not exists
        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0755, true);
        }
        
        $template = $this->getMigrationTemplate($className, $migrationName);
        file_put_contents($migrationPath, $template);
        
        $this->success("Migration {$migrationName} created successfully!");
        $this->info("Location: {$migrationPath}");
    }
    
    private function formatClassName(string $name): string
    {
        $words = explode('_', $name);
        return implode('', array_map('ucfirst', $words));
    }
    
    private function getMigrationTemplate(string $className, string $migrationName): string
    {
        $isCreateTable = strpos($migrationName, 'create_') === 0 && strpos($migrationName, '_table') !== false;
        
        if ($isCreateTable) {
            // Extract table name from migration name
            $tableName = str_replace(['create_', '_table'], '', $migrationName);
            
            return "<?php

            use App\\Database\\Schema;
            use App\\Database\\Migration;

            class {$className} extends Migration
            {
                public function up(): void
                {
                    Schema::create('{$tableName}', function(\$table) {
                        \$table->id();
            // Add your columns here
                        \$table->timestamps();
                        });
                    }
                    
                    public function down(): void
                    {
                        Schema::dropIfExists('{$tableName}');
                    }
                }
                ";
            } else {
                return "<?php

                use App\\Database\\Schema;
                use App\\Database\\Migration;
                use App\\Database\\Connection;

                class {$className} extends Migration
                {
                    public function up(): void
                    {
        // Add your migration logic here
                    }
                    
                    public function down(): void
                    {
        // Add your rollback logic here
                    }
                }
                ";
            }
        }
    }