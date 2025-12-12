<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pimpinan extends Authenticatable
{
    use HasFactory;

    protected $table = 'pimpinan';
    protected $primaryKey = 'kode_pimpinan';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['kode_pimpinan', 'nama', 'username', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'kode_pimpinan' => 'integer',
    ];

    public function satker(): BelongsTo
    {
        return $this->BelongsTo(Satker::class, 'kode_pimpinan', 'kode_pimpinan');
    }

    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class, 'pimpinan_id', 'kode_pimpinan');
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class, 'pimpinan_id', 'kode_pimpinan');
    }
}