<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json($this->tokenResponse($token), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = auth('api')->attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
        }

        return response()->json($this->tokenResponse($token));
    }

    public function me(): JsonResponse
    {
        return response()->json(['data' => auth('api')->user()]);
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return response()->json($this->tokenResponse($token));
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'ログアウトしました']);
    }

    private function tokenResponse(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
