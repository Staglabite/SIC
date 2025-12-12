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
        $nrp = Auth::guard('personel')->user()->nrp;

        // Ambil data personel sekali saja
        $personel = DB::table('personel')
            ->where('nrp', $nrp)
            ->select('nrp', 'name as nama_personel', 'pangkat', 'jabatan', 'golongan')
            ->first();

        // CUTI
        $cuti = DB::table('pengajuan_cuti')
            ->leftJoin('cuti', 'pengajuan_cuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->where('pengajuan_cuti.personel_id', $nrp)
            ->select([
                'pengajuan_cuti.id',
                DB::raw("'cuti' as jenis"),
                'cuti.jenis_cuti as nama_jenis',
                'pengajuan_cuti.catatan as keterangan',
                'pengajuan_cuti.tujuan',
                'pengajuan_cuti.pergi_dari',
                'pengajuan_cuti.transportasi',
                'pengajuan_cuti.pengikut',
                'pengajuan_cuti.catatan as catatan', // tambahan
                'pengajuan_cuti.mulai_tgl as tanggal_mulai',
                'pengajuan_cuti.sampai_tgl as tanggal_selesai',
                'pengajuan_cuti.status',
                'pengajuan_cuti.pathFile_bukti',
                'pengajuan_cuti.namaFile_bukti',
                'pengajuan_cuti.created_at',
                // Data personel (sama untuk semua baris)
                DB::raw("'{$personel->nama_personel}' as nama_personel"),
                DB::raw("'{$personel->nrp}' as nrp"),
                DB::raw("'{$personel->pangkat}' as pangkat"),
                DB::raw("'{$personel->jabatan}' as jabatan"),
                DB::raw("'{$personel->golongan}' as golongan"),
            ]);

        // IZIN
        $izin = DB::table('pengajuanizin')
            ->where('pengajuanizin.personel_id', $nrp)
            ->select([
                'pengajuanizin.id',
                DB::raw("'izin' as jenis"),
                DB::raw("'IZIN' as nama_jenis"),
                'pengajuanizin.keperluan as keterangan',
                'pengajuanizin.tujuan',
                'pengajuanizin.pergi_dari',
                'pengajuanizin.transportasi',
                'pengajuanizin.pengikut',
                'pengajuanizin.catatan', // pastikan kolom ini ada di tabel pengajuanizin
                'pengajuanizin.tgl_berangkat as tanggal_mulai',
                'pengajuanizin.tgl_kembali as tanggal_selesai',
                'pengajuanizin.status',
                'pengajuanizin.pathFile_bukti',
                'pengajuanizin.namaFile_bukti',
                'pengajuanizin.created_at',
                // Data personel
                DB::raw("'{$personel->nama_personel}' as nama_personel"),
                DB::raw("'{$personel->nrp}' as nrp"),
                DB::raw("'{$personel->pangkat}' as pangkat"),
                DB::raw("'{$personel->jabatan}' as jabatan"),
                DB::raw("'{$personel->golongan}' as golongan"),
            ]);

        $riwayat = $cuti->unionAll($izin)->orderByDesc('created_at')->get();

        return view('dashboard.personel', compact('riwayat'));
    }

    // FORM PENGAJUAN BARU
    public function create()
    {
        return view('dashboard.personelpengajuan');
    }

    public function storeCuti(Request $request)
    {
        $request->validate([
            'kode_cuti'     => 'required|exists:cuti,kode_cuti',
            'catatan'       => 'required|string|max:1000',
            'tujuan'        => 'required|string|max:255',
            'pergi_dari'    => 'required|string|max:255',        // WAJIB
            'transportasi'  => 'required|string|max:255',        // WAJIB
            'pengikut'      => 'nullable|string|max:255',
            'mulai_tgl'     => 'required|date',
            'sampai_tgl'    => 'nullable|date|after_or_equal:mulai_tgl',
            'bukti'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $nrp = Auth::guard('personel')->user()->nrp;

        // Ambil kode_satker dari personel
        $kode_satker = DB::table('personel')->where('nrp', $nrp)->value('kode_satker');

        // Cari Renmin & Pimpinan di satker yang sama
        $kode_renmin   = DB::table('renmin')->where('kode_satker', $kode_satker)->value('kode_renmin')
                        ?? DB::table('renmin')->value('kode_renmin') ?? 1;

        $kode_pimpinan = DB::table('pimpinan')->where('kode_satker', $kode_satker)->value('kode_pimpinan')
                        ?? DB::table('pimpinan')->value('kode_pimpinan') ?? 1;

        $data = [
            'personel_id'   => $nrp,
            'kode_cuti'     => $request->kode_cuti,
            'catatan'       => $request->catatan,
            'tujuan'        => $request->tujuan,
            'pergi_dari'    => $request->pergi_dari,          // TAMBAHAN
            'transportasi'  => $request->transportasi,        // TAMBAHAN
            'pengikut'      => $request->pengikut ?? null,    // TAMBAHAN
            'mulai_tgl'     => $request->mulai_tgl,
            'sampai_tgl'    => $request->sampai_tgl,
            'renmin_id'     => $kode_renmin,
            'pimpinan_id'   => $kode_pimpinan,
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

        DB::table('pengajuan_cuti')->insert($data);

        return redirect()->route('personel.dashboard')
            ->with('success', 'Pengajuan Cuti berhasil dikirim!');
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'keperluan'     => 'required|string|max:1000',
            'tujuan'        => 'required|string|max:255',
            'pergi_dari'    => 'required|string|max:255',
            'transportasi'  => 'required|string|max:255',
            'pengikut'      => 'nullable|string|max:255',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali'   => 'nullable|date|after_or_equal:tgl_berangkat',
            'bukti'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $nrp = Auth::guard('personel')->user()->nrp;

        // Ambil kode_satker dari personel
        $kode_satker = DB::table('personel')->where('nrp', $nrp)->value('kode_satker');

        // Cari Renmin & Pimpinan di satker yang sama
        $kode_renmin = DB::table('renmin')
            ->where('kode_satker', $kode_satker)
            ->value('kode_renmin') ?? DB::table('renmin')->value('kode_renmin') ?? 1;

        $kode_pimpinan = DB::table('pimpinan')
            ->where('kode_satker', $kode_satker)
            ->value('kode_pimpinan') ?? DB::table('pimpinan')->value('kode_pimpinan') ?? 1;

        $data = [
            'personel_id'   => $nrp,
            'keperluan'     => $request->keperluan,
            'tujuan'        => $request->tujuan,
            'pergi_dari'    => $request->pergi_dari,
            'transportasi'  => $request->transportasi,
            'pengikut'      => $request->pengikut ?? null,
            'tgl_berangkat' => $request->tgl_berangkat,
            'tgl_kembali'   => $request->tgl_kembali,
            'renmin_id'     => $kode_renmin,
            'pimpinan_id'   => $kode_pimpinan,        // INI YANG KURANG TADI!
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

        return redirect()->route('personel.dashboard')
            ->with('success', 'Pengajuan Izin berhasil dikirim!');
    }

    // EDIT — hanya butuh $id saja
    public function edit($id)
    {
        // Cari di kedua tabel
        $cuti = DB::table('pengajuan_cuti')->where('id', $id)->first();
        if ($cuti) {
            $data = $cuti;
            $data->jenis = 'cuti';
            $data->nama_jenis = DB::table('cuti')->where('kode_cuti', $data->kode_cuti)->value('jenis_cuti');
            $data->keterangan = $data->catatan;
            $data->tanggal_mulai = $data->mulai_tgl;
            $data->tanggal_selesai = $data->sampai_tgl;
        } else {
            $izin = DB::table('pengajuanizin')->where('id', $id)->first();
            if (!$izin) abort(404);
            $data = $izin;
            $data->jenis = 'izin';
            $data->nama_jenis = 'IZIN';
            $data->keterangan = $data->keperluan;
            $data->tanggal_mulai = $data->tgl_berangkat;
            $data->tanggal_selesai = $data->tgl_kembali;
        }

        // Hanya boleh edit kalau status "Tidak Valid" atau "Proses" (sesuai kebijakanmu)
        if (!in_array($data->status, ['Tidak Valid', 'Proses'])) {
            return redirect()->route('personel.dashboard')->with('error', 'Tidak diizinkan mengedit.');
        }

        return view('dashboard.personelpengajuan', compact('data'));
    }

    // UPDATE — VERSI BARU YANG COCOK DENGAN MODAL AJAX
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan'     => 'required|string|max:1000',
            'tujuan'         => 'required|string|max:255',
            'pergi_dari'     => 'required|string|max:255',
            'transportasi'   => 'required|string|max:255',
            'pengikut'       => 'nullable|string|max:500',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan'        => 'nullable|string|max:1000',
            'file413'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Cari pengajuan di kedua tabel
        $cuti = DB::table('pengajuan_cuti')->where('id', $id)->first();
        $izin = DB::table('pengajuanizin')->where('id', $id)->first();

        if ($cuti) {
            $table = 'pengajuan_cuti';
            $old   = $cuti;
            $jenis = 'cuti';
        } elseif ($izin) {
            $table = 'pengajuanizin';
            $old   = $izin;
            $jenis = 'izin';
        } else {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Hanya boleh edit kalau status Tidak Valid / Proses
        if (!in_array($old->status, ['Tidak Valid', 'Proses'])) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan mengedit pengajuan ini'], 403);
        }

        // Pastikan milik user yang login
        if ($old->personel_id !== Auth::guard('personel')->user()->nrp) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Data yang akan diupdate
        $updateData = [
            'tujuan'       => $request->tujuan,
            'pergi_dari'   => $request->pergi_dari,
            'transportasi' => $request->transportasi,
            'pengikut'     => $request->pengikut,
            'catatan'      => $request->catatan,
            'updated_at'   => now(),
        ];

        if ($jenis === 'cuti') {
            $updateData['catatan']     = $request->keterangan;           // di cuti pakai kolom catatan
            $updateData['mulai_tgl']   = $request->tanggal_mulai;
            $updateData['sampai_tgl']  = $request->tanggal_selesai;
        } else {
            $updateData['keperluan']       = $request->keterangan;       // di izin pakai kolom keperluan
            $updateData['tgl_berangkat']   = $request->tanggal_mulai;
            $updateData['tgl_kembali']     = $request->tanggal_selesai;
        }

        // Handle file bukti baru
        if ($request->hasFile('file_bukti')) {
            if ($old->pathFile_bukti) {
                Storage::disk('public')->delete($old->pathFile_bukti);
            }
            $file = $request->file('file_bukti');
            $nama = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pengajuan', $nama, 'public');
            $updateData['pathFile_bukti'] = $path;
            $updateData['namaFile_bukti'] = $file->getClientOriginalName();
        }

        DB::table($table)->where('id', $id)->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil diperbarui!'
        ]);
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