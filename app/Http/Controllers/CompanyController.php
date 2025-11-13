<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class CompanyController extends Controller
{
    public function editMe(Request $request)
    {
        $company = $request->user()->company()->firstOrFail();

        return Inertia::render('Companies/Edit', [
            'company' => $company->only([
                'id',
                'legal_name','trade_name','cif','sector',
                'website','linkedin',
                'phone','city','province','postal_code',
                'contact_name','contact_email',
                'description',
            ]),
        ]);
    }

    public function updateMe(Request $request)
    {
        $company = $request->user()->company()->firstOrFail();

        $data = $request->validate([
            'legal_name'    => ['nullable','string','max:255'],
            'trade_name'    => ['nullable','string','max:255'],
            'cif'           => ['nullable','string','max:20'],
            'sector'        => ['nullable','string','max:255'],
            'website'       => ['nullable','url','max:255'],
            'linkedin'      => ['nullable','url','max:255'],
            'phone'         => ['nullable','string','max:30'],
            'city'          => ['nullable','string','max:255'],
            'province'      => ['nullable','string','max:255'],
            'postal_code'   => ['nullable','string','max:15'],
            'contact_name'  => ['nullable','string','max:255'],
            'contact_email' => ['nullable','email','max:255'],
            'description'   => ['nullable','string'],
        ]);

        $company->update($data);

        return back()->with('success', 'Perfil de empresa actualizado.');
    }

    public function publicShow(Company $company)
    {
        return Inertia::render('Companies/PublicShow', [
            'company' => $company->only([
                'id','trade_name','legal_name','sector',
                'website','linkedin',
                'city','province',
                'description',
            ]),
        ]);
    }
}
