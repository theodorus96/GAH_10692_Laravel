<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class season extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "season";
    protected $primaryKey = 'id_season';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis_season',
        'mulai_season',
        'akhir_season',
    ];

    public function season_kamar()
    {
        return $this->hasMany(season_kamar::class, 'id_season');
    }
}