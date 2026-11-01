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

@section('title', 'Daftar Laporan Relawan')

@section('content')

    <h1>Daftar Laporan Karhutla</h1>

    <p>
        Halaman pemantauan laporan untuk Relawan Pemantau.
    </p>

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
    <hr>
    
        <form action="{{ route('relawan.reports.index') }}" method="GET">

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
                    <option value="">Semua Status Aktif</option>

                    <option value="baru"
                        @selected(request('status') === 'baru')>
                        Baru
                    </option>

                    <option value="ditinjau"
                        @selected(request('status') === 'ditinjau')>
                        Ditinjau
                    </option>
                </select>
            </div>

            <button type="submit">Cari dan Filter</button>

            <a href="{{ route('relawan.reports.index') }}">
                Reset Filter
            </a>

        </form>

    <hr>

    @if ($reports->isEmpty())
        <p>Belum ada laporan yang perlu ditinjau.</p>
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
                            <td>{{ $report->incident_at->format('d-m-Y H:i') }}</td>
                            <td>{{ ucfirst($report->status) }}</td>
                            <td>
                                <a href="{{ route('relawan.reports.show', $report) }}">
                                    Tinjau
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
