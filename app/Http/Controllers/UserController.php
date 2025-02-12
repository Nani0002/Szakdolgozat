<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function home()
    {
        if (Auth::user()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "tickets" => [], "userUrls" => Auth::user()->getUserUrls()]);
        } else {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(false)]);
        }
    }

    public function create()
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "tickets" => [], "userUrls" => Auth::user()->getUserUrls()]);
        } else {
            return redirect(route('home'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['string'],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => isset($request->role) ? 'liable' : 'coworker'
            ]);

            event(new Registered($user));

            Auth::logout();

            Auth::login($user);

            return redirect(route('home'));
        } else {
            return redirect(route('home'));
        }
    }

    public function show()
    {
        if (Auth::user()) {
            return view('user.user', ["navUrls" => User::getNavUrls(true), "userUrls" => Auth::user()->getUserUrls(), "profile" => Auth::user()]);
        }
        return redirect(route('home'));
    }

    public function update(Request $request) {}

    public function newPassword(Request $request) {

    }
}
