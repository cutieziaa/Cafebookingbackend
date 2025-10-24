<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Users - Cafe Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-stat { transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-2px); }
        .user-role { font-size: 0.8rem; color: #6c757d; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Cafe Booking</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('booking.index') }}">Booking</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('menu.index') }}">Menu</a>
                        </li>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isCs())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('menu.create') }}">Kelola Menu</a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('users.index') }}">Kelola User</a>
                        </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->nama }}
                                <span class="user-role">({{ ucfirst(auth()->user()->peran) }})</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
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
                    @endguest
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

        <!-- Content Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Users</h2>
            @if(auth()->user()->isAdmin())
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus"></i> Tambah User
            </button>
            @endif
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Nomor WA</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $user->nama }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->peran == 'admin' ? 'danger' : ($user->peran == 'cs' ? 'warning' : 'success') }}">
                                        {{ strtoupper($user->peran) }}
                                    </span>
                                </td>
                                <td>{{ $user->nomor_wa ?? '-' }}</td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if(auth()->user()->isAdmin())
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal"
                                                data-user-id="{{ $user->id }}"
                                                data-user-nama="{{ $user->nama }}"
                                                data-user-email="{{ $user->email }}"
                                                data-user-peran="{{ $user->peran }}"
                                                data-user-nomor-wa="{{ $user->nomor_wa }}"
                                                title="Edit User">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Yakin hapus user {{ $user->nama }}?')"
                                                    title="Hapus User">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @else
                                        <button class="btn btn-secondary" disabled title="Tidak bisa hapus akun sendiri">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">Hanya admin</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-3"></i>
                                    <br>
                                    Tidak ada data user
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Info Summary -->
                <div class="mt-3 text-muted">
                    <small>
                        Total: <strong>{{ $users->count() }}</strong> user | 
                        Admin: <strong>{{ $users->where('peran', 'admin')->count() }}</strong> | 
                        CS: <strong>{{ $users->where('peran', 'cs')->count() }}</strong> | 
                        Customer: <strong>{{ $users->where('peran', 'customer')->count() }}</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create User -->
    @if(auth()->user()->isAdmin())
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createUserModalLabel">
                        <i class="fas fa-user-plus"></i> Tambah User Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama') }}" 
                                   placeholder="Masukkan nama lengkap" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="Masukkan email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Masukkan password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Konfirmasi password" required>
                        </div>

                        <div class="mb-3">
                            <label for="peran" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('peran') is-invalid @enderror" 
                                    id="peran" name="peran" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ old('peran') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="cs" {{ old('peran') == 'cs' ? 'selected' : '' }}>Customer Service</option>
                                <option value="customer" {{ old('peran') == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                            @error('peran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nomor_wa" class="form-label">Nomor WhatsApp</label>
                            <input type="text" class="form-control @error('nomor_wa') is-invalid @enderror" 
                                   id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}" 
                                   placeholder="Contoh: 081234567890">
                            @error('nomor_wa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opsional</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editUserModalLabel">
                        <i class="fas fa-user-edit"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" 
                                   id="edit_nama" name="nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" 
                                   id="edit_email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password</label>
                            <input type="password" class="form-control" 
                                   id="edit_password" name="password"
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <div class="form-text">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_peran" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_peran" name="peran" required>
                                <option value="admin">Administrator</option>
                                <option value="cs">Customer Service</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_nomor_wa" class="form-label">Nomor WhatsApp</label>
                            <input type="text" class="form-control" 
                                   id="edit_nomor_wa" name="nomor_wa"
                                   placeholder="Contoh: 081234567890">
                            <div class="form-text">Opsional</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sync-alt"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-3">
        <div class="container text-center">
            <small>&copy; 2024 Cafe Booking System. All rights reserved.</small>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Users Management Loaded');
        
        // Edit User Modal Handler
        const editUserModal = document.getElementById('editUserModal');
        if (editUserModal) {
            editUserModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const userNama = button.getAttribute('data-user-nama');
                const userEmail = button.getAttribute('data-user-email');
                const userPeran = button.getAttribute('data-user-peran');
                const userNomorWa = button.getAttribute('data-user-nomor-wa');

                console.log('Editing User ID:', userId);
                console.log('User Data:', { userNama, userEmail, userPeran, userNomorWa });

                // Update form fields
                document.getElementById('edit_nama').value = userNama;
                document.getElementById('edit_email').value = userEmail;
                document.getElementById('edit_peran').value = userPeran;
                document.getElementById('edit_nomor_wa').value = userNomorWa || '';

                // PERBAIKAN PENTING: Update form action dengan parameter user ID
                const form = document.getElementById('editUserForm');
                const newAction = `/users/${userId}`;
                form.action = newAction;
                
                console.log('Form action updated to:', newAction);
                console.log('Current form action:', form.action);
            });
        }

        // Clear form when create modal is hidden
        const createUserModal = document.getElementById('createUserModal');
        if (createUserModal) {
            createUserModal.addEventListener('hidden.bs.modal', function () {
                document.getElementById('createUserForm').reset();
            });
        }

        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        if (password && confirmPassword) {
            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Password tidak cocok');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        }

        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // Debug function to check all form actions
    function debugFormActions() {
        console.log('=== DEBUG FORM ACTIONS ===');
        console.log('Create form action:', document.getElementById('createUserForm')?.action);
        console.log('Edit form action:', document.getElementById('editUserForm')?.action);
        
        const deleteForms = document.querySelectorAll('form[method="POST"]');
        deleteForms.forEach((form, index) => {
            if (form.action.includes('/users/') && form.querySelector('input[name="_method"][value="DELETE"]')) {
                console.log(`Delete form ${index + 1}:`, form.action);
            }
        });
    }

    // Run debug on load
    setTimeout(debugFormActions, 1000);
    </script>
</body>
</html>