<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemilih extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama',
        'qr_code',
        'kode_logout',
        'is_voted',
        'tps',
    ];

}
