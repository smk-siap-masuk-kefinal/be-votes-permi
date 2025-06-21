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
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PemilihResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PemilihResource\RelationManagers;
use Filament\Forms\Components\Placeholder;

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
                        if(blank($state)){
                            // $set('url_qr_code', null); //simpan ketika sudah mau buat new migration
                            $set('qr_code', null);
                            $set('kode_logout', null);
                            return;
                        }

                        //buat url qr dengan url aplikasi/url/isi-nik-unik  
                        $kodeLogout = substr($state, -6);
                        $set('kode_logout', $kodeLogout); //simpan kode logout
                        $urlQR = env('APP_URL') . '/url/' . $state ; //link asli
                        // $urlQR = 'https://ui-avatars.com/api/?name=' . $state; // link kw smntara
                        // $urlQR = 'https://ui-avatars.com/api/?name=Adli&Kece' ; // link kw smntara
                        $set('url_qr_code', $urlQR); //simpan url barcode
                        
                        $barcode = new DNS2D();
                        // $barcodePath = $state;
                        $barcodePath = 'https://ui-avatars.com/api/?name=Adli&Kece' ; // link kw barcode smntara
                        
                        
                    // ambil barcode dengan library milon
                        // $generatedBarcode = $barcode->getBarcodePNGPath('https://ui-avatars.com/api/?name=Adli&Kece', 'QRCODE');
                        // $generatedBarcode = $barcode->getBarcodePNGPath('https://ui-avatars.com/api/?name=Adli&Kece', 'QRCODE');
                        $generatedBarcode =  DNS2D::getBarcodePNGPath('4455666', 'QRCODE', 3, 3);
                     // simpan qr code ke storage
                        Storage::disk('public')->put('barcodes/123.png', $generatedBarcode);

                    // simpan path qr code di db
                        $set('qr_code', $barcodePath);

                    })
                    ,
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
                        // ->toggleable(isToggledHiddenByDefault: true)
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
