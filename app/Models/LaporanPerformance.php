<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPerformance extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'lolos_sortir',
        'konfirmasi_hadir',
        'lolos',
        'training',
        'tandem',
        'PKM_baru',
        'PKM_batal_join',
        'resign',
        'bulan',
        'tahun',
        'posisi_id',
        'wilayah_id',
        
    ];

}
