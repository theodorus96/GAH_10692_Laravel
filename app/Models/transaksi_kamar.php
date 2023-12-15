<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_kamar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "transaksi_kamar";
    protected $primaryKey = 'id_transaksiKamar';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jenisKamar',
        'id_reservasi',
        'jumlah',
        'subtotal'
    ];

    public function jenis_kamar()
    {
        return $this->belongsTo(jenis_kamar::class, 'id_jenisKamar');
    }

    public function reservasi()
    {
        return $this->belongsTo(reservasi::class, 'id_reservasi');
    }
}