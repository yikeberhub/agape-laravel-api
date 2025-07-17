<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\DisabilityResource;
use App\Http\Requests\DisabilityRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;



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

    return jsonResponse(true, 'Disabilities fetched successfully.', [
        'disabilities' => DisabilityResource::collection($disabilities),
        'pagination' => [
            'current_page' => $disabilities->currentPage(),
            'last_page' => $disabilities->lastPage(),
            'per_page' => $disabilities->perPage(),
            'total' => $disabilities->total(),
            'from' => $disabilities->firstItem(),
            'to' => $disabilities->lastItem(),
            'next_page_url' => $disabilities->nextPageUrl(),
            'prev_page_url' => $disabilities->previousPageUrl(),
        ],
        
    ]);
}

public function show($id)
{
    try {
        $disability = Disability::with(['recorder', 'warrant', 'equipment'])->findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return jsonResponse(false, 'Disability not found.', [], 404);
    }

    return jsonResponse(true, 'Disability details fetched successfully.', new DisabilityResource($disability));
}

   
public function store(DisabilityRequest $request)
{
    $validated = $request->validated();
    $validated['recorder_id'] = Auth::id();

    $disability = null;

    DB::transaction(function () use (&$disability, $validated, $request) {
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
    });

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

public function filter(Request $request)
{
    $page = $request->query('page', 1);
    $perPage = $request->query('per_page', 10);
    $filters = $request->query();

    $query = Disability::with(['warrant', 'recorder', 'equipment']);

    if (!empty($filters['gender'])) {
        $query->where('gender', $filters['gender']);
    }

    if (isset($filters['is_provided'])) {
        $query->where('is_provided', $filters['is_provided']);
    }

    if (isset($filters['is_active'])) {
        $query->where('is_active', $filters['is_active']);
    }

    if (!empty($filters['year'])) {
        $query->whereYear('created_at', $filters['year']);
    }

    if (!empty($filters['min_age']) || !empty($filters['max_age'])) {
        $today = Carbon::today();

        if (!empty($filters['min_age'])) {
            $maxDob = $today->copy()->subYears($filters['min_age']);
            $query->whereDate('date_of_birth', '<=', $maxDob);
        }

        if (!empty($filters['max_age'])) {
            $minDob = $today->copy()->subYears($filters['max_age']);
            $query->whereDate('date_of_birth', '>=', $minDob);
        }
    }

    if (!empty($filters['start_date'])) {
        $query->whereDate('created_at', '>=', $filters['start_date']);
    }

    if (!empty($filters['end_date'])) {
        $query->whereDate('created_at', '<=', $filters['end_date']);
    }

    $disabilities = $query->paginate($perPage, ['*'], 'page', $page);

    return jsonResponse(true, 'Disabilities fetched successfully.', $disabilities, 200);
}


public function search(Request $request)
{
    $query = Disability::query()->with(['warrant', 'recorder', 'equipment']);

    $keyword = $request->input('q');

    if ($keyword) {
        $query->where(function ($q) use ($keyword) {
            $q->where('first_name', 'like', "%{$keyword}%")
              ->orWhere('middle_name', 'like', "%{$keyword}%")
              ->orWhere('last_name', 'like', "%{$keyword}%")
              ->orWhere('phone_number', 'like', "%{$keyword}%")
              ->orWhere('region', 'like', "%{$keyword}%")
              ->orWhere('city', 'like', "%{$keyword}%")
              ->orWhereHas('warrant', function ($q2) use ($keyword) {
                  $q2->where('first_name', 'like', "%{$keyword}%")
                     ->orWhere('last_name', 'like', "%{$keyword}%");
              })
              ->orWhereHas('equipment', function ($q3) use ($keyword) {
                  $q3->where('type', 'like', "%{$keyword}%")
                     ->orWhere('cause_of_need', 'like', "%{$keyword}%");
              });
        });
    }

    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $disabilities = $query->paginate($perPage, ['*'], 'page', $page);

    return jsonResponse(true, 'Disabilities fetched successfully.', $disabilities, 200);
}


}