<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function home()
    {
        if (Auth::check()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "tickets" => Auth::user()->sortedTickets(), "userUrls" => Auth::user()->getUserUrls(), "ticketTypes" => Ticket::getStatuses()]);
        } else {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(false)]);
        }
    }

    public function create()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(true), "tickets" => [], "userUrls" => Auth::user()->getUserUrls()]);
        } else {
            return redirect(route('home'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
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
        if (Auth::check()) {
            $user = Auth::user();
            return view('user.user', ["navUrls" => User::getNavUrls(true), "userUrls" => $user->getUserUrls(), "profile" => $user]);
        }
        return redirect(route('home'));
    }

    public function update(Request $request) {}

    public function newPassword(Request $request)
    {
        if (Auth::check()) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = Auth::user();

            $user->password = $request['password'];

            $user->save();

            return response()->json(["message" => "Sikeres módosítás!"]);
        }
        return redirect(route('home'));
    }

    public function setImage(Request $request)
    {
        if (Auth::check()) {
            $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

            $user = Auth::user();

            if ($user->imagename_hash != "default_user.png") {
                Storage::delete('public/images/' . $user->imagename_hash);
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $hashedName = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();

            $request->file('image')->storeAs('public/images', $hashedName);

            $user->update([
                'imagename' => $originalName,
                'imagename_hash' => $hashedName,
            ]);

            return response()->json([
                'success' => true,
                'new_image_url' => Storage::url('images/' . $hashedName)
            ]);
        }
        return response()->json(['error' => 'Unauthorized', 'redirect' => url('/')], 401);
    }
}
