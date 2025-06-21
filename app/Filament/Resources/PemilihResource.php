<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pemilih;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Milon\Barcode\DNS2D;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\PemilihResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PemilihResource\RelationManagers;

class PemilihResource extends Resource
{
    protected static ?string $model = Pemilih::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->placeholder('16 Digit NIK')
                    ->required()
                    ->numeric()
                    ->minLength(16)
                    ->maxLength(16)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function(Set $set, ?string $state){
                        if (blank($state)) {
                            $set('kode_logout', null);
                            $set('url_qr_code', null);
                            $set('qr_code', null);
                            return;
                        }

                        $kodeLogout = substr($state, -6);
                        $set('kode_logout', $kodeLogout);
                        
                        $urlKtp = env('APP_URL') . $state;
                        $set('url_qr_code', $urlKtp);

                        // Panggil API GoQR.me untuk membuat barcode
                        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
                        $response = Http::get($apiUrl, [
                            'data' => $urlKtp,
                            'size' => '250x250',
                            'ecc'  => 'M',
                            'margin' => 1,
                        ]);

                        // Simpan gambar 
                        if ($response->successful()) {
                            $imageData = $response->body();
                            $filePath = 'barcodes/' . $state . '.png';
                            Storage::disk('public')->put($filePath, $imageData);
                            
                            // simpan patj di db
                            $set('qr_code', $filePath);
                        } else {
                            // kasi kosong klo gagal
                            $set('qr_code', null);
                        }
                    }),
                    
                 Forms\Components\TextInput::make('url_qr_code')
                    ->label('URL QR Code')
                    // ->disabled() 
                    ->readOnly()
                    ->helperText('Kolom ini dibuat secara otomatis'),
            
                // preview qr code
                Forms\Components\ViewField::make('qr_code')
                    ->label('Preview QR Code')
                    ->view('filament.forms.qr-code-preview') // view manual
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('qr_code')
                //     ->maxLength(255),
                // Forms\Components\FileUpload::make('attachment')
                // ->disk('s3')
                // ->directory('form-attachments')
                // ->visibility('private'),
                Forms\Components\TextInput::make('kode_logout')
                    ->maxLength(255)
                    // ->disabled()
                    ->readOnly()
                    ->helperText('Kolom ini dibuat secara otomatis')
                    ,
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
                Tables\Columns\ImageColumn::make('qr_code')
                    ->label('QR Code')
                    ->disk('public')
                    // ->path
                    // ->defaultImageUrl('https://ui-avatars.com/api/?name=Avatar&background=random')
                    // ->defaultImageUrl(asset('/images/default/default.png'))
                     ->defaultImageUrl(url('/images/default/default.png'))
                    ->square()
                        // ->toggleable(isToggledHiddenByDef ault: true)
                    ->size(150)
                ,
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
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
