<!-- resources/views/booking/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Detail Booking #{{ $booking->id }}</h4>
                        <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Alert Status -->
                    @if($booking->booking_status == 'menunggu')
                        <div class="alert alert-warning">
                            <strong>Status:</strong> Menunggu Konfirmasi
                        </div>
                    @elseif($booking->booking_status == 'dikonfirmasi')
                        <div class="alert alert-success">
                            <strong>Status:</strong> Booking Dikonfirmasi
                        </div>
                    @elseif($booking->booking_status == 'batal')
                        <div class="alert alert-danger">
                            <strong>Status:</strong> Booking Dibatalkan
                        </div>
                    @elseif($booking->booking_status == 'selesai')
                        <div class="alert alert-info">
                            <strong>Status:</strong> Booking Selesai
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informasi Booking</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Kode Booking</th>
                                    <td>#{{ $booking->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Booking</th>
                                    <td>{{ $booking->tanggal_booking->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td>{{ $booking->waktu_mulai }} - {{ $booking->waktu_selesai }}</td>
                                </tr>
                                <tr>
                                    <th>Durasi</th>
                                    <td>{{ $booking->durasi_minutes }} menit</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Orang</th>
                                    <td>{{ $booking->jumlah_orang }} orang</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Informasi Meja</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Kode Meja</th>
                                    <td>{{ $booking->meja->kode_meja }}</td>
                                </tr>
                                <tr>
                                    <th>Tipe Meja</th>
                                    <td>{{ $booking->meja->tipe->nama_tipe }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $booking->meja->kapasitas }} orang</td>
                                </tr>
                                <tr>
                                    <th>Status Meja</th>
                                    <td>
                                        <span class="badge bg-{{ $booking->meja->status == 'aktif' ? 'success' : 'danger' }}">
                                            {{ $booking->meja->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Informasi Customer</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama</th>
                                    <td>{{ $booking->user->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $booking->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor WA</th>
                                    <td>{{ $booking->user->nomor_wa }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Informasi Pembayaran</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Total Amount</th>
                                    <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Paid Amount</th>
                                    <td>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        <span class="badge bg-{{ $booking->payment_status == 'lunas' ? 'success' : ($booking->payment_status == 'dp_dibayar' ? 'info' : 'secondary') }}">
                                            {{ $booking->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Booking Status</th>
                                    <td>
                                        <span class="badge bg-{{ $booking->booking_status == 'selesai' ? 'success' : ($booking->booking_status == 'batal' ? 'danger' : 'warning') }}">
                                            {{ $booking->booking_status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($booking->catatan)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Catatan</h5>
                            <div class="alert alert-light">
                                {{ $booking->catatan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                @if(auth()->user()->isCustomer() && $booking->booking_status == 'menunggu')
                                    <form action="{{ route('booking.destroy', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin batalkan booking?')">
                                            Batalkan Booking
                                        </button>
                                    </form>
                                @endif

                                @if((auth()->user()->isAdmin() || auth()->user()->isCs()) && $booking->booking_status == 'menunggu')
                                    <form action="{{ route('booking.update-status', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" name="booking_status" value="dikonfirmasi" class="btn btn-success">
                                            Konfirmasi Booking
                                        </button>
                                    </form>
                                    <form action="{{ route('booking.update-status', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" name="booking_status" value="batal" class="btn btn-danger">
                                            Tolak Booking
                                        </button>
                                    </form>
                                @endif

                                @if((auth()->user()->isAdmin() || auth()->user()->isCs()) && $booking->booking_status == 'dikonfirmasi')
                                    <form action="{{ route('booking.update-status', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" name="booking_status" value="selesai" class="btn btn-info">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('booking.index') }}" class="btn btn-secondary ms-auto">Kembali ke Daftar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection