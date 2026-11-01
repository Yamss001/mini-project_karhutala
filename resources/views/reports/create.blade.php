@extends('layouts.auth')

@section('title', 'Buat Laporan')

@section('content')

    <h1>Buat Laporan Kejadian</h1>

    <p>
        Silakan masukkan informasi indikasi kebakaran
        yang ditemukan.
    </p>

    <a href="{{ route('reports.index') }}">
        Kembali ke laporan saya
    </a>

    @include('reports._form', [
        'action' => route('reports.store'),
        'method' => 'POST',
        'report' => $report,
        'areas' => $areas,
    ])

@endsection
