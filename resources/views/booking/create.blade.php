<!-- resources/views/booking/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Buat Booking Baru</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('booking.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_booking" class="form-label">Tanggal Booking *</label>
                                    <input type="date" class="form-control @error('tanggal_booking') is-invalid @enderror" 
                                           id="tanggal_booking" name="tanggal_booking" 
                                           value="{{ old('tanggal_booking') }}" 
                                           min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal_booking')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="waktu_mulai" class="form-label">Waktu Mulai *</label>
                                    <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                           id="waktu_mulai" name="waktu_mulai" 
                                           value="{{ old('waktu_mulai', '14:00') }}" required>
                                    @error('waktu_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="durasi_minutes" class="form-label">Durasi (menit) *</label>
                                    <select class="form-select @error('durasi_minutes') is-invalid @enderror" 
                                            id="durasi_minutes" name="durasi_minutes" required>
                                        <option value="">Pilih Durasi</option>
                                        <option value="60" {{ old('durasi_minutes') == 60 ? 'selected' : '' }}>1 Jam</option>
                                        <option value="90" {{ old('durasi_minutes') == 90 ? 'selected' : '' }}>1.5 Jam</option>
                                        <option value="120" {{ old('durasi_minutes') == 120 ? 'selected' : '' }}>2 Jam</option>
                                        <option value="180" {{ old('durasi_minutes') == 180 ? 'selected' : '' }}>3 Jam</option>
                                        <option value="240" {{ old('durasi_minutes') == 240 ? 'selected' : '' }}>4 Jam</option>
                                    </select>
                                    @error('durasi_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jumlah_orang" class="form-label">Jumlah Orang *</label>
                                    <input type="number" class="form-control @error('jumlah_orang') is-invalid @enderror" 
                                           id="jumlah_orang" name="jumlah_orang" 
                                           value="{{ old('jumlah_orang', 2) }}" min="1" max="20" required>
                                    @error('jumlah_orang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tipe_id" class="form-label">Tipe Meja *</label>
                            <select class="form-select @error('tipe_id') is-invalid @enderror" 
                                    id="tipe_id" name="tipe_id" required>
                                <option value="">Pilih Tipe Meja</option>
                                @foreach($tipeMeja as $tipe)
                                    <option value="{{ $tipe->id }}" {{ old('tipe_id') == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->nama_tipe }} - {{ $tipe->deskripsi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                      id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Buat Booking</button>
                            <a href="{{ route('booking.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipe_id');
    const mejaSelect = document.getElementById('meja_id');
    const tanggalInput = document.getElementById('tanggal_booking');
    const waktuInput = document.getElementById('waktu_mulai');
    const durasiSelect = document.getElementById('durasi_minutes');
    const jumlahOrangInput = document.getElementById('jumlah_orang');
    const mejaInfo = document.getElementById('meja-info');

    function loadAvailableMeja() {
        const tipeId = tipeSelect.value;
        const tanggal = tanggalInput.value;
        const waktu = waktuInput.value;
        const durasi = durasiSelect.value;
        const jumlahOrang = jumlahOrangInput.value;

        if (!tipeId || !tanggal || !waktu || !durasi) {
            mejaSelect.innerHTML = '<option value="">Pilih semua field terlebih dahulu</option>';
            mejaSelect.disabled = true;
            return;
        }

        // Simple AJAX request untuk mendapatkan meja yang tersedia
        fetch(`/booking/available-meja?tipe_id=${tipeId}&tanggal=${tanggal}&waktu_mulai=${waktu}&durasi=${durasi}&jumlah_orang=${jumlahOrang}`)
            .then(response => response.json())
            .then(data => {
                mejaSelect.innerHTML = '';
                
                if (data.length === 0) {
                    mejaSelect.innerHTML = '<option value="">Tidak ada meja tersedia</option>';
                    mejaInfo.textContent = 'Tidak ada meja yang tersedia untuk kriteria yang dipilih.';
                } else {
                    mejaSelect.innerHTML = '<option value="">Pilih Meja</option>';
                    data.forEach(meja => {
                        const option = document.createElement('option');
                        option.value = meja.id;
                        option.textContent = `${meja.kode_meja} - Kapasitas: ${meja.kapasitas} orang`;
                        mejaSelect.appendChild(option);
                    });
                    mejaInfo.textContent = `Tersedia ${data.length} meja`;
                    mejaSelect.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mejaSelect.innerHTML = '<option value="">Error loading meja</option>';
            });
    }

    // Event listeners
    tipeSelect.addEventListener('change', loadAvailableMeja);
    tanggalInput.addEventListener('change', loadAvailableMeja);
    waktuInput.addEventListener('change', loadAvailableMeja);
    durasiSelect.addEventListener('change', loadAvailableMeja);
    jumlahOrangInput.addEventListener('change', loadAvailableMeja);

    // Set tanggal minimal ke hari ini
    if (!tanggalInput.value) {
        const today = new Date().toISOString().split('T')[0];
        tanggalInput.value = today;
    }
});
</script>
@endsection