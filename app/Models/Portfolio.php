<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'stock_id', 'quantity', 'cost', 'amount', 'pnl'];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function stock() : BelongsTo {
        return $this->belongsTo(Stock::class);
    }
}
