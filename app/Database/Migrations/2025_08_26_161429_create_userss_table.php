<?php

use App\Database\Schema;
use App\Database\Migration;

class CreateUserssTable extends Migration
{
    public function up(): void
    {
        Schema::create('userss', function($table) {
            $table->id();
            $table->string("username");
            $table->integer('chat_id');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('userss');
    }
}
