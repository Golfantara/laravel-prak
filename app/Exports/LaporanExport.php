<?php

namespace App\Exports;

use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    public function view(): View
    {
        // Data kategori
        $kategori = Kategori::all();

        // Data filter
        $dari = $_GET['dari'];
        $sampai = $_GET['sampai'];
        $id_kategori = $_GET['kategori'];

        // Periksa kategori yang dipilih
        if ($id_kategori == "semua") {
            // Jika semua, tampilkan semua transaksi
            $laporan = Transaksi::whereDate('tanggal', '>=', $dari)
                                ->whereDate('tanggal', '<=', $sampai)
                                ->orderBy('id', 'desc')
                                ->get();
        } else {
            // Jika yang dipilih bukan semua, tampilkan transaksi kategori yang dipilih
            $laporan = Transaksi::where('kategori_id', $id_kategori)
                                ->whereDate('tanggal', '>=', $dari)
                                ->whereDate('tanggal', '<=', $sampai)
                                ->orderBy('id', 'desc')
                                ->get();
        }

        // Passing data laporan ke view laporan
        return view('laporan_excel', [
            'laporan' => $laporan,
            'kategori' => $kategori,
            'dari' => $dari,
            'sampai' => $sampai,
            'kat' => $id_kategori
        ]);
    }
}
