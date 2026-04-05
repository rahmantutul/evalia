<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoicePrintTranscription extends Model
{
    protected $fillable = ['file_name', 'voice_id', 'transcription', 'raw_response'];

    protected $casts = [
        'raw_response' => 'array',
    ];
}
