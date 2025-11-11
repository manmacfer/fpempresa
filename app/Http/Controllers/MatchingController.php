<?php

namespace App\Http\Controllers;

use App\Models\Matching;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\MatchingStatus; //

class MatchingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Matching::class, 'matching');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Matching::with(['student.user', 'company.user']);

        if ($user->isProfessor()) {
            // all
        } elseif ($user->isCompany()) {
            $query->where('company_id', optional($user->company)->id);
        } elseif ($user->isStudent()) {
            $query->where('student_id', optional($user->student)->id);
        } else {
            abort(403);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'company_id' => ['required', 'exists:companies,id'],
            'status'     => [
                'nullable',
                'string',
                Rule::in(array_column(MatchingStatus::cases(), 'value')),
            ],
        ]);

        $matching = Matching::create([
            'student_id' => $validated['student_id'],
            'company_id' => $validated['company_id'],
            'status'     => $validated['status'] ?? MatchingStatus::Pending->value,
        ]);

        return response()->json($matching->load(['student.user', 'company.user']), 201);
    }

    public function show(Matching $matching)
    {
        return response()->json($matching->load(['student.user', 'company.user']));
    }

    public function update(Request $request, Matching $matching)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(array_column(MatchingStatus::cases(), 'value')),
            ],
        ]);

        $matching->update(['status' => $validated['status']]);

        return response()->json($matching->load(['student.user', 'company.user']));
    }

    public function destroy(Matching $matching)
    {
        $matching->delete();
        return response()->noContent();
    }

    public function accept(Request $request, Matching $matching)
    {
        $this->authorize('update', $matching);
        $matching->update(['status' => MatchingStatus::Accepted->value]);
        return response()->json($matching->refresh()->load(['student.user', 'company.user']));
    }

    public function reject(Request $request, Matching $matching)
    {
        $this->authorize('update', $matching);
        $matching->update(['status' => MatchingStatus::Rejected->value]);
        return response()->json($matching->refresh()->load(['student.user', 'company.user']));
    }
}
