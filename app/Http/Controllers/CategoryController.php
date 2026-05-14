<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('categories.index', [
            'categories' => Category::withCount('recipes')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create', ['category' => new Category()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedCategory($request);
        $data['slug'] = Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('categories.index')->with('status', 'Categoria creada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return redirect()->route('recipes.index', ['category' => $category->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $this->validatedCategory($request, $category);
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()->route('categories.index')->with('status', 'Categoria actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->recipes()->exists()) {
            return back()->withErrors(['category' => 'No se puede eliminar una categoria con recetas asociadas.']);
        }

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'Categoria eliminada.');
    }

    private function validatedCategory(Request $request, ?Category $category = null): array
    {
        $id = $category?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name,'.$id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
