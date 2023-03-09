<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class LoadBlogController extends Controller
{
    public function index() {
        $blogCate = BlogCategory::all();
        // $blog = Blog::all();
        $blog = Blog::paginate(5);
        $blogRandom = Blog::inRandomOrder()->limit(5)->get();
        // dd($blogRandom);
        $blogHashtag = Blog::select('hashtag')->get();
        // dd($blogHashtag);
        return view('frontend.blogger.blog-list')->with(compact('blog', 'blogCate', 'blogRandom', 'blogHashtag'));
    }

    public function show($id) {
        $blog = Blog::findOrFail($id);
        return view('frontend.blogger.blog-detail')->with(compact('blog'));
    }

    // public function loadData() {
    //     return view();
    // }

    // public function getData() {
    //     return view();
    // }
}
