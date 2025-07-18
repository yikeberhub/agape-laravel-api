<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentTypeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class EquipmentTypeController extends Controller
{
    public function index()
    {
        try {
            $types = EquipmentType::all();
            return jsonResponse(true, 'Equipment types retrieved successfully', EquipmentTypeResource::collection($types));
        } catch (\Exception $e) {
            return jsonResponse(false, 'Failed to retrieve equipment types', null, 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $type = EquipmentType::with('subTypes')->findOrFail($id);
            return jsonResponse(true, 'Equipment type retrieved successfully', new EquipmentTypeResource($type));
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Equipment type not found', null, 404);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred', null, 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:equipment_types,name',
            ]);

            $type = EquipmentType::create($validated);
            return jsonResponse(true, 'Equipment type created successfully', $type, 201);
        } catch (ValidationException $e) {
            return jsonResponse(false, 'Validation failed', null, 422, $e->errors());
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while creating equipment type', null, 500, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $type = EquipmentType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|unique:equipment_types,name,' . $id,
            ]);

            $type->update($validated);
            return jsonResponse(true, 'Equipment type updated successfully', $type);
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Equipment type not found', null, 404);
        } catch (ValidationException $e) {
            return jsonResponse(false, 'Validation failed', null, 422, $e->errors());
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while updating equipment type', null, 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $type = EquipmentType::findOrFail($id);
            $type->delete();

            return jsonResponse(true, 'Equipment type deleted successfully', null, 204);
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Equipment type not found', null, 404);
        } catch (\Exception $e) {
            return jsonResponse(false, 'Error occurred while deleting equipment type', null, 500, $e->getMessage());
        }
    }
}
