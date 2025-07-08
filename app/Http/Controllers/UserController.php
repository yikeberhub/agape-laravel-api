<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

use App\Helpers\jsonResponse;

class UserController extends Controller
{
    
    public function getUsers(Request $request)
    { 
        if (!Auth::check()) {
            return jsonResponse(false, 'Unauthorized', null, 401);
        }
    
        $query = User::where('is_active', true);
        $user = Auth::user();
    
        if ($user->role !== 'admin') {
            $query->where('id', $user->id);
        }
    
        if ($searchTerm = $request->query('search')) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('email', 'like', "%$searchTerm%")
                  ->orWhere('gender', 'like', "%$searchTerm%")
                  ->orWhere('first_name', 'like', "%$searchTerm%")
                  ->orWhere('middle_name', 'like', "%$searchTerm%")
                  ->orWhere('last_name', 'like', "%$searchTerm%")
                  ->orWhere('phone_number', 'like', "%$searchTerm%")
                  ->orWhere('role', 'like', "%$searchTerm%");
            });
        }
    
        $users = $query->paginate(10);
    
        return jsonResponse(true, 'Users fetched successfully.', $users);
    }
    public function createUser(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return jsonResponse(false, 'Unauthorized', null, 403);
        }

        $validatedData = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,field_worker',
            'phone_number' => 'required|string|max:11|unique:users,phone_number',
        ]);

        try {
            $user = User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'first_name' => $validatedData['first_name'],
                'role' => $validatedData['role'],
                'phone_number' => $validatedData['phone_number'],
                'is_active' => true,
            ]);

            return jsonResponse(true, 'User created successfully.', $user, 201);
        } catch (QueryException $e) {
            return jsonResponse(false, 'Database error: ' . $e->getMessage(), null, 400);
        } catch (Exception $e) {
            return jsonResponse(false, 'An error occurred: ' . $e->getMessage(), null, 500);
        }
    }

    public function showUserDetail($id)
    {
        $user = User::find($id);

        if (!$user) {
            return jsonResponse(false, 'User not found.', null, 404);
        }

        if (!$user->is_active) {
            return jsonResponse(false, 'User is deactivated and cannot be accessed.', null, 403);
        }

        return jsonResponse(true, 'User details fetched successfully.', $user);
    }

    public function blockedUsers()
    {
        $blockedUsers = User::where('is_active', false)->where('role', 'field_worker')->get();

        return jsonResponse(true, 'Blocked users retrieved successfully.', $blockedUsers);
    }

    public function blockUser($id)
    {
        $user = User::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            return jsonResponse(false, 'You are not allowed to perform this action.', null, 403);
        }

        $user->is_active = !$user->is_active; // Toggle active status
        $user->save();

        return jsonResponse(true, $user->is_active ? 'User unblocked successfully.' : 'User blocked successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if (Auth::user()->role !== 'admin') {
            return jsonResponse(false, 'You are not allowed to perform this action.', null, 403);
        }

        $user->delete();

        return jsonResponse(true, 'User deleted permanently.', null, 204);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return jsonResponse(true, 'Password updated successfully.');
    }

    public function filter(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', 'like', '%' . $request->role . '%');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                  ->orWhere('gender', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('middle_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        $users = $query->paginate(10);

        return jsonResponse(true, 'Users fetched successfully.', $users);
    }
}