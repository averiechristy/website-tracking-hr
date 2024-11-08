<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTidakAktif extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'jabatan',
        'keterangan',
        'flag_ket',
        'bulan',
        'tahun',
        'posisi_id',
        'nama_posisi',
       ];
}
