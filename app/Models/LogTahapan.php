<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTahapan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kandidat_id',
        'status_tahapan',
        'tanggal',
        'bulan',
        'tahun',
        'posisi_id',
        'wilayah_id',
        'flag_lolos',
        'hasil_status',
        'flag_tahapan',
        'flag_kehadiran',
        'flag_schedule'

    ];


     public function kandidat()
      {
  
          return $this->belongsTo(Kandidat::class, 'kandidat_id');
      }

      public function posisi()
      {
  
          return $this->belongsTo(Posisi::class, 'posisi_id');
      }

      public function wilayah()
      {
  
          return $this->belongsTo(Wilayah::class, 'wilayah_id');
      }
}
