<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Menu - Cafe Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .card-img-custom {
            height: 200px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Cafe Booking</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('booking.index') }}">Booking</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('menu.index') }}">Menu</a>
                        </li>
                        @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('meja.index') }}">Kelola Meja</a>
                        </li>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Kelola User</a>
                        </li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ auth()->user()->nama }} ({{ ucfirst(auth()->user()->peran) }})
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Menu</h2>
            @if(auth()->user()->isAdmin() || auth()->user()->isCs())
            <a href="{{ route('menu.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Menu
            </a>
            @endif
        </div>

        <div class="row">
            @forelse($menus as $menu)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $menu->gambar_url }}" class="card-img-top card-img-custom" alt="{{ $menu->nama }}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $menu->nama }}</h5>
                        <p class="card-text flex-grow-1 text-muted">
                            {{ $menu->deskripsi ?: 'Tidak ada deskripsi' }}
                        </p>
                        <div class="mt-auto">
                            <p class="card-text">
                                <strong class="text-primary">Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong>
                            </p>
                            <p class="card-text">
                                Status: 
                                <span class="badge bg-{{ $menu->tersedia ? 'success' : 'danger' }}">
                                    {{ $menu->tersedia ? 'Tersedia' : 'Habis' }}
                                </span>
                            </p>
                            
                            @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                            <div class="btn-group w-100">
                                <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Yakin hapus menu {{ $menu->nama }}?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                    <h4>Belum ada menu</h4>
                    <p class="text-muted">Silakan tambahkan menu pertama Anda</p>
                    @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                    <a href="{{ route('menu.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Tambah Menu Pertama
                    </a>
                    @endif
                </div>
            </div>
            @endforelse
        </div>

        <!-- Summary -->
        @if($menus->count() > 0)
        <div class="mt-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center py-3">
                            <h6 class="card-title mb-1">Total Menu</h6>
                            <h3 class="mb-0">{{ $menus->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-3">
                            <h6 class="card-title mb-1">Tersedia</h6>
                            <h3 class="mb-0">{{ $menus->where('tersedia', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center py-3">
                            <h6 class="card-title mb-1">Habis</h6>
                            <h3 class="mb-0">{{ $menus->where('tersedia', false)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center py-3">
                            <h6 class="card-title mb-1">Rata-rata Harga</h6>
                            <h3 class="mb-0">Rp {{ number_format($menus->avg('harga'), 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>