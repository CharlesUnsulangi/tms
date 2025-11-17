@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Master Driver</h1>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary mb-3">Tambah Driver</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Nama Keluarga</th>
                <th>Bank Acc</th>
                <th>HP</th>
                <th>Email</th>
                <th>No Mitra</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($drivers as $driver)
            <tr>
                <td>{{ $driver->ms_tms_driver_id }}</td>
                <td>{{ $driver->nama }}</td>
                <td>{{ $driver->nama_keluarga }}</td>
                <td>{{ $driver->bank_acc }}</td>
                <td>{{ $driver->hp }}</td>
                <td>{{ $driver->email }}</td>
                <td>{{ $driver->no_mitra }}</td>
                <td>
                    <a href="{{ route('drivers.show', $driver->ms_tms_driver_id) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('drivers.edit', $driver->ms_tms_driver_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('drivers.destroy', $driver->ms_tms_driver_id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus driver?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
