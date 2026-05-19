<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
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
            'nickname' => $request->nickname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(
            $this->tokenResponse($token) + ['user' => new UserResource($user)],
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = auth('api')->attempt($request->only('email', 'password'));

        if (! $token) {
            return response()->json(['message' => 'メールアドレスまたはパスワードが正しくありません'], 401);
        }

        return response()->json(
            $this->tokenResponse($token) + ['user' => new UserResource(auth('api')->user())]
        );
    }

    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();
        $user->load(['activeSubscription.plan']);

        return response()->json(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();
        $user->fill($request->validated());
        $user->save();

        return response()->json(new UserResource($user));
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => '現在のパスワードが正しくありません'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'パスワードを変更しました']);
    }

    public function withdraw(): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();
        $user->status = 'withdrawn';
        $user->save();

        auth('api')->logout();

        return response()->json(null, 204);
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
