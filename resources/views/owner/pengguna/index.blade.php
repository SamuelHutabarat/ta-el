@extends('layouts.crm')

@section('title', 'Kelola Pengguna - CRM Owner')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6">
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Kelola Pengguna Sistem</h2>
            <p class="text-muted-coffee">Kelola kredensial login akun Owner, Barista, dan Pelanggan.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('owner.pengguna.create') }}" class="btn btn-gold">
                <i class="fa-solid fa-user-plus me-2"></i> Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <div class="card-coffee">
        <div class="card-coffee-header">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-users-gear me-2 text-warning"></i>Database Login Pengguna</h5>
        </div>
        <div class="card-coffee-body">
            <div class="table-responsive">
                <table class="table table-coffee table-striped align-middle" id="penggunaTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pengguna</th>
                            <th>Alamat Email</th>
                            <th>Hak Akses / Role</th>
                            <th>Tanggal Registrasi</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="fw-bold text-light">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'owner')
                                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1.5"><i class="fa-solid fa-crown me-1"></i> Owner</span>
                                    @elseif($user->role === 'barista')
                                        <span class="badge bg-info text-dark fw-bold px-2.5 py-1.5"><i class="fa-solid fa-cash-register me-1"></i> Kasir</span>
                                    @else
                                        <span class="badge bg-secondary text-light fw-bold px-2.5 py-1.5"><i class="fa-solid fa-user me-1"></i> Pelanggan</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('owner.pengguna.edit', $user->id) }}" class="btn btn-sm btn-outline-gold py-1.5 px-2.5" title="Edit Akun">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('owner.pengguna.destroy', $user->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1.5 px-2.5" title="Hapus Akun">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary py-1.5 px-2.5 ms-1" disabled title="Anda tidak dapat menghapus akun Anda sendiri">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


