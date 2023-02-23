<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportProblem extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'payload',
        'type',
        'resolved_at',
    ];

    const BUG = 'bug';
    const SUGGESTION = 'suggestion';
    const INAPPROPRIATE = 'inappropriate';
    const SEXUAL_CONTENT = 'content_sexual';
    const VIOLENCE_CONTENT = 'content_violence';
    const HATEFUL_CONTENT = 'content_hateful';
    const HARMFUL_CONTENT = 'content_harmful';
    const SPAM_CONTENT = 'content_spam';
    const COPYRIGHT_CONTENT = 'content_copyright';
    const CHILD_CONTENT = 'content_child';
    const OTHER_CONTENT = 'content_other';


    const TYPES = [
        self::BUG,
        self::SUGGESTION,
        self::INAPPROPRIATE,
        self::SEXUAL_CONTENT,
        self::VIOLENCE_CONTENT,
        self::HATEFUL_CONTENT,
        self::HARMFUL_CONTENT,
        self::SPAM_CONTENT,
        self::COPYRIGHT_CONTENT,
        self::CHILD_CONTENT,
        self::OTHER_CONTENT,
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class, 'post_id', 'id');
    }

}
