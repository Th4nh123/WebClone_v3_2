<?php

namespace App\Http\Controllers\Admin;

class SiteMapController extends Controller
{
    public function index() {
        $posts = Blog::all();
        return response()->view('index', [
            'posts' => $posts
        ])->header('Content-Type', 'text/xml');
    }
}

