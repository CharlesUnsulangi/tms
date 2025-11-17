<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistik & Trucking Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">TMS Logistik</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/drivers">Master Driver</a></li>
                    <li class="nav-item"><a class="nav-link" href="/vehicles">Master Armada</a></li>
                    <li class="nav-item"><a class="nav-link" href="/routes">Master Rute</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dispatcher">Dispatcher</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 bg-light vh-100 p-0">
                <div class="list-group list-group-flush py-3">
                    <a href="/dashboard" class="list-group-item list-group-item-action">Dashboard</a>
                    <button class="list-group-item list-group-item-action" type="button" data-bs-toggle="collapse" data-bs-target="#masterMenu" aria-expanded="false" aria-controls="masterMenu">
                        Pengaturan Master
                    </button>
                    <div class="collapse show" id="masterMenu">
                        <a href="/drivers" class="list-group-item list-group-item-action ms-3">Master Driver</a>
                        <a href="/vehicles" class="list-group-item list-group-item-action ms-3">Master Armada</a>
                        <a href="/routes" class="list-group-item list-group-item-action ms-3">Master Rute</a>
                    </div>
                    <a href="/dispatcher" class="list-group-item list-group-item-action">Dispatcher</a>
                </div>
            </div>
            <div class="col-md-10 py-4">
                @yield('content')
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</html>
