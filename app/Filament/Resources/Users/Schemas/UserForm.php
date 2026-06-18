<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                Section::make('Informasi Pengguna')
                                    ->description('Detail akun dan otentikasi pengguna')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('name')
                                                ->label('Nama')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('email')
                                                ->email()
                                                ->required()
                                                ->maxLength(255)
                                                ->unique(ignorable: fn($record) => $record),
                                            TextInput::make('password')
                                                ->password()
                                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                                ->dehydrated(fn (?string $state): bool => filled($state))
                                                ->required(fn (string $operation): bool => $operation === 'create')
                                                ->maxLength(255)
                                                ->columnSpanFull()
                                                ->revealable(),
                                        ]),
                                    ]),
                            ]),
                            
                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Peran & Status')
                                    ->description('Pengaturan akses')
                                    ->schema([
                                        Select::make('roles')
                                            ->label('Peran')
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required(),
                                        Toggle::make('is_active')
                                            ->label('Status Aktif')
                                            ->default(true)
                                            ->helperText('Matikan jika user sudah pensiun atau pindah tugas.')
                                            ->inline(false),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

