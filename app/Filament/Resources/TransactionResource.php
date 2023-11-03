<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Enums\TransactionType;
use App\Models\Portfolio;
use Filament\Forms\Set;
use Filament\Forms\Get;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('stock_id')
                    ->relationship('stock', 'symbol')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(TransactionType::class)
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                    // ->afterStateUpdated(function (Get $get, Set $set) {
                    //     $set('amount', $get('price') * $get('quantity'));
                    // }),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->suffix(config('app.currency_unit')),
                    // ->afterStateUpdated(function (Get $get, Set $set) {
                    //     $set('amount', $get('price') * $get('quantity'));
                    // }),
                Forms\Components\Hidden::make('amount')
                    // ->suffix(config('app.currency_unit'))
                    // ->readOnly()
                    // ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('user.name')
                //     ->sortable(),
                Tables\Columns\TextColumn::make('stock.symbol')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->prepareDataBeforeSave($data);
                    })
                    ->after(function( array $data ) { 
                        $this->updatePortfolio($data);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make()
                //     ->mutateFormDataUsing(function (array $data): array {
                //         return $this->prepareDataBeforeSave($data);
                //     })
                //     ->after(function( array $data ) { 
                //         $this->updatePortfolio($data);
                //     }),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }

    public function prepareDataBeforeSave( array $data ) : array {

        if (!array_key_exists('user_id', $data)) $data['user_id'] = auth()->user()->id;

        $data['amount'] = $data['quantity'] * $data['price'];

        return $data;
    }

    public function updatePortfolio( array $transaction) : void {

        $portfolio = Portfolio::where('user_id', $transaction['user_id'])
                        ->where('stock_id', $transaction['stock_id'])
                        ->first();

        if($portfolio) {
            if($transaction['type'] == TransactionType::Buy->value ) { 
                $portfolio->quantity += $transaction['quantity'];
                $portfolio->amount += $transaction['amount'];
            } else { 
                $portfolio->quantity -= $transaction['quantity'];
                $portfolio->amount -= $transaction['amount'];
                
            }

            $portfolio->cost = $portfolio->amount / $portfolio->quantity;
            $portfolio->save();
            return;
        }

        $portfolio = new Portfolio();
        $portfolio->user_id = $transaction['user_id'];
        $portfolio->stock_id = $transaction['stock_id'];
        $portfolio->quantity = $transaction['quantity'];
        $portfolio->amount = $transaction['amount'];
        $portfolio->cost = $transaction['price'];
        $portfolio->save();
    }
}
