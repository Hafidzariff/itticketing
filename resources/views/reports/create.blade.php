@extends('layouts.app')

@section('content')

{{-- Tombol Cek Status --}}
<div class="text-end mt-3">
    <p class="mb-1">Sudah pernah lapor?</p>

    <a href="{{ url('/cek-laporan') }}" class="btn btn-outline-primary btn-sm">
        🔍 Cek Status Laporan
    </a>
</div>

<h3>Form Laporan Kerusakan IT</h3>

<a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm mb-3">
    ⬅ Kembali ke Dashboard
</a>


{{-- Alert Error --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


<form action="{{ route('reports.store') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf


    {{-- Nama --}}
    <div class="mb-3">
        <label>Nama Pelapor</label>
        <input type="text"
               name="nama_pelapor"
               class="form-control"
               value="{{ old('nama_pelapor') }}"
               required>
    </div>


    {{-- Departemen --}}
    <div class="mb-3">
        <label>Departemen</label>

        <select name="departemen"
                id="departemen"
                class="form-control"
                required>

            <option value="">-- Pilih Departemen --</option>

            <option value="Administrasi">Administrasi</option>
            <option value="Produksi">Produksi</option>
            <option value="Keuangan">Keuangan</option>
            <option value="Gudang">Gudang</option>
            <option value="HRD">HRD</option>
            <option value="Marketing">Marketing</option>
            <option value="IT">IT</option>
            <option value="Lainnya">Lainnya</option>

        </select>
    </div>


    {{-- Input Divisi Lainnya --}}
    <div class="mb-3 d-none" id="divisi-lainnya">
        <label>Divisi Lainnya</label>

        <input type="text"
               name="departemen_lainnya"
               class="form-control"
               placeholder="Masukkan nama divisi">
    </div>


    {{-- Jenis Masalah --}}
    <div class="mb-3">
        <label>Jenis Masalah</label>

        <select name="jenis_masalah"
                class="form-control"
                required>

            <option value="Maintenence">Maintenence</option>
            <option value="Troubleshooting/Helpdesk">Troubleshooting / Helpdesk</option>
            <option value="Instalasi Software">Instalasi Software</option>
            <option value="Instalasi Perangkat Jaringan">Instalasi Perangkat Jaringan</option>
            <option value="Instalasi Perangkat Hardware">Instalasi Perangkat Hardware</option>
            <option value="Lainnya">Lainnya</option>

        </select>
    </div>


    {{-- Deskripsi --}}
    <div class="mb-3">
        <label>Deskripsi Masalah</label>

        <textarea name="deskripsi"
                  class="form-control"
                  rows="4"
                  required>{{ old('deskripsi') }}</textarea>
    </div>


    {{-- Foto --}}
    <div class="mb-3">
        <label>Upload Foto (Opsional)</label>

        <input type="file"
               name="foto"
               class="form-control">
    </div>


    {{-- Submit --}}
    <button type="submit" class="btn btn-primary">
        Kirim Laporan
    </button>

</form>


{{-- Script Toggle Divisi Lainnya --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('departemen');
    const lainnya = document.getElementById('divisi-lainnya');

    select.addEventListener('change', function () {

        if (this.value === 'Lainnya') {
            lainnya.classList.remove('d-none');
        } else {
            lainnya.classList.add('d-none');
        }

    });

});
</script>

@endsection
