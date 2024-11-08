<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'kandidat_id',
        'keterangan',
        'posisi_id',
        'wilayah_id',
        
    ];
    public function kandidat()
    {

        return $this->belongsTo(Kandidat::class, 'kandidat_id');
    }
}
