<!-- resources/views/booking/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Booking</h2>
        @if(auth()->user()->isCustomer())
        <a href="{{ route('booking.create') }}" class="btn btn-primary">Buat Booking Baru</a>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                            <th>Customer</th>
                            @endif
                            <th>Meja</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Durasi</th>
                            <th>Jumlah Orang</th>
                            <th>Total</th>
                            <th>Status Booking</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                            <td>{{ $booking->user->nama }}</td>
                            @endif
                            <td>{{ $booking->meja->kode_meja }}</td>
                            <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                            <td>{{ $booking->waktu_mulai }}</td>
                            <td>{{ $booking->durasi_minutes }} menit</td>
                            <td>{{ $booking->jumlah_orang }} orang</td>
                            <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $booking->booking_status == 'selesai' ? 'success' : ($booking->booking_status == 'batal' ? 'danger' : 'warning') }}">
                                    {{ $booking->booking_status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $booking->payment_status == 'lunas' ? 'success' : ($booking->payment_status == 'dp_dibayar' ? 'info' : 'secondary') }}">
                                    {{ $booking->payment_status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('booking.show', $booking->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                
                                @if((auth()->user()->isAdmin() || auth()->user()->isCs()) && $booking->booking_status == 'menunggu')
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('booking.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" name="booking_status" value="dikonfirmasi" class="dropdown-item">Konfirmasi</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('booking.update-status', $booking->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" name="booking_status" value="batal" class="dropdown-item">Batalkan</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endif

                                @if(auth()->user()->isCustomer() && $booking->booking_status == 'menunggu')
                                <form action="{{ route('booking.destroy', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin batalkan booking?')">Batalkan</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($bookings->isEmpty())
            <p class="text-center text-muted py-4">Belum ada booking.</p>
            @endif
        </div>
    </div>
</div>
@endsection