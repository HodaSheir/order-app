<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'logs';

    protected $fillable = [
        'order_id',
        'url',
        'response',
        'payment_gateway',
    ];
}
