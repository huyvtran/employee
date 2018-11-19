<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller {
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->employee = Role::where('name', 'Employee')->first();
        $this->admin    = Role::where('name', 'Admin')->first();
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login() {
        $credentials = request(['email', 'password']);
        $token       = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();

        if ($user->role_id == $this->admin->id || $user->role_id == $this->employee->id) {
            return $this->respondWithToken($token);
        }
        auth('api')->logout();
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        $user = auth('api')->user();
        if ($user->actived) {

            if ($user->role_id == $this->admin->id) {
                $res = [
                    'type'    => 'success',
                    'message' => 'Get Information Successfully!!!',
                    'data'    => new AuthResource($user),
                ];
                return response($res, 200);
            } else if ($user->role_id == $this->employee->id) {
                $res = [
                    'type'    => 'success',
                    'message' => 'Get Information Successfully!!!',
                    'data'    => new AuthResource($user),
                ];
                return response($res, 200);
            }

            auth('api')->logout();
        } else {
            auth('api')->logout();
            $res = [
                'type'    => 'error',
                'message' => 'Tài khoản chưa được kích hoạt. Vui lòng truy cập vào hộp thư để kích hoạt tài khoản',
                'data'    => [],
            ];
            return response($res, 200);
        }
        return response('Unauthorized Error', 401);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     * TTL 480 Hours
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60 * 1000,
        ]);
    }
}
