<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StravaAccountResource\Pages;
use App\Jobs\SyncStravaHistoricalActivities;
use App\Models\StravaAccount;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StravaAccountResource extends Resource
{
    protected static ?string $model = StravaAccount::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Strava';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-link';
    }

    public static function getModelLabel(): string
    {
        return 'Account Strava';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Account Strava';
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('strava_athlete_id')
                    ->label('Strava Athlete ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('connection_status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'connected'    => 'success',
                        'disconnected' => 'gray',
                        'error'        => 'danger',
                        default        => 'gray',
                    }),
                Tables\Columns\TextColumn::make('last_sync_at')
                    ->label('Ultima sync')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('connection_status')
                    ->label('Stato')
                    ->options([
                        'connected'    => 'Connesso',
                        'disconnected' => 'Disconnesso',
                        'error'        => 'Errore',
                    ]),
            ])
            ->actions([
                Action::make('force_sync')
                    ->label('Forza sincronizzazione')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (StravaAccount $record) => SyncStravaHistoricalActivities::dispatch($record)),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStravaAccounts::route('/'),
        ];
    }
}
