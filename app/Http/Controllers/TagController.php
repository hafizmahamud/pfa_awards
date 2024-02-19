<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:tag list', ['only' => ['index', 'show']]);
        $this->middleware('can:tag create', ['only' => ['create', 'store']]);
        $this->middleware('can:tag edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:tag delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::with('childTags')->orderby('order')->get();

        return Inertia::render('Admin/Tag/Index', [
            'tags' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => ['required', 'string', 'max:255'],
        ]);

        //Get last order number
        $order = Tag::select('order')->orderBy('order', 'desc')->first();

        $data = Tag::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parenttag'),
            'created_at' => now(),
            'updated_at' => now(),
            'order' => ($order ? $order->order + 1 : 1)
        ]);
        sleep(1);
        //redirect with refresh page
        return redirect()->to('/admin/tag')->with('message', 'Tag Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tags = Tag::find($id);
        $alltag = Tag::all();

        return Inertia::render('Admin/Tag/Edit', [
            'tags' => $tags,
            'alltag' => $alltag
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $tag->name = $request->name;
        $tag->parent_id = $request->parenttag;
        $tag->save();

        return redirect()->route('tag.index')->with('message', 'Tag Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        //update order after delete
        $tag->update(['order' => 0]);
        $alltag = Tag::all();
        $i = 1;

        foreach ($alltag as $tags) {
            $id = $tags->id;
            $tags->update(['order' => $i]);
            $i++;
        }

        return redirect()->route('tag.index')
            ->with('message', __('Tag Deleted Successfully'));
    }


    public function updateAll(Request $request)
    {
        $tags = Tag::all();

        foreach ($tags as $tag) {
            $id = $tag->id;
            foreach ($request->tags as $tagFrontEnd) {

                if ($tagFrontEnd['id'] == $id) {
                    $tag->update(['order' => $tagFrontEnd['order']]);
                }
            }
        }
        return response('Update Successful.', 200);
    }
}
