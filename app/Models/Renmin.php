<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Renmin extends Authenticatable
{
    use HasFactory;

    protected $table = 'renmin';
    protected $primaryKey = 'kode_renmin';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['kode_renmin', 'username', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'kode_renmin' => 'integer',
    ];

    // HAPUS SELURUH FUNGSI INI! → BIAR TIDAK HASH 2 KALI
    // public function setPasswordAttribute($value) { ... }

    public function satker(): HasMany
    {
        return $this->hasMany(Satker::class, 'kode_renmin', 'kode_renmin');
    }

    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class, 'renmin_id', 'kode_renmin');
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class, 'renmin_id', 'kode_renmin');
    }
}