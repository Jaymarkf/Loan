<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function showLoginForm()
    {
        return view('login');
    }
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);
    
        // Fetch the user manually
        $user = DB::table('users')->where('email', $credentials['email'])->first();
    
        if ($user && $user->password === md5($credentials['password'])) {
            // Login the user manually
            Auth::loginUsingId($user->id);
            
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
    
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    
}
