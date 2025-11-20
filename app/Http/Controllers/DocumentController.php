<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matching;
use App\Models\MatchingDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Store a new document for a matching
     */
    public function store(Request $request, Matching $matching)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png',
            'type' => 'required|in:contract,report,certificate,other'
        ]);

        $user = Auth::user();
        $conversation = $matching->conversation;

        // Check if user can upload documents (student, company, or teacher)
        if (!$conversation) {
            return back()->with('error', 'La conversación no existe.');
        }

        $participants = $conversation->getParticipants();
        $userIds = $participants->pluck('id')->toArray();

        if (!in_array($user->id, $userIds) && $user->role_id !== 2) {
            return back()->with('error', 'No tienes permiso para subir documentos.');
        }

        // Store the file
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('matching_documents', $filename, 'public');

        // Create document record
        MatchingDocument::create([
            'matching_id' => $matching->id,
            'uploaded_by' => $user->id,
            'name' => $file->getClientOriginalName(),
            'type' => $request->input('type'),
            'file_path' => $path
        ]);

        return back()->with('success', 'Documento subido correctamente.');
    }

    /**
     * Download a document
     */
    public function download(MatchingDocument $document)
    {
        $user = Auth::user();
        $matching = $document->matching;
        $conversation = $matching->conversation;

        if (!$conversation) {
            abort(403, 'No tienes acceso a este documento.');
        }

        $participants = $conversation->getParticipants();
        $userIds = $participants->pluck('id')->toArray();

        // Allow access to participants or teachers
        if (!in_array($user->id, $userIds) && $user->role_id !== 2) {
            abort(403, 'No tienes acceso a este documento.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Documento no encontrado.');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        
        return response()->download($filePath, $document->name);
    }
}
