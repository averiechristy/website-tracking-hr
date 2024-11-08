<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPosisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'posisi_id',
        'posisi',
        'wilayah_id',
        'wilayah',
    ];


    public function user()
    {

        return $this->belongsTo(User::class, 'user_id');
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



