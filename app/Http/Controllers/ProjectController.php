<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Jurisdiction;
use App\Models\ProjectPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DB as Database;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'show']]);
        $this->middleware('can:project create', ['only' => ['create', 'store']]);
        $this->middleware('can:project edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:project delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects = Project::query()
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
            });

        $projects = $projects->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $projects = $projects->with(['author', 'tags', 'members'])
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render(
            'Projects/Index',
            [
                'projects' => $projects,
                'filters' => $request->only(['search', 'jurisdiction', 'tag']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'can' => [
                    'create' => Auth::user()->can('project create'),
                    'edit' => Auth::user()->can('project edit'),
                    'delete' => Auth::user()->can('project delete'),
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
            'Projects/Create',
            [
                'jurisdictions' => fn () => Jurisdiction::all(),
                'tags' => fn () => Tag::all(),
                'members' => fn () => User::query()->get()->map(function ($user) {
                    return ['value' => $user->id, 'label' => $user->name];
                })
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
        $request->validate([
            'title' => 'required',
            'owner' => 'required',
            'description' => 'required',
            'jurisdictions' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'members' => 'sometimes|array',
        ]);

        DB::transaction(function () use ($request) {
            if (
                Project::where('title', $request->input('title'))
                ->orWhere('slug', Str::slug($request->input('title')))
                ->exists()
            ) {
                return back()->with('message', 'Project with that title already exists.');
            }

            $project = Project::create([
                'title' => $request->input('title'),
                'content' => $request->input('description'),
                'user_id' => $request->input('owner'),
                'slug' => Str::slug($request->input('title'))
            ]);

            $project->jurisdictions()->sync($request->input('jurisdictions'));
            $project->tags()->sync($request->input('tags'));
            $project->members()->sync($request->input('members'));
        });

        return redirect()->route('admin.projects')->with('message', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        $project->members = $project->members;
        $project->posts = ProjectPost::query()
            ->whereBelongsTo($project)
            ->orderByDesc('updated_at')
            ->with(['author', 'tags'])
            ->get();
        $project->owner = $project->author;
        // dd($project->author->id == auth()->user()->id);
        return Inertia::render(
            'Projects/Show',
            [
                'project' => $project,
                'isMember' => $project->members->contains(auth()->user()->id),
                'isOwner' => $project->author->id == auth()->user()->id
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        $selected_jurisdictions = $project->jurisdictions
            ->map(function ($jurisdiction, $key) {
                return $jurisdiction->id;
            });

        $selected_tags = $project->tags
            ->map(function ($tag, $key) {
                return $tag->id;
            });

        $selected_users = $project->members
            ->map(function ($members, $key) {
                return $members->id;
            });

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'selected_jurisdictions' => $selected_jurisdictions,
            'selected_tags' => $selected_tags,
            'selected_users' => $selected_users,
            'jurisdictions' => Jurisdiction::all(),
            'tags' => Tag::all(),
            // 'memberlist' => $memberslist,
            'members' => fn () => User::query()->get()->map(function ($user) {
                return ['value' => $user->id, 'label' => $user->name];
            }),
            'users' => fn () => User::query()->get()->map(function ($user) {
                return ['id' => $user->id, 'name' => $user->name];
            }),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required',
            'owner' => 'required',
            'description' => 'required',
            'jurisdictions' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'members' => 'sometimes|array',
        ]);

        $project->title = $request->input('title');
        $project->user_id = $request->input('owner');
        $project->slug = $request->input('title');
        $project->content = $request->input('description');
        $project->updated_at = now();
        $project->save();

        $project->jurisdictions()->sync($request->input('jurisdictions'));
        $project->tags()->sync($request->input('tags'));
        $project->members()->sync($request->input('members'));
        sleep(1);

        return redirect()->route('admin.projects')->with('message', 'Project Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // dd($project);
        $project->delete();
        sleep(1);

        return redirect()->route('admin.projects')->with('message', 'Project Deleted Successfully');
    }
}
