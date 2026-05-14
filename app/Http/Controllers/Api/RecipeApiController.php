<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recipe::with(['category:id,name', 'user:id,name', 'ingredients', 'ratings'])
            ->withAvg('ratings', 'score')
            ->latest()
            ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $recipe = DB::transaction(function () use ($data, $request) {
            $recipeData = collect($data)->except('ingredients')->all();
            $recipe = Recipe::create([
                ...$recipeData,
                'user_id' => $request->user()->id,
                'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
                'is_published' => $request->boolean('is_published', true),
            ]);

            $this->syncIngredients($recipe, $data['ingredients'] ?? []);

            return $recipe;
        });

        return response()->json($recipe->load(['category', 'ingredients']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        return $recipe->load(['category', 'user:id,name', 'ingredients', 'comments.user:id,name', 'ratings']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        abort_unless($request->user()->isAdmin() || $recipe->user_id === $request->user()->id, 403);

        $data = $this->validatePayload($request, updating: true);

        DB::transaction(function () use ($data, $request, $recipe) {
            $recipeData = collect($data)->except('ingredients')->all();
            $recipe->update([
                ...$recipeData,
                'is_published' => $request->boolean('is_published', $recipe->is_published),
            ]);

            if (array_key_exists('ingredients', $data)) {
                $this->syncIngredients($recipe, $data['ingredients']);
            }
        });

        return response()->json($recipe->refresh()->load(['category', 'ingredients']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    {
        abort_unless(request()->user()->isAdmin() || $recipe->user_id === request()->user()->id, 403);
        $recipe->delete();

        return response()->json(['message' => 'Receta eliminada.']);
    }

    private function validatePayload(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes' : 'required';

        return $request->validate([
            'category_id' => [$required, 'exists:categories,id'],
            'title' => [$required, 'string', 'max:255'],
            'description' => [$required, 'string', 'min:20'],
            'steps' => [$required, 'string', 'min:20'],
            'prep_minutes' => [$required, 'integer', 'min:1', 'max:600'],
            'cook_minutes' => [$required, 'integer', 'min:0', 'max:600'],
            'servings' => [$required, 'integer', 'min:1', 'max:50'],
            'difficulty' => [$required, 'in:facil,media,dificil'],
            'is_published' => ['sometimes', 'boolean'],
            'ingredients' => [$updating ? 'sometimes' : 'required', 'array', 'min:1'],
            'ingredients.*.name' => ['required_with:ingredients', 'string', 'max:120'],
            'ingredients.*.quantity' => ['required_with:ingredients', 'string', 'max:120'],
        ]);
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->delete();

        collect($ingredients)->values()->each(fn (array $ingredient, int $index) => Ingredient::create([
            'recipe_id' => $recipe->id,
            'name' => $ingredient['name'],
            'quantity' => $ingredient['quantity'],
            'position' => $index + 1,
        ]));
    }
}
