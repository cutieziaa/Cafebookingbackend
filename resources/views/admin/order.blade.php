@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Daftar Pesanan</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->menu->nama }}</td>
                <td>{{ $order->jumlah }}</td>
                <td>{{ ucfirst($order->tipe_order) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        <select name="status" class="form-control d-inline w-auto">
                            <option value="pending" {{ $order->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="diproses" {{ $order->status=='diproses'?'selected':'' }}>Diproses</option>
                            <option value="selesai" {{ $order->status=='selesai'?'selected':'' }}>Selesai</option>
                        </select>
                        <button class="btn btn-sm btn-success">Update</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
