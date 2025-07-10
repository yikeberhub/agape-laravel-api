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
        $disability = Disability::with(['recorder', 'warrant', 'equipment'])->findOrFail($id);

        return jsonResponse(true, 'Disability details fetched successfully.', new DisabilityResource($disability));
    }

   
    public function store(DisabilityRequest $request)
    {
        $validated = $request->validated();
    
        $validated['recorder_id'] = Auth::id();
    
        $warrantData = $request->warrant;
    
        $warrantIdImage = null;
        if ($request->hasFile('warrant.id_image')) {
            $warrantIdImage = $request->file('warrant.id_image')->store('warrants/idImages', 'public');
            $warrantIdImage = basename($warrantIdImage);
        } elseif (!empty($warrantData['id_image'])) {
            $warrantIdImage = $warrantData['id_image'];
        }
    
        $warrant = Warrant::firstOrCreate(
            ['phone_number' => $warrantData['phone_number']],
            [
                'first_name' => $warrantData['first_name'],
                'middle_name' => $warrantData['middle_name'] ?? null,
                'last_name' => $warrantData['last_name'],
                'gender' => $warrantData['gender'] ?? null,
                'id_image' => $warrantIdImage,
                'is_deleted' => false,
            ]
        );
    
        $validated['warrant_id'] = $warrant->id;
    
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

    if ($request->hasFile('profile_image')) {
        $profileImagePath = $request->file('profile_image')->store('disabilities/profileImages', 'public');
        $validated['profile_image'] = basename($profileImagePath);
    }

    if ($request->hasFile('id_image')) {
        $idImagePath = $request->file('id_image')->store('disabilities/idImages', 'public');
        $validated['id_image'] = basename($idImagePath);
    }

    // Handle Warrant
    $warrantData = $request->warrant;
    if (!empty($warrantData)) {
        if (!empty($warrantData['id'])) {
            $warrant = Warrant::findOrFail($warrantData['id']);
            $warrantUpdate = [
                'first_name' => $warrantData['first_name'],
                'middle_name' => $warrantData['middle_name'] ?? null,
                'last_name' => $warrantData['last_name'],
                'phone_number' => $warrantData['phone_number'],
                'gender' => $warrantData['gender'] ?? null,
            ];

            if (isset($warrantData['id_image']) && $request->hasFile('warrant.id_image')) {
                $warrantIdImagePath = $request->file('warrant.id_image')->store('warrants/idImages', 'public');
                $warrantUpdate['id_image'] = basename($warrantIdImagePath);
            }

            $warrant->update($warrantUpdate);
        } else {
            $warrant = Warrant::firstOrCreate(
                ['phone_number' => $warrantData['phone_number']],
                [
                    'first_name' => $warrantData['first_name'],
                    'middle_name' => $warrantData['middle_name'] ?? null,
                    'last_name' => $warrantData['last_name'],
                    'gender' => $warrantData['gender'] ?? null,
                    'id_image' => $request->hasFile('warrant.id_image')
                        ? basename($request->file('warrant.id_image')->store('warrants/idImages', 'public'))
                        : ($warrantData['id_image'] ?? null),
                    'is_deleted' => false,
                ]
            );
        }

        $validated['warrant_id'] = $warrant->id;
    }

    // Handle Equipment
    $equipmentData = $request->equipment;
    if (!empty($equipmentData)) {
        if (!empty($equipmentData['id'])) {
            $equipment = Equipment::findOrFail($equipmentData['id']);
            $equipment->update([
                'type' => $equipmentData['type'],
                'size' => $equipmentData['size'],
                'cause_of_need' => $equipmentData['cause_of_need'],
            ]);
        } else {
            $equipment = Equipment::firstOrCreate(
                [
                    'type' => $equipmentData['type'],
                    'size' => $equipmentData['size'],
                    'cause_of_need' => $equipmentData['cause_of_need'],
                ]
            );
        }

        $validated['equipment_id'] = $equipment->id;
    }

    $disability->update($validated);

    return jsonResponse(true, 'Disability updated successfully.', new DisabilityResource($disability));
}


public function destroy($id)
{
    $disability = Disability::findOrFail($id);

    $disability->delete();

    return jsonResponse(true, 'Disability deleted successfully.');
}

}