<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class data_user extends Model
{
    use HasFactory;
    protected $table = "data_user";
    public $timestamps = false;
    protected $primaryKey = 'id_dataUser';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'nama',
        'no_identitas',
        'nomor_telepon',
        'alamat',
        'institusi',
        'role',
        'jenis_customer'
    ];

    protected $attributes = [
        'role' => 'Customer',
    ];

    public function user()
    {
        return $this->belongsTo(user::class, 'id_user');
    }

    public function reservasi()
    {
        return $this->hasMany(reservasi::class, 'id_dataUser');
    }
}
