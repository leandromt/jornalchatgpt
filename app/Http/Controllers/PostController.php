<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index($category, $slug)
    {
        $post = Post::where('slug', $slug)->first();

        return view('post', compact('post'));
    }
}
