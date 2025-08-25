<?php

namespace App\Console\Commands;

class MakeModelCommand extends Command
{
    public function handle(array $args): void
    {
        if (empty($args[0])) {
            $this->error("Model name is required!");
            $this->info("Usage: php phptelebot make:model <ModelName>");
            return;
        }
        
        $modelName = ucfirst($args[0]);
        $tableName = strtolower(preg_replace('/([A-Z])/', '_$1', $modelName));
        $tableName = ltrim($tableName, '_') . 's';
        
        $modelPath = "src/Models/{$modelName}.php";
        
        if (file_exists($modelPath)) {
            $this->error("Model {$modelName} already exists!");
            return;
        }
        
        $template = $this->getModelTemplate($modelName, $tableName);
        
        // Create Models directory if not exists
        if (!is_dir('src/Models')) {
            mkdir('src/Models', 0755, true);
        }
        
        file_put_contents($modelPath, $template);
        
        $this->success("Model {$modelName} created successfully!");
        $this->info("Location: {$modelPath}");
        
        // Ask if user wants to create migration too
        if ($this->confirm("Create migration for {$modelName}?")) {
            $migrationName = "create_{$tableName}_table";
            $migrationCommand = new MakeMigrationCommand();
            $migrationCommand->handle([$migrationName]);
        }
    }
    
    private function getModelTemplate(string $modelName, string $tableName): string
    {
        return "<?php

        namespace PhpTeleBot\\Models;

        use PhpTeleBot\\Database\\Model;

        class {$modelName} extends Model
        {
            protected static string \$table = '{$tableName}';
            
    // Fillable fields
            protected array \$fillable = [
        // Add your fillable fields here
            ];
            
    // Custom methods
            public function example(): string
            {
                return 'This is an example method for {$modelName}';
            }
        }
        ";
    }
}