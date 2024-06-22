@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    Tambah Transaksi
                    <a href="{{ url('/transaksi') }}" class="float-right btn btn-sm btn-primary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ url('/transaksi/aksi') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label>Tanggal transaksi</label>
                            <input type="date" name="tanggal" class="form-control">
                            @if ($errors->has('tanggal'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('tanggal') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Jenis</label>
                            <select class="form-control" name="jenis">
                                <option value="">Pilihan Jenis</option>
                                <option value="Pemasukan">Pemasukan</option>
                                <option value="Pengeluaran">Pengeluaran</option>
                            </select>
                            @if ($errors->has('jenis'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('jenis') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-control" name="kategori">
                                <option value="">Pilihan Kategori</option>
                                @foreach ($kategori as $k)
                                    <option value="{{ $k->id }}">{{ $k->kategori }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('kategori'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('kategori') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Nominal</label>
                            <input type="number" name="nominal" class="form-control">
                            @if ($errors->has('nominal'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('nominal') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                            @if ($errors->has('keterangan'))
                                <span class="text-danger">
                                    <strong>{{ $errors->first('keterangan') }}</strong>
                                </span>
                            @endif
                        </div>

                        <input type="submit" class="btn btn-primary" value="Simpan">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection