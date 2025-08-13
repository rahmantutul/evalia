<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded =['id'];

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
    ];

}
