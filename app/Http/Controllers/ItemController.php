<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // Ajoutez des validations pour filtrer, trier ou paginer les résultats si nécessaire
        $validatedData = $request->validate([
            'orderBy' => 'in:nom,created_at',
            'orderDirection' => 'in:asc,desc',
            'page' => 'integer|min:1',
            'perPage' => 'integer|min:1',
        ]);

        // Récupérez les items en utilisant $validatedData
        $items = Item::orderBy($validatedData['orderBy'] ?? 'nom', $validatedData['orderDirection'] ?? 'asc')
            ->paginate($validatedData['perPage'] ?? 10, ['*'], 'page', $validatedData['page'] ?? 1);


        return response()->json(['items' => $items], 200);
    }

    public function store(Request $request)
    {
          // Ajoutez ces logs pour déboguer
    // \Log::info('Request data:', $request->all());
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pdf,audio',
            'fichier' => 'required_if:type,pdf|mimes:pdf|max:10240', // Ajoutez une validation pour les fichiers PDF
            'lien_youtube' => 'required_if:type,audio|url', // Ajoutez une validation pour les liens YouTube
        ]);

        // \Log::info('Validated data:', $validatedData);

        if ($request->hasFile('fichier')) {
            // Téléchargez le fichier PDF
            $pdfPath = $request->file('fichier')->store('pdfs', 'public');
            $validatedData['fichier'] = $pdfPath;
        }

       // Enregistrez l'item dans la base de données en utilisant $validatedData
       $item = Item::create($validatedData);

        return response()->json(['message' => 'Item created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pdf,audio',
            'fichier' => 'sometimes|required_if:type,pdf|mimes:pdf|max:10240', // Ajoutez une validation pour les fichiers PDF (si fourni)
            'lien_youtube' => 'sometimes|required_if:type,audio|url', // Ajoutez une validation pour les liens YouTube (si fourni)
        ]);

        //Mettez à jour l'item dans la base de données en utilisant $validatedData
        $item = Item::findOrFail($id); // Assurez-vous que l'item existe

        if ($request->hasFile('fichier')) {
            // Si un nouveau fichier PDF est fourni, remplacez l'ancien
            $pdfPath = $request->file('fichier')->store('pdfs', 'public');
            $validatedData['fichier'] = $pdfPath;
        }

        // Si un nouveau lien YouTube est fourni, mettez à jour le lien
        if ($request->filled('lien_youtube')) {
            $validatedData['lien_youtube'] = $request->input('lien_youtube');
        }

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Ajoutez des validations si nécessaire, par exemple, pour s'assurer que l'item existe
        $item = Item::findOrFail($id);

        // Supprimez l'item de la base de données
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
}
