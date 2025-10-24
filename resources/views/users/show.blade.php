<!-- resources/views/users/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Detail User: {{ $user->nama }}</h4>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <span class="text-white fw-bold fs-4">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nama</th>
                            <td>{{ $user->nama }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                <span class="badge bg-{{ $user->peran == 'admin' ? 'danger' : ($user->peran == 'cs' ? 'warning' : 'success') }}">
                                    {{ strtoupper($user->peran) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Nomor WhatsApp</th>
                            <td>{{ $user->nomor_wa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <td>{{ $user->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Update</th>
                            <td>{{ $user->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>

                    @if(auth()->user()->isAdmin())
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-md-2">Edit</a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Yakin hapus user?')">Hapus</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection