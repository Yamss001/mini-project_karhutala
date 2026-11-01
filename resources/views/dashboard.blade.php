@extends('layouts.auth')

@section('title', 'Dashboard')

@section('content')

    <h1>Dashboard Karhutala</h1>

    <p>
        Selamat datang, {{ auth()->user()->name }}.
    </p>

    <p>
        Role:
        <strong>{{ auth()->user()->role }}</strong>
    </p>

    <hr>

    <h2>Pemantauan Laporan Berdasarkan Wilayah</h2>

    <p>
        Pilih wilayah untuk melihat informasi laporan
        indikasi kebakaran hutan dan lahan.
    </p>

    <div class="field">
        <label for="area_id">Pilih Wilayah</label>

        <select name="area_id" id="area_id">
            <option value="">-- Pilih Wilayah --</option>

            @foreach ($areas as $area)
                <option value="{{ $area->id }}">
                    {{ $area->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div
        id="area-report-info"
        hidden
        style="
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        "
        aria-live="polite"
    >
        <h3 id="area-report-title">
            Informasi Laporan
        </h3>

        <h2 id="area-report-total">0</h2>
        <p>Total Laporan Aktif</p>

        <hr>

        <ul id="area-report-status"></ul>
    </div>

    <p id="area-report-message" aria-live="polite"></p>

    <hr>

    <h3>Menu Aplikasi</h3>

    @if (auth()->user()->role === 'pelapor')
        <p>
            <a href="{{ route('reports.create') }}">
                Buat Laporan
            </a>
        </p>

        <p>
            <a href="{{ route('reports.index') }}">
                Laporan Saya
            </a>
        </p>
    @elseif (auth()->user()->role === 'relawan_pemantau')
        <p>
            <a href="{{ route('relawan.reports.index') }}">
                Daftar Laporan Relawan
            </a>
        </p>
    @elseif (auth()->user()->role === 'koordinator_relawan')
        <p>
            <a href="{{ route('koordinator.reports.index') }}">
                Monitoring Laporan
            </a>
        </p>
    @endif

    <hr>

    <form action="{{ route('logout') }}" method="POST">
        @csrf

        <button type="submit">Logout</button>
    </form>


    {{-- JavaScript Fetch API --}}
    <script>
        (() => {
            const areaSelect = document.getElementById('area_id');
            const infoPanel = document.getElementById('area-report-info');
            const message = document.getElementById('area-report-message');

            const title = document.getElementById('area-report-title');
            const total = document.getElementById('area-report-total');
            const statusList = document.getElementById('area-report-status');

            const endpointTemplate = @json(
                route('areas.active-reports', ['area' => '__AREA__'])
            );

            let activeController = null;

            async function loadAreaInfo(areaId) {

                if (activeController) {
                    activeController.abort();
                }

                if (!areaId) {
                    infoPanel.hidden = true;
                    message.textContent = '';
                    return;
                }

                activeController = new AbortController();

                const controller = activeController;

                infoPanel.hidden = false;
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
                            'Gagal mengambil informasi laporan.'
                        );
                    }

                    const data = await response.json();

                    title.textContent =
                        'Informasi Laporan ' + data.area.name;

                    total.textContent = data.total_active;

                    const labels = {
                        baru: 'Baru',
                        ditinjau: 'Ditinjau',
                        diteruskan: 'Diteruskan'
                    };

                    statusList.replaceChildren();

                    Object.entries(labels).forEach(([key, label]) => {
                        const item = document.createElement('li');

                        item.textContent =
                            label + ': ' + data.status_counts[key];

                        statusList.appendChild(item);
                    });

                    message.textContent = '';

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
