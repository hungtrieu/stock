<?php

namespace App\Filament\Resources\PortfolioResource\Pages;

use App\Filament\Resources\PortfolioResource;
use App\Models\Portfolio;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePortfolios extends ManageRecords
{
    protected static string $resource = PortfolioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('calculate profit and loss')
                ->action(function (): void {
                    $portfolio = Portfolio::where('user_id', auth()->user()->id)->get();
                    if($portfolio) {
                        foreach($portfolio as $item) {
                            $item->pnl = $item->quantity * ($item->stock->price - $item->cost);
                            $item->save();
                        }
                    }
                }),
        ];
    }
}
