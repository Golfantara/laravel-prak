<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Transaksi;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function kategori()
    {
        $kategori = Kategori::all();
        return view('kategori', ['kategori' => $kategori]);
    }

    public function kategori_tambah()
    {
        return view('kategori_tambah');
    }

    public function kategori_aksi(Request $data)
    {
        $data->validate([
            'kategori' => 'required',
        ]);
        $kategori = $data->kategori;

        Kategori::insert([
            'kategori' => $kategori
        ]);

        return redirect('kategori')->with('sukses', 'kategor berhasil tersimpan');
    }

    public function kategori_edit($id)
    {
        $kategori = Kategori::find($id);

        return view('kategori_edit', ['kategori' => $kategori]);
    }

    public function kategori_update(Request $data, $id)
    {
        $data->validate([
            'kategori' => 'required'
        ]);

        $nama_kategori = $data->kategori;

        $kategori = Kategori::find($id);
        $kategori->kategori = $nama_kategori;
        $kategori->save();

        return redirect('kategori')->with('sukses', 'kategori berhasil diubah');
    }

   // membuat method untuk menghapus kategori
public function kategori_hapus($id)
{
    // Hapus transaksi berdasarkan id kategori yang dihapus
    Transaksi::where('kategori_id', $id)->delete();

    // Hapus kategori berdasarkan id yang dipilih
    $kategori = Kategori::find($id);
    $kategori->delete();

    return redirect('kategori')->with('sukses', 'Kategori Berhasil dihapus');
}


    public function transaksi()
    {
        // mengambil data transaksi
        $transaksi = Transaksi::orderBy('id', 'desc')->paginate(5);
        // passing data transaksi ke view transaksi.blade.php
        return view('transaksi', ['transaksi' => $transaksi]);
    }

    public function transaksi_tambah()
    {
        // mengambil data kategori
        $kategori = Kategori::all();

        // passing data kategori ke view transaksi_tambah.blade.php

        return view('transaksi_tambah', ['kategori' => $kategori]);
    }

    // untuk menjalankan input data transaksi
public function transaksi_aksi(Request $data)
{
    // validasi tanggal, jenis, kategori, nominal, wajib diisi
    $data->validate([
        'tanggal' => 'required',
        'jenis' => 'required',
        'kategori' => 'required',
        'nominal' => 'required'
    ]);

    // insert data ke tabel transaksi
    Transaksi::insert([
        'tanggal' => $data->tanggal,
        'jenis' => $data->jenis,
        'kategori_id' => $data->kategori,
        'nominal' => $data->nominal,
        'keterangan' => $data->keterangan
    ]);

    // alihkan halaman ke halaman transaksi sambil mengirim session pesan notifikasi
    return redirect('transaksi')->with('sukses', 'Transaksi Berhasil tersimpan');
}


public function transaksi_edit($id)
{
// mengambil data kategori
$kategori = Kategori :: all();

// mengambil data transaksi berdasarkan id
$transaksi = Transaksi :: find($id);

//Passing data kategori dan transaksi ke view edit.blade.php

return view('transaksi_edit', ['kategori' => $kategori, 'transaksi' => $transaksi]);
}

public function transaksi_update(Request $data, $id)
{
    // validasi semua kolom pada form wajib diisi
    $data->validate([
        'tanggal' => 'required',
        'jenis' => 'required',
        'kategori' => 'required',
        'nominal' => 'required'
    ]);

    // ambil data transaksi
    $transaksi = Transaksi::find($id);

    // ubah data tanggal, jenis, kategori, nominal, keterangan
    $transaksi->tanggal = $data->tanggal;
    $transaksi->jenis = $data->jenis;
    $transaksi->kategori_id = $data->kategori;
    $transaksi->nominal = $data->nominal;
    $transaksi->keterangan = $data->keterangan;

    // simpan perubahan
    $transaksi->save();

    // alihkan halaman ke halaman transaksi sambil mengirim session pesan notifikasi
    return redirect('transaksi')->with('sukses', 'Transaksi berhasil diubah');
}

public function transaksi_hapus($id)
{
// Ambil data transaksi berdasarkan id, kemudian hapus
$transaksi = Transaksi::find($id);
$transaksi->delete();

// alihkan halam kehalaman transaksi sambil mengirimkan pesan notifikasi

return redirect('transaksi')->with("sukses", "Transaksi Berhasil dihapus");
}

public function transaksi_cari(Request $data)
{
    // keyword pencarian
    $cari = $data->cari;

    // mengambil data transaksi
    $transaksi = Transaksi::where(function ($query) use ($cari) {
        $query->where('jenis', 'like', "%" . $cari . "%")
              ->orWhere('tanggal', 'like', "%" . $cari . "%")
              ->orWhere('keterangan', 'like', "%" . $cari . "%")
              ->orWhere('nominal', '=', $cari);
    })->orderBy('id', 'desc')->paginate(5);

    // menambah keyword pencarian ke data transaksi
    $transaksi->appends($data->only('cari'));

    // passing data transaksi ke view transaksi.blade.php
    return view('transaksi', ['transaksi' => $transaksi]);
}

public function laporan()
{
    $kategori = kategori::all();

    return view('laporan', ['kategori' => $kategori]);
}

public function laporan_hasil(Request $req)
{
    $req->validate([
        'dari' => 'required',
        'sampai' => 'required'
    ]);

    // data kategori
    $kategori = Kategori::all();

    // data filter
    $dari = $req->dari;
    $sampai = $req->sampai;
    $id_kategori = $req->kategori;

    if ($id_kategori == "semua") {
        // jika semua, tampilkan semua transaksi
        $laporan = Transaksi::whereDate('tanggal', '>=', $dari)
                            ->whereDate('tanggal', '<=', $sampai)
                            ->orderBy('id', 'desc')
                            ->get();
    } else {
        // jika yang dipilih bukan semua
        // tampilkan transaksi berdasarkan kategori yang dipilih
        $laporan = Transaksi::where('kategori_id', $id_kategori)
                            ->whereDate('tanggal', '>=', $dari)
                            ->whereDate('tanggal', '<=', $sampai)
                            ->orderBy('id', 'desc')
                            ->get();
    }

    // passing data laporan ke view laporan_hasil
    return view('laporan_hasil', [
        'laporan' => $laporan,
        'kategori' => $kategori,
        'dari' => $dari,
        'sampai' => $sampai,
        'kat' => $id_kategori
    ]);
}


}
