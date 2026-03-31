@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Laporan Masuk</h3>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">⬅ Kembali</a>
        <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm">+ Tambah Laporan</a>

        <form action="{{ route('reports.destroyAll') }}" method="POST" class="d-inline"
              onsubmit="return confirm('⚠️ Yakin ingin menghapus SEMUA laporan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">🗑 Hapus Semua</button>
        </form>
    </div>
</div>
<form method="GET" action="{{ route('reports.index') }}" class="row g-2 mb-3">

    {{-- Filter Divisi --}}
    <div class="col-md-3">
        <select name="departemen" class="form-control">

            <option value="">-- Semua Divisi --</option>

            <option value="Administrasi"
                {{ request('departemen')=='Administrasi' ? 'selected' : '' }}>
                Administrasi
            </option>

            <option value="Produksi"
                {{ request('departemen')=='Produksi' ? 'selected' : '' }}>
                Produksi
            </option>

            <option value="Keuangan"
                {{ request('departemen')=='Keuangan' ? 'selected' : '' }}>
                Keuangan
            </option>

            <option value="Gudang"
                {{ request('departemen')=='Gudang' ? 'selected' : '' }}>
                Gudang
            </option>

            <option value="HRD"
                {{ request('departemen')=='HRD' ? 'selected' : '' }}>
                HRD
            </option>

            <option value="Marketing"
                {{ request('departemen')=='Marketing' ? 'selected' : '' }}>
                Marketing
            </option>

            <option value="IT"
                {{ request('departemen')=='IT' ? 'selected' : '' }}>
                IT
            </option>

        </select>
    </div>

    {{-- Search --}}
    <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="Cari laporan..."
               value="{{ request('search') }}">
    </div>

    {{-- Tombol --}}
    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary">
            🔍 Filter
        </button>

        <a href="{{ route('reports.index') }}"
           class="btn btn-secondary">
            🔄 Reset
        </a>
    </div>

</form>

{{-- FILTER STATUS --}}
<div class="mb-3">
    <a href="{{ route('reports.index') }}"
       class="btn btn-sm {{ request('status')=='' ? 'btn-dark' : 'btn-outline-dark' }}">
        Semua
    </a>

    <a href="{{ route('reports.index', ['status' => 'Baru']) }}"
       class="btn btn-sm {{ request('status')=='Baru' ? 'btn-danger' : 'btn-outline-danger' }}">
        Baru
    </a>

    <a href="{{ route('reports.index', ['status' => 'Sedang Dikerjakan']) }}"
       class="btn btn-sm {{ request('status')=='Sedang Dikerjakan' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
        Sedang Dikerjakan
    </a>

    <a href="{{ route('reports.index', ['status' => 'Selesai']) }}"
       class="btn btn-sm {{ request('status')=='Selesai' ? 'btn-success' : 'btn-outline-success' }}">
        Selesai
    </a>
</div>

{{-- FILTER TANGGAL --}}
<form method="GET" action="{{ route('reports.index') }}" class="row g-2 mb-3">
    <input type="hidden" name="status" value="{{ request('status') }}">

    <div class="col-md-3">
        <label class="small">Dari tanggal</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
    </div>

    <div class="col-md-3">
        <label class="small">Sampai tanggal</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-success btn-sm me-1">🔍 Filter</button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</form>

{{-- EXPORT --}}
<a href="{{ route('reports.export', request()->all()) }}"
   class="btn btn-warning btn-sm mb-3">
    📥 Export Excel
</a>

{{-- SEARCH --}}
<form method="GET" action="{{ route('reports.index') }}" class="input-group mb-3" style="max-width: 350px;">
    <input type="text" name="search" value="{{ request('search') }}"
           class="form-control form-control-sm" placeholder="🔍 Cari laporan...">

    <button class="btn btn-primary btn-sm">Cari</button>

    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="hidden" name="from" value="{{ request('from') }}">
    <input type="hidden" name="to" value="{{ request('to') }}">
</form>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- LIST LAPORAN --}}
@forelse($reports as $report)
<div class="card shadow-sm mb-3" style="border-radius: 10px;">
    <div class="card-body">

        <div class="d-flex justify-content-between">

            {{-- INFO --}}
            <div style="max-width: 75%;">
                <strong>{{ $report->nama_pelapor }}</strong> - {{ $report->departemen }}<br>
                Masalah: <b>{{ $report->jenis_masalah }}</b><br>
                Deskripsi: {{ $report->deskripsi }}<br>

                Status:
                @if($report->status == 'Baru')
                    <span class="badge bg-danger">Baru</span>
                @elseif($report->status == 'Sedang Dikerjakan')
                    <span class="badge bg-warning text-dark">Sedang Dikerjakan</span>
                @else
                    <span class="badge bg-success">Selesai</span>
                @endif

                <br>
                <small class="text-muted">
                    Tanggal: {{ $report->tanggal_laporan }}
                </small>

                {{-- FOTO --}}
                @if($report->foto)
                <div class="mt-2">
                    <img src="{{ asset('uploads/laporan/' . $report->foto) }}"
                         class="shadow-sm"
                         data-bs-toggle="modal"
                         data-bs-target="#fotoModal{{ $report->id }}"
                         style="max-width:180px;border-radius:8px;cursor:pointer">

                    <br>
                    <a href="{{ asset('uploads/laporan/' . $report->foto) }}" download
                       class="btn btn-outline-primary btn-sm mt-2">
                        ⬇ Download Foto
                    </a>
                </div>
                @endif
            </div>

            {{-- DELETE --}}
            <form action="{{ route('reports.destroy', $report->id) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </div>

        {{-- UPDATE --}}
        <form action="{{ route('reports.update', $report->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="row g-2">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option {{ $report->status=='Baru' ? 'selected' : '' }}>Baru</option>
                        <option {{ $report->status=='Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option {{ $report->status=='Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div class="col-md-7">
                    <textarea name="catatan_teknisi" rows="1"
                              class="form-control form-control-sm"
                              placeholder="Catatan teknisi">{{ $report->catatan_teknisi }}</textarea>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success btn-sm w-100">Update</button>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- MODAL FOTO --}}
@if($report->foto)
<div class="modal fade" id="fotoModal{{ $report->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="{{ asset('uploads/laporan/' . $report->foto) }}" class="img-fluid w-100">
      </div>
    </div>
  </div>
</div>
@endif

@empty
<div class="alert alert-info mt-3">Belum ada laporan.</div>
@endforelse

{{-- PAGINATION --}}
<div class="d-flex justify-content-between align-items-center mt-4">
    <small class="text-muted">
        Menampilkan {{ $reports->firstItem() }} - {{ $reports->lastItem() }}
        dari {{ $reports->total() }} laporan
    </small>

    {{ $reports->links() }}
</div>

@endsection
