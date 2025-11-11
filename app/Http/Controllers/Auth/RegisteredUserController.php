<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return inertia('Auth/Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name'  => ['required','string','max:150'],
            'cycle'      => ['required','in:1 DAM,2 DAM,1 DAW,2 DAW'],
            'email'      => ['required','string','lowercase','email','max:255','unique:'.User::class],
            'password'   => ['required','confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'       => $validated['first_name'].' '.$validated['last_name'],
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
        ]);

        // Perfil mínimo sin full_name
        Student::firstOrCreate(
            ['user_id' => $user->id],
            ['cycle' => $validated['cycle']]
        );

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}
