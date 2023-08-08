<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            $user->token = $user->createToken('auth_token')->plainTextToken;

            $message = 'Login bem-sucedido!';
            return $this->handleResponse($user, $message);
        }

        $message = 'As credenciais fornecidas não correspondem aos nossos registros';
        return $this->handleError($message, ['error' => 'Credenciais inválidas.'], 401);
    }

    /**
     * Log the user out of the application
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        $message = 'Logout bem-sucedido!';
        return $this->handleResponse([], $message);
    }
}
