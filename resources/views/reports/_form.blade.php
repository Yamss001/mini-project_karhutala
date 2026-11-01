<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="field">
        <label for="area_id">Wilayah</label>

        <select name="area_id" id="area_id" required>
            <option value="">Pilih wilayah</option>

            @foreach ($areas as $area)
                <option value="{{ $area->id }}"
                    @selected(old('area_id', $report->area_id) == $area->id)>
                    {{ $area->name }}
                </option>
            @endforeach
        </select>

        @error('area_id')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="incident_at">Waktu Kejadian</label>

        <input
            type="datetime-local"
            name="incident_at"
            id="incident_at"
            value="{{ old('incident_at', $report->incident_at?->format('Y-m-d\TH:i')) }}"
            required
        >

        @error('incident_at')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="latitude">Latitude</label>

        <input
            type="number"
            step="any"
            min="-90"
            max="90"
            name="latitude"
            id="latitude"
            value="{{ old('latitude', $report->latitude) }}"
            required
        >

        @error('latitude')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="longitude">Longitude</label>

        <input
            type="number"
            step="any"
            min="-180"
            max="180"
            name="longitude"
            id="longitude"
            value="{{ old('longitude', $report->longitude) }}"
            required
        >

        @error('longitude')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="condition">Kondisi yang Ditemukan</label>

        <input
            type="text"
            name="condition"
            id="condition"
            maxlength="150"
            value="{{ old('condition', $report->condition) }}"
            placeholder="Contoh: Asap tebal"
            required
        >

        @error('condition')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="description">Deskripsi Kejadian</label>

        <textarea
            name="description"
            id="description"
            rows="5"
            maxlength="5000"
            required
        >{{ old('description', $report->description) }}</textarea>

        @error('description')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    <div class="field">
        <label for="evidence">Bukti Pendukung</label>

        <input
            type="file"
            name="evidence"
            id="evidence"
            accept=".jpg,.jpeg,.png,.pdf"
        >

        <small>
            Format JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.
        </small>

        @error('evidence')
            <p class="error">{{ $message }}</p>
        @enderror
    </div>

    @if ($report->evidence_path)
        <p>
            Bukti saat ini tersedia.
            File baru dapat diunggah untuk menggantikannya.
        </p>
    @endif

    @if ($areas->isEmpty())
        <p class="error">
            Belum ada wilayah yang tersedia.
        </p>
    @endif

    <button type="submit" @disabled($areas->isEmpty())>
        {{ $method === 'POST' ? 'Kirim Laporan' : 'Simpan Perubahan' }}
    </button>
</form>
