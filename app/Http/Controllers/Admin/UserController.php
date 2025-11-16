<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $users = User::withTrashed()->get();
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:customer,admin,owner,cashier',
            'phone' => 'nullable|string|max:15'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone
        ]);

        return $this->successResponse($user, 'User created successfully', 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user, 'User retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:customer,admin,owner,cashier',
            'phone' => 'nullable|string|max:15'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $updateData = $request->only(['name', 'email', 'role', 'phone']);
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        return $this->successResponse($user, 'User updated successfully');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->delete();
        return $this->successResponse(null, 'User deleted successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::withTrashed()->find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        if ($user->trashed()) {
            $user->restore();
            $message = 'User activated successfully';
        } else {
            $user->delete();
            $message = 'User deactivated successfully';
        }

        return $this->successResponse($user->fresh(), $message);
    }

    public function resetPassword($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $newPassword = 'password';
        $user->update(['password' => Hash::make($newPassword)]);

        return $this->successResponse(
            ['email' => $user->email, 'new_password' => $newPassword],
            'Password reset successfully'
        );
    }
}