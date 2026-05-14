@php
    $ingredients = old('ingredients', $recipe->exists ? $recipe->ingredients->map(fn ($item) => ['name' => $item->name, 'quantity' => $item->quantity])->toArray() : [
        ['name' => '', 'quantity' => ''],
        ['name' => '', 'quantity' => ''],
        ['name' => '', 'quantity' => ''],
    ]);
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-gray-700" for="title">Titulo</label>
        <input id="title" name="title" value="{{ old('title', $recipe->title) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="category_id">Categoria</label>
        <select id="category_id" name="category_id" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">Selecciona una categoria</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $recipe->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="prep_minutes">Minutos de preparacion</label>
        <input id="prep_minutes" type="number" min="1" name="prep_minutes" value="{{ old('prep_minutes', $recipe->prep_minutes) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="cook_minutes">Minutos de coccion</label>
        <input id="cook_minutes" type="number" min="0" name="cook_minutes" value="{{ old('cook_minutes', $recipe->cook_minutes) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="servings">Porciones</label>
        <input id="servings" type="number" min="1" name="servings" value="{{ old('servings', $recipe->servings) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="difficulty">Dificultad</label>
        <select id="difficulty" name="difficulty" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
            @foreach (['facil' => 'Facil', 'media' => 'Media', 'dificil' => 'Dificil'] as $value => $label)
                <option value="{{ $value }}" @selected(old('difficulty', $recipe->difficulty) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-5">
    <label class="text-sm font-semibold text-gray-700" for="description">Descripcion</label>
    <textarea id="description" name="description" rows="3" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $recipe->description) }}</textarea>
</div>

<div class="mt-5">
    <label class="text-sm font-semibold text-gray-700" for="steps">Pasos</label>
    <textarea id="steps" name="steps" rows="6" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('steps', $recipe->steps) }}</textarea>
</div>

<div class="mt-5">
    <div class="mb-2 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Ingredientes</h2>
        <p class="text-xs text-gray-500">Llena al menos uno. Puedes dejar filas sobrantes vacias.</p>
    </div>
    <div class="space-y-3">
        @for ($i = 0; $i < max(6, count($ingredients)); $i++)
            <div class="grid gap-3 md:grid-cols-2">
                <input name="ingredients[{{ $i }}][name]" value="{{ $ingredients[$i]['name'] ?? '' }}" placeholder="Ingrediente" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                <input name="ingredients[{{ $i }}][quantity]" value="{{ $ingredients[$i]['quantity'] ?? '' }}" placeholder="Cantidad" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
            </div>
        @endfor
    </div>
</div>

<label class="mt-5 flex items-center gap-2 text-sm text-gray-700">
    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $recipe->is_published ?? true)) class="rounded border-gray-300 text-emerald-700 focus:ring-emerald-600">
    Publicar receta
</label>

<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">{{ $button }}</button>
    <a href="{{ route('recipes.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancelar</a>
</div>
