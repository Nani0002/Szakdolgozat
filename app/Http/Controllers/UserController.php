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
        /** @var \App\Models\User $user|null */
        $user = Auth::user();

        if ($user) {
            /** @var \App\Models\User $user */

            return view('layouts.menu', [
                "navActions" => [['type' => 'create', 'text' => "hibajegy", "url" => route('ticket.create')]],
                "tickets" => $user->sortedTickets(),
                "ticketTypes" => Ticket::getStatuses()
            ]);
        } else {
            return view('layouts.menu', ["navUrls" => User::getNavUrls(false)]);
        }
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('layouts.menu');
        } else {
            return redirect(route('home'));
        }
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['string'],
            ]);

            $user = User::create([
                'name' => $validated["name"],
                'email' => $validated["email"],
                'password' => Hash::make($validated["password"]),
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            return view('user.user', ["profile" => $user]);
        }
        return redirect(route('home'));
    }

    public function newPassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $validated = $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user["password"] = $validated['password'];

            $user->save();

            return response()->json([
                "success" => true,
                "message" => "Sikeres módosítás!"
            ]);
        }
        return response()->json(['error' => 'Unauthorized', 'redirect' => route('home')], 401);
    }

    public function setImage(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

            if ($user->imagename_hash != "default_user.png" && $user->imagename_hash != "default_computer.jpg") {
                Storage::delete('public/images/' . $user->imagename_hash);
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $hashedName = Str::random(40) . '.' . $request->file('image')->getClientOriginalExtension();

            $request->file('image')->storeAs('public/images', $hashedName);

            $user["imagename"] = $originalName;
            $user["imagename_hash"] = $hashedName;

            $user->save();

            return response()->json([
                'success' => true,
                'new_image_url' => Storage::url('images/' . $hashedName)
            ]);
        }
        return response()->json(['error' => 'Unauthorized', 'redirect' => route('home')], 401);
    }
}
