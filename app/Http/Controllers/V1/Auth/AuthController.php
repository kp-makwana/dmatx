<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
  protected AuthService $auth;

  public function __construct(AuthService $auth)
  {
    $this->auth = $auth;
  }

  public function showRegisterForm()
  {
    return view('auth.register');
  }

  public function showLoginForm()
  {
    return view('auth.login');
  }

  public function register(RegisterRequest $request)
  {
    $this->auth->register($request->validated());
    return redirect()->route('dashboard')->with('success', 'Registration successful!');
  }

  public function login(LoginRequest $request)
  {
    $this->auth->login($request->validated());
    return redirect()->route('dashboard')->with('success', 'Logged in successfully!');
  }

  public function logout()
  {
    auth()->logout();
    return redirect()->route('login')->with('success', 'Logged out successfully!');
  }

}
