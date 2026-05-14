<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-700">{{ $recipe->category->name }}</p>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $recipe->title }}</h1>
            </div>
            @auth
                @if (Auth::user()->isAdmin() || Auth::id() === $recipe->user_id)
                    <div class="flex gap-2">
                        <a href="{{ route('recipes.edit', $recipe) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Editar</a>
                        <form method="POST" action="{{ route('recipes.destroy', $recipe) }}">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Eliminar</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </x-slot>
    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[2fr_1fr] lg:px-8">
            <div>
                @include('shared.flash')
                <article class="rounded-lg bg-white p-6 shadow-sm">
                    <p class="text-gray-700">{{ $recipe->description }}</p>
                    <div class="mt-5 grid gap-3 text-sm text-gray-700 sm:grid-cols-4">
                        <span><strong>Prep:</strong> {{ $recipe->prep_minutes }} min</span>
                        <span><strong>Coccion:</strong> {{ $recipe->cook_minutes }} min</span>
                        <span><strong>Porciones:</strong> {{ $recipe->servings }}</span>
                        <span><strong>Dificultad:</strong> {{ ucfirst($recipe->difficulty) }}</span>
                    </div>
                    <h2 class="mt-6 text-lg font-semibold text-gray-950">Ingredientes</h2>
                    <ul class="mt-3 list-disc space-y-1 ps-5 text-gray-700">
                        @foreach ($recipe->ingredients as $ingredient)
                            <li>{{ $ingredient->quantity }} de {{ $ingredient->name }}</li>
                        @endforeach
                    </ul>
                    <h2 class="mt-6 text-lg font-semibold text-gray-950">Preparacion</h2>
                    <p class="mt-3 whitespace-pre-line text-gray-700">{{ $recipe->steps }}</p>
                </article>

                <section class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-950">Comentarios y valoraciones</h2>
                    @auth
                        <form method="POST" action="{{ route('recipes.comments.store', $recipe) }}" class="mt-4 space-y-4">
                            @csrf
                            <textarea name="body" rows="3" required placeholder="Aporta una mejora, variacion o experiencia..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('body') }}</textarea>
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <select name="score" class="rounded-md border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                                    <option value="">Sin valoracion</option>
                                    @for ($score = 1; $score <= 5; $score++)
                                        <option value="{{ $score }}" @selected($userRating == $score)>{{ $score }} estrella{{ $score > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                <button class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Comentar</button>
                            </div>
                        </form>
                    @else
                        <p class="mt-3 text-sm text-gray-600"><a class="font-semibold text-emerald-700" href="{{ route('login') }}">Ingresa</a> para comentar y valorar.</p>
                    @endauth

                    <div class="mt-6 space-y-4">
                        @forelse ($recipe->comments as $comment)
                            <div class="border-t border-gray-100 pt-4">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</p>
                                    @auth
                                        @if (Auth::user()->isAdmin() || Auth::id() === $comment->user_id)
                                            <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs font-semibold text-red-700 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>
                                <p class="mt-1 text-sm text-gray-700">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Aun no hay comentarios.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="rounded-lg bg-white p-6 shadow-sm lg:self-start">
                <h2 class="text-lg font-semibold text-gray-950">Resumen</h2>
                <dl class="mt-4 space-y-3 text-sm text-gray-700">
                    <div><dt class="font-semibold">Autor</dt><dd>{{ $recipe->user->name }}</dd></div>
                    <div><dt class="font-semibold">Valoracion promedio</dt><dd>{{ $recipe->averageRating() }} / 5</dd></div>
                    <div><dt class="font-semibold">Comentarios</dt><dd>{{ $recipe->comments->count() }}</dd></div>
                    <div><dt class="font-semibold">Estado</dt><dd>{{ $recipe->is_published ? 'Publicada' : 'Borrador' }}</dd></div>
                </dl>
            </aside>
        </div>
    </div>
</x-app-layout>
