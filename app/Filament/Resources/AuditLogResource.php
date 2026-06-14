<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Sistema';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }

    public static function getModelLabel(): string
    {
        return 'Log Audit';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Log Audit';
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('adminUser.name')
                    ->label('Admin')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('targetUser.name')
                    ->label('Utente target')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Azione')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metadata')
                    ->label('Metadata')
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—')
                    ->limit(60)
                    ->tooltip(fn (AuditLog $record): ?string => $record->metadata
                        ? json_encode($record->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        : null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('admin_user_id')
                    ->label('Admin')
                    ->relationship('adminUser', 'name'),
                Tables\Filters\SelectFilter::make('action')
                    ->label('Azione')
                    ->options(fn () => AuditLog::distinct()->pluck('action', 'action')->filter()->toArray()),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
