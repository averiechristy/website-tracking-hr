<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKonfirm extends Model
{
    use HasFactory;

    protected $fillable = [
       'tanggal',
       'nama_sourcing',
       'sourcing_id',
       'posisi_id',
       'wilayah_id',
       'nama_posisi',
       'nama_wilayah',
       'jumlah_undang_otomatis',
       'jumlah_konfirm_manual',
       'keterangan',
       'day',
       'month',
       'year',
       'keterangan',
       ];

       public function posisi()
       {
   
           return $this->belongsTo(Posisi::class, 'posisi_id');
       }
 
       public function wilayah()
       {
   
           return $this->belongsTo(Wilayah::class, 'wilayah_id');
       }
       public function user()
       {
   
           return $this->belongsTo(User::class, 'sourcing_id');
       }
 
       


}
