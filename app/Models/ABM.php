<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ABM extends Model
{
    use HasFactory;

    protected $fillable = [
       'nama_ABM',
       'created_by',
       'updated_by',
        
    ];

    public function abm()
    {

        return $this->hasMany(ABM::class);
    }
}
