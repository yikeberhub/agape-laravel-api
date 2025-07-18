<?php

namespace App\Http\Controllers;

use App\Models\EquipmentSubType;
use Illuminate\Http\Request;
use App\Models\EquipmentType;
use App\Http\Resources\EquipmentSubTypeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EquipmentSubTypeController extends Controller
{
    public function index()
    {
        try {
            $subTypes = EquipmentSubType::with('equipmentType')->get();
            return jsonResponse(true, 'Sub types retrieved successfully', EquipmentSubTypeResource::collection($subTypes));
        } catch (\Exception $e) {
            return jsonResponse(false, 'Failed to retrieve sub types', null, 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $subType = EquipmentSubType::with('equipmentType')->findOrFail($id);
            return jsonResponse(true, 'Sub type retrieved successfully', new EquipmentSubTypeResource($subType));
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Sub type not found', null, 404);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred', null, 500, $e->getMessage());
        }
    }

    public function store(Request $request, $equipmentTypeId)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $equipmentType = EquipmentType::findOrFail($equipmentTypeId);

            $subType = $equipmentType->subTypes()->create([
                'name' => $validated['name'],
            ]);

            return jsonResponse(true, 'Sub type created successfully', $subType, 201);
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Equipment type not found', null, 404);
        } catch (ValidationException $e) {
            return jsonResponse(false, 'Validation failed', null, 422, $e->errors());
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while creating sub type', null, 500, $e->getMessage());
        }
    }

    public function update(Request $request, $equipmentTypeId, $subTypeId)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $subType = EquipmentSubType::where('equipment_type_id', $equipmentTypeId)
                ->where('id', $subTypeId)
                ->firstOrFail();

            $subType->update([
                'name' => $validated['name'],
            ]);

            return jsonResponse(true, 'Sub type updated successfully', $subType);
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Sub type not found', null, 404);
        } catch (ValidationException $e) {
            return jsonResponse(false, 'Validation failed', null, 422, $e->errors());
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while updating sub type', null, 500, $e->getMessage());
        }
    }

    public function destroy($equipmentTypeId, $subTypeId)
    {
        try {
            $subType = EquipmentSubType::where('equipment_type_id', $equipmentTypeId)
                ->where('id', $subTypeId)
                ->firstOrFail();

            $subType->delete();

            return jsonResponse(true, 'Sub type deleted successfully');
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Sub type not found', null, 404);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while deleting sub type', null, 500, $e->getMessage());
        }
    }
}
