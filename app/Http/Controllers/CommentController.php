<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:5', 'max:1000'],
            'score' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        Comment::create([
            'recipe_id' => $recipe->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        if (! empty($data['score'])) {
            Rating::updateOrCreate(
                ['recipe_id' => $recipe->id, 'user_id' => $request->user()->id],
                ['score' => $data['score']]
            );
        }

        return back()->with('status', 'Comentario agregado. Gracias por colaborar.');
    }

    public function destroy(Comment $comment)
    {
        abort_unless(auth()->user()->isAdmin() || $comment->user_id === auth()->id(), 403);
        $comment->delete();

        return back()->with('status', 'Comentario eliminado.');
    }
}
