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
    /**
     * Show the home page of the application.
     */
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('layouts.menu');
        } else {
            return redirect(route('home'), 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
            'role' => 'string',
        ], [
            'name.required' => 'A név megadása kötelező.',
            'name.string' => 'A név csak szöveg lehet.',
            'name.max' => 'A név legfeljebb 255 karakter lehet.',

            'email.required' => 'Az email cím megadása kötelező.',
            'email.string' => 'Az email cím formátuma érvénytelen.',
            'email.lowercase' => 'Az email cím kisbetűs kell legyen.',
            'email.email' => 'Kérlek érvényes email címet adj meg.',
            'email.max' => 'Az email legfeljebb 255 karakter lehet.',
            'email.unique' => 'Ez az email cím már foglalt.',

            'password.required' => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A jelszavak nem egyeznek.',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',

            'role.string' => 'A szerepkör érvénytelen formátumban van.',
        ]);

        $user = new User();

        $user->name = $validated["name"];
        $user->email = $validated["email"];
        $user->password = Hash::make($validated["password"]);
        $user->role = isset($request->role) ? 'liable' : 'coworker';

        $user->save();

        event(new Registered($user));

        Auth::logout();

        Auth::login($user);

        return redirect(route('home'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return view('layouts.menu', ["profile" => Auth::user()]);
    }

    /**
     * Update the password for the current user.
     */
    public function newPassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                Rules\Password::defaults(),
            ],
        ], [
            'password.required' => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A jelszavak nem egyeznek.',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;

        $user->save();

        return redirect(route('user'), 201);
    }

    /**
     * Update the profile image for the current user.
     */
    public function newImage(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'], [
            'image.required' => 'A kép feltöltése kötelező.',
            'image.image' => 'A fájlnak érvényes képnek kell lennie.',
            'image.mimes' => 'A képnek JPEG, PNG, JPG vagy GIF formátumúnak kell lennie.',
            'image.max' => 'A kép mérete nem haladhatja meg a 2MB-ot.',
        ]);

        if ($user->imagename_hash != "default_user.png" && $user->imagename_hash != "default_computer.jpg") {
            Storage::disk('public')->delete('images/' . $user->imagename_hash);
        }

        $originalName = $request->file('image')->getClientOriginalName();
        $hashedName = $request->file('image')->hashName();;

        $request->file('image')->storeAs('images', $hashedName, 'public');

        $user["imagename"] = $originalName;
        $user["imagename_hash"] = $hashedName;

        $user->save();

        return response()->json([
            'success' => true,
            'new_image_url' => Storage::url('images/' . $hashedName)
        ]);
    }
}
