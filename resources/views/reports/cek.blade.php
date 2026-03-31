@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <h4>Cek Status Laporan</h4>

    <form method="POST" action="/cek-laporan">
        @csrf

        <div class="mb-3">
            <label>Kode Laporan</label>
            <input type="text" name="kode_laporan" class="form-control" required>
        </div>

        <button class="btn btn-primary">
            Cek Status
        </button>
    </form>

</div>

@endsection
