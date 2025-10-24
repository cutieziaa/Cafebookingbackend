@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Edit Meja: {{ $meja->kode_meja }}</h4>
                        <a href="{{ route('meja.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('meja.update', $meja->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="kode_meja" class="form-label">Kode Meja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_meja') is-invalid @enderror" 
                                   id="kode_meja" name="kode_meja" value="{{ old('kode_meja', $meja->kode_meja) }}" 
                                   required>
                            @error('kode_meja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tipe_id" class="form-label">Tipe Meja <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipe_id') is-invalid @enderror" 
                                    id="tipe_id" name="tipe_id" required>
                                @foreach($tipeMejas as $tipe)
                                    <option value="{{ $tipe->id }}" {{ old('tipe_id', $meja->tipe_id) == $tipe->id ? 'selected' : '' }}>
                                        {{ $tipe->nama_tipe }} - {{ $tipe->deskripsi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" 
                                   id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $meja->kapasitas) }}" 
                                   min="1" max="20" required>
                            @error('kapasitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="aktif" {{ old('status', $meja->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status', $meja->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('meja.index') }}" class="btn btn-secondary me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Meja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection