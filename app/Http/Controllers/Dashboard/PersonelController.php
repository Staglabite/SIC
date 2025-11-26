<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonelController extends Controller
{
    // DASHBOARD + RIWAYAT
    public function index()
    {
        $nrp = Auth::guard('personel')->check()
            ? Auth::guard('personel')->user()->nrp
            : Auth::user()->nrp;

        $cuti = DB::table('pengajuan_cuti')
            ->leftJoin('cuti', 'pengajuan_cuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->leftJoin('personel', 'pengajuan_cuti.personel_id', '=', 'personel.nrp')
            ->where('pengajuan_cuti.personel_id', $nrp)
            ->select([
                'pengajuan_cuti.id',
                DB::raw("'cuti' as jenis"),
                'cuti.jenis_cuti as nama_jenis',
                'pengajuan_cuti.catatan as keterangan',
                'pengajuan_cuti.tujuan',
                'pengajuan_cuti.mulai_tgl as tanggal_mulai',
                'pengajuan_cuti.sampai_tgl as tanggal_selesai',
                'pengajuan_cuti.status',
                'pengajuan_cuti.pathFile_bukti',
                'pengajuan_cuti.namaFile_bukti',
                'personel.nrp',
                'personel.name as nama_personel',
                'pengajuan_cuti.created_at',
            ]);

        $izin = DB::table('pengajuanizin')
            ->leftJoin('personel', 'pengajuanizin.personel_id', '=', 'personel.nrp')
            ->where('pengajuanizin.personel_id', $nrp)
            ->select([
                'pengajuanizin.id',
                DB::raw("'izin' as jenis"),
                DB::raw("'IZIN' as nama_jenis"),
                'pengajuanizin.keperluan as keterangan',
                'pengajuanizin.tujuan',
                'pengajuanizin.tgl_berangkat as tanggal_mulai',
                'pengajuanizin.tgl_kembali as tanggal_selesai',
                'pengajuanizin.status',
                'pengajuanizin.pathFile_bukti',
                'pengajuanizin.namaFile_bukti',
                'personel.nrp',
                'personel.name as nama_personel',
                'pengajuanizin.created_at',
            ]);

        $riwayat = $cuti->unionAll($izin)->orderByDesc('created_at')->get();

        return view('dashboard.personel', compact('riwayat'));
    }

    // FORM PENGAJUAN BARU
    public function create()
    {
        return view('dashboard.personelpengajuan');
    }

    // SIMPAN PENGAJUAN
    public function storeCuti(Request $request)
    {
        $request->validate([
            'keperluan'  => 'required|string|max:255',
            'tujuan'     => 'required|string|max:255',
            'tgl_mulai'  => 'required|date',
            'tgl_selesai'=> 'nullable|date|after_or_equal:tgl_mulai',
            'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $nrp = Auth::guard('personel')->user()->nrp;

        $data = [
            'personel_id' => $nrp,
            'kode_cuti'   => $request->kode_cuti ?? 'CUTI-001',
            'catatan'     => $request->keperluan,
            'tujuan'      => $request->tujuan,
            'mulai_tgl'   => $request->tgl_mulai,
            'sampai_tgl'  => $request->tgl_selesai,
            'status'      => 'Proses',
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $nama = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pengajuan', $nama, 'public');
            $data['pathFile_bukti'] = $path;
            $data['namaFile_bukti'] = $file->getClientOriginalName();
        }

        DB::table('pengajuan_cuti')->insert($data);

        return redirect()->route('personel.dashboard')->with('success', 'Pengajuan Cuti berhasil dikirim!');
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'keperluan'  => 'required|string|max:255',
            'tujuan'     => 'required|string|max:255',
            'tgl_mulai'  => 'required|date',
            'tgl_selesai'=> 'nullable|date|after_or_equal:tgl_mulai',
            'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $nrp = Auth::guard('personel')->user()->nrp;

        $data = [
            'personel_id'   => $nrp,
            'keperluan'     => $request->keperluan,
            'tujuan'        => $request->tujuan,
            'tgl_berangkat' => $request->tgl_mulai,
            'tgl_kembali'   => $request->tgl_selesai,
            'status'        => 'Proses',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $nama = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pengajuan', $nama, 'public');
            $data['pathFile_bukti'] = $path;
            $data['namaFile_bukti'] = $file->getClientOriginalName();
        }

        DB::table('pengajuanizin')->insert($data);

        return redirect()->route('personel.dashboard')->with('success', 'Pengajuan Izin berhasil dikirim!');
    }

    // EDIT (hanya Tidak Valid)
    public function edit($id, $tipe)
    {
        $table = $tipe === 'cuti' ? 'pengajuan_cuti' : 'pengajuanizin';
        $data  = DB::table($table)->where('id', $id)->first();

        if (!$data || $data->status !== 'Tidak Valid') {
            return redirect()->route('personel.dashboard')
                ->with('error', 'Tidak diizinkan mengedit pengajuan ini.');
        }

        return view('dashboard.personelpengajuan', compact('data', 'tipe'));
    }

    // UPDATE
    public function update(Request $request, $id, $tipe)
    {
        // Validasi sama seperti store
        $request->validate([            
            'jenis'      => 'required|in:cuti,izin',
            'keperluan'  => 'required|string|max:255',
            'tujuan'     => 'required|string|max:255',
            'tgl_mulai'  => 'required|date',
            'tgl_selesai'=> 'nullable|date|after_or_equal:tgl_mulai',
            'bukti'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048']);

        $table = $tipe === 'cuti' ? 'pengajuan_cuti' : 'pengajuanizin';
        $old   = DB::table($table)->where('id', $id)->first();

        if (!$old || $old->status !== 'Tidak Valid') {
            return redirect()->route('personel.dashboard')
                ->with('error', 'Tidak diizinkan!');
        }

        $data = ['tujuan' => $request->tujuan, 'updated_at' => now()];

        if ($tipe === 'cuti') {
            $data['catatan']    = $request->keperluan;
            $data['mulai_tgl']  = $request->tgl_mulai;
            $data['sampai_tgl'] = $request->tgl_selesai;
        } else {
            $data['keperluan']     = $request->keperluan;
            $data['tgl_berangkat'] = $request->tgl_mulai;
            $data['tgl_kembali']   = $request->tgl_selesai;
        }

        if ($request->hasFile('bukti')) {
            if ($old->pathFile_bukti) Storage::disk('public')->delete($old->pathFile_bukti);
            $file = $request->file('bukti');
            $nama = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pengajuan', $nama, 'public');
            $data['pathFile_bukti'] = $path;
            $data['namaFile_bukti'] = $file->getClientOriginalName();
        }

        DB::table($table)->where('id', $id)->update($data);

        return redirect()->route('personel.dashboard')
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }

    // KIRIM ULANG
    public function kirimUlang(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'tipe' => 'required|in:cuti,izin'
        ]);

        $table = $request->tipe === 'cuti' ? 'pengajuan_cuti' : 'pengajuanizin';

        $updated = DB::table($table)
            ->where('id', $request->id)
            ->where('status', 'Tidak Valid')
            ->update(['status' => 'Proses']);

        return response()->json(['success' => $updated > 0]);
    }
}