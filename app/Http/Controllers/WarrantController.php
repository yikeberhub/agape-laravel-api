<?php

namespace App\Http\Controllers;

use App\Models\Warrant;
use Illuminate\Http\Request;
use App\Helpers\jsonResponse;

class WarrantController extends Controller
{
    public function index()
    {
        $warrants = Warrant::where('is_deleted', false)->paginate(10);
        return jsonResponse(true, 'Warrants fetched successfully.', $warrants);
    }

    public function show($id)
    {
        $warrant = Warrant::find($id);

        if (!$warrant || $warrant->is_deleted) {
            return jsonResponse(false, 'Warrant not found.', [], 404);
        }

        return jsonResponse(true, 'Warrant details fetched successfully.', $warrant);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'id_image'=>'required|string',
            'gender'=>'required|string',
            'phone_number'=>'required|string|max:11',
        ]);

        $warrant = Warrant::create($validated);
        return jsonResponse(true, 'Warrant created successfully.', $warrant, 201);
    }
}