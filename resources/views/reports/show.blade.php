@extends('layouts.auth')

@section('title', 'Detail Laporan')

@section('content')

    <h1>Detail Laporan #{{ $report->id }}</h1>

    @if (session('success'))
        <p style="color: green">
            {{ session('success') }}
        </p>
    @endif

    <p>Pelapor: {{ $report->reporter->name }}</p>

    <p>Wilayah: {{ $report->area->name }}</p>

    <p>
        Waktu kejadian:
        {{ $report->incident_at->format('d-m-Y H:i') }}
    </p>

    <p>Latitude: {{ $report->latitude }}</p>

    <p>Longitude: {{ $report->longitude }}</p>

    <p>Kondisi: {{ $report->condition }}</p>

    <p>Deskripsi: {{ $report->description }}</p>

    <p>Status: {{ ucfirst($report->status) }}</p>

    @if ($report->evidence_path)
        <a href="{{ route('reports.evidence', $report) }}">
            Unduh Bukti Pendukung
        </a>
    @else
        <p>Belum ada bukti pendukung.</p>
    @endif

    @if ($report->status === 'baru')
        <p>
            <a href="{{ route('reports.edit', $report) }}">
                Edit Laporan
            </a>
        </p>
    @endif

    <h3>Riwayat Perubahan Status</h3>

    @forelse ($report->statusLogs as $log)

        <p>
            {{ $log->old_status ?? 'Awal' }}
            →
            {{ $log->new_status }}
            ({{ $log->created_at->format('d-m-Y H:i') }})
        </p>

    @empty

        <p>Belum ada riwayat perubahan status.</p>

    @endforelse

    <a href="{{ route('reports.index') }}">
        Kembali ke laporan saya
    </a>

@endsection
