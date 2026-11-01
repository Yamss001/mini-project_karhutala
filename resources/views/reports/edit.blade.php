@extends('layouts.auth')

@section('title', 'Edit Laporan')

@section('content')

    <h1>Edit Laporan</h1>

    <p>Perbaiki informasi laporan Anda.</p>

    <a href="{{ route('reports.show', $report) }}">
        Kembali ke detail laporan
    </a>

    @include('reports._form', [
        'action' => route('reports.update', $report),
        'method' => 'PUT',
        'report' => $report,
        'areas' => $areas,
    ])

@endsection
