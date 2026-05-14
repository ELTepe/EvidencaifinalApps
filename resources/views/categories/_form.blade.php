<div class="space-y-5">
    <div>
        <label class="text-sm font-semibold text-gray-700" for="name">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
    </div>
    <div>
        <label class="text-sm font-semibold text-gray-700" for="description">Descripcion</label>
        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $category->description) }}</textarea>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">{{ $button }}</button>
    <a href="{{ route('categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancelar</a>
</div>
