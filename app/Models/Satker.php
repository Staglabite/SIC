<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satker extends Model
{
    protected $table = 'satker';
    protected $primaryKey = 'kode_satker';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_satker',
        'kode_renmin',
        'kode_pimpinan',
        'name',
        'deskripsi'
    ];

    public function renmin(): BelongsTo
    {
        return $this->belongsTo(Renmin::class, 'kode_renmin', 'kode_renmin');
    }

    public function pimpinan(): BelongsTo
    {
        return $this->belongsTo(Pimpinan::class, 'kode_pimpinan', 'kode_pimpinan');
    }

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class, 'satker_id', 'kode_satker');
    }
}