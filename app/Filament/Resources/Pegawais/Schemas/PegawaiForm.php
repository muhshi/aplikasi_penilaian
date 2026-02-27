<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun (User)')
                    ->schema([
                        TextInput::make('user.name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Section::make('Data Kepegawaian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nip')
                                    ->label('NIP Baru')
                                    ->required()
                                    ->numeric()
                                    ->minLength(18)
                                    ->maxLength(18)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('nip_lama')
                                    ->label('NIP Lama')
                                    ->required()
                                    ->maxLength(9)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('no_hp')
                                    ->label('No. Handphone')
                                    ->tel()
                                    ->maxLength(15),
                                TextInput::make('jabatan')
                                    ->label('Jabatan')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('pangkat')
                                    ->label('Pangkat')
                                    ->maxLength(255),
                                TextInput::make('golongan')
                                    ->label('Golongan')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }
}
