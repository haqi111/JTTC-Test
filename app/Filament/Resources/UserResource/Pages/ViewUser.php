<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(UserResource::getUrl('index'))
                ->color('secondary')
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function resolveRecord($key): Model
    {
        return User::with([
            'division',
            'contracts',
        ])->findOrFail($key);
    }
}
