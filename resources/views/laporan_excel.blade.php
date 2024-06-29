<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Cetak Laporan Keuangan</title>
<style type="text/css">
table {
    border-collapse: collapse;
}

table th, table td {
    border: 1px solid black;
    text-align: center;
}

@media print { 
    @page { 
        size: landscape; 
    } 
}
</style>
</head>
<body>
<center>
    <h4>LAPORAN TRANSAKSI KEUANGAN</h4>
</center>

<?php
$dari = $_GET['dari'];
$sampai = $_GET['sampai'];
$kat = $_GET['kategori'];
?>

<table>
<thead>
<tr>
    <th rowspan="2" width="11%">Tanggal</th>
    <th rowspan="2" width="5%">Jenis</th>
    <th rowspan="2">Keterangan</th>
    <th rowspan="2">Kategori</th>
    <th colspan="2">Transaksi</th>
</tr>
<tr>
    <th>Pemasukan</th>
    <th>Pengeluaran</th>
</tr>
</thead>
<tbody>
@php
$total_pemasukan = 0;
$total_pengeluaran = 0;
@endphp

@foreach ($laporan as $t)
<tr>
    <td>{{ date('d-m-Y', strtotime($t->tanggal)) }}</td>
    <td>{{ $t->jenis }}</td>
    <td>{{ $t->keterangan }}</td>
    <td>{{ $t->kategori->kategori }}</td>
    <td>
        @if ($t->jenis == "Pemasukan")
            {{ "RP. ".number_format($t->nominal).",-" }}
            @php $total_pemasukan += $t->nominal; @endphp
        @endif
    </td>
    <td>
        @if ($t->jenis == "Pengeluaran")
            {{ "RP. ".number_format($t->nominal).",-" }}
            @php $total_pengeluaran += $t->nominal; @endphp
        @endif
    </td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr>
    <td colspan="4">TOTAL</td>
    <td>{{ "RP. ".number_format($total_pemasukan).",-" }}</td>
    <td>{{ "RP. ".number_format($total_pengeluaran).",-" }}</td>
</tr>
</tfoot>
</table>

<script type="text/javascript">
window.print();
</script>
</body>
</html>
