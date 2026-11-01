@extends('layouts.auth')

@section('title', 'Detail Laporan Relawan')

@section('content')

    <h1>Detail Laporan #{{ $report->id }}</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    <p>
        <a href="{{ route('relawan.reports.index') }}">
            Kembali ke daftar laporan
        </a>
    </p>

    <hr>

    <h3>Informasi Laporan</h3>

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

    <p>
        Status:
        <strong>{{ ucfirst($report->status) }}</strong>
    </p>

    <hr>

    <h3>Riwayat Peninjauan</h3>

    @forelse ($report->reviews as $review)

        <article>
            <p>
                Relawan: {{ $review->reviewer->name }}
            </p>

            <p>Catatan: {{ $review->notes }}</p>

            <p>
                Waktu:
                {{ $review->reviewed_at->format('d-m-Y H:i') }}
            </p>
        </article>

        <hr>

    @empty

        <p>Belum ada catatan peninjauan.</p>

    @endforelse

    @if (in_array($report->status, ['baru', 'ditinjau'], true))

        <h3>Tambah Catatan Peninjauan</h3>

        <form
            action="{{ route('relawan.reports.review', $report) }}"
            method="POST"
        >
            @csrf

            <div class="field">
                <label for="notes">Catatan Peninjauan</label>

                <textarea
                    name="notes"
                    id="notes"
                    rows="5"
                    maxlength="3000"
                    required
                >{{ old('notes') }}</textarea>

                @error('notes')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">
                Simpan Catatan
            </button>
        </form>

    @endif

    @if (
        $report->status === 'ditinjau'
        && $report->forwardings->isEmpty()
    )

        <hr>

        <h3>Selesaikan Laporan</h3>

        <p>
            Gunakan tindakan ini jika laporan tidak memerlukan
            penerusan kepada pihak eksternal.
        </p>

        <form
            action="{{ route('relawan.reports.complete', $report) }}"
            method="POST"
            onsubmit="return confirm('Selesaikan proses laporan ini?')"
        >
            @csrf

            <button type="submit">
                Tandai Selesai
            </button>
        </form>

    @endif

    <hr>

    <h3>Laporan Lain di Wilayah yang Sama</h3>

    @forelse ($relatedReports as $related)

        <article>
            <p>
                <strong>Laporan #{{ $related->id }}</strong>
            </p>

            <p>Kondisi: {{ $related->condition }}</p>

            <p>Wilayah: {{ $related->area->name }}</p>

            <p>Status: {{ ucfirst($related->status) }}</p>

            <a href="{{ route('relawan.reports.show', $related) }}">
                Lihat Laporan
            </a>
        </article>

        <hr>

    @empty

        <p>Tidak ditemukan laporan lain pada wilayah ini.</p>

    @endforelse

@endsection
