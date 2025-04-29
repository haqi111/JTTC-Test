<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Models\User;
use App\Models\Contract;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ContractResource;

class UserContractHistory extends Page
{
    protected static string $resource = \App\Filament\Resources\ContractResource::class;

    protected static string $view = 'filament.resources.contract-resource.pages.user-contract-history';

    public User $user;
    public $contracts;

    public function mount($record): void
    {
        $this->user = User::findOrFail($record);
        $this->contracts = Contract::where('user_id', $record)
            ->orderBy('start_date', 'desc')
            ->get();
    }

}
