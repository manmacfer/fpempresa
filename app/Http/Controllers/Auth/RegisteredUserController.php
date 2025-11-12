<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
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
            $companyName = $request->input('company_name') ?? $request->input('name');

            $validated = $request->validate([
                'role'                  => ['required', 'in:student,company'],
                'company_name'          => ['nullable', 'string', 'max:255'],
                'name'                  => ['nullable', 'string', 'max:255'],
                'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password'              => ['required', 'confirmed', Rules\Password::defaults()],
                'password_confirmation' => ['required'],
            ]);

            if (empty($companyName)) {
                return back()->withErrors(['company_name' => 'El nombre de la empresa es obligatorio.'])->withInput();
            }

            $user = User::create([
                'name'     => $companyName,
                'email'    => $validated['email'],
                'role'     => 'company',
                'password' => Hash::make($validated['password']),
            ]);

            Company::create([
                'user_id'    => $user->id,
                // compat con BDD antigua:
                'name'       => $companyName,
                // columnas actuales “bonitas”:
                'trade_name' => $companyName,
            ]);
        } else {
            $first = $request->input('first_name');
            $last  = $request->input('last_name');

            if (!$first && $request->filled('name')) {
                $parts = preg_split('/\s+/', trim($request->input('name')), 2);
                $first = $parts[0] ?? null;
                $last  = $parts[1] ?? null;
            }

            $validated = $request->validate([
                'role'                  => ['required', 'in:student,company'],
                'first_name'            => ['nullable', 'string', 'max:100'],
                'last_name'             => ['nullable', 'string', 'max:150'],
                'name'                  => ['nullable', 'string', 'max:255'],
                'cycle'                 => ['nullable', 'string', 'max:20'],
                'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password'              => ['required', 'confirmed', Rules\Password::defaults()],
                'password_confirmation' => ['required'],
            ]);

            $displayName = trim(($first ?? '') . ' ' . ($last ?? ''));
            if (!$displayName && $request->filled('name')) {
                $displayName = $request->input('name');
            }
            if (!$displayName) {
                return back()->withErrors(['first_name' => 'Nombre obligatorio.'])->withInput();
            }

            $user = User::create([
                'name'     => $displayName,
                'email'    => $validated['email'],
                'role'     => 'student',
                'password' => Hash::make($validated['password']),
            ]);

            Student::firstOrCreate(
                ['user_id' => $user->id],
                ['cycle'   => $validated['cycle'] ?? null]
            );
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->to(
            $user->role === 'company'
                ? '/companies/me/edit'
                : '/students/me/edit'
        );
    }
}
