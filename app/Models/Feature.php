<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
     protected $fillable = ['name', 'type', 'description','key','sort_order'];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_feature')
                    ->withPivot('value')
                    ->withTimestamps();
    }
}