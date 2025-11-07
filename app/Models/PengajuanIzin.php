<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzin extends Model
{
    protected $table = 'pengajuanizin';

    protected $fillable = [
        'personel_id',
        'renmin_id',
        'pimpinan_id',
        'keperluan',
        'pengikut',
        'pergi_dari',
        'tujuan',
        'tgl_berangkat',
        'tgl_kembali',
        'transportasi',
        'catatan',
        'namaFile_bukti',
        'pathFile_bukti',
        'status',
    ];

    protected $casts = [
        'tgl_berangkat' => 'date',
        'tgl_kembali'   => 'date',
    ];

    // Relasi belongsTo
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
}