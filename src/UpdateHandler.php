<?php

namespace Root0x7;

class UpdateHandler
{
    private Api $bot;
    private array $handlers = [];
    
    public function __construct(Api $bot)
    {
        $this->bot = $bot;
    }
    
    public function onMessage(callable $handler): self
    {
        $this->handlers['message'][] = $handler;
        return $this;
    }
    
    public function onCommand(string $command, callable $handler): self
    {
        $this->handlers['command'][$command] = $handler;
        return $this;
    }
    
    public function onCallbackQuery(callable $handler): self
    {
        $this->handlers['callback_query'][] = $handler;
        return $this;
    }
    
    public function onInlineQuery(callable $handler): self
    {
        $this->handlers['inline_query'][] = $handler;
        return $this;
    }
    
    public function handle(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }
        
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
        
        if (isset($update['inline_query'])) {
            $this->handleInlineQuery($update['inline_query']);
        }
    }
    
    private function handleMessage(array $message): void
    {
        // Command handler
        if (isset($message['text']) && strpos($message['text'], '/') === 0) {
            $command = explode(' ', $message['text'])[0];
            if (isset($this->handlers['command'][$command])) {
                call_user_func($this->handlers['command'][$command], $message, $this->bot);
                return;
            }
        }
        
        // General message handlers
        if (isset($this->handlers['message'])) {
            foreach ($this->handlers['message'] as $handler) {
                call_user_func($handler, $message, $this->bot);
            }
        }
    }
    
    private function handleCallbackQuery(array $callbackQuery): void
    {
        if (isset($this->handlers['callback_query'])) {
            foreach ($this->handlers['callback_query'] as $handler) {
                call_user_func($handler, $callbackQuery, $this->bot);
            }
        }
    }
    
    private function handleInlineQuery(array $inlineQuery): void
    {
        if (isset($this->handlers['inline_query'])) {
            foreach ($this->handlers['inline_query'] as $handler) {
                call_user_func($handler, $inlineQuery, $this->bot);
            }
        }
    }
}
