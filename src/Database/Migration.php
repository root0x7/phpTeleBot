<?php

namespace Root0x7\Database;

abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;
    
    protected function info(string $message): void
    {
        echo "  ℹ️  {$message}\n";
    }
}