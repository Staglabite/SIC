<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RenminController extends Controller
{
    // === DASHBOARD RENMIN (tidak diubah banyak) ===
    public function dashboard()
    {
        $total_cuti = DB::table('pengajuan_cuti')->count();
        $total_izin = DB::table('pengajuanizin')->count();
        $total_tahun_ini = DB::table('pengajuan_cuti')
                    ->whereYear('created_at', 2025)
                    ->count() + DB::table('pengajuanizin')
                    ->whereYear('created_at', 2025)
                    ->count();
        $belum_divalidasi = DB::table('pengajuan_cuti')
                    ->where('status', 'Proses')
                    ->count() + DB::table('pengajuanizin')
                    ->where('status', 'Proses')
                    ->count();

        $bulanan_cuti = [];
        $bulanan_izin = [];
        for ($m = 1; $m <= 12; $m++) {
            $bulanan_cuti[] = DB::table('pengajuan_cuti')
                            ->whereYear('created_at', 2025)
                            ->whereMonth('created_at', $m)
                            ->count();
            $bulanan_izin[] = DB::table('pengajuanizin')
                            ->whereYear('created_at', 2025)
                            ->whereMonth('created_at', $m)
                            ->count();
        }

        $stats = compact('total_cuti', 'total_izin', 'total_tahun_ini', 'belum_divalidasi', 'bulanan_cuti', 'bulanan_izin');
        return view('dashboard.renmin', compact('stats'));
    }

    // === VALIDASI PENGAJUAN – YANG DIPERBAIKI TOTAL ===
    public function validasi()
    {
        // Query CUTI
        $cuti = DB::table('pengajuan_cuti')
            ->leftJoin('cuti', 'pengajuan_cuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->leftJoin('personel', 'pengajuan_cuti.personel_id', '=', 'personel.nrp')
            ->select([
                'pengajuan_cuti.id',
                DB::raw("'cuti' as jenis"),
                'cuti.jenis_cuti as nama_jenis',
                'pengajuan_cuti.catatan as keterangan',
                'pengajuan_cuti.tujuan',
                'pengajuan_cuti.pergi_dari',
                'pengajuan_cuti.transportasi',
                'pengajuan_cuti.pengikut',
                'pengajuan_cuti.catatan as catatan',               // catatan tambahan
                'pengajuan_cuti.mulai_tgl as tanggal_mulai',
                'pengajuan_cuti.sampai_tgl as tanggal_selesai',
                'pengajuan_cuti.status',
                'pengajuan_cuti.pathFile_bukti',
                'pengajuan_cuti.namaFile_bukti',
                'pengajuan_cuti.created_at',

                // DATA PERSONEL – WAJIB!
                'personel.name as nama_personel',
                'personel.nrp',
                'personel.pangkat',
                'personel.jabatan',
                'personel.golongan',                               // tambah kalau ada kolomnya
            ]);

        // Query IZIN
        $izin = DB::table('pengajuanizin')
            ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
            ->select([
                'pengajuanizin.id',
                DB::raw("'izin' as jenis"),
                DB::raw("'IZIN' as nama_jenis"),
                'pengajuanizin.keperluan as keterangan',
                'pengajuanizin.tujuan',
                'pengajuanizin.pergi_dari',
                'pengajuanizin.transportasi',
                'pengajuanizin.pengikut',
                'pengajuanizin.catatan',                           // pastikan kolom ini ADA
                'pengajuanizin.tgl_berangkat as tanggal_mulai',
                'pengajuanizin.tgl_kembali as tanggal_selesai',
                'pengajuanizin.status',
                'pengajuanizin.pathFile_bukti',
                'pengajuanizin.namaFile_bukti',
                'pengajuanizin.created_at',

                // DATA PERSONEL – WAJIB!
                'personel.name as nama_personel',
                'personel.nrp',
                'personel.pangkat',
                'personel.jabatan',
                'personel.golongan',
            ]);

        // Gabungkan & urutkan
        $pengajuan = $cuti->unionAll($izin)
                          ->orderByDesc('created_at')
                          ->get();

        return view('dashboard.renminvalidasi', compact('pengajuan'));
    }

    // === UPDATE STATUS (VALID / TOLAK / TIDAK VALID) ===
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:Tervalidasi,Ditolak,Tidak Valid',
            'tipe'   => 'required|in:cuti,izin'
        ]);

        $table = $request->tipe === 'cuti' ? 'pengajuan_cuti' : 'pengajuanizin';

        $updated = DB::table($table)
                    ->where('id', $request->id)
                    ->update(['status' => $request->status]);

        return response()->json([
            'success' => $updated > 0,
            'message' => $updated > 0 ? 'Status berhasil diubah!' : 'Gagal mengubah status.'
        ]);
    }
}