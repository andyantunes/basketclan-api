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
    private $userData = ['id', 'name', 'email', 'deleted_at'];

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
            $user = User::where('email', $request->email)->first($this->userData);
            $user->active = is_null($user->deleted_at);
            $user->token = $user->createToken('auth_token')->plainTextToken;

            unset($user->deleted_at);

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
