<?php
namespace App\Http\Controllers;

use App\Models\MsDriver;
use Illuminate\Http\Request;

class MsDriverController extends Controller
{
    public function index()
    {
        $drivers = MsDriver::all();
        return view('msdriver.index', compact('drivers'));
    }

    public function show($id)
    {
        $driver = MsDriver::findOrFail($id);
        return view('msdriver.show', compact('driver'));
    }

    public function create()
    {
        return view('msdriver.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Drv_Id' => 'required|string|max:50|unique:ms_driver,Drv_Id',
            'Drv_FistName' => 'nullable|string|max:100',
            'Drv_LastName' => 'nullable|string|max:100',
            'Drv_Addrase' => 'nullable|string|max:200',
            'Drv_BPlace' => 'nullable|string|max:100',
            'Drv_Bdate' => 'nullable|date',
            'Drv_StartDate' => 'nullable|date',
            'Drv_EndDate' => 'nullable|date',
            'Drv_Phone' => 'nullable|string|max:20',
            'Drv_CellPhone' => 'nullable|string|max:50',
            'Drv_License' => 'nullable|string|max:50',
            'Drv_LicenseExpire' => 'nullable|date',
            'Drv_LastEducation' => 'nullable|string|max:50',
            'Drv_SpvId' => 'nullable|string|max:50',
            'Drv_Merid' => 'nullable|string|max:1',
            'Drv_ChildNo' => 'nullable|string|max:10',
            'Drv_VhCode' => 'nullable|string|max:50',
            'Drv_DptCode' => 'nullable|string|max:50',
            'Drv_SimDate' => 'nullable|date',
            'Drv_Phone_Drt' => 'nullable|string|max:20',
            'Drv_Name_Drt' => 'nullable|string|max:50',
            'Drv_Instagram' => 'nullable|string|max:50',
            'Drv_Facebook' => 'nullable|string|max:50',
            'drv_no_rek' => 'nullable|string|max:50',
            'drv_bank_rek' => 'nullable|string|max:50',
            'drv_email' => 'required|string|max:100',
            'drv_branch_code' => 'nullable|string|max:50',
            'drv_ranking' => 'nullable|integer',
            'drv_gender' => 'nullable|string|max:1',
            'nik_driver' => 'nullable|string|max:50',
        ]);
        MsDriver::create($validated);
        return redirect()->route('msdriver.index')->with('success', 'Driver berhasil ditambah');
    }

    public function edit($id)
    {
        $driver = MsDriver::findOrFail($id);
        return view('msdriver.edit', compact('driver'));
    }

    public function update(Request $request, $id)
    {
        $driver = MsDriver::findOrFail($id);
        $validated = $request->validate([
            'Drv_FistName' => 'nullable|string|max:100',
            'Drv_LastName' => 'nullable|string|max:100',
            'Drv_Addrase' => 'nullable|string|max:200',
            'Drv_BPlace' => 'nullable|string|max:100',
            'Drv_Bdate' => 'nullable|date',
            'Drv_StartDate' => 'nullable|date',
            'Drv_EndDate' => 'nullable|date',
            'Drv_Phone' => 'nullable|string|max:20',
            'Drv_CellPhone' => 'nullable|string|max:50',
            'Drv_License' => 'nullable|string|max:50',
            'Drv_LicenseExpire' => 'nullable|date',
            'Drv_LastEducation' => 'nullable|string|max:50',
            'Drv_SpvId' => 'nullable|string|max:50',
            'Drv_Merid' => 'nullable|string|max:1',
            'Drv_ChildNo' => 'nullable|string|max:10',
            'Drv_VhCode' => 'nullable|string|max:50',
            'Drv_DptCode' => 'nullable|string|max:50',
            'Drv_SimDate' => 'nullable|date',
            'Drv_Phone_Drt' => 'nullable|string|max:20',
            'Drv_Name_Drt' => 'nullable|string|max:50',
            'Drv_Instagram' => 'nullable|string|max:50',
            'Drv_Facebook' => 'nullable|string|max:50',
            'drv_no_rek' => 'nullable|string|max:50',
            'drv_bank_rek' => 'nullable|string|max:50',
            'drv_email' => 'required|string|max:100',
            'drv_branch_code' => 'nullable|string|max:50',
            'drv_ranking' => 'nullable|integer',
            'drv_gender' => 'nullable|string|max:1',
            'nik_driver' => 'nullable|string|max:50',
        ]);
        $driver->update($validated);
        return redirect()->route('msdriver.index')->with('success', 'Driver berhasil diupdate');
    }

    public function destroy($id)
    {
        $driver = MsDriver::findOrFail($id);
        $driver->delete();
        return redirect()->route('msdriver.index')->with('success', 'Driver berhasil dihapus');
    }
}
