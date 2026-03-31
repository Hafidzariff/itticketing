@extends('layouts.app')

@section('content')

<div class="container mt-5">

<h4>Detail Laporan</h4>

<table class="table table-bordered">
    <tr>
        <th>Kode</th>
        <td>{{ $report->kode_laporan }}</td>
    </tr>
    <tr>
        <th>Nama</th>
        <td>{{ $report->nama_pelapor }}</td>
    </tr>
    <tr>
        <th>Departemen</th>
        <td>{{ $report->departemen }}</td>
    </tr>
    <tr>
        <th>Masalah</th>
        <td>{{ $report->jenis_masalah }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>
            <span class="badge bg-info">
                {{ $report->status }}
            </span>
        </td>
    </tr>
    <tr>
        <th>Catatan</th>
        <td>{{ $report->catatan_teknisi }}</td>
    </tr>
</table>

<a href="/cek-laporan" class="btn btn-secondary">
    Kembali
</a>

</div>

@endsection
