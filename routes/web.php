
<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatcherController;
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dispatcher', [DispatcherController::class, 'index'])->name('dispatcher');
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MsTmsDriverController;
use App\Http\Controllers\MsDriverController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('drivers', MsTmsDriverController::class);
Route::get('drivers/import-from-ms-driver', [MsTmsDriverController::class, 'importFromMsDriver'])->name('drivers.importFromMsDriver');

use App\Http\Controllers\MsVehicleController;
Route::resource('vehicles', MsVehicleController::class);
