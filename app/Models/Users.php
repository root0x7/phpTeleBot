<?php

namespace App\Models;

use App\Database\Model;

class Users extends Model
{
    protected static string $table = 'userss';
    
    // Fillable fields
    protected array $fillable = [
        // Add your fillable fields here
    ];
    
    // Custom methods
    public function example(): string
    {
        return 'This is an example method for Users';
    }
}
