<?php

namespace App\Http\Controllers;

use App\Http\Resources\DisabilityResource;
use App\Http\Requests\DisabilityRequest;
use Illuminate\Support\Facades\Auth;

use App\Models\Equipment;
use App\Models\Warrant;
use App\Models\User;
use App\Models\Disability;

class DisabilityController extends Controller
{
    
    public function index()
{
    $disabilities = Disability::with(['recorder', 'warrant', 'equipment'])
        ->where('is_deleted', false)
        ->paginate(10);

    return jsonResponse(true, 'Disabilities fetched successfully.', DisabilityResource::collection($disabilities));
}


    public function show($id)
    {
        $disability = Disability::with(['user', 'warrant', 'equipment'])->findOrFail($id);

        return jsonResponse(true, 'Disability details fetched successfully.', new DisabilityResource($disability));
    }

   
    public function store(DisabilityRequest $request)
{
    $validated = $request->validated();

    $validated['recorder_id'] = Auth::id();

    $warrantData = $request->warrant;
    $warrant = Warrant::firstOrCreate(
        ['phone_number' => $warrantData['phone_number']],
        [
            'first_name' => $warrantData['first_name'],
            'middle_name' => $warrantData['middle_name'],
            'last_name' => $warrantData['last_name'],
            'gender' => $warrantData['gender'],
            'id_image' => $warrantData['id_image'],
            'is_deleted' => false,
        ]
    );

    $validated['warrant_id'] = $warrant->id;

    // Handle the equipment
    $equipmentData = $request->equipment;
    $equipment = null;

    if (!empty($equipmentData)) {
        $equipment = Equipment::firstOrCreate(
            [
                'type' => $equipmentData['type'],
                'size' => $equipmentData['size'],
                'cause_of_need' => $equipmentData['cause_of_need'],
            ]
        );

        $validated['equipment_id'] = $equipment->id;
    }

    $disability = Disability::create($validated);

    return jsonResponse(true, 'Disability created successfully.', new DisabilityResource($disability), 201);
}

    public function update(DisabilityRequest $request, $id)
    {
        $disability = Disability::findOrFail($id);

        $validated = $request->validated();

        $disability->update($validated);

        return jsonResponse(true, 'Disability updated successfully.', new DisabilityResource($disability));
    }

    public function destroy($id)
    {
        $disability = Disability::findOrFail($id);

        $disability->update(['is_deleted' => true]);

        return jsonResponse(true, 'Disability deleted successfully.');
    }
}