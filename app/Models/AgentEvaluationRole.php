<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentEvaluationRole extends Model
{
    protected $fillable = [
        'name',
        'company_id',
        'eval_kb',
        'eval_policies',
        'eval_risks',
        'eval_extractions',
        'eval_professionalism',
        'eval_assessment',
        'eval_cooperation',
        'eval_linguistic',
    ];

    protected $casts = [
        'eval_kb' => 'boolean',
        'eval_policies' => 'boolean',
        'eval_risks' => 'boolean',
        'eval_extractions' => 'boolean',
        'eval_professionalism' => 'boolean',
        'eval_assessment' => 'boolean',
        'eval_cooperation' => 'boolean',
        'eval_linguistic' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'evaluation_role_id');
    }
}
