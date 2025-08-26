<?php

namespace Root0x7\Console\Commands;

abstract class Command
{
    abstract public function handle(array $args): void;
    
    protected function info(string $message): void
    {
        echo "ℹ️  {$message}\n";
    }
    
    protected function success(string $message): void
    {
        echo "✅ {$message}\n";
    }
    
    protected function error(string $message): void
    {
        echo "❌ {$message}\n";
    }
    
    protected function warn(string $message): void
    {
        echo "⚠️  {$message}\n";
    }
    
    protected function ask(string $question): string
    {
        echo $question . ": ";
        return trim(fgets(STDIN));
    }
    
    protected function confirm(string $question): bool
    {
        $answer = $this->ask($question . " (y/N)");
        return strtolower($answer) === 'y' || strtolower($answer) === 'yes';
    }
}
