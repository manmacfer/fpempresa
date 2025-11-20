<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function editMe(Request $request)
    {
        $company = $request->user()->company()->firstOrFail();

        return Inertia::render('Companies/Edit', [
            'company' => array_merge(
                $company->toArray(),
                ['logo_url' => $company->logo_url]
            ),
        ]);
    }

    public function updateMe(Request $request)
    {
        $company = $request->user()->company()->firstOrFail();

        $validated = $request->validate([
            'description' => 'nullable|string',
            'legal_name' => 'nullable|string',
            'trade_name' => 'nullable|string',
            'sector' => 'nullable|string',
            'website' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Manejar logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
            unset($validated['logo']);
        }

        $company->update($validated);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function publicShow(Company $company)
    {
        return Inertia::render('Companies/PublicShow', [
            'company' => array_merge(
                $company->only([
                    'id',
                    'trade_name',
                    'legal_name',
                    'sector',
                    'website',
                    'linkedin',
                    'city',
                    'province',
                    'description',
                    'contact_name',
                    'contact_email',
                ]),
                ['logo_url' => $company->logo_url]
            ),
        ]);
    }
}