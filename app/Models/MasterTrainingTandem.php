<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTrainingTandem extends Model
{
    use HasFactory;



    protected $fillable = [
       'nama_karyawan',
       'posisi',
       'domisili',
       'kelas_training',
       'tanggal_training',
       'status',
       'reason',
        'posisi_id',
        'hari',
        'bulan',
        'tahun'

    ];

}
