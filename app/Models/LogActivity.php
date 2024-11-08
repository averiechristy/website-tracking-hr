<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;
    
    protected $fillable = [
       'role_id',
       'user_id',
       'nama_user',
       'activity',
       'description',
      ];

      public function role()
      {
  
          return $this->belongsTo(Role::class, 'role_id');
      }

      public function user()
      {
  
          return $this->belongsTo(User::class, 'user_id');
      }
}
