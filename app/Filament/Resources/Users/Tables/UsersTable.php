<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assign_roles')
                        ->label('Assign Role Massal')
                        ->icon('heroicon-o-users')
                        ->form([
                            Select::make('role_id')
                                ->label('Pilih Role')
                                ->options(Role::pluck('name', 'id')->toArray())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $role = Role::findById($data['role_id']);
                            foreach ($records as $record) {
                                $record->syncRoles([$role->name]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
