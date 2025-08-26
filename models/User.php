<?php

namespace PhpTeleBot\Models;

use PhpTeleBot\Database\Model;

class User extends Model
{
    protected static string $table = 'users';
    
    // Fillable fields
    protected array $fillable = [
        // Add your fillable fields here
    ];
    
    // Custom methods
    public function example(): string
    {
        return 'This is an example method for User';
    }
}
