<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-900">Nueva receta</h1>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @include('shared.flash')
            <form method="POST" action="{{ route('recipes.store') }}" class="rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @include('recipes._form', ['button' => 'Publicar receta'])
            </form>
        </div>
    </div>
</x-app-layout>
