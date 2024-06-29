<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    $tanggal_hari_ini = date('Y-m-d');
    $bulan_ini = date('m');
    $tahun_ini = date('Y');

    $pemasukan_hari_ini = Transaksi::where('jenis', 'Pemasukan')
        ->whereDate('tanggal', $tanggal_hari_ini)
        ->sum('nominal');

    $pemasukan_bulan_ini = Transaksi::where('jenis', 'Pemasukan')
        ->whereMonth('tanggal', $bulan_ini)
        ->sum('nominal');

    $pemasukan_tahun_ini = Transaksi::where('jenis', 'Pemasukan')
        ->whereYear('tanggal', $tahun_ini)
        ->sum('nominal');

    $seluruh_pemasukan = Transaksi::where('jenis', 'Pemasukan')
        ->sum('nominal');

    $pengeluaran_hari_ini = Transaksi::where('jenis', 'Pengeluaran')
        ->whereDate('tanggal', $tanggal_hari_ini)
        ->sum('nominal');

    $pengeluaran_bulan_ini = Transaksi::where('jenis', 'Pengeluaran')
        ->whereMonth('tanggal', $bulan_ini)
        ->sum('nominal');

    $pengeluaran_tahun_ini = Transaksi::where('jenis', 'Pengeluaran')
        ->whereYear('tanggal', $tahun_ini)
        ->sum('nominal');

    $seluruh_pengeluaran = Transaksi::where('jenis', 'Pengeluaran')
        ->sum('nominal');

    return view('home', [
        'pemasukan_hari_ini' => $pemasukan_hari_ini,
        'pemasukan_bulan_ini' => $pemasukan_bulan_ini,
        'pemasukan_tahun_ini' => $pemasukan_tahun_ini,
        'seluruh_pemasukan' => $seluruh_pemasukan,
        'pengeluaran_hari_ini' => $pengeluaran_hari_ini,
        'pengeluaran_bulan_ini' => $pengeluaran_bulan_ini,
        'pengeluaran_tahun_ini' => $pengeluaran_tahun_ini,
        'seluruh_pengeluaran' => $seluruh_pengeluaran
    ]);
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

public function laporan_print(Request $req)
{
    $req->validate([
        'dari' => 'required',
        'sampai' => 'required'
    ]);

    // mengambil data kategori
    $kategori = Kategori::all();

    // data filter
    $dari = $req->dari;
    $sampai = $req->sampai;
    $id_kategori = $req->kategori;

    // periksa kategori yang dipilih
    if ($id_kategori == "semua") {
        // jika semua, tampilkan semua transaksi
        $laporan = Transaksi::whereDate('tanggal', '>=', $dari)
                            ->whereDate('tanggal', '<=', $sampai)
                            ->orderBy('id', 'desc')
                            ->get();
    } else {
        // jika yang dipilih bukan semua, tampilkan transaksi kategori yang dipilih
        $laporan = Transaksi::where('kategori_id', $id_kategori)
                            ->whereDate('tanggal', '>=', $dari)
                            ->whereDate('tanggal', '<=', $sampai)
                            ->orderBy('id', 'desc')
                            ->get();
    }

    // passing data laporan ke view
    return view('laporan_print', [
        'laporan' => $laporan,
        'kategori' => $kategori,
        'dari' => $dari,
        'sampai' => $sampai,
        'kat' => $id_kategori
    ]);
}

    public function laporan_excel()
    {
        return Excel::download(new LaporanExport, 'laporan.xlsx');
    }

    public function ganti_password()
    {
        return view('ganti_password');
    }

    public function ganti_password_aksi(Request $request)
 {
 // periksa apakah inputan password sekarang ('current-password') sesusai dengan password sekarang
 if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
 // jika tidak sesuai, alihkan halaman kembali ke form ganti password
 // sambil mengirimkan pemberitahuan bahwa password tidak sesuai
 return redirect()->back()->with("error", "Password sekarang tidak sesuai");
 }
 // periksa jika password baru sama dengan password sekarang
 if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
 //jika password baru yang di inputkan sama dengan password lama
 return redirect()->back()->with("error", "Password baru tidak boleh sama dengan password sekarang");
 }
 // membuat form validasi
 $validateData = $request->validate([
 'current-password' => 'required',
 'new-password' => 'required|string|min:6|confirmed'
 ]);
 // ganti password user yang sedang login dengan password baru
 $user = Auth::user();
 $user->password = bcrypt($request->get('new-password'));
 $user->save();
 return redirect()->back()->with("success", "Password berhasil diganti");
 }


}
