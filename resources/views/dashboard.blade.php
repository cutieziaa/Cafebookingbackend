<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard 
        <small class="text-muted">
            - {{ ucfirst(auth()->user()->peran) }}
        </small>
    </h2>

    @if(auth()->user()->isAdmin())
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary card-stat">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2>{{ $data['total_users'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success card-stat">
                <div class="card-body">
                    <h5 class="card-title">Total Meja</h5>
                    <h2>{{ $data['total_meja'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info card-stat">
                <div class="card-body">
                    <h5 class="card-title">Total Menu</h5>
                    <h2>{{ $data['total_menu'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning card-stat">
                <div class="card-body">
                    <h5 class="card-title">Booking Menunggu</h5>
                    <h2>{{ $data['booking_menunggu'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger card-stat">
                <div class="card-body">
                    <h5 class="card-title">Order Baru</h5>
                    <h2>{{ $data['order_baru'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->isCs())
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning card-stat">
                <div class="card-body">
                    <h5 class="card-title">Booking Menunggu</h5>
                    <h2>{{ $data['booking_menunggu'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success card-stat">
                <div class="card-body">
                    <h5 class="card-title">Booking Dikonfirmasi</h5>
                    <h2>{{ $data['booking_dikonfirmasi'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info card-stat">
                <div class="card-body">
                    <h5 class="card-title">Order Baru</h5>
                    <h2>{{ $data['order_baru'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->isCustomer())
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light card-stat">
                <div class="card-body">
                    <h5 class="card-title">Booking Aktif Saya</h5>
                    <h2>{{ ($data['active_bookings'] ?? collect())->count() }}</h2>
                    <a href="{{ route('booking.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light card-stat">
                <div class="card-body">
                    <h5 class="card-title">Total Booking</h5>
                    <h2>{{ ($data['my_bookings'] ?? collect())->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light card-stat">
                <div class="card-body">
                    <h5 class="card-title">Total Order</h5>
                    <h2>{{ ($data['my_orders'] ?? collect())->count() }}</h2>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Activities -->
    <div class="row">
        @if(auth()->user()->isAdmin() || auth()->user()->isCs())
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Terbaru</h5>
                </div>
                <div class="card-body">
                    @if(isset($data['recent_bookings']) && $data['recent_bookings']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Meja</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['recent_bookings'] as $booking)
                                <tr>
                                    <td>{{ $booking->user->nama }}</td>
                                    <td>{{ $booking->meja->kode_meja }}</td>
                                    <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->booking_status == 'selesai' ? 'success' : ($booking->booking_status == 'batal' ? 'danger' : 'warning') }}">
                                            {{ $booking->booking_status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">Belum ada booking.</p>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($data['recent_orders']) && $data['recent_orders']->count() > 0)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['recent_orders'] as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->user->nama }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->order_status == 'selesai' ? 'success' : ($order->order_status == 'dibatalkan' ? 'danger' : 'info') }}">
                                            {{ $order->order_status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @else
        <!-- Customer View -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Booking Saya</h5>
                </div>
                <div class="card-body">
                    @if(isset($data['my_bookings']) && $data['my_bookings']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Meja</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Jumlah Orang</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['my_bookings'] as $booking)
                                <tr>
                                    <td>{{ $booking->meja->kode_meja }}</td>
                                    <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                                    <td>{{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}</td>
                                    <td>{{ $booking->jumlah_orang }} orang</td>
                                    <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->booking_status == 'selesai' ? 'success' : ($booking->booking_status == 'batal' ? 'danger' : 'warning') }}">
                                            {{ $booking->booking_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">Belum ada booking.</p>
                    <a href="{{ route('booking.create') }}" class="btn btn-primary">Buat Booking Pertama</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection