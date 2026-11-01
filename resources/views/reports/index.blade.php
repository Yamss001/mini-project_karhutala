@extends('layouts.auth')

@section('title', 'Laporan Saya')

@section('content')

    <h1>Laporan Saya</h1>

    @if (session('success'))
        <p style="color: green">
            {{ session('success') }}
        </p>
    @endif

    <a href="{{ route('reports.create') }}">
        + Buat Laporan
    </a>

    <hr>

    @forelse ($reports as $report)

        <article>
            <h3>
                <a href="{{ route('reports.show', $report) }}">
                    Laporan #{{ $report->id }}
                </a>
            </h3>

            <p>Wilayah: {{ $report->area->name }}</p>

            <p>Kondisi: {{ $report->condition }}</p>

            <p>
                Waktu kejadian:
                {{ $report->incident_at->format('d-m-Y H:i') }}
            </p>

            <p>Status: {{ ucfirst($report->status) }}</p>
        </article>

        <hr>

    @empty

        <p>Anda belum mempunyai laporan.</p>

    @endforelse

    {{ $reports->links('vendor.pagination.paginate') }}

@endsection
