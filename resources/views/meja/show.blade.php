@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Detail Meja: {{ $meja->kode_meja }}</h4>
                        <a href="{{ route('meja.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-table text-white fa-2x"></i>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Kode Meja</th>
                            <td>{{ $meja->kode_meja }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Meja</th>
                            <td>{{ $meja->tipe->nama_tipe }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi Tipe</th>
                            <td>{{ $meja->tipe->deskripsi }}</td>
                        </tr>
                        <tr>
                            <th>Kapasitas</th>
                            <td>{{ $meja->kapasitas }} orang</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $meja->status == 'aktif' ? 'success' : 'danger' }}">
                                    {{ $meja->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $meja->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Update</th>
                            <td>{{ $meja->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('meja.edit', $meja->id) }}" class="btn btn-warning me-md-2">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('meja.destroy', $meja->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Yakin hapus meja {{ $meja->kode_meja }}?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection