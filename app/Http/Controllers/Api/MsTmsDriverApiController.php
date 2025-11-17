<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MsTmsDriver;
use Illuminate\Http\Request;

class MsTmsDriverApiController extends Controller
{
    // List all drivers
    public function index()
    {
        return response()->json(MsTmsDriver::all());
    }

    // Show single driver
    public function show($id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        return response()->json($driver);
    }

    // Create driver
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ms_tms_driver_id' => 'required|string|max:50|unique:ms_tms_driver,ms_tms_driver_id',
            'nama' => 'nullable|string|max:50',
            'nama_keluarga' => 'nullable|string|max:50',
            'bank_acc' => 'nullable|string|max:50',
            'hp' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:50',
            'no_mitra' => 'nullable|string|max:50',
        ]);
        $driver = MsTmsDriver::create($validated);
        return response()->json($driver, 201);
    }

    // Update driver
    public function update(Request $request, $id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        $validated = $request->validate([
            'nama' => 'nullable|string|max:50',
            'nama_keluarga' => 'nullable|string|max:50',
            'bank_acc' => 'nullable|string|max:50',
            'hp' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:50',
            'no_mitra' => 'nullable|string|max:50',
        ]);
        $driver->update($validated);
        return response()->json($driver);
    }

    // Delete driver
    public function destroy($id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        $driver->delete();
        return response()->json(['message' => 'Driver deleted']);
    }
}
