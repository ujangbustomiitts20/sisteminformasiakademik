<?php

namespace App\Http\Controllers;

use App\Models\AkunBank;
use App\Models\MutasiBank;
use Illuminate\Http\Request;

class AkunBankController extends Controller
{
    public function index()
    {
        $akunBank = AkunBank::withCount(['mutasiBank', 'rekonsiliasi'])
            ->orderBy('nama_bank')
            ->paginate(15);

        $stats = [
            'total' => AkunBank::count(),
            'aktif' => AkunBank::active()->count(),
            'total_saldo' => AkunBank::active()->sum('saldo_sistem'),
        ];

        return view('keuangan.rekonsiliasi.akun-bank.index', compact('akunBank', 'stats'));
    }

    public function create()
    {
        return view('keuangan.rekonsiliasi.akun-bank.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50|unique:akun_bank,nomor_rekening',
            'nama_rekening' => 'required|string|max:150',
            'cabang' => 'nullable|string|max:100',
            'kode_bank' => 'nullable|string|max:20',
            'tipe' => 'required|in:penampungan,operasional,beasiswa',
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        $akunBank = AkunBank::create([
            'nama_bank' => $request->nama_bank,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_rekening' => $request->nama_rekening,
            'cabang' => $request->cabang,
            'kode_bank' => $request->kode_bank,
            'tipe' => $request->tipe,
            'saldo_awal' => $request->saldo_awal,
            'saldo_sistem' => $request->saldo_awal,
            'is_active' => true,
        ]);

        return redirect()->route('akun-bank.index')
            ->with('success', 'Akun bank berhasil ditambahkan');
    }

    public function show(AkunBank $akunBank)
    {
        // Get latest 50 mutasi manually (avoid window functions for MariaDB compatibility)
        $recentMutasi = MutasiBank::where('akun_bank_id', $akunBank->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'total_kredit' => $akunBank->mutasiBank()->kredit()->sum('nominal'),
            'total_debit' => $akunBank->mutasiBank()->debit()->sum('nominal'),
            'total_mutasi' => $akunBank->mutasiBank()->count(),
            'matched' => $akunBank->mutasiBank()->where('status', 'matched')->count(),
            'pending' => $akunBank->mutasiBank()->where('status', 'pending')->count(),
            'unmatched' => $akunBank->mutasiBank()->unmatched()->count(),
            'manual' => $akunBank->mutasiBank()->where('status', 'manual')->count(),
        ];

        // Set relation manually
        $akunBank->setRelation('mutasiBank', $recentMutasi);

        return view('keuangan.rekonsiliasi.akun-bank.show', compact('akunBank', 'stats'));
    }

    public function edit(AkunBank $akunBank)
    {
        return view('keuangan.rekonsiliasi.akun-bank.edit', compact('akunBank'));
    }

    public function update(Request $request, AkunBank $akunBank)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:50|unique:akun_bank,nomor_rekening,' . $akunBank->id,
            'nama_rekening' => 'required|string|max:150',
            'cabang' => 'nullable|string|max:100',
            'kode_bank' => 'nullable|string|max:20',
            'tipe' => 'required|in:penampungan,operasional,beasiswa',
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        $akunBank->update($request->only([
            'nama_bank', 'nomor_rekening', 'nama_rekening', 
            'cabang', 'kode_bank', 'tipe', 'saldo_awal'
        ]));

        // Recalculate saldo sistem
        $akunBank->updateSaldoSistem();

        return redirect()->route('akun-bank.index')
            ->with('success', 'Akun bank berhasil diperbarui');
    }

    public function destroy(AkunBank $akunBank)
    {
        // Cek apakah ada mutasi
        if ($akunBank->mutasiBank()->exists()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun bank yang sudah memiliki mutasi');
        }

        $akunBank->delete();

        return redirect()->route('akun-bank.index')
            ->with('success', 'Akun bank berhasil dihapus');
    }

    public function toggleStatus(AkunBank $akunBank)
    {
        $akunBank->is_active = !$akunBank->is_active;
        $akunBank->save();

        $status = $akunBank->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Akun bank berhasil {$status}");
    }
}
