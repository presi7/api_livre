<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

        // Récupérez les catégories en utilisant $validatedData
        $categories = Category::orderBy($validatedData['orderBy'] ?? 'nom', $validatedData['orderDirection'] ?? 'asc')
            ->paginate($validatedData['perPage'] ?? 10, ['*'], 'page', $validatedData['page'] ?? 1);

        return response()->json(['categories' => $categories], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        // Enregistrez la catégorie dans la base de données en utilisant $validatedData
        $category = Category::create($validatedData);

        return response()->json(['message' => 'Category created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

         // Mettez à jour la catégorie dans la base de données en utilisant $validatedData
         $category = Category::findOrFail($id); // Assurez-vous que la catégorie existe
        //  $category->update($validatedData);
        $category->update($request->all());


        return response()->json(['message' => 'Category updated successfully'], 200);
    }

    public function destroy($id)
    {
        // Ajoutez des validations si nécessaire, par exemple, pour s'assurer que la catégorie existe
        $category = Category::findOrFail($id);

        // Supprimez la catégorie de la base de données
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
