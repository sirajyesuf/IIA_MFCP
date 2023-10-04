<?php

namespace App\Filament\Resources\MemberResource\Widgets;

use App\Models\Member;
use App\Models\Plage;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class ActivePlageDetail extends Widget
{
    protected static string $view = 'filament.resources.member-resource.widgets.active-plage-detail';

    public ?Model $record = null;
    public ?Plage $activePlage = null;


}


