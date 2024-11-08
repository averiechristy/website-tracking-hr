<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetJumlah extends Model
{
    use HasFactory;


    protected $fillable = [
       'bulan',
       'tahun',
       'target_mpp',
       'jumlah_mitra',
       'target_join',
       'posisi_id',
       'created_by',
       'updated_by',
     
      ];

      public function posisi()
      {
  
          return $this->belongsTo(Posisi::class, 'posisi_id');
      }


}
