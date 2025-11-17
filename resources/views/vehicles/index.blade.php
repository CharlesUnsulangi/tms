@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Master Armada</h1>
    <a href="{{ route('vehicles.create') }}" class="btn btn-primary mb-3">Tambah Armada</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nomor Polisi</th>
                <th>Jenis</th>
                <th>Merk</th>
                <th>Tahun</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $vehicle)
            <tr>
                <td>{{ $vehicle->id }}</td>
                <td>{{ $vehicle->no_polisi }}</td>
                <td>{{ $vehicle->jenis }}</td>
                <td>{{ $vehicle->merk }}</td>
                <td>{{ $vehicle->tahun }}</td>
                <td>
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus armada?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
