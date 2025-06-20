<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\API\v1\BaseAPI;
use Illuminate\Http\Request;
use App\Services\AuthSV;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AdminLoginRequest;
class AdminAuthController extends BaseAPI
{
    protected $AuthSV;
    public function __construct()
    {
        $this->AuthSV = new AuthSV();
    }

    // Register Admin
    public function register(StoreUserRequest $request){
        try {
            
            $params = $request->validated();
            $params['role'] = 'admin';
            $admin = $this->AuthSV->register($params);
            return $this->successResponse($admin, "Admin Register Successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    // Login Admin
   public function login(AdminLoginRequest $request){
    try{
        $credentials = $request->only('email', 'password');
        $adminData = $request->only('email', 'name');
        $role = 'admin';
        
        $loginResult = $this->AuthSV->login($credentials, $adminData, $role);
            
            // Assuming $loginResult is an array ['user' => ..., 'token' => ...]
            return response()->json($loginResult);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }


    // Refresh Token

    public function refreshToken()
    {
        try {
            $token = $this->AuthSV->refreshToken();
            return $token;
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

        // Logout
        public function logout()
        {
            try {
                $this->AuthSV->logout();
                return $this->successResponse(null, 'Admin logged out successfully');
            } catch (\Exception $e) {
                return $this->errorResponse($e->getMessage(), $e->getCode());
            }
        }
}
