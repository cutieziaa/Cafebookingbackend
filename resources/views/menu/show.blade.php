@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-info-circle"></i> Detail Menu</h4>
                        <a href="{{ route('menu.index') }}" class="btn btn-light btn-sm">Kembali</a>
                    </div>
                </div>

                <div class="card-body text-center">
                    @if($menu->gambar_url && $menu->gambar_url != asset('images/default-food.jpg'))
                    <img src="{{ $menu->gambar_url }}" alt="{{ $menu->nama }}" 
                         class="img-fluid rounded mb-4" style="max-height: 300px;">
                    @else
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" 
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-utensils fa-3x text-muted"></i>
                    </div>
                    @endif

                    <h3 class="text-primary">{{ $menu->nama }}</h3>
                    <h4 class="text-success mb-4">Rp {{ number_format($menu->harga, 0, ',', '.') }}</h4>
                    
                    @if($menu->deskripsi)
                    <div class="alert alert-light border">
                        <p class="mb-0">{{ $menu->deskripsi }}</p>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Status</h6>
                                    <span class="badge bg-{{ $menu->tersedia ? 'success' : 'danger' }} fs-6">
                                        {{ $menu->tersedia ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6>Ditambahkan</h6>
                                    <small class="text-muted">{{ $menu->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-warning me-md-2">
                            <i class="fas fa-edit"></i> Edit Menu
                        </a>
                        <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Yakin hapus menu {{ $menu->nama }}?')">
                                <i class="fas fa-trash"></i> Hapus Menu
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection