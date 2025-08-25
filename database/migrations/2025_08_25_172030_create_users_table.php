<?php
use PhpTeleBot\Database\Schema;
use PhpTeleBot\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function($table) {
            $table->id();
            // Add your columns here
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
