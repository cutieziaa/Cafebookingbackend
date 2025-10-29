@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Riwayat Pesanan Saya</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->menu->nama }}</td>
                <td>{{ $order->jumlah }}</td>
                <td>{{ ucfirst($order->tipe_order) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
