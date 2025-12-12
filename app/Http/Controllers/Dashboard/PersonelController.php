<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonelController extends Controller
{
    // Di controller

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
        $cuti = DB::table('pengajuancuti')
            ->leftJoin('cuti', 'pengajuancuti.kode_cuti', '=', 'cuti.kode_cuti')
            ->where('pengajuancuti.personel_id', $nrp)
            ->select([
                'pengajuancuti.id',
                DB::raw("'cuti' as jenis"),
                'cuti.jenis_cuti as nama_jenis',
                'pengajuancuti.catatan as keterangan',
                'pengajuancuti.tujuan',
                'pengajuancuti.pergi_dari',
                'pengajuancuti.transportasi',
                'pengajuancuti.pengikut',
                'pengajuancuti.catatan as catatan', // tambahan
                'pengajuancuti.mulai_tgl as tanggal_mulai',
                'pengajuancuti.sampai_tgl as tanggal_selesai',
                'pengajuancuti.status',
                'pengajuancuti.pathFile_bukti',
                'pengajuancuti.namaFile_bukti',
                'pengajuancuti.created_at',
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
        $nrp = Auth::guard('personel')->user()->nrp;

        // // Cek apakah sudah pernah buat pengajuan hari ini
        // $today = now()->toDateString(); // format: YYYY-MM-DD

        // $sudahAda = DB::table('pengajuancuti')
        //         ->where('personel_id', $nrp)
        //         ->whereDate('created_at', $today)
        //         ->exists()
        //     ||
        //     DB::table('pengajuanizin')
        //         ->where('personel_id', $nrp)
        //         ->whereDate('created_at', $today)
        //         ->exists();

        $mulai = $request->mulai_tgl;
        $sampai = $request->sampai_tgl;

        $bentrok = DB::table('pengajuancuti')
            ->where('personel_id', $nrp)
            ->whereIn('status', ['Proses', 'Disetujui'])
            ->where(function($q) use ($mulai, $sampai) {
                $q->where('mulai_tgl', '<=', $sampai)
                ->where('sampai_tgl', '>=', $mulai);
            })
            ->exists()
        ||
        DB::table('pengajuanizin')
            ->where('personel_id', $nrp)
            ->where(function($q) use ($mulai, $sampai) {
                $q->where('tgl_berangkat', '<=', $sampai)
                ->where('tgl_kembali', '>=', $mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Tanggal pengajuan bertabrakan dengan pengajuan lain. Tidak dapat membuat pengajuan.');
        }

        // if ($sudahAda) {
        //     return redirect()->back()->with('error', 'Anda sudah membuat pengajuan hari ini. Tunggu besok untuk membuat pengajuan baru.');
        // }

        $request->validate([
            'kode_cuti'     => 'required|exists:cuti,kode_cuti',
            'catatan'       => 'nullable|string|max:255',
            'tujuan'        => 'required|string|max:20',
            'pergi_dari'    => 'required|string|max:20',        // WAJIB
            'transportasi'  => 'required|string|max:255',        // WAJIB
            'pengikut'      => 'nullable|string|max:255',
            'mulai_tgl'     => 'required|date|after_or_equal:today',
            'sampai_tgl'    => 'required|date|after_or_equal:mulai_tgl',
            'bukti'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
            $extension = $file->getClientOriginalExtension();
            $newFilename = $nrp . '_buktipendukung.' . $extension;
            $path = $file->storeAs('bukti_pengajuan', $newFilename, 'public');
            $data['pathFile_bukti'] = $path;
            $data['namaFile_bukti'] = $newFilename; // Simpan nama file baru
        }

        DB::table('pengajuancuti')->insert($data);

        return redirect()->route('personel.dashboard')
            ->with('success', 'Pengajuan Cuti berhasil dikirim!');
    }

    public function storeIzin(Request $request)
    {
        $nrp = Auth::guard('personel')->user()->nrp;

        // Cek apakah sudah pernah buat pengajuan hari ini
        $today = now()->toDateString();

        // $sudahAda = DB::table('pengajuancuti')
        //         ->where('personel_id', $nrp)
        //         ->whereDate('created_at', $today)
        //         ->exists()
        //     ||
        //     DB::table('pengajuanizin')
        //         ->where('personel_id', $nrp)
        //         ->whereDate('created_at', $today)
        //         ->exists();


        // if ($sudahAda) {
        //     return redirect()->back()->with('error', 'Anda sudah membuat pengajuan hari ini. Tunggu besok untuk membuat pengajuan baru.');
        // }

        $mulai = $request->tgl_berangkat;
        $sampai = $request->tgl_kembali;

        // CEK BENTROK
        $bentrok = DB::table('pengajuancuti')
            ->where('personel_id', $nrp)
            ->whereIn('status', ['Proses', 'Disetujui'])
            ->where(function($q) use ($mulai, $sampai) {
                $q->where('mulai_tgl', '<=', $sampai)
                ->where('sampai_tgl', '>=', $mulai);
            })
            ->exists()
        ||
        DB::table('pengajuanizin')
            ->where('personel_id', $nrp)
            ->where(function($q) use ($mulai, $sampai) {
                $q->where('tgl_berangkat', '<=', $sampai)
                ->where('tgl_kembali', '>=', $mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Tanggal pengajuan bertabrakan dengan pengajuan lain. Tidak dapat membuat pengajuan.');
        }

        $request->validate([
            'keperluan'     => 'required|string|max:1000',
            'tujuan'        => 'required|string|max:255',
            'pergi_dari'    => 'required|string|max:255',
            'transportasi'  => 'required|string|max:255',
            'pengikut'      => 'nullable|string|max:255',
            'tgl_berangkat' => 'required|date|after_or_equal:today',
            'tgl_kembali'   => 'required|date|after_or_equal:tgl_berangkat',
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
            'pimpinan_id'   => $kode_pimpinan,
            'status'        => 'Proses',
            'created_at'    => now(),
            'updated_at'    => now(),
        ];

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $extension = $file->getClientOriginalExtension();
            $newFilename = $nrp . '_buktipendukung.' . $extension;
            $path = $file->storeAs('bukti_pengajuan', $newFilename, 'public');
            $data['pathFile_bukti'] = $path;
            $data['namaFile_bukti'] = $newFilename; // Simpan nama file baru
        }

        DB::table('pengajuanizin')->insert($data);

        return redirect()->route('personel.dashboard')
            ->with('success', 'Pengajuan Izin berhasil dikirim!');
    }

    // EDIT — hanya butuh $id saja
    public function edit($id)
    {
        // Cari di kedua tabel
        $cuti = DB::table('pengajuancuti')->where('id', $id)->first();
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
        $cuti = DB::table('pengajuancuti')->where('id', $id)->first();
        $izin = DB::table('pengajuanizin')->where('id', $id)->first();

        if ($cuti) {
            $table = 'pengajuancuti';
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
            $nrp = Auth::guard('personel')->user()->nrp;
            
            if ($old->pathFile_bukti) {
                Storage::disk('public')->delete($old->pathFile_bukti);
            }
            
            $file = $request->file('file_bukti');
            $extension = $file->getClientOriginalExtension();
            $newFilename = $nrp . '_buktipendukung.' . $extension;
            $path = $file->storeAs('bukti_pengajuan', $newFilename, 'public');
            $updateData['pathFile_bukti'] = $path;
            $updateData['namaFile_bukti'] = $newFilename;
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

        $table = $request->tipe === 'cuti' ? 'pengajuancuti' : 'pengajuanizin';

        $updated = DB::table($table)
            ->where('id', $request->id)
            ->where('status', 'Tidak Valid')
            ->update(['status' => 'Proses']);

        return response()->json(['success' => $updated > 0]);
    }

    public function downloadSurat($id)
    {
        try {
            // 1. Authentication check
            if (!Auth::guard('personel')->check()) {
                abort(403, 'Silakan login sebagai personel');
            }
            
            $nrp = Auth::guard('personel')->user()->nrp;
            
            // 2. Query data
            $cuti = DB::table('pengajuancuti')
                ->leftJoin('cuti', 'pengajuancuti.kode_cuti', '=', 'cuti.kode_cuti')
                ->leftJoin('personel', 'personel.nrp', '=', 'pengajuancuti.personel_id')
                ->where('pengajuancuti.id', $id)
                ->where('pengajuancuti.personel_id', $nrp)
                ->select('pengajuancuti.*', 'cuti.jenis_cuti as nama_jenis', 'personel.*')
                ->first();
                
            $izin = DB::table('pengajuanizin')
                ->leftJoin('personel', 'personel.nrp', '=', 'pengajuanizin.personel_id')
                ->where('pengajuanizin.id', $id)
                ->where('pengajuanizin.personel_id', $nrp)
                ->select('pengajuanizin.*', DB::raw("'IZIN JALAN' as nama_jenis"), 'personel.*')
                ->first();
                
            if (!$cuti && !$izin) {
                abort(404, 'Data tidak ditemukan');
            }
            
            $pengajuan = $cuti ?? $izin;
            $jenis = $cuti ? 'cuti' : 'izin';
            
            if ($pengajuan->status !== 'Disetujui') {
                abort(403, 'Status belum disetujui');
            }
            
            // 3. Generate PDF
            $view = $jenis === 'cuti' ? 'pdf.surat-cuti' : 'pdf.surat-izin';
            
            $pdf = PDF::loadView($view, compact('pengajuan'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'dejavu sans',
                    'isHtml5ParserEnabled' => true
                ]);
                
            return $pdf->download("Surat_{$jenis}_{$pengajuan->nrp}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('PDF Error: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}