<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
      'kode_role',
      'nama_role',
      'permision',
      'updated_by',
      'created_by',
      ];

    public function user()
    {

        return $this->hasMany(User::class);
    }

    public function logactivity()
    {

        return $this->hasMany(LogActivity::class);
    }
}
