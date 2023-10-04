<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Form;
use Filament\Forms;
use Filament\Forms\Components\Wizard\Step;
use App\Enums\PaymentPeriod;
use Filament\Forms\Get;
use App\Services\MemberService;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;
use App\Enums\Gender;
use Carbon\Carbon;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    use CreateRecord\Concerns\HasWizard;


    protected function getSteps(): array
    {
        return [
            Step::make('Member Detail')
            ->schema([
                Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Section::make('Basic Info')
                    ->schema([
                Forms\Components\TextInput::make('full_name')
                ->label('Name')
                ->required(),
                Forms\Components\Select::make('gender')
                ->label('Gender')
                ->options(Gender::array()),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Detail')
                    ->schema([
                 Forms\Components\TextInput::make('phone_number_1')
                ->label('Phone Number 1')
                ->required(),
                Forms\Components\TextInput::make('phone_number_2')
                ->label('Phone Number 2'),
                Forms\Components\TextInput::make('email')
                ->label('Email Address'),
                Forms\Components\TextInput::make('tg_handler')
                ->label('Telegram Handler')
                ->required()
                    ])
                    ->columns(2),

                    Forms\Components\Section::make('Address')
                    ->schema([
                
                Forms\Components\TextInput::make('g_address')
                ->required()
                ->label('General Address'),
                Forms\Components\TextInput::make('s_address')
                ->label('Specific Address')
                ->required(),
                    ])->columns(2),


                ])
            ]),

            Step::make('Plage')
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
                Forms\Components\DatePicker::make('start_date')
                ->label('Start date')
                ->minDate(today())
                // ->visible(fn(Get $get ) => $get('payment_period') ==  PaymentPeriod::MONTHLY),

                // Forms\Components\DatePicker::make('start_date')
                // ->label('Start Date')
                // ->minDate(today())
                // ->visible(fn(Get $get ) => $get('payment_period') ==  PaymentPeriod::YEARLY),

            ])
            ->columns(2)
        ];
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $data['member_id'] = MemberService::IDGenerator();
        $data['status'] = Status::ACTIVE->value;

        return $data;
    }

    protected  function handleRecordCreation(array $data): Model
    {
        $plageData =  [
        
            'amount' => $data['amount'],
            'payment_period' => $data['payment_period'],
            'start_date'   => $data['start_date'],
            'end_date' => $data['payment_period'] == PaymentPeriod::MONTHLY ? Carbon::create($data['start_date'])->addYear():Carbon::create($data['start_date'])->addYears(5)
        ];

        $member = static::getModel()::create(array_diff($data,$plageData));

        $plageData['status'] = Status::ACTIVE->value;

        return $member->plages()->create($plageData);

    }


}
