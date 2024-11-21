<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingABM extends Model
{
    use HasFactory;

    protected $fillable = [
        'kandidat_id',
        'abm_id',
        'nama_kandidat',
        'nama_abm',
        'created_by',
        'updated_by',
        'tanggal',
      ];

      public function kandidat()
    {

        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }


    public function abm()
    {

        return $this->belongsTo(ABM::class, 'abm_id');
    }

}
