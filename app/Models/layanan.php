<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class layanan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "layanan";
    protected $primaryKey = 'id_layanan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
    ];

    public function transaksi()
    {
        return $this->hasMany(transaksi::class, 'id_layanan');
    }
}