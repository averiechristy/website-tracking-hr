<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_posisi',
        'created_by',
        'updated_by',
      ];

      public function kandidat()
      {
  
          return $this->hasMany(Kandidat::class);
      }

      public function detailposisi()
    {

        return $this->hasMany(DetailPosisi::class);
    }

    public function konfirm()
    {

        return $this->hasMany(MasterKonfirm::class);
    }

    public function logtahapan()
    {

        return $this->hasMany(LogTahapan::class);
    }

    public function targetjumlah()
    {

        return $this->hasMany(TargetJumlah::class);
    }
}
