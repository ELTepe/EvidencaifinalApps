<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recipes = Recipe::with(['category', 'user', 'ratings'])
            ->withAvg('ratings', 'score')
            ->latest()
            ->paginate(9);

        return view('recipes.index', compact('recipes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recipes.create', [
            'recipe' => new Recipe(['servings' => 4, 'difficulty' => 'facil', 'is_published' => true]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedRecipe($request);

        $recipe = null;

        DB::transaction(function () use ($data, $request, &$recipe) {
            $recipeData = collect($data)->except('ingredients')->all();
            $recipe = Recipe::create([
                ...$recipeData,
                'user_id' => $request->user()->id,
                'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
                'is_published' => $request->boolean('is_published'),
            ]);

            $this->syncIngredients($recipe, $data['ingredients'] ?? []);
        });

        return redirect()->route('recipes.show', $recipe)->with('status', 'Receta publicada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['category', 'user', 'ingredients', 'comments.user', 'ratings']);
        $userRating = auth()->check()
            ? $recipe->ratings->firstWhere('user_id', auth()->id())?->score
            : null;

        return view('recipes.show', compact('recipe', 'userRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);

        return view('recipes.edit', [
            'recipe' => $recipe->load('ingredients'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);

        $data = $this->validatedRecipe($request);

        DB::transaction(function () use ($data, $request, $recipe) {
            $recipeData = collect($data)->except('ingredients')->all();
            $recipe->update([
                ...$recipeData,
                'is_published' => $request->boolean('is_published'),
            ]);

            $this->syncIngredients($recipe, $data['ingredients'] ?? []);
        });

        return redirect()->route('recipes.show', $recipe)->with('status', 'Receta actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);
        $recipe->delete();

        return redirect()->route('recipes.index')->with('status', 'Receta eliminada.');
    }

    private function validatedRecipe(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'steps' => ['required', 'string', 'min:20'],
            'prep_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'cook_minutes' => ['required', 'integer', 'min:0', 'max:600'],
            'servings' => ['required', 'integer', 'min:1', 'max:50'],
            'difficulty' => ['required', 'in:facil,media,dificil'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.name' => ['nullable', 'string', 'max:120'],
            'ingredients.*.quantity' => ['nullable', 'string', 'max:120'],
        ]);

        $ingredients = collect($data['ingredients'] ?? [])
            ->map(fn (array $ingredient) => [
                'name' => trim($ingredient['name'] ?? ''),
                'quantity' => trim($ingredient['quantity'] ?? ''),
            ]);

        $hasPartialIngredient = $ingredients
            ->contains(fn (array $ingredient) => filled($ingredient['name']) xor filled($ingredient['quantity']));

        if ($hasPartialIngredient) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'ingredients' => ['Cada ingrediente debe incluir nombre y cantidad.'],
            ]);
        }

        $ingredients = $ingredients
            ->filter(fn (array $ingredient) => filled($ingredient['name']) && filled($ingredient['quantity']))
            ->values()
            ->all();

        if (empty($ingredients)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'ingredients' => ['Debes agregar al menos un ingrediente con nombre y cantidad.'],
            ]);
        }

        return array_merge($data, ['ingredients' => $ingredients]);
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->delete();

        collect($ingredients)
            ->filter(fn (array $ingredient) => filled($ingredient['name'] ?? null) && filled($ingredient['quantity'] ?? null))
            ->values()
            ->each(fn (array $ingredient, int $index) => Ingredient::create([
                'recipe_id' => $recipe->id,
                'name' => $ingredient['name'],
                'quantity' => $ingredient['quantity'],
                'position' => $index + 1,
            ]));
    }

    private function authorizeRecipe(Recipe $recipe): void
    {
        abort_unless(auth()->user()->isAdmin() || $recipe->user_id === auth()->id(), 403);
    }
}
