<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kandidat extends Model
{
    use HasFactory;


    protected $fillable = [
        'tanggal',
        'hari',
        'bulan',
        'tahun',
        'nama_kandidat',
        'posisi_id',
        'wilayah_id',
        'sumber_id',
        'no_hp',
        'email',
        'status_copy',
        'status_hire',
        'created_by',
        'user_id',
        'updated_by',
      ];

      public function posisi()
      {
  
          return $this->belongsTo(Posisi::class, 'posisi_id');
      }

      

      public function user()
      {
  
          return $this->belongsTo(User::class, 'user_id');
      }

      public function sumber()
      {
  
          return $this->belongsTo(Sumber::class, 'sumber_id');
      }
      public function wilayah()
      {
  
          return $this->belongsTo(Wilayah::class, 'wilayah_id');
      }

      public function logtahapan()
      {
  
          return $this->hasMany(LogTahapan::class);
      }

      public function blacklist()
{
    return $this->hasOne(BlackList::class);
}

}
