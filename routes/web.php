<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/kategori', [HomeController::class, 'kategori']);
Route::get('/kategori/tambah', [HomeController::class, 'kategori_tambah']);
Route::get('/kategori/edit/{id}', [HomeController::class, 'kategori_edit']);
Route::get('/kategori/hapus/{id}', [HomeController::class, 'kategori_hapus']);

Route::get('/transaksi', [HomeController::class, 'transaksi']);
Route::get('/transaksi/tambah', [HomeController::class, 'transaksi_tambah']);
Route::get('/transaksi/edit/{id}', [HomeController::class, 'transaksi_edit']);
Route::get('/transaksi/hapus/{id}', [HomeController::class, 'transaksi_hapus']);
Route::get('/transaksi/cari', [HomeController::class, 'transaksi_cari']);

Route::get('/laporan', [HomeController::class, 'laporan']);
Route::get('/laporan/hasil', [HomeController::class, 'laporan_hasil']);

Route::post('/kategori/aksi', [HomeController::class, 'kategori_aksi']);
Route::put('/kategori/update/{id}', [HomeController::class, 'kategori_update']);

Route::post('/transaksi/aksi', [HomeController::class, 'transaksi_aksi']);
Route::put('/transaksi/update/{id}',[HomeController::class, 'transaksi_update']);
