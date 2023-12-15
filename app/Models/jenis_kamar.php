<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jenis_kamar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "jenis_kamar";
    protected $primaryKey = 'id_jenisKamar';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis',
        'rincian_kamar',
        'deskripsi',
        'ukuran',
        'kapasitas',
        'harga',
    ];

    public function kamar()
    {
        return $this->hasMany(kamar::class, 'id_jenisKamar');
    }

    public function season_kamar()
    {
        return $this->hasMany(season_kamar::class, 'id_jenisKamar');
    }

    public function transaksi_kamar()
    {
        return $this->hasMany(transaksi_kamar::class, 'id_jenisKamar');
    }
}