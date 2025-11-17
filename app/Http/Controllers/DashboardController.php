<?php
namespace App\Http\Controllers;

use App\Models\MsTmsDriver;
use App\Models\MsVehicle;
use App\Models\MsRoute;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDriver = MsTmsDriver::count();
        $totalVehicle = class_exists('App\\Models\\MsVehicle') ? MsVehicle::count() : null;
        $totalRoute = class_exists('App\\Models\\MsRoute') ? MsRoute::count() : null;
        return view('dashboard', compact('totalDriver', 'totalVehicle', 'totalRoute'));
    }
}
