<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'show']]);
        $this->middleware('can:post create', ['only' => ['create', 'store']]);
        $this->middleware('can:post edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:post delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->input('jurisdiction')) {
            $request->merge(['jurisdiction' => 1]);
        }

        $posts = Post::query()
            ->where('status', '=', 'publish')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                $query->whereHas('jurisdictions', function ($query) use ($jurisdiction) {
                    $query->whereId($jurisdiction);
                });
            })
            ->when($request->input('tag'), function ($query, $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->whereId($tag);
                });
            })
            ->when($request->input('author'), function ($query, $author) {
                $query->where('user_id', '=', "$author");
            });


        if ($request->input('sort_by') && $request->input('sort_by') == 'oldest') {
            //if ($request->input('order_by')) {
            $posts = $posts->oldest();
        } else {
            $posts = $posts->latest();
        }

        $per_page = $request->input('per_page') ? $request->input('per_page') : 3;

        $posts = $posts->with(['author', 'tags'])
            ->paginate($per_page)
            ->withQueryString();

        // $authors = [];
        // dd($allposts);
        $authors = Post::all()->map(function ($post, $key) {
            return ['id' => $post->author->id, 'name' => $post->author->name];
        })->unique()->sortBy('name');

        $authors = $authors->values()->all();
        // dd($authors);

        return Inertia::render(
            'Posts/Index',
            [
                'posts' => $posts,
                'authors' => $authors,
                'filters' => $request->only(['search', 'jurisdiction', 'tag', 'author', 'sort_by']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'can' => [
                    'create' => Auth::user()->can('post create'),
                    'edit' => Auth::user()->can('post edit'),
                    'delete' => Auth::user()->can('post delete'),
                ]
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Posts/Create',
            [
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all()
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->input('tags'));
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required'
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->store('posts', 'public');
            $request['attachment'] = $attachment;
        }

        $request['slug'] = $this->makeSlugFromTitle($request->input('title'));
        $request['user_id'] = auth()->user()->id;
        $request['status'] = "publish";
        // dd($request->all());
        $post = Post::create($request->all());

        $post->jurisdictions()->sync($request->input('jurisdictions'));
        $post->tags()->sync($request->input('tags'));
        sleep(1);

        // dd($post);

        return redirect()->route('admin.posts')->with('message', 'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $selected_jurisdictions = $post->jurisdictions
            ->map(function ($jurisdiction, $key) {
                return $jurisdiction->id;
            });
        // dd($selected_jurisdictions);
        // $post->tags = $post->tags;
        $selected_tags = $post->tags
            ->map(function ($tag, $key) {
                return $tag->id;
            });

        return Inertia::render(
            'Posts/Edit',
            [
                'post' => $post,
                'selected_jurisdictions' => $selected_jurisdictions,
                'selected_tags' => $selected_tags,
                'file' => asset('storage/' . $post->attachment),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // dd($request);
        $request->validate([
            'title' => 'required|string|max:255',
            // 'slug' => 'required|string|max:255',
            'content' => 'required',
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->store('posts', 'public');
            $post->attachment = $attachment;
        } else {
            // unset($request->attachment);
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->status = 'publish';

        $post->save();

        $post->jurisdictions()->sync($request->input('jurisdictions'));
        $post->tags()->sync($request->input('tags'));
        sleep(1);

        return redirect()->route('admin.posts')->with('message', 'Post Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        sleep(1);

        return redirect()->route('admin.posts')->with('message', 'Post Delete Successfully');
    }

    /**
     * Create a conversation slug.
     *
     * @param  string $title
     * @return string
     */
    public function makeSlugFromTitle($title)
    {
        $slug = Str::slug($title);

        $count = Post::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin(Request $request)
    {

        $posts = Post::query()
            // ->where('status', '=', 'publish')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                });
            })
            ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                $query->whereHas('jurisdictions', function ($query) use ($jurisdiction) {
                    $query->whereId($jurisdiction);
                });
            })
            ->when($request->input('tag'), function ($query, $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->whereId($tag);
                });
            });

        // if ($request->input('order_by')) {
        //     switch ($request->input('order_by')) {
        //         case 'oldest':
        //             $posts = $posts->oldest();
        //             break;

        //         default:
        //             $posts = $posts->latest();
        //             break;
        //     }
        // }
        $posts = $posts->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $posts = $posts->with(['author', 'tags'])
            ->paginate($per_page)
            ->withQueryString();

        // $authors = [];
        // dd($allposts);
        // $authors = Post::all()->map(function ($post, $key) {
        //     return ['id' => $post->author->id, 'name' => $post->author->name];
        // })->unique()->sortBy('name');

        // $authors = $authors->values()->all();
        // dd($authors);

        return Inertia::render(
            'Posts/Admin',
            [
                'posts' => $posts,
                // 'authors' => $authors,
                'filters' => $request->only(['search', 'jurisdiction', 'tag']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'can' => [
                    'create' => Auth::user()->can('post create'),
                    'edit' => Auth::user()->can('post edit'),
                    'delete' => Auth::user()->can('post delete'),
                ]
            ]
        );
    }

    public function downloadPdf(Request $request, $id)
    {
        // dd($id);
        $post = Post::find($id);
        // $file = Storage::get($award->pdf);
        // dd($award->pdf);
        $headers  = array(
            'Content-Type: application/pdf',
        );
        // dd($award->pdf);
        return Storage::download($post->attachment, $post->title . '.pdf', $headers);
    }
}
