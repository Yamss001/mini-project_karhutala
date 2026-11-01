@extends('layouts.auth')

@section('title', 'Dashboard Karhutala')

@section('styles')
<style>
    body {
        align-items: flex-start;
        padding: 24px 0;
    }

    .container {
        width: calc(100% - 48px);
        max-width: 1440px;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin: 24px 0;
    }

    .summary-card {
        padding: 22px;
        background: #f3f5f4;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
    }

    .summary-card h2 {
        font-size: 32px;
        margin: 0 0 8px;
    }

    .summary-card p {
        margin: 0;
        color: #555;
    }

    .dashboard-section {
        margin-top: 30px;
    }

    .dashboard-filter {
        max-width: 400px;
        margin: 20px 0;
    }

    .dashboard-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .dashboard-table th,
    .dashboard-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    .dashboard-table th:first-child,
    .dashboard-table td:first-child {
        text-align: left;
    }

    .dashboard-table th {
        background: #f3f5f4;
    }

    .dashboard-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .dashboard-menu a {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 6px;
        background: #246b45;
        color: white;
        text-decoration: none;
    }

    .dashboard-menu a:hover {
        background: #195336;
    }

    .logout-button {
        width: auto;
        padding: 10px 18px;
    }

    @media (max-width: 650px) {
        body {
            padding: 12px 0;
        }

        .container {
            width: calc(100% - 20px);
            padding: 18px;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

<div class="dashboard-page">

    {{-- Header --}}
    <div class="dashboard-header">

        <div>
            <h1>Dashboard Karhutala</h1>

            <p>
                Selamat datang,
                <strong>{{ auth()->user()->name }}</strong>.
            </p>

            <p>
                Role:
                <strong>{{ auth()->user()->role }}</strong>
            </p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="logout-button">
                Logout
            </button>
        </form>

    </div>

    <hr>

    {{-- Filter wilayah --}}
    <section class="dashboard-section">

        <h2>Pemantauan Berdasarkan Wilayah</h2>

        <p>
            Pilih provinsi untuk melihat informasi laporan
            indikasi kebakaran hutan dan lahan.
        </p>

        <div class="dashboard-filter">

            <label for="area_id">Pilih Provinsi</label>

            <select id="area_id" name="area_id">

                <option value="">Semua Wilayah</option>

                @foreach ($areas as $area)
                    <option value="{{ $area->id }}">
                        {{ $area->name }}
                    </option>
                @endforeach

            </select>

        </div>

        <p id="area-report-message" aria-live="polite"></p>

    </section>

    {{-- Rekap untuk Relawan dan Koordinator --}}
    @if ($showRecap)

        <section class="dashboard-section">

            <h2>Ringkasan Laporan</h2>

            <div class="summary-grid">

                <div class="summary-card">
                    <h2 id="total-reports">
                        {{ $initialRecap['summary']['total_reports'] }}
                    </h2>

                    <p>Total Laporan</p>
                </div>

                <div class="summary-card">
                    <h2 id="active-reports">
                        {{ $initialRecap['summary']['active_reports'] }}
                    </h2>

                    <p>Laporan Aktif</p>
                </div>

                <div class="summary-card">
                    <h2 id="completed-reports">
                        {{ $initialRecap['summary']['completed_reports'] }}
                    </h2>

                    <p>Laporan Selesai</p>
                </div>

            </div>

        </section>

        <section class="dashboard-section">

            <h2>Rekap Laporan Berdasarkan Provinsi</h2>

            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>
                        <tr>
                            <th>Provinsi</th>
                            <th>Baru</th>
                            <th>Ditinjau</th>
                            <th>Diteruskan</th>
                            <th>Selesai</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody id="recap-table-body">

                        @forelse ($initialRecap['rows'] as $row)

                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['baru'] }}</td>
                                <td>{{ $row['ditinjau'] }}</td>
                                <td>{{ $row['diteruskan'] }}</td>
                                <td>{{ $row['selesai'] }}</td>
                                <td>{{ $row['total'] }}</td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    Belum ada data laporan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    @else

        {{-- Informasi untuk Pelapor --}}
        <section
            id="pelapor-area-info"
            class="dashboard-section"
            hidden
        >

            <div class="summary-card">

                <h2 id="pelapor-active-total">0</h2>

                <p>
                    Total laporan aktif pada wilayah yang dipilih.
                </p>

            </div>

        </section>

    @endif

    <hr>

    {{-- Menu berdasarkan role --}}
    <section class="dashboard-section">

        <h2>Menu Aplikasi</h2>

        <div class="dashboard-menu">

            @if (auth()->user()->role === 'pelapor')

                <a href="{{ route('reports.create') }}">
                    Buat Laporan
                </a>

                <a href="{{ route('reports.index') }}">
                    Laporan Saya
                </a>

            @elseif (auth()->user()->role === 'relawan_pemantau')

                <a href="{{ route('relawan.reports.index') }}">
                    Daftar Laporan Relawan
                </a>

            @elseif (auth()->user()->role === 'koordinator_relawan')

                <a href="{{ route('koordinator.reports.index') }}">
                    Monitoring Laporan
                </a>

            @endif

        </div>

    </section>

</div>


{{-- Fetch API --}}
<script>
(() => {

    const areaSelect = document.getElementById('area_id');
    const message = document.getElementById('area-report-message');

    const endpointTemplate = @json(
        route('areas.active-reports', ['area' => '__AREA__'])
    );

    const showRecap = @json($showRecap);
    const initialRecap = @json($initialRecap);

    let activeController = null;

    const totalReports = document.getElementById('total-reports');
    const activeReports = document.getElementById('active-reports');
    const completedReports = document.getElementById('completed-reports');
    const tableBody = document.getElementById('recap-table-body');

    const pelaporInfo = document.getElementById('pelapor-area-info');
    const pelaporActiveTotal = document.getElementById('pelapor-active-total');

    function createCell(row, value) {
        const cell = document.createElement('td');
        cell.textContent = value;
        row.appendChild(cell);
    }

    function renderRecap(data) {

        totalReports.textContent = data.summary.total_reports;
        activeReports.textContent = data.summary.active_reports;
        completedReports.textContent = data.summary.completed_reports;

        tableBody.replaceChildren();

        if (data.rows.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');

            cell.colSpan = 6;
            cell.textContent = 'Belum ada data laporan.';

            row.appendChild(cell);
            tableBody.appendChild(row);

            return;
        }

        data.rows.forEach(item => {

            const row = document.createElement('tr');

            createCell(row, item.name);
            createCell(row, item.baru);
            createCell(row, item.ditinjau);
            createCell(row, item.diteruskan);
            createCell(row, item.selesai);
            createCell(row, item.total);

            tableBody.appendChild(row);

        });
    }

    async function loadAreaInfo(areaId) {

        if (activeController) {
            activeController.abort();
        }

        if (!areaId) {

            message.textContent = '';

            if (showRecap) {
                renderRecap(initialRecap);
            } else {
                pelaporInfo.hidden = true;
            }

            return;
        }

        activeController = new AbortController();

        const controller = activeController;

        message.textContent = 'Memuat informasi laporan...';

        try {

            const url = endpointTemplate.replace(
                '__AREA__',
                encodeURIComponent(areaId)
            );

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil informasi wilayah.'
                );
            }

            const data = await response.json();

            if (showRecap) {

                renderRecap({
                    summary: data.summary,

                    rows: [{
                        name: data.area.name,
                        baru: data.status_counts.baru,
                        ditinjau: data.status_counts.ditinjau,
                        diteruskan: data.status_counts.diteruskan,
                        selesai: data.status_counts.selesai,
                        total: data.summary.total_reports
                    }]
                });

            } else {

                pelaporActiveTotal.textContent =
                    data.summary.active_reports;

                pelaporInfo.hidden = false;

            }

            message.textContent =
                'Informasi wilayah berhasil diperbarui.';

        } catch (error) {

            if (error.name === 'AbortError') {
                return;
            }

            message.textContent = error.message;

        }

    }

    areaSelect.addEventListener('change', function () {
        loadAreaInfo(this.value);
    });

})();
</script>

@endsection