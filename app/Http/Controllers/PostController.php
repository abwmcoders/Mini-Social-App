<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller implements HasMiddleware
{

    public static function middleware() : array {
        return [
            //new Middleware('auth', only:['store']),
            new Middleware('auth', except:['index', 'show']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(6);
        return view('posts.index', ['posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        //! Validate the post
        $request->validate(
            [
                'title'=> ['required', 'max:255'],
                'body' => ['required'],
                'image' => ['nullable', 'file', 'max:1000', 'mimes:png,jpg, webp']
            ]
            );

        //! Store image if available
        $path = null;
        if($request->hasFile('image')){
            $path = Storage::disk('public')->put('/post_images', $request->image);
        }
        
        //! Create the post
        // Post::create(['user_id' => Auth::id(), ...$fields]);
        $post = Auth::user()->posts()->create([
            'title' => $request->title,
            'body' => $request->body,
            'image' => $path,
        ]);

        //! Send email
        Mail::to('malikabdulwahabmbmalik@gmail.com')->send(new WelcomeMail(Auth::user(), $post));

        //! Redirect to dashboard
        return back()->with('success', 'Your post was created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('modify', $post);
        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('modify', $post);

        //! Validate the post
        $request->validate(
            [
                'title' => ['required', 'max:255'],
                'body' => ['required'],
                'image' => ['nullable', 'file', 'max:1000', 'mimes:png,jpg, webp']
            ]
        );

        //! Store image if available
        $path = $post->image ?? null;
        if ($request->hasFile('image')) {
            //! delete current post image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $path = Storage::disk('public')->put('/post_images', $request->image);
        }
        

        //! Update the post
        $post->update(['title' => $request->title,
            'body' => $request->body,
            'image' => $path,
        ]);

        //! Redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Your post was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {

        //! delete post image if exists
        if($post->image){
            Storage::disk('public')->delete($post->image);
        }
        Gate::authorize('modify', $post);
        //! Delete the post
        $post->delete();
        //! Redirect back to dashboard
        return back()->with('delete', 'Your post was deleted!');
    }
}
