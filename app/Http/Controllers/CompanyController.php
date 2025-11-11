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
$company = Company::firstOrCreate(['user_id' => $request->user()->id], [
'name' => $request->user()->name,
]);


return Inertia::render('Companies/Edit', [
'company' => [
'id' => $company->id,
'name' => $company->name,
'city' => $company->city,
'website' => $company->website,
'description' => $company->description,
'sectors' => $company->sectors ?? [],
'avatar_url' => $company->avatar_url,
'user_name' => $request->user()->name,
],
]);
}


public function updateMe(Request $request)
{
$company = Company::firstOrCreate(['user_id' => $request->user()->id]);


$data = $request->validate([
'name' => ['required','string','max:255'],
'city' => ['nullable','string','max:100'],
'website' => ['nullable','url'],
'description' => ['nullable','string'],
'sectors' => ['nullable','array'],
'sectors.*' => ['string'],
'avatar' => ['nullable','file','mimes:jpg,jpeg,png','max:2048'],
]);


if ($request->hasFile('avatar')) {
if ($company->avatar_path) Storage::disk('public')->delete($company->avatar_path);
$data['avatar_path'] = $request->file('avatar')->store('company_avatars', 'public');
}


$company->fill($data);
$company->saveOrFail();


return redirect()->route('companies.edit.me')->with('success', 'Perfil de empresa guardado.');
}


public function publicShow(Company $company)
{
return Inertia::render('Companies/PublicShow', [
'company' => [
'id' => $company->id,
'name' => $company->name,
'city' => $company->city,
'website' => $company->website,
'description' => $company->description,
'sectors' => $company->sectors ?? [],
'avatar_url' => $company->avatar_url,
],
]);
}
}