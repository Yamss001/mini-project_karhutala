@extends('layouts.auth')

@section('styles')
<style>
    .container {
        max-width: 1100px;
    }

    .report-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        min-width: 700px;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }
</style>
@endsection

@section('title', 'Daftar Laporan Koordinator')

@section('content')

    <h1>Monitoring Laporan Karhutla</h1>

    <p>
        Halaman pemantauan untuk Koordinator Relawan.
    </p>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    <hr>
        
        <form action="{{ route('koordinator.reports.index') }}" method="GET">

            <div class="field">
                <label for="search">Cari Laporan</label>

                <input
                    type="search"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Cari ID, kondisi, deskripsi, atau nama pelapor..."
                >
            </div>

            <div class="field">
                <label for="area_id">Filter Wilayah</label>

                <select name="area_id" id="area_id">
                    <option value="">Semua Wilayah</option>

                    @foreach (\App\Models\Area::orderBy('name')->get() as $area)
                        <option
                            value="{{ $area->id }}"
                            @selected(request('area_id') == $area->id)
                        >
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="status">Filter Status</label>

                <select name="status" id="status">
                    <option value="">Semua Status</option>

                    <option value="baru"
                        @selected(request('status') === 'baru')>
                        Baru
                    </option>

                    <option value="ditinjau"
                        @selected(request('status') === 'ditinjau')>
                        Ditinjau
                    </option>

                    <option value="diteruskan"
                        @selected(request('status') === 'diteruskan')>
                        Diteruskan
                    </option>

                    <option value="selesai"
                        @selected(request('status') === 'selesai')>
                        Selesai
                    </option>
                </select>
            </div>

            <button type="submit">Cari dan Filter</button>

            <a href="{{ route('koordinator.reports.index') }}">
                Reset Filter
            </a>

        </form>

    <hr>

    @if ($reports->isEmpty())

        <p>Belum terdapat laporan.</p>

    @else
        <div class="report-table-wrapper">

            <table class="report-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelapor</th>
                        <th>Wilayah</th>
                        <th>Kondisi</th>
                        <th>Waktu Kejadian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($reports as $report)

                        <tr>
                            <td>{{ $report->id }}</td>

                            <td>{{ $report->reporter->name }}</td>

                            <td>{{ $report->area->name }}</td>

                            <td>{{ $report->condition }}</td>

                            <td>
                                {{ $report->incident_at->format('d-m-Y H:i') }}
                            </td>

                            <td>{{ ucfirst($report->status) }}</td>

                            <td>
                                <a href="{{ route('koordinator.reports.show', $report) }}">
                                    Detail
                                </a>
                            </td>
                        </tr>

                    @endforeach

                </tbody>
            </table>

        </div>

    {{ $reports->links('vendor.pagination.paginate') }}

    @endif

@endsection
