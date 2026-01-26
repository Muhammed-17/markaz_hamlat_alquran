<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPrice extends Model
{
    protected $fillable = ['circle_level', 'education_level', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}