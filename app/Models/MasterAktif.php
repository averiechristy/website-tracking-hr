<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAktif extends Model
{
    use HasFactory;
    
    protected $fillable = [
     'karyawan_id',
     'nama_karyawan',
     'jabatan',
     'bulan',
     'tahun',
     'posisi_id',
     'nama_posisi'
    ];
    
}
