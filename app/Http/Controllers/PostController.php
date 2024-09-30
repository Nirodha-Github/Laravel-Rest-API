<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        // Require authentication for all actions except viewing posts
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    // Get all posts (optional: with pagination)
    public function index()
    {
        $posts = Post::with('user')->paginate(10); 
        return response()->json($posts);
    }

    // Show a single post
    public function show($id)
    {
        $post = Post::with('comments.user')->findOrFail($id);
        return response()->json($post);
    }

    // Create a new post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post = new Post([
            'title' => $request->title,
            'body' => $request->body,
            'user_id' => Auth::id(), // Set the authenticated user as the author
        ]);

        $post->save();

        return response()->json($post, 201);
    }

    // Update a post
    public function update(Request $request, $id)
    {
        // Retrieve the post instance
        $post = Post::findOrFail($id);

        // Check if the authenticated user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'string|max:255',
            'body' => 'string',
        ]);

        $post->update($request->all());
        
        return response()->json($post);
    }

    // Delete a post
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Check if the authenticated user is the owner of the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    public function search(Request $request)
    {
        $query = Post::query()->with('user', 'comments');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->paginate(10);
    }

}
