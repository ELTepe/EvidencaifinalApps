<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Editar receta</h1>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @include('shared.flash')
            <form method="POST" action="{{ route('recipes.update', $recipe) }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')
                @include('recipes._form', ['button' => 'Guardar cambios'])
            </form>
        </div>
    </div>
</x-app-layout>
