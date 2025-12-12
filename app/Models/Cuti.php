<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'kode_cuti';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['jenis_cuti', 'deskripsi', 'jatah'];

    public function pengajuanCutis(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class, 'kode_cuti', 'kode_cuti');
    }
}