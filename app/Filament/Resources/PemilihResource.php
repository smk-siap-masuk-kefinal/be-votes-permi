<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemilihResource\Pages;
use App\Filament\Resources\PemilihResource\RelationManagers;
use App\Models\Pemilih;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PemilihResource extends Resource
{
    protected static ?string $model = Pemilih::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->required()
                    ->numeric()
                    ->maxLength(16)
                    ,
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('qr_code')
                //     ->maxLength(255),
                // Forms\Components\FileUpload::make('attachment')
                // ->disk('s3')
                // ->directory('form-attachments')
                // ->visibility('private'),
                // Forms\Components\TextInput::make('kode_logout')
                //     ->maxLength(255),
                // Forms\Components\Toggle::make('is_voted')
                //     ->required(),
                Forms\Components\TextInput::make('tps')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->disk('public')
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Avatar&background=random')
                    ->square()
                        // ->toggleable(isToggledHiddenByDefault: true)
                    ->size(150)
                ,
                Tables\Columns\TextColumn::make('nik')
                    ->copyable()
                    ->copyMessage('NIK Berhasil disalin')
                    ->copyMessageDuration(1500)
                    // ->numeric()
                    // ->sortable()
                ,
                Tables\Columns\TextColumn::make('nama')
                // ->searchable()
                // ->limit(5)
                ,
                // Tables\Columns\TextColumn::make('qr_code')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('kode_logout')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                ,
                Tables\Columns\IconColumn::make('is_voted')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true)
                ,
                Tables\Columns\TextColumn::make('tps')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemilihs::route('/'),
            'create' => Pages\CreatePemilih::route('/create'),
            'edit' => Pages\EditPemilih::route('/{record}/edit'),
        ];
    }
}
