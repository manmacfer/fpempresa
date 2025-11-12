<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return inertia('Auth/Register');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $role = $request->input('role', 'student');

        if ($role === 'company') {
            $validated = $request->validate([
                'role'                  => ['required','in:student,company'],
                'company_name'          => ['required','string','max:255'],
                'email'                 => ['required','string','lowercase','email','max:255','unique:'.User::class],
                'password'              => ['required','confirmed', Rules\Password::defaults()],
                'password_confirmation' => ['required'],
            ]);

            // IDs con fallback por si el seeder no corrió
            $roleId = Role::where('slug','company')->value('id') ?? 4;

            $user = User::create([
                'name'     => $validated['company_name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),

                // ⚠️ Guardamos ambas cosas mientras migramos
                'role_id'  => $roleId,
                'role'     => 'company',
            ]);

            Company::create([
                'user_id'    => $user->id,
                // compat si tu tabla aún conserva `name`
                'name'       => $validated['company_name'],
                'trade_name' => $validated['company_name'],
            ]);
        } else {
            $validated = $request->validate([
                'role'                  => ['required','in:student,company'],
                'first_name'            => ['required','string','max:100'],
                'last_name'             => ['required','string','max:150'],
                'cycle'                 => ['required','string','max:20'],
                'email'                 => ['required','string','lowercase','email','max:255','unique:'.User::class],
                'password'              => ['required','confirmed', Rules\Password::defaults()],
                'password_confirmation' => ['required'],
            ]);

            $roleId = Role::where('slug','student')->value('id') ?? 3;

            $user = User::create([
                'name'     => trim($validated['first_name'].' '.$validated['last_name']),
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),

                // ⚠️ Guardamos ambas cosas
                'role_id'  => $roleId,
                'role'     => 'student',
            ]);

            Student::create([
                'user_id'    => $user->id,
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'cycle'      => $validated['cycle'],
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        // Redirige a la zona del rol (si prefieres al dashboard, cambia por '/dashboard')
        return redirect()->to(
            ($user->role ?? null) === 'company'
                ? '/companies/me/edit'
                : '/students/me/edit'
        );
    }
}
