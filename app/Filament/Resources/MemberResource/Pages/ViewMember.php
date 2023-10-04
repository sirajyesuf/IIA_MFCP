<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Forms;
class ViewMember extends ViewRecord
{

    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('Payment')
            ->modalContent(view('filament.pages.actions.create_payment'))
            ->form([
                forms\Components\TextInput::make('bonus')
                ->integer()
                ->minValue(0)
                ->label('Bouns(ETB)'),
                forms\Components\TextInput::make('advance')
                ->integer()
                ->minValue(0)
                ->label('Advance(ETB'),
            ])
            ->slideOver()
        ];
    }

    public function infolist(InfoList $infolist):InfoList
    {
        
        return $infolist
            ->record($this->record)
            ->schema([
            Infolists\Components\Section::make('Member Detail')
            ->schema([
                Infolists\Components\TextEntry::make('member_id'),
                Infolists\Components\TextEntry::make('full_name'),
                Infolists\Components\TextEntry::make('gender'),
                Infolists\Components\TextEntry::make('phone_number_1'),
                Infolists\Components\TextEntry::make('phone_number_2'),
                Infolists\Components\TextEntry::make('g_address')
                ->label('G/Address'),
                Infolists\Components\TextEntry::make('s_address')
                ->label('S/Address'),
                Infolists\Components\TextEntry::make('email'),
                Infolists\Components\TextEntry::make('tg_handler'),
                ])
            ]);
    }



}
