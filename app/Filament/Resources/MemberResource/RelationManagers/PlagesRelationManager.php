<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use App\Enums\PaymentPeriod;
use App\Enums\Status;
use App\Enums\Gender;
use App\Filament\Resources\MemberResource;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PlagesRelationManager extends RelationManager
{
    protected static string $relationship = 'plages';

    


    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('payment_period')
                ->options([
                    PaymentPeriod::MONTHLY => PaymentPeriod::MONTHLY,
                    PaymentPeriod::YEARLY  => PaymentPeriod::YEARLY
                ])
                ->reactive(),
                Forms\Components\TextInput::make('amount')
                ->label('Amount (ETB)')
                ->integer()
                ->required(),
                Flatpickr::make("date")
                ->range()
                // Forms\Components\DatePicker::make('start_date')
                // ->label('Start Date')
                // ->native(false)
                // ->reactive()
                // ->minDate(Carbon::today()),
                // Forms\Components\DatePicker::make('end_date')
                // ->label('End Date')
                // ->native(false)
                // ->minDate(fn(Get $get) => Carbon::parse($get('start_date')))
                // ->maxDate(fn(Get $get) => $get('payment_period') == "Mo" ? Carbon::parse($get('start_date'))->addYear() : Carbon::parse($get('start_date'))->addYears(5))
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                ->label('Amount (ETB)')
                ->getStateUsing(function (Model $record): string {
                    return $record->amount . " ETB / ". $record->payment_period;
                }),
                Tables\Columns\TextColumn::make('start_date')
                ->date(),
                Tables\Columns\TextColumn::make('end_date')
                ->date(),
                Tables\Columns\IconColumn::make('status')
                ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Payment')
                ->url(MemberResource::getUrl('payment',['record' => $this->ownerRecord->id]))

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
}
