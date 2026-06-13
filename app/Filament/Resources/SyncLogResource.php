<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SyncLogResource\Pages;
use App\Models\SyncLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SyncLogResource extends Resource
{
    protected static ?string $model = SyncLog::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-arrow-path';
    }

    public static function getModelLabel(): string
    {
        return 'Log Sincronizzazione';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Log Sincronizzazioni';
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
                Tables\Columns\TextColumn::make('sync_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'historical'  => 'info',
                        'incremental' => 'gray',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'completed' => 'success',
                        'running'   => 'warning',
                        'pending'   => 'gray',
                        'failed'    => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Data inizio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durata')
                    ->getStateUsing(fn (SyncLog $record): string => $record->completed_at
                        ? $record->started_at->diffForHumans($record->completed_at, true)
                        : '—'),
                Tables\Columns\TextColumn::make('activities_imported')
                    ->label('Attività importate')
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Errore')
                    ->limit(60)
                    ->tooltip(fn (SyncLog $record): ?string => $record->error_message),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'pending'   => 'In attesa',
                        'running'   => 'In corso',
                        'completed' => 'Completata',
                        'failed'    => 'Fallita',
                    ]),
                Tables\Filters\SelectFilter::make('sync_type')
                    ->label('Tipo')
                    ->options([
                        'historical'  => 'Storica',
                        'incremental' => 'Incrementale',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name'),
            ])
            ->defaultSort('started_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSyncLogs::route('/'),
        ];
    }
}
