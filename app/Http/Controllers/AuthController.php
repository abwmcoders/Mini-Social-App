<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //! Register User
    public function register(Request $request) {
        //! Validate
        $fields = $request->validate(
            [
            'username' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'min:3', 'confirmed'],
        ],
    );
        //! Register
        $user = User::create($fields);
        //! Login
        Auth::login($user);

        event(new Registered($user));

        //! Redirect
        return redirect()->route('home');
    }

    public function verifyNotice() {
        return view('auth.verify-email');
    }

    public function verifyHandler(Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return view('auth.verify-email');
    }
    
    public function verifyEmail (EmailVerificationRequest $request) {
        $request->fulfill();
        return back()->with('message', 'Verification link sent!');
    }

    //! Login User
    public function login(Request $request) {
        //! Validate
        $fields = $request->validate(
            [
                'email' => ['required', 'max:255', 'email'],
                'password' => ['required'],
            ],
        );

        //! Login the user
       if( Auth::attempt($fields, $request->remember)){
        return redirect()->intended('dashboard');
       }else{
        return back()->withErrors([
            'failed' => 'The provided credentials do not match our records.'
        ]);
       }

    }

    //! LOgout User
    public function logout(Request $request) {
        //! Logout user
        Auth::logout();
        //! Invalidate session
        $request->session()->invalidate();
        //! Regenerate token
        $request->session()->regenerateToken();
        //! Redirect to home
        return redirect('/');
    }
}
