<?php

namespace App\Http\Controllers;

use App\Http\Resources\DisabilityResource;
use App\Http\Requests\DisabilityRequest;
use App\Models\Disability;
use Illuminate\Support\Facades\Auth;

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

    $disability = Disability::create($validated);

    if (isset($request->warrant['id'])) {
        $disability->warrant_id = $request->warrant['id'];
    }

    // Associate the equipment if provided
    if (isset($request->equipment['id'])) {
        $disability->equipment_id = $request->equipment['id'];
    }

    // Save the updated disability record
    $disability->save();

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