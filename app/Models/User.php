<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
   use HasFactory;

    // Remove all authentication-related properties and methods
    // We're using this model only to store API user data temporarily
    
    protected $fillable = [
        'id', 'username', 'email', 'full_name', 'position', 
        'phone', 'is_active', 'role', 'company_id', 'supervisor_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'role' => 'array'
    ];

    // Implement required Authenticatable methods (empty since we use API)
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return ''; // No local password
    }

    public function getRememberToken()
    {
        return '';
    }

    public function setRememberToken($value) {}

    public function getRememberTokenName()
    {
        return '';
    }
}
