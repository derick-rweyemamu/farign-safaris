<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'body', 'status', 'recipients_count', 'sent_at'];

    protected $casts = [
        'sent_at' => 'datetime'
    ];
}
