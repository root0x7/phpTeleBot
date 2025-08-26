<?php

namespace Root0x7\Console\Commands;

use Root0x7\TelegramBot;
use Root0x7\UpdateHandler;

class BotServeCommand extends Command
{
    public function handle(array $args): void
    {
        if (!file_exists('index.php')) {
            $this->error("index.php file not found!");
            $this->info("Create a index.php file with your bot logic.");
            return;
        }
        
        $this->info("Starting bot in long polling mode...");
        $this->info("Press Ctrl+C to stop.");
        
        include 'index.php';
    }
}