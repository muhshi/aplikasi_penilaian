<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class MasterTokenWidget extends Widget
{
    use HasWidgetShield;

    protected string $view = 'filament.widgets.master-token-widget';

    public ?string $token = null;

    protected int|string|array $columnSpan = 'full';

    public function generateToken()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Buat token baru dengan nama standar 'master-data-api'
        $tokenResult = $user->createToken('master-data-api');

        $this->token = $tokenResult->plainTextToken;
    }
}
