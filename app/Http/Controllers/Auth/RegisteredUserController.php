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
    public function create()
    {
        return inertia('Auth/Register');
    }

    public function store(Request $request)
    {
        $role = $request->input('role', 'student');

        if ($role === 'company') {
            $validated = $request->validate([
                'role'         => ['required', 'in:student,company'],
                'company_name' => ['required','string','max:255'],
                'email'        => ['required','string','lowercase','email','max:255','unique:'.User::class],
                'password'     => ['required','confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name'     => $validated['company_name'],
                'email'    => $validated['email'],
                'role'     => 'company',
                'password' => Hash::make($validated['password']),
            ]);

            Company::create([
                'user_id' => $user->id,
                'name'    => $validated['company_name'],
            ]);
        } else {
            $validated = $request->validate([
                'role'       => ['required', 'in:student,company'],
                'first_name' => ['required','string','max:100'],
                'last_name'  => ['required','string','max:150'],
                'cycle'      => ['required','string','max:20'],
                'email'      => ['required','string','lowercase','email','max:255','unique:'.User::class],
                'password'   => ['required','confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name'     => trim($validated['first_name'].' '.$validated['last_name']),
                'email'    => $validated['email'],
                'role'     => 'student',
                'password' => Hash::make($validated['password']),
            ]);

            Student::firstOrCreate(
                ['user_id' => $user->id],
                ['cycle'   => $validated['cycle']]
            );
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(
            $user->role === 'company'
                ? route('companies.edit.me')
                : route('students.edit.me')
        );
    }
}
