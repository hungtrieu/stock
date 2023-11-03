<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactions extends ManageRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $transaction_resource = new TransactionResource;
                    return $transaction_resource->prepareDataBeforeSave($data);
                })
                ->after(function( array $data ) {
                    $transaction_resource = new TransactionResource; 
                    $transaction_resource->updatePortfolio($data);
                }),
        ];
    }
}
