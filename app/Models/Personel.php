<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personel extends Authenticatable
{
    use HasFactory;

    protected $table = 'personel';
    protected $primaryKey = 'nrp';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nrp',
        'password',
        'name',
        'pangkat',
        'golongan',
        'jabatan',
        'role',
        'satker_id',
    ];

    protected $hidden = ['password'];

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id', 'kode_satker');
    }

    public function pengajuanIzins()
    {
        return $this->hasMany(PengajuanIzin::class, 'personel_id', 'nrp');
    }

    public function pengajuanCutis()
    {
        return $this->hasMany(PengajuanCuti::class, 'personel_id', 'nrp');
    }
}