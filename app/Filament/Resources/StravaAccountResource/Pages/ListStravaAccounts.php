<?php

namespace App\Filament\Resources\StravaAccountResource\Pages;

use App\Filament\Resources\StravaAccountResource;
use Filament\Resources\Pages\ListRecords;

class ListStravaAccounts extends ListRecords
{
    protected static string $resource = StravaAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
