<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration view.
     */
    public function create()
    {
        return inertia('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','lowercase','email','max:255','unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role'     => ['required','in:student,company'],
        ]);

        $user = new User([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->saveOrFail();

        // Crea entidad mínima según rol
        if ($user->role === 'student') {
            Student::create([
                'user_id'    => $user->id,
                'first_name' => $user->name, // Ajusta a tu esquema real
            ]);
        } else {
            Company::create([
                'user_id'    => $user->id,
                'trade_name' => $user->name, // Algo visible por defecto
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route($user->role === 'company' ? 'companies.edit.me' : 'students.edit.me');
    }
}
