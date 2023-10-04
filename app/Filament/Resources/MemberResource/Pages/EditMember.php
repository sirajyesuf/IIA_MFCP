<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
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

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;
    protected static ?string $recordTitleAttribute = 'name';



    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }



    public function form(Forms\Form $form): Forms\Form
    {
        return $form
        ->schema([

                Forms\Components\TextInput::make('full_name')
                ->label('Name')
                ->required(),

                Forms\Components\Select::make('gender')
                ->label('Gender')
                ->options(Gender::array()),

                 Forms\Components\TextInput::make('phone_number_1')
                ->label('Phone Number 1')
                ->required(),

                Forms\Components\TextInput::make('phone_number_2')
                ->label('Phone Number 2'),

                Forms\Components\TextInput::make('email')
                ->label('Email Address'),

                Forms\Components\TextInput::make('tg_handler')
                ->label('Telegram Handler')
                ->required(),

                Forms\Components\TextInput::make('g_address')
                ->required()
                ->label('General Address'),

                Forms\Components\TextInput::make('s_address')
                ->label('Specific Address')
                ->required(),
        ]);
    }
}
