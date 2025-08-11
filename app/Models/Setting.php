<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    
    protected $fillable = [
        'company_name',
        'company_tagline',
        'company_logo',
        'favicon',
        'pricing_title',
        'pricing_subtitle',
        'offer_text',
        'dashboard_text',
        'dashboard_subtext',
        'currency',
        'currency_symbol',
        'timezone'
    ];
}