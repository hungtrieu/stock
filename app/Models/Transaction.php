<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'stock_id', 'type', 'quantity', 'price', 'amount'];

    protected $casts = [
        'type' =>TransactionType::class,
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function stock() : BelongsTo {
        return $this->belongsTo(Stock::class);
    }
}
