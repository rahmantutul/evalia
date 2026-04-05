<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoicePrint extends Model
{
    protected $fillable = ['internal_id', 'name', 'embedding'];

    protected $casts = [
        'embedding' => 'array',
    ];
}
