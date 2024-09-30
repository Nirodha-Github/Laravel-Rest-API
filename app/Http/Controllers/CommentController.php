<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        // Require authentication for comment creation and deletion
        $this->middleware('auth:sanctum')->only(['store', 'destroy']);
    }

    // Add a new comment to a post
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $comment = new Comment([
            'body' => $request->body,
            'user_id' => Auth::id(), // Set the authenticated user as the commenter
        ]);

        $post->comments()->save($comment);

        return response()->json($comment, 201);
    }

    // Delete a comment
    public function destroy($post,$id)
    {
       
        $comment = Comment::where('post_id', $post)
                            ->where('id', $id)
                            ->findOrFail($id);

        // Check if the authenticated user is the owner of the post
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
