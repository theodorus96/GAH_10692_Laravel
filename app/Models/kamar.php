<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kamar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "kamar";
    protected $primaryKey = 'id_kamar';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jenisKamar',
        'nomor_kamar',
        'tipe_kasur',
    ];

    public function jenis_kamar()
    {
        return $this->belongsTo(jenis_kamar::class, 'id_jenisKamar');
    }

    public function transaksi_kamar()
    {
        return $this->hasMany(transaksi_kamar::class, 'id_kamar');
    }
}
