<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SubscriptionPrice extends Model
{
    use HasRoles;
    protected $fillable = ['circle_level', 'education_level', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}