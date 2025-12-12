<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanCuti extends Model
{
    protected $table = 'pengajuancuti';

    protected $fillable = [
        'personel_id', 'renmin_id', 'pimpinan_id', 'kode_cuti',
        'pengikut', 'pergi_dari', 'mulai_tgl', 'sampai_tgl',
        'tujuan', 'transportasi', 'catatan',
        'namaFile_bukti', 'pathFile_bukti', 'status'
    ];

    protected $casts = [
        'mulai_tgl' => 'date',
        'sampai_tgl' => 'date',
    ];

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class, 'personel_id', 'nrp');
    }

    public function renmin(): BelongsTo
    {
        return $this->belongsTo(Renmin::class, 'renmin_id', 'kode_renmin');
    }

    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(Pimpinan::class, 'pimpinan_id', 'kode_pimpinan');
    }

    public function cuti(): BelongsTo
    {
        return $this->belongsTo(Cuti::class, 'kode_cuti', 'kode_cuti');
    }
}