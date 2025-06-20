<?php

namespace App\Http\Controllers\API\v1\Admin;

use App\Http\Controllers\API\v1\BaseAPI;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserSV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseAPI
{
    /**
     * Display a listing of the resource.
     */
    private $userService;
    public function __construct(UserSV $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $filters = [];

        if ($request->has('status')) {
            $filters['status'] = $request->query('status');
        }

        $params = [
            'filterBy' => $filters,
            'perPage' => $request->query('perPage', 1000), // default to 10
        ];

        $users = $this->userService->getAllUsers($params);
        return $this->successResponse($users, 'Users retrieved successfully');

    }

    public function getUserById($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $params = $request->validated();
            $params['status'] = $params['status'] ?? 1; 
            DB::beginTransaction();
            $user = $this->userService->createUser($params);
            DB::commit();
            return $this->successResponse($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Customer not found!'], 404);
        }
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {
            $params = $request->validated();
            DB::beginTransaction();
            $updatedUser = $this->userService->updateUser($params, $id);
            DB::commit();
            return $this->successResponse($updatedUser, 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function deactivateUser($id)
    {
        try {
             DB::beginTransaction();
            $user = $this->userService->deactivateUser($id);
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }
            DB::commit();
            return $this->successResponse($user, 'User deactivated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role!== 'customer') {
            return response()->json(['message' => 'Customer not found!'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'Customer deleted successfully!']);
    }
}
