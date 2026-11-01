@extends('layouts.auth')

@section('title', 'Detail Laporan Koordinator')

@section('content')

    <h1>Detail Laporan #{{ $report->id }}</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p>
        <a href="{{ route('koordinator.reports.index') }}">
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

    <h3>Catatan Peninjauan Relawan</h3>

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

    <h3>Riwayat Penerusan</h3>

    @forelse ($report->forwardings as $forwarding)

        <article>
            <p>
                Penerima: {{ $forwarding->recipient_name }}
            </p>

            <p>
                Dicatat oleh:
                {{ $forwarding->coordinator->name }}
            </p>

            <p>
                Waktu penerusan:
                {{ $forwarding->forwarded_at->format('d-m-Y H:i') }}
            </p>

            <p>Catatan: {{ $forwarding->notes }}</p>
        </article>

        <hr>

    @empty

        <p>Belum ada informasi yang diteruskan.</p>

    @endforelse

    @if (in_array($report->status, ['ditinjau', 'diteruskan'], true))

        <h3>Catat Penerusan Informasi</h3>

        <form
            action="{{ route('koordinator.reports.forward', $report) }}"
            method="POST"
        >
            @csrf

            <div class="field">
                <label for="recipient_name">
                    Pihak yang Menerima
                </label>

                <input
                    type="text"
                    name="recipient_name"
                    id="recipient_name"
                    maxlength="150"
                    placeholder="Contoh: BPBD"
                    value="{{ old('recipient_name') }}"
                    required
                >

                @error('recipient_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="forwarded_at">
                    Waktu Penerusan
                </label>

                <input
                    type="datetime-local"
                    name="forwarded_at"
                    id="forwarded_at"
                    value="{{ old('forwarded_at', now()->format('Y-m-d\TH:i')) }}"
                    required
                >

                @error('forwarded_at')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="notes">
                    Catatan Penerusan
                </label>

                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    maxlength="3000"
                >{{ old('notes') }}</textarea>

                @error('notes')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">
                Simpan Penerusan
            </button>
        </form>

    @endif

    @if (
        $report->status === 'diteruskan'
        && $report->forwardings->isNotEmpty()
    )

        <hr>

        <h3>Selesaikan Proses Laporan</h3>

        <p>
            Informasi sudah diteruskan kepada pihak terkait.
            Proses laporan dalam aplikasi komunitas dapat diselesaikan.
        </p>

        <form
            action="{{ route('koordinator.reports.complete', $report) }}"
            method="POST"
            onsubmit="return confirm('Tandai proses laporan sebagai selesai?')"
        >
            @csrf

            <button type="submit">
                Tandai Selesai
            </button>
        </form>

    @endif

@endsection
