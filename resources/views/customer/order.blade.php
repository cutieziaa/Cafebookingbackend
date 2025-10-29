@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Pesan Menu</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @foreach($menus as $menu)
        <div class="col-md-4 mb-3">
            <div class="card">
                @if($menu->gambar)
                    <img src="{{ asset('storage/' . $menu->gambar) }}" class="card-img-top">
                @endif
                <div class="card-body">
                    <h5>{{ $menu->nama }}</h5>
                    <p>{{ $menu->deskripsi }}</p>
                    <p><strong>Rp {{ number_format($menu->harga, 0, ',', '.') }}</strong></p>

                    <form method="POST" action="{{ route('order.store') }}">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <input type="number" name="jumlah" min="1" class="form-control mb-2" placeholder="Jumlah">
                        <select name="tipe_order" class="form-control mb-2">
                            <option value="pickup">Pickup</option>
                            <option value="dine-in">Dine-In</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100">Pesan</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
