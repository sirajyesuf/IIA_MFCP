<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Filament\Resources\MemberResource\Widgets\ActivePlageDetail;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Plage;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Filament\Actions;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Notifications\Notification;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Ramsey\Uuid\Type\Integer;

class PaymentMember extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = MemberResource::class;

    protected static string $view = 'filament.resources.member-resource.pages.payment-member';

    public $record;



    protected function getDefaultDateRange($activePlage){

        $latest_payment = $activePlage->payments()->latest()->first();

        if($latest_payment){

            $new_payment_start_date  = $latest_payment->aganist_end_date;
            $new_payment_end_date = Carbon::createFromDate($latest_payment->aganist_end_date)->addMonth();
        }

        else{

            $new_payment_start_date = $activePlage->start_date;
            $new_payment_end_date = Carbon::createFromDate($activePlage->start_date)->addMonth();
        }

        return $new_payment_start_date . " to ". $new_payment_end_date;
    }


    protected function getNumberOfMonth($dateRange){

        // Split the date range into two dates
        $dateParts = explode(" to ", $dateRange);
        
        if (count($dateParts) === 2) {

            // If the array has two elements, it means the split was successful
            $startDate = Carbon::parse($dateParts[0]);
            $endDate = Carbon::parse($dateParts[1]);
        
            // Calculate the number of months between the two dates
            $numberOfMonths = $startDate->diffInMonths($endDate);
        
            return $numberOfMonths;
        }
    }


    protected function getShortNameOfMonths($record)
    {
            $months = [];
        
            $currentMonth = Carbon::parse($record->aganist_start_date)->copy();
            while ($currentMonth->lessThan(Carbon::parse($record->aganist_end_date))) {
                $months[] = $currentMonth->format('F');
                $currentMonth->addMonth();
            }
        
            $numberOfMonths = count($months);
        
            $monthNames = implode(', ', $months);

            return $monthNames;
        
    }


    protected function getMinDate($activePlage)
    {
        $latest_payment = $activePlage->payments()->latest()->first();

        if($latest_payment) return $latest_payment->aganist_end_date;

        return $activePlage->start_date;
    }


    protected function calculateAdvance($activePlage,$amount,$dateRange){

        $numberOfMonths = $this->getNumberOfMonth($dateRange);

        $required_amount = $activePlage->amount * $numberOfMonths;

        $result =  $amount - ($required_amount + $activePlage->advance);

        if($result < 0 ) return $result * -1;

        return 0;

    }


    protected function calculateBonus($activePlage,$amount,$dateRange){

        $numberOfMonths = $this->getNumberOfMonth($dateRange);

        $required_amount = $activePlage->amount * $numberOfMonths;

        $result = $amount - ($required_amount + $activePlage->advance);

        if($result > 0 ) return $result;

        return 0;

    }

    protected function form1(): array
    {

        $activePlage = Member::findOrFail($this->record)->ActivePlage()->first();


        return [
                Flatpickr::make('aganist_date')
                ->range()
                ->helperText('Your full name here, including any middle names.')
                ->hint(function(Get $get){
                    return $this->getNumberOfMonth($get('aganist_date'));
                })
                ->live()
                ->required()
                ->default(function() use ($activePlage){
                    return $this->getDefaultDateRange($activePlage);
                })
                ->afterStateUpdated(function (Set $set,Get $get,?string $state) use ($activePlage) {
                    $set('advance',$this->calculateAdvance($activePlage,$get('amount'),$state));
                    $set('bonus',$this->calculateBonus($activePlage,$get('amount'),$state));
                })
                ->minDate($this->getMinDate($activePlage))
                ->maxDate(Carbon::parse($activePlage->end_date)->subMonth()),
                Forms\Components\TextInput::make('amount')
                ->numeric()
                ->integer()
                ->live()
                ->afterStateUpdated(function (Set $set,Get $get,?string $state) use ($activePlage) {
                    $set('advance',$this->calculateAdvance($activePlage,$state,$get('aganist_date')));
                    $set('bonus',$this->calculateBonus($activePlage,$state,$get('aganist_date')));
                })
                ->required(),
                Forms\Components\TextInput::make('advance')
                ->numeric()
                ->integer()
                ->disabled(),
                Forms\Components\TextInput::make('bonus')
                ->numeric()
                ->integer()
                ->disabled()
                
        ];

        
    }

    protected function createPayment($data){



        $activePlage = Member::findOrFail($this->record)->ActivePlage()->first();

        $dateRange = $data['aganist_date'][0]." to ".$data['aganist_date'][1];

        $this->updateActivePlageAdvanceAndBonus($activePlage,$data['amount'],$dateRange);


        $data['aganist_start_date'] = $data['aganist_date'][0];
        $data['aganist_end_date'] = $data['aganist_date'][1];

        unset($data['aganist_date']);
 
        return $activePlage->payments()->create($data);

    }


    protected function updateActivePlageAdvanceAndBonus($activePlage,$amount,$dateRange){

        $advance = $this->calculateAdvance($activePlage,$amount,$dateRange);
        $bonus = $this->calculateBonus($activePlage,$amount,$dateRange);

        $activePlage->update([
            'advance' => $advance,
            'bonus'  => $bonus + $activePlage->bonus
        ]);

    }

    protected function getHeaderWidgets(): array
    {
        return [
            ActivePlageDetail::make([
                'record' => Member::findOrFail($this->record),
                'activePlage' => Member::findOrFail($this->record)->ActivePlage()->first()
            ]),
            
        ];
    }


    public function table(Table $table): Table
    {
        $active_plage = Member::findOrFail($this->record)->ActivePlage()->first();
        $latest_payment = $active_plage->payments()->latest()->first();

        return $table
            ->query(Payment::where('plage_id',$active_plage->id))
            ->columns([
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('aganist_start_date'),
                Tables\Columns\TextColumn::make('aganist_end_date'),
                Tables\Columns\TextColumn::make('months')
                ->state(function(Payment $record) { 

                    return $this->getShortNameOfMonths($record);
                })
                ->badge()
                ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()
                ->form($this->form1())
                ->using(fn($data) => $this->createPayment($data))
                ->slideOver()
                ->visible(Carbon::parse($latest_payment->aganist_end_date)->lessThan(Carbon::parse($active_plage->end_date)))
            ])
            ->actions([])
            
            ;
    }
}
