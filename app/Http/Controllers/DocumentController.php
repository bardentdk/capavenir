<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Enregistrer un nouveau document (GED).
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'client_id' => 'required|exists:clients,id',
    //         'file' => 'required|file|max:10240', // Max 10 Mo
    //         'name' => 'nullable|string|max:255',
    //     ]);

    //     $file = $request->file('file');
    //     // Nom du fichier : soit celui donné, soit le nom d'origine
    //     $name = $request->input('name') ?: $file->getClientOriginalName();

    //     // Stockage sécurisé
    //     $path = $file->store('documents', 'public');

    //     Document::create([
    //         'client_id' => $request->client_id,
    //         'name' => $name,
    //         'path' => $path,
    //         'type' => $file->getClientOriginalExtension(), // pdf, jpg...
    //     ]);

    //     return back()->with('success', 'Document ajouté au dossier.');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'file' => 'required|file|max:10240', // Max 10 Mo
            'name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');

        // Nom affiché (Human readable)
        $displayName = $request->input('name') ?: $file->getClientOriginalName();

        // --- FIX UPLOAD WINDOWS ---
        try {
            // 1. On génère un nom de fichier unique nous-mêmes
            $extension = $file->getClientOriginalExtension();
            $filename = 'doc_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
            $destinationPath = 'documents/' . $filename;

            // 2. On écrit le contenu physiquement (Bypass le bug "Path empty")
            \Illuminate\Support\Facades\Storage::disk('public')->put($destinationPath, file_get_contents($file));

            $path = $destinationPath;

        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Erreur sauvegarde disque : ' . $e->getMessage()]);
        }
        // --------------------------

        Document::create([
            'client_id' => $request->client_id,
            'name' => $displayName,
            'path' => $path,
            'type' => $extension,
        ]);

        return back()->with('success', 'Document ajouté au dossier.');
    }
    /**
     * Supprimer un document.
     */
    public function destroy(Document $document)
    {
        // Suppression du fichier physique
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        // Suppression en base
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}