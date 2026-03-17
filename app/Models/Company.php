<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'filler_words' => 'array',
        'main_topics' => 'array',
        'call_types' => 'array',
        'call_outcomes' => 'array',
        'company_policies' => 'array',
        'agent_assessments_configs' => 'array',
        'agent_cooperation_configs' => 'array',
        'agent_performance_configs' => 'array',
        'llm_total_usage' => 'array',
        'restricted_phrases' => 'array',
        'source' => 'array',
        'faq' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function agents()
    {
        return $this->hasMany(User::class)->where('user_type', User::TYPE_AGENT);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
