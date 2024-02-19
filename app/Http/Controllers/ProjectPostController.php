<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectPost;
use App\Models\Tag;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProjectPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        return Inertia::render(
            'ProjectPosts/Create',
            [
                'tags' => Tag::all(),
                'project' => $project
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
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'content' => 'required',
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->store('project_posts', 'public');
            $request['attachment'] = $attachment;
        }

        $request['user_id'] = auth()->user()->id;

        $post = ProjectPost::create($request->all());

        $post->tags()->sync($request->input('tags'));
        sleep(1);

        // dd($post);

        return redirect()->route('projects.show', $request->input('project_id'))->with('message', 'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, ProjectPost $post)
    {
        $tags = $post->tags
            ->map(function ($tag, $key) {
                return $tag->id;
            });


        // dd($tags);

        $post->selected_tags = $tags->toArray();

        // dd($post);

        if ($post->attachment)
            $post->attachment = asset('storage/' . $post->attachment);

        return Inertia::render(
            'ProjectPosts/Edit',
            [
                'post' => $post,
                'tags' => Tag::all(),
                'project' => $project
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, ProjectPost $post)
    {
        // dd($request);

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'content' => 'required',
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->store('project_posts', 'public');
            $post->attachment = $attachment;
        } else {
            // unset($request->attachment);
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->save();

        $post->tags()->sync($request->input('tags'));
        sleep(1);

        return redirect()->route('projects.show', $project->id)->with('message', 'Post Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
