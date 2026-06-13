<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Strava';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-bolt';
    }

    public static function getModelLabel(): string
    {
        return 'Attività';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Attività';
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome attività')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('sport_type')
                    ->label('Sport')
                    ->badge(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance')
                    ->label('Distanza (km)')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1000, 1) : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('elevation_gain')
                    ->label('Dislivello (m)')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 0) : '—')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Utente')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('sport_type')
                    ->label('Sport')
                    ->options(fn () => Activity::distinct()->pluck('sport_type', 'sport_type')->filter()->toArray()),
                Tables\Filters\Filter::make('started_at')
                    ->label('Data attività')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Da'),
                        Forms\Components\DatePicker::make('until')->label('A'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q) => $q->whereDate('started_at', '>=', $data['from']))
                        ->when($data['until'] ?? null, fn ($q) => $q->whereDate('started_at', '<=', $data['until']))),
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
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
