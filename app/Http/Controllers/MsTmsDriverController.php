<?php
namespace App\Http\Controllers;

use App\Models\MsTmsDriver;
use Illuminate\Http\Request;

use App\Models\MsDriver;

class MsTmsDriverController extends Controller
{
    public function index()
    {
        $drivers = MsTmsDriver::all();
        return view('drivers.index', compact('drivers'));
    }

    public function show($id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        return view('drivers.show', compact('driver'));
    }

    public function create()
    {
        return view('drivers.create');
    }

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
        MsTmsDriver::create($validated);
        return redirect()->route('drivers.index')->with('success', 'Driver berhasil ditambah');
    }

    public function edit($id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        return view('drivers.edit', compact('driver'));
    }

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
        return redirect()->route('drivers.index')->with('success', 'Driver berhasil diupdate');
    }

    public function destroy($id)
    {
        $driver = MsTmsDriver::findOrFail($id);
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver berhasil dihapus');
    }
    // Sinkronisasi driver dari ms_driver ke ms_tms_driver
    public function importFromMsDriver()
    {
        $drivers = MsDriver::all();
        $imported = 0;
        foreach ($drivers as $drv) {
            $exists = MsTmsDriver::where('ms_tms_driver_id', $drv->Drv_Id)->first();
            if (!$exists) {
                MsTmsDriver::create([
                    'ms_tms_driver_id' => $drv->Drv_Id,
                    'nama' => $drv->Drv_FistName,
                    'nama_keluarga' => $drv->Drv_LastName,
                    'bank_acc' => $drv->drv_no_rek,
                    'hp' => $drv->Drv_CellPhone,
                    'email' => $drv->drv_email,
                    'no_mitra' => null,
                ]);
                $imported++;
            }
        }
            $drivers = \DB::table('ms_driver')->get();
            foreach ($drivers as $drv) {
                \App\Models\MsTmsDriver::updateOrCreate(
                    ['ms_tms_driver_id' => $drv->Drv_Id],
                    [
                        'nama' => $drv->Nama,
                        'nama_keluarga' => $drv->Nama_Keluarga,
                        'bank_acc' => $drv->Bank_Acc,
                        'hp' => $drv->HP,
                        'email' => $drv->Email,
                        'no_mitra' => $drv->No_Mitra
                    ]
                );
            }
            return redirect()->route('drivers.index')->with('success', 'Data driver berhasil diimport!');
    }
}
