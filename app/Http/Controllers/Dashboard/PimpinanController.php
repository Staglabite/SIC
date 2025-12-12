<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    // === DASHBOARD PIMPINAN ===
    public function dashboard()
    {
        // Ambil kode satker pimpinan yang login
        $kodeSatker = auth()->user()->kode_satker;

        // TOTAL CUTI
        $total_cuti = DB::table('pengajuancuti')
            ->leftJoin('personel', 'pengajuancuti.personel_id', '=', 'personel.nrp')
            ->where('personel.kode_satker', $kodeSatker)
            ->count();

        // TOTAL IZIN
        $total_izin = DB::table('pengajuanizin')
            ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
            ->where('personel.kode_satker', $kodeSatker)
            ->count();

        // TOTAL PENGAJUAN TAHUN 2025
        $total_tahun_ini =
            DB::table('pengajuancuti')
                ->leftJoin('personel', 'pengajuancuti.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->whereYear('pengajuancuti.created_at', 2025)
                ->count()
            +
            DB::table('pengajuanizin')
                ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->whereYear('pengajuanizin.created_at', 2025)
                ->count();

        // BELUM DIVALIDASI
        $belum_divalidasi =
            DB::table('pengajuancuti')
                ->leftJoin('personel', 'pengajuancuti.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->where('pengajuancuti.status', 'Proses')
                ->count()
            +
            DB::table('pengajuanizin')
                ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->where('pengajuanizin.status', 'Proses')
                ->count();

        // DATA BULANAN
        $bulanan_cuti = [];
        $bulanan_izin = [];

        for ($m = 1; $m <= 12; $m++) {

            $bulanan_cuti[] = DB::table('pengajuancuti')
                ->leftJoin('personel', 'pengajuancuti.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->whereYear('pengajuancuti.created_at', 2025)
                ->whereMonth('pengajuancuti.created_at', $m)
                ->count();

            $bulanan_izin[] = DB::table('pengajuanizin')
                ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
                ->where('personel.kode_satker', $kodeSatker)
                ->whereYear('pengajuanizin.created_at', 2025)
                ->whereMonth('pengajuanizin.created_at', $m)
                ->count();
        }

        $stats = compact(
            'total_cuti',
            'total_izin',
            'total_tahun_ini',
            'belum_divalidasi',
            'bulanan_cuti',
            'bulanan_izin'
        );

        return view('dashboard.pimpinan', compact('stats'));
    }


    // === VALIDASI / APPROVAL PENGAJUAN PIMPINAN ===
    public function validasi()
    {
        // Ambil kode satker pimpinan login
        $kodeSatker = auth()->user()->kode_satker;

        // =============================
        //  QUERY CUTI
        // =============================
        $cuti = DB::table('pengajuancuti')
            ->leftJoin('cuti', 'pengajuancuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->leftJoin('personel', 'pengajuancuti.personel_id', '=', 'personel.nrp')
            ->where('personel.kode_satker', $kodeSatker)   // FILTER SATKER
            ->select([
                'pengajuancuti.id',
                DB::raw("'cuti' as jenis"),
                'cuti.jenis_cuti as nama_jenis',
                'pengajuancuti.catatan as keterangan',
                'pengajuancuti.tujuan',
                'pengajuancuti.pergi_dari',
                'pengajuancuti.transportasi',
                'pengajuancuti.pengikut',
                'pengajuancuti.catatan as catatan',
                'pengajuancuti.mulai_tgl as tanggal_mulai',
                'pengajuancuti.sampai_tgl as tanggal_selesai',
                'pengajuancuti.status',
                'pengajuancuti.pathFile_bukti',
                'pengajuancuti.namaFile_bukti',
                'pengajuancuti.created_at',

                'personel.name as nama_personel',
                'personel.nrp',
                'personel.pangkat',
                'personel.jabatan',
                'personel.golongan',
            ]);

        // =============================
        //  QUERY IZIN
        // =============================
        $izin = DB::table('pengajuanizin')
            ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
            ->where('personel.kode_satker', $kodeSatker)   // FILTER SATKER
            ->select([
                'pengajuanizin.id',
                DB::raw("'izin' as jenis"),
                DB::raw("'IZIN' as nama_jenis"),
                'pengajuanizin.keperluan as keterangan',
                'pengajuanizin.tujuan',
                'pengajuanizin.pergi_dari',
                'pengajuanizin.transportasi',
                'pengajuanizin.pengikut',
                'pengajuanizin.catatan',
                'pengajuanizin.tgl_berangkat as tanggal_mulai',
                'pengajuanizin.tgl_kembali as tanggal_selesai',
                'pengajuanizin.status',
                'pengajuanizin.pathFile_bukti',
                'pengajuanizin.namaFile_bukti',
                'pengajuanizin.created_at',

                'personel.name as nama_personel',
                'personel.nrp',
                'personel.pangkat',
                'personel.jabatan',
                'personel.golongan',
            ]);

        // =============================
        //  UNION + ORDER BY
        // =============================
        $pengajuan = $cuti->unionAll($izin)
                        ->orderByDesc('created_at')
                        ->get();

        return view('dashboard.pimpinanapproval', compact('pengajuan'));
    }


    // === UPDATE STATUS APPROVAL OLEH PIMPINAN ===
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|integer',
            'status' => 'required|in:Disetujui,Ditolak,Tidak Valid', // Pimpinan biasanya pakai "Disetujui"
            'tipe'   => 'required|in:cuti,izin'
        ]);

        $table = $request->tipe === 'cuti' ? 'pengajuancuti' : 'pengajuanizin';

        $updated = DB::table($table)
                    ->where('id', $request->id)
                    ->update(['status' => $request->status]);

        return response()->json([
            'success' => $updated > 0,
            'message' => $updated > 0 ? 'Status approval berhasil diubah!' : 'Gagal mengubah status.'
        ]);
    }
}