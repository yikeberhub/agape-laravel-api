<?php 

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Helpers\jsonResponse;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::where('is_deleted', false)->get();
        return jsonResponse(true, 'Equipments fetched successfully.', $equipments);
    }


public function show($id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment || $equipment->is_deleted) {
            return jsonResponse(false, 'Equipment not found.', [], 404);
        }

        return jsonResponse(true, 'Equipment details fetched successfully.', $equipment);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_type' => 'required|string',
            'size' => 'required|string',
            'cause_of_need' => 'required|string',
        ]);

        $equipment = Equipment::create($validated);
        return jsonResponse(true, 'Equipment created successfully.', $equipment, 201);
    }


    public function update(Request $request, $id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment || $equipment->is_deleted) {
            return jsonResponse(false, 'Equipment not found.', [], 404);
        }

        $validated = $request->validate([
            'equipment_type' => 'sometimes|required|string',
            'size' => 'sometimes|required|string',
            'cause_of_need' => 'sometimes|required|string',
        ]);

        $equipment->update($validated);
        return jsonResponse(true, 'Equipment updated successfully.', $equipment);
    }

    public function destroy($id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment || $equipment->is_deleted) {
            return jsonResponse(false, 'Equipment not found.', [], 404);
        }

        $equipment->is_deleted = true;
        $equipment->save();
        
        return jsonResponse(true, 'Equipment deleted successfully.');
    }

}