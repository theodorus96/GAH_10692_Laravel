<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class pegawai extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = "pegawai";
    protected $primaryKey = 'id_pegawai';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'nama',
        'role',
    ];

    public function reservasi()
    {
        return $this->hasMany(reservasi::class, 'id_pegawai');
    }

    public function invoice()
    {
        return $this->hasOne(invoice::class, 'id_pegawai');
    }
}