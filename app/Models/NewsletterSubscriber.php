<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $fillable = ['email', 'status', 'unsubscribe_token'];
}
