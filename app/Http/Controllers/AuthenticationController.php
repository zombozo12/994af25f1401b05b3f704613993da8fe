<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Response\Response;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthenticationController extends Controller
{
    /**
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = new Response(now(), $request->fingerprint());

        $request->validated();

        $email = $request->json('email');
        $password = $request->json('password');

        $attempt = auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);

        if (!$attempt) {
            return $response->setBadResponse(
                [],
                HttpResponse::HTTP_UNAUTHORIZED,
                'Email/password is incorrect.'
            );
        }

        $user = auth()->user();

        $token = $user->createToken('auth_token')->accessToken;

        return $response->setOKResponse([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $response = new Response(now(), $request->fingerprint());

        $request->validated();

        $name = $request->json('name');
        $email = $request->json('email');
        $password = $request->json('password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        return $response->setOKResponse([
            'user' => $user,
        ]);
    }
}
