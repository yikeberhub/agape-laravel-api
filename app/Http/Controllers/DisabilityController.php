<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

use App\Http\Resources\DisabilityResource;
use App\Http\Requests\DisabilityRequest;
use App\Models\Equipment;
use App\Models\Warrant;
use App\Models\User;
use App\Models\Disability;

class DisabilityController extends Controller
{
    public function index()
    {
        $disabilities = Disability::with(['recorder', 'warrant', 'equipment','equipment.type','equipment.subType'])
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
            $disability = Disability::with(['recorder', 'warrant', 'equipment','equipment.type','equipment.subType'])->findOrFail($id);
            return jsonResponse(true, 'Disability details fetched successfully.', new DisabilityResource($disability));
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Disability not found.', [], 404);
        }
    }

    public function store(DisabilityRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['recorder_id'] = Auth::id();

            $disability = null;

            DB::transaction(function () use (&$disability, $validated, $request) {
                $warrantData = $request->warrant;

                $warrantIdImage = null;
                if ($request->hasFile('warrant.id_image')) {
                    $warrantIdImage = basename($request->file('warrant.id_image')->store('warrants/idImages', 'public'));
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
                if (!empty($equipmentData)) {
                    $equipment = Equipment::firstOrCreate(
                        [
                            'type_id' => $equipmentData['type_id'],
                            'sub_type_id' => $equipmentData['sub_type_id'],
                            'size' => $equipmentData['size'],
                            'cause_of_need' => $equipmentData['cause_of_need'],
                        ]
                    );
                    $validated['equipment_id'] = $equipment->id;
                }

                $disability = Disability::create($validated);
            });

            return jsonResponse(true, 'Disability created successfully.', new DisabilityResource($disability), 201);
        } catch (Exception $e) {
            return jsonResponse(false, 'Failed to create disability.', null, 500, ['error' => $e->getMessage()]);
        }
    }

    public function update(DisabilityRequest $request, $id)
    {
        try {
            $disability = Disability::findOrFail($id);
            $validated = $request->validated();

            if ($request->hasFile('profile_image')) {
                $validated['profile_image'] = basename($request->file('profile_image')->store('disabilities/profileImages', 'public'));
            }

            if ($request->hasFile('id_image')) {
                $validated['id_image'] = basename($request->file('id_image')->store('disabilities/idImages', 'public'));
            }

            $warrantData = $request->warrant;
            if (!empty($warrantData)) {
                if (!empty($warrantData['id'])) {
                    $warrant = Warrant::findOrFail($warrantData['id']);
                    $updateData = [
                        'first_name' => $warrantData['first_name'],
                        'middle_name' => $warrantData['middle_name'] ?? null,
                        'last_name' => $warrantData['last_name'],
                        'phone_number' => $warrantData['phone_number'],
                        'gender' => $warrantData['gender'] ?? null,
                    ];

                    if ($request->hasFile('warrant.id_image')) {
                        $updateData['id_image'] = basename($request->file('warrant.id_image')->store('warrants/idImages', 'public'));
                    }

                    $warrant->update($updateData);
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
                        'type_id' => $equipmentData['type_id'],
                        'sub_type_id' => $equipmentData['sub_type_id'],
                        'size' => $equipmentData['size'],
                        'cause_of_need' => $equipmentData['cause_of_need'],
                    ]);
                } else {
                    $equipment = Equipment::firstOrCreate(
                        [
                            'type_id' => $equipmentData['type_id'],
                            'sub_type_id' => $equipmentData['sub_type_id'],
                            'size' => $equipmentData['size'],
                            'cause_of_need' => $equipmentData['cause_of_need'],
                        ]
                    );
                }

                $validated['equipment_id'] = $equipment->id;
            }

            $disability->update($validated);

            return jsonResponse(true, 'Disability updated successfully.', new DisabilityResource($disability));
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Disability not found.', null, 404);
        } catch (Exception $e) {
            return jsonResponse(false, 'Failed to update disability.', null, 500, ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $disability = Disability::findOrFail($id);
            $disability->delete();

            return jsonResponse(true, 'Disability deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return jsonResponse(false, 'Disability not found.', null, 404);
        } catch (Exception $e) {
            return jsonResponse(false, 'Failed to delete disability.', null, 500, ['error' => $e->getMessage()]);
        }
    }

    public function filter(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);
        $filters = $request->query();

        $query = Disability::with(['warrant', 'recorder', 'equipment','equipment.type','equipment.subType']);

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

    public function search(Request $request)
    {
        $query = Disability::query()->with(['warrant', 'recorder', 'equipment','equipment.type','equipment.subType']);

        $keyword = $request->input('q');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('middle_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('phone_number', 'like', "%{$keyword}%")
                    ->orWhere('gender', $keyword)
                    ->orWhere('region', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhereHas('warrant', function ($q2) use ($keyword) {
                        $q2->where('first_name', 'like', "%{$keyword}%")
                            ->orWhere('last_name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('equipment.type', function ($q4) use ($keyword) {
                        $q4->where('name', 'like', "%{$keyword}%");
                    });
                    
            });
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $disabilities = $query->paginate($perPage, ['*'], 'page', $page);

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
}
