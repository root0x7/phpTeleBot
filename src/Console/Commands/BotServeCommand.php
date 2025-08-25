<?php

namespace App\Console\Commands;

use PhpTeleBot\TelegramBot;
use PhpTeleBot\UpdateHandler;

class BotServeCommand extends Command
{
    public function handle(array $args): void
    {
        if (!file_exists('bot.php')) {
            $this->error("bot.php file not found!");
            $this->info("Create a bot.php file with your bot logic.");
            return;
        }
        
        $this->info("Starting bot in long polling mode...");
        $this->info("Press Ctrl+C to stop.");
        
        include 'bot.php';
    }
}