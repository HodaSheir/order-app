<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_gateway',
        'payment_data',
        'payment_status',
        'transaction_id',
        'amount',
        'currency'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship to User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
