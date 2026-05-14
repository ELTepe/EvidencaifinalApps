<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Recetas',
            'email' => 'admin@recetas.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $users = collect([
            ['Ana Colaboradora', 'ana@recetas.test'],
            ['Luis Cocinero', 'luis@recetas.test'],
            ['Marta Sabor', 'marta@recetas.test'],
            ['Diego Mesa', 'diego@recetas.test'],
        ])->map(fn (array $user) => User::create([
            'name' => $user[0],
            'email' => $user[1],
            'password' => Hash::make('password'),
            'role' => 'usuario',
        ]));

        $categories = collect([
            ['Desayunos', 'Recetas rapidas para iniciar el dia.'],
            ['Comida mexicana', 'Platillos caseros con sabores tradicionales.'],
            ['Vegetariano', 'Opciones sin carne para compartir.'],
            ['Postres', 'Preparaciones dulces para la comunidad.'],
            ['Bebidas', 'Bebidas frescas y calientes.'],
            ['Cena ligera', 'Ideas sencillas para cerrar el dia.'],
            ['Sopas', 'Recetas caldosas y reconfortantes.'],
            ['Ensaladas', 'Mezclas frescas con buen balance.'],
            ['Panaderia', 'Masas, panes y horneados.'],
            ['Botanas', 'Entradas y snacks colaborativos.'],
        ])->map(fn (array $category) => Category::create([
            'name' => $category[0],
            'slug' => Str::slug($category[0]),
            'description' => $category[1],
        ]));

        $recipeNames = [
            'Chilaquiles verdes de domingo',
            'Tacos de champinon al ajillo',
            'Ensalada de quinoa y mango',
            'Pan frances con canela',
            'Agua fresca de pepino y limon',
            'Sopa de tortilla ligera',
            'Brownies de cacao intenso',
            'Molletes colaborativos',
            'Pasta cremosa de poblano',
            'Guacamole con granada',
        ];

        foreach ($recipeNames as $index => $title) {
            $recipe = Recipe::create([
                'user_id' => $index === 0 ? $admin->id : $users[$index % $users->count()]->id,
                'category_id' => $categories[$index]->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Receta pensada para que la comunidad pueda ajustarla, comentar variaciones y calificar el resultado final.',
                'steps' => "1. Prepara y mide todos los ingredientes.\n2. Cocina a fuego medio cuidando textura y sazon.\n3. Sirve caliente o fresco segun el platillo y registra tus mejoras en comentarios.",
                'prep_minutes' => 10 + ($index * 2),
                'cook_minutes' => 15 + ($index * 3),
                'servings' => 2 + ($index % 5),
                'difficulty' => ['facil', 'media', 'dificil'][$index % 3],
                'is_published' => true,
            ]);

            foreach (['Base principal', 'Sazonador', 'Guarnicion'] as $position => $ingredient) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $ingredient.' '.$recipe->id,
                    'quantity' => [ '2 tazas', '1 cucharadita', '1/2 taza' ][$position],
                    'position' => $position + 1,
                ]);
            }

            foreach ($users->take(2) as $user) {
                Comment::create([
                    'recipe_id' => $recipe->id,
                    'user_id' => $user->id,
                    'body' => 'La probe y agregaria un ajuste de sal al final para mejorar el balance.',
                ]);
                Rating::updateOrCreate(
                    ['recipe_id' => $recipe->id, 'user_id' => $user->id],
                    ['score' => 4 + (($recipe->id + $user->id) % 2)]
                );
            }
        }
    }
}
