<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MasterTokenWidget extends Widget
{
    protected string $view = 'filament.widgets.master-token-widget';

    public ?string $token = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function generateToken()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Buat token baru dengan nama standar 'master-data-api'
        $tokenResult = $user->createToken('master-data-api');

        $this->token = $tokenResult->plainTextToken;
    }
}
