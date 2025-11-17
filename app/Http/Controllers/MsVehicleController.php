<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MsVehicle;

class MsVehicleController extends Controller
{
    public function index()
    {
        $vehicles = MsVehicle::all();
        return view('vehicles.index', compact('vehicles'));
    }

    public function show($id)
    {
        $vehicle = MsVehicle::findOrFail($id);
        return view('vehicles.show', compact('vehicle'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        MsVehicle::create($request->all());
        return redirect()->route('vehicles.index');
    }

    public function edit($id)
    {
        $vehicle = MsVehicle::findOrFail($id);
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = MsVehicle::findOrFail($id);
        $vehicle->update($request->all());
        return redirect()->route('vehicles.index');
    }

    public function destroy($id)
    {
        $vehicle = MsVehicle::findOrFail($id);
        $vehicle->delete();
        return redirect()->route('vehicles.index');
    }
}
