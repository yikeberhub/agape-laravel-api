<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Disability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\jsonResponse;

class AdminController extends Controller
{
    public function stat()
    {
        $disabilities = Disability::with('equipment', 'recorder')->get();

        $now = Carbon::now();
        $today = $now->toDateString();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        $today_records = Disability::whereDate('created_at', $today)->count();
        $weekly_records = Disability::whereBetween('created_at', [$weekStart, $now])->count();
        $monthly_records = Disability::whereBetween('created_at', [$monthStart, $now])->count();

        $ages = $disabilities->map(function ($d) {
            return Carbon::parse($d->date_of_birth)->age;
        });

        $avg_age = round($ages->avg(), 1);
        $min_age = $ages->min();
        $max_age = $ages->max();

        $incomplete_records = $disabilities->filter(function ($d) {
            return !$d->phone_number || !$d->equipment_id;
        })->count();

        $image_uploaded_percent = round(
            $disabilities->filter(fn($d) => $d->profile_image && $d->id_image)->count() / max($disabilities->count(), 1) * 100,
            1
        );

        $region_data = Disability::select('region', DB::raw('count(*) as count'))
            ->groupBy('region')
            ->get();

        $top_regions = $region_data->sortByDesc('count')->take(3)->values();

        // ✅ FIXED: Use correct table name 'equipments' instead of 'equipment'
        $equipment_distribution = Disability::join('equipments as e', 'disabilities.equipment_id', '=', 'e.id')
            ->select('e.type', DB::raw('count(*) as count'))
            ->groupBy('e.type')
            ->get();

        $top_recorders = Disability::select('recorder_id', DB::raw('count(*) as count'))
            ->groupBy('recorder_id')
            ->orderByDesc('count')
            ->with('recorder')
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'user' => optional($d->recorder)->first_name . ' ' . optional($d->recorder)->last_name,
                'count' => $d->count
            ]);

        $users = User::all();

        $monthly_registration = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $monthly_disability_records = Disability::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $response = [
            'disability' => [
                'total_records' => $disabilities->count(),
                'num_of_males' => $disabilities->where('gender', 'male')->count(),
                'num_of_females' => $disabilities->where('gender', 'female')->count(),
                'approved_records' => $disabilities->where('is_provided', true)->count(),
                'unapproved_records' => $disabilities->where('is_provided', false)->count(),
                'time_stats' => compact('today_records', 'weekly_records', 'monthly_records'),
                'age_stats' => compact('avg_age', 'min_age', 'max_age'),
                'data_quality' => compact('incomplete_records', 'image_uploaded_percent'),
                'equipment_distribution' => $equipment_distribution,
                'region_data' => $region_data,
                'top_regions' => $top_regions,
                'top_recorders' => $top_recorders,
                'monthly_records' => $monthly_disability_records,
            ],
            'users' => [
                'total_users' => $users->count(),
                'admins' => $users->where('role', 'admin')->count(),
                'active_admins' => $users->where('role', 'admin')->where('is_active', true)->count(),
                'blocked_admins' => $users->where('role', 'admin')->where('is_active', false)->count(),
                'sub_admins' => $users->where('role', 'field_worker')->count(),
                'active_sub_admins' => $users->where('role', 'field_worker')->where('is_active', true)->count(),
                'blocked_sub_admins' => $users->where('role', 'field_worker')->where('is_active', false)->count(),
                'monthly_registration' => $monthly_registration
            ]
        ];

        return jsonResponse(true, 'Dashboard data fetched successfully.', $response);
    }

public function userSearch(Request $request)
{
    $query = User::query();

    if ($search = $request->input('q')) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('middle_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%")
              ->orWhere('country', 'like', "%$search%")
              ->orWhere('region', 'like', "%$search%")
              ->orWhere('city', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('phone_number', 'like', "%$search%");
        });
    }

    if ($role = $request->input('role')) {
        $query->where('role', $role);
    }
    if ($is_blocked = $request->input('is_blocked')) {
        $query->where('is_blocked', $is_blocked);
    }

    if (!is_null($request->input('is_active'))) {
        $query->where('is_active', $request->input('is_active'));
    }

    $perPage = $request->input('per_page', 10);
    $users = $query->paginate($perPage);

    return jsonResponse(true, 'Users fetched successfully.', $users);
}

}
