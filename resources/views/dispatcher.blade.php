@extends('layouts.app')

@section('content')
<h2>Dispatcher</h2>
<div class="row">
    <div class="col-md-3">
        <div class="list-group mb-4">
            <a href="/dashboard" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="/drivers" class="list-group-item list-group-item-action">Master Driver</a>
            <a href="/vehicles" class="list-group-item list-group-item-action">Master Armada</a>
            <a href="/routes" class="list-group-item list-group-item-action">Master Rute</a>
            <a href="/dispatcher" class="list-group-item list-group-item-action active">Dispatcher</a>
        </div>
    </div>
    <div class="col-md-9">
        <h4>Dispatch Board</h4>
        <div class="alert alert-info">Fitur: assign order ke armada/driver, monitoring status pengiriman, tracking posisi armada.</div>
        <div class="card mb-4">
            <div class="card-body">
                <p>Daftar order pengiriman, status, dan aksi dispatch akan ditampilkan di sini.</p>
                <p><em>(Integrasi data order, armada, dan driver sesuai kebutuhan operasional)</em></p>
            </div>
        </div>
        <a href="/orders" class="btn btn-primary">Kelola Order</a>
        <a href="/drivers" class="btn btn-success">Kelola Driver</a>
        <a href="/vehicles" class="btn btn-info">Kelola Armada</a>
    </div>
</div>
@endsection
