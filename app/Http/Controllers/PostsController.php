<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Excel;

class PostsController extends Controller
{
	public function store()
	{
		 $rules = array(
		 'title' => 'required',
		 'body' => 'required',
	 );
	 $validator = Validator::make(Input::all(), $rules);
	// process the login
		 if ($validator->fails()) {
		 return Redirect::to('/posts/create')
		 ->withErrors($validator)
		 ->withInput(Input::except('password'));
		 } 
		 else {
		 // store
		 $blog = new Post;
		 $blog->title = Input::get('title');
		 $blog->body = Input::get('body');
		 $blog->save();
		// redirect
		 Session::flash('message', 'Successfully posted your blog!');
		 return Redirect::to('/posts');
		 }
		return view('posts.create');
	}
}