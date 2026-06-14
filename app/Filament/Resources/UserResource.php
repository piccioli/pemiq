<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationGroup(): ?string
    {
        return 'Utenti';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function getModelLabel(): string
    {
        return 'Utente';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Utenti';
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Toggle::make('is_premium')
                ->label('Premium'),
            Forms\Components\DateTimePicker::make('premium_started_at')
                ->label('Inizio Premium')
                ->nullable(),
            Forms\Components\DateTimePicker::make('premium_expires_at')
                ->label('Scadenza Premium')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Ruolo')
                    ->badge(),
                Tables\Columns\TextColumn::make('is_premium')
                    ->label('Premium')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Sì' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email verificata')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Ruolo')
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_premium')
                    ->label('Premium'),
                Tables\Filters\Filter::make('created_at')
                    ->label('Data registrazione')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Da'),
                        Forms\Components\DatePicker::make('until')->label('A'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'] ?? null, fn ($q) => $q->whereDate('created_at', '<=', $data['until']))),
            ])
            ->actions([
                EditAction::make(),
                Action::make('impersonate')
                    ->label('Impersona')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => !$record->hasRole('administrator'))
                    ->action(function (User $record) {
                        $adminId = auth()->id();
                        AuditLog::create([
                            'admin_user_id'  => $adminId,
                            'target_user_id' => $record->id,
                            'action'         => 'impersonate_start',
                            'metadata'       => ['ip' => request()->ip(), 'user_agent' => request()->userAgent()],
                            'created_at'     => now(),
                        ]);
                        session(['impersonating_admin_id' => $adminId]);
                        Auth::loginUsingId($record->id);
                        return redirect()->to(route('dashboard'));
                    }),
                Action::make('promote_premium')
                    ->label('Promuovi a Premium')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('premium_expires_at')
                            ->label('Scadenza Premium'),
                    ])
                    ->action(fn (User $record, array $data) => $record->update([
                        'is_premium' => true,
                        'premium_started_at' => now(),
                        'premium_expires_at' => $data['premium_expires_at'] ?? null,
                    ])),
                Action::make('revoke_premium')
                    ->label('Revoca Premium')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update([
                        'is_premium' => false,
                        'premium_expires_at' => null,
                    ])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('promote_selected')
                        ->label('Promuovi selezionati')
                        ->icon('heroicon-o-star')
                        ->action(fn ($records) => $records->each->update([
                            'is_premium' => true,
                            'premium_started_at' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('revoke_selected')
                        ->label('Revoca Premium selezionati')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'is_premium' => false,
                            'premium_expires_at' => null,
                        ]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
