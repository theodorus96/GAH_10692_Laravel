<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class season_kamar extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "season_kamar";
    protected $primaryKey = 'id_seasonKamar';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jenisKamar',
        'id_season',
        'harga_seasonKamar',
    ];

    public function season(){
        return $this->belongsTo(season::class, 'id_seasonKamar');
    }

    public function jenis_Kamar(){
        return $this->belongsTo(jenis_kamar::class, 'id_jenisKamar');
    }
}