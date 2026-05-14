<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Recetas colaborativas</h1>
                <p class="mt-1 text-sm text-gray-600">Comparte recetas, comenta mejoras y valora las favoritas de la comunidad.</p>
            </div>
            @auth
                <a href="{{ route('recipes.create') }}" class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Nueva receta</a>
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('shared.flash')
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($recipes as $recipe)
                    <article class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $recipe->category->name }}</span>
                            <span class="text-sm text-amber-700">{{ number_format($recipe->ratings_avg_score ?? 0, 1) }} / 5</span>
                        </div>
                        <h2 class="mt-4 text-xl font-semibold text-gray-950">
                            <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-emerald-800">{{ $recipe->title }}</a>
                        </h2>
                        <p class="mt-2 line-clamp-3 text-sm text-gray-600">{{ $recipe->description }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-600">
                            <span>{{ $recipe->prep_minutes + $recipe->cook_minutes }} min</span>
                            <span>{{ $recipe->servings }} porciones</span>
                            <span>{{ ucfirst($recipe->difficulty) }}</span>
                        </div>
                        <p class="mt-4 text-xs text-gray-500">Publicada por {{ $recipe->user->name }}</p>
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-8 text-center text-gray-600 md:col-span-2 lg:col-span-3">Aun no hay recetas publicadas.</div>
                @endforelse
            </div>
            <div class="mt-6">{{ $recipes->links() }}</div>
        </div>
    </div>
</x-app-layout>
