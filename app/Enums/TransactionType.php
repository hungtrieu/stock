<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
 
enum TransactionType: int implements HasLabel
{
    case Buy = 1;
    case Sell = 0;
    
    public function getLabel(): ?string
    {
        return $this->name;
        
        // or
    
        return match ($this) {
            self::Buy => __('Buy'),
            self::Sell => __('Sell'),
        };
    }
}