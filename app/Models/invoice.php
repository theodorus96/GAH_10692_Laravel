<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $table = "invoice";
    protected $primaryKey = 'no_invoice';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_invoice',
        'id_reservasi',
        'id_pegawai',
        'tanggal_pelunasan',
        'cash',
        'total_pajak',
        'total_semua',
        'total_layanan',
        'total_harga',
    ];

    public function pegawai()
    {
        return $this->belongsTo(pegawai::class, 'id_pegawai');
    }

    public function reservasi()
    {
        return $this->belongsTo(reservasi::class, 'id_reservasi');
    }
}
