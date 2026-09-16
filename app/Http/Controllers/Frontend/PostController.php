<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('content', 'like', $searchTerm)
                  ->orWhere('excerpt', 'like', $searchTerm);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->sort === 'oldest') {
            $query->orderBy('published_at', 'asc');
        } else {
            $query->orderByDesc('published_at');
        }

        $posts = $query->paginate(12)->withQueryString();

        return view('frontend.posts.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $recentPosts = Post::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('frontend.posts.show', compact('post', 'recentPosts'));
    }
}
