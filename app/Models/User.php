<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      'role_id',
      'nama',
      'email',
      'email_verified_at',
      'password',
      'posisi',
      'wilayah',
      'created_by',
      'updated_by',
    ];


    public function role()
    {

        return $this->belongsTo(Role::class, 'role_id');
    }

    public function logactivity()
    {

        return $this->hasMany(LogActivity::class);
    }
    public function kandidat()
    {

        return $this->hasMany(Kandidat::class);
    }

    public function detailposisi()
    {

        return $this->hasMany(DetailPosisi::class);
    }


    public function isSuperAdmin()
    {
        $jenis_role = $this->role->nama_role;
        
        return strtoupper($jenis_role) === 'SUPERADMIN';
    }
    public function konfirm()
    {

        return $this->hasMany(MasterKonfirm::class);
    }

    public function isTrainer()
    {
        $jenis_role = $this->role->nama_role;
        
        return strtoupper($jenis_role) === 'TRAINER';
    }
    public function isRekrutmen()
    {
        $jenis_role = $this->role->nama_role;
        return strtoupper($jenis_role) === 'REKRUTMEN';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
