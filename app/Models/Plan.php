<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = ['id'];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'plan_feature')
                    ->withPivot('value')
                    ->withTimestamps();
    }
}