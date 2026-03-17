<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'company_id',
        'title',
        'file_name',
        'file_path',
        'file_type',
        'content',
        'description',
        'keywords',
        'is_active',
    ];

    /**
     * Get the company that owns the knowledge base.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
