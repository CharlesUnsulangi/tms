@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group mb-4">
            <a href="/dashboard" class="list-group-item list-group-item-action active">Dashboard</a>
            <a href="/drivers" class="list-group-item list-group-item-action">Master Driver</a>
            <a href="/vehicles" class="list-group-item list-group-item-action">Master Armada</a>
            <a href="/routes" class="list-group-item list-group-item-action">Master Rute</a>
            <a href="/dispatcher" class="list-group-item list-group-item-action">Dispatcher</a>
        </div>
    </div>
    <div class="col-md-9">
        <h2>Dashboard Logistik & Trucking</h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Driver</h5>
                        <p class="card-text">{{ $totalDriver ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Armada</h5>
                        <p class="card-text">{{ $totalVehicle ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Rute</h5>
                        <p class="card-text">{{ $totalRoute ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <h4>Quick Links</h4>
        <a href="/drivers" class="btn btn-primary">Kelola Driver</a>
        <a href="/vehicles" class="btn btn-success">Kelola Armada</a>
        <a href="/routes" class="btn btn-info">Kelola Rute</a>
        <a href="/dispatcher" class="btn btn-warning">Dispatcher</a>
    </div>
</div>
@endsection
