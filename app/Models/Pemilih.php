<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Pemilih extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nik',
        'nama',
        'qr_code',
        'kode_logout',
        'is_voted',
        'tps',
    ];

}
