<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Award;
use App\Models\Clause;
use Illuminate\Support\Str;
use App\Models\AwardContent;
use App\Models\Jurisdiction;
use App\Models\Subclause;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AwardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'show']]);
        $this->middleware('can:award create', ['only' => ['create', 'store']]);
        $this->middleware('can:award edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:award delete', ['only' => ['destroy']]);
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

        if ($request->input('jurisdiction') != 11) {
            $awards = Award::query()
                ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                    $query->where('jurisdiction_id', '=', $jurisdiction);
                })
                ->latest('updated_at')
                // ->with(['clauses_with_subclauses'])
                ->get();

            // dd($awards);

            // dd($award);
            $award = Award::query()
                ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                    $query->where('jurisdiction_id', '=', $jurisdiction);
                })
                ->when($request->input('award_id'), function ($query, $award_id) {
                    $query->where('id', '=', $award_id);
                });
            // ->when($request->input('tag'), function ($query, $tag) {
            //     $query->whereHas('tags', function ($query) use ($tag) {
            //         $query->whereId($tag);
            //     });
            // })
        } else {
            $awards = Award::query()
                ->latest('updated_at')
                // ->with(['clauses_with_subclauses'])
                ->get();

            $award = Award::query()
                ->when($request->input('award_id'), function ($query, $award_id) {
                    $query->where('id', '=', $award_id);
                });
        }

        $award = $award
            // ->with(['award_contents'])
            ->latest('updated_at')
            ->first();

        $filter = [];
        if ($request->input('tag')) {
            $filter['tag'] = $request->input('tag');
        }

        if ($award == null) {
            $content = null;
        } else {
            $content = $award->getContent($filter);
        }

        // build content
        // $content = $award->content;

        // dd($content);


        return Inertia::render(
            'Awards/Index',
            [
                'content' => $content,
                'award' => $award,
                'awards' => $awards,
                'filters' => $request->only(['jurisdiction', 'tag', 'award_id', 'clause_id']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'can' => [
                    'create' => Auth::user()->can('award create'),
                    'edit' => Auth::user()->can('award edit'),
                    'delete' => Auth::user()->can('award delete'),
                ]
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $awards = Award::query()
            ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                $query->where('jurisdiction_id', '=', $jurisdiction);
            })
            ->latest('updated_at')
            // ->with(['clauses_with_subclauses'])
            ->get();

        return Inertia::render(
            'Awards/Create',
            [
                // 'award' => $award,
                'awards' => $awards,
                'filters' => $request->only(['jurisdiction', 'award_id']),
                'jurisdictions' => Jurisdiction::all(),
                // 'tags' => Tag::all()
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
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'jurisdiction_id' => 'required|integer',
            'file' => [
                'required',
                File::types(['pdf'])
            ],
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->storeAs('pdfs', $request->file('file')->getClientOriginalName(), 'public');
            $request['pdf'] = $attachment;
        }

        $start_date = Carbon::parse($request['start_date']);
        $request['start_date'] = $start_date->format('Y-m-d H:i:s');

        $end_date = Carbon::parse($request['end_date']);
        $request['end_date'] = $end_date->format('Y-m-d H:i:s');

        $request['slug'] = $this->makeSlugFromTitle($request->input('title'));
        $request['user_id'] = auth()->user()->id;
        // dd($request->all());
        $award = Award::create($request->all());
        sleep(1);

        // dd($post);

        return redirect()->route('awards.edit', $award->id)->with('message', 'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function show(Award $award)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function edit(Award $award, Request $request)
    {
        // dd($award);
        // $clauses = Clause::all();
        // dd($clauses);
        if ($request->input('clause')) {
            $subclauses = Subclause::whereBelongsTo(Clause::find($request->input('clause')))->get();
        } else {
            $subclauses = [];
        }

        $award_content = null;
        // dd($request->input('subclause'));
        // dd($award->id);
        if ($request->input('subclause')) {
            $award_content = AwardContent::where('subclause_id', '=', $request->input('subclause'))
                ->where('award_id', '=', $award->id)
                ->with('tags')
                ->get()
                ->first();
            // dd($award_content);
            if ($award_content != null) {
                $award_content->tags_array = $award_content->tags->map(function ($tag) {
                    return $tag->id;
                });
            }
        }

        return Inertia::render(
            'Awards/Edit',
            [
                'award' => $award,
                'jurisdictions' => Jurisdiction::all(),
                'pdf' => asset('storage/' . $award->pdf),
                'tags' => Tag::all(),
                'clauses' => Clause::all(),
                'subclauses' => $subclauses,
                'award_content' => $award_content,
                'filters' => $request->only(['clause', 'subclause']),
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Award $award)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'jurisdiction_id' => 'required|integer',
        ]);

        if ($request->file('file')) {
            $attachment = $request->file('file')->storeAs('pdfs', $request->file('file')->getClientOriginalName(), 'public');
            $award->pdf = $attachment;
        }

        $start_date = Carbon::parse($request['start_date']);
        $award->start_date = $start_date->format('Y-m-d H:i:s');

        $end_date = Carbon::parse($request['end_date']);
        $award->end_date = $end_date->format('Y-m-d H:i:s');
        // dd($request->all());
        $award->jurisdiction_id = $request->input('jurisdiction_id');
        // dd($award);
        $award->save();
        sleep(1);

        // dd($post);

        return redirect()->route('awards.edit', $award->id)->with('message', 'Award Updated Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function content_update(Request $request)
    {
        $request->validate([
            'award_id' => 'required|integer',
            'subclause_id' => 'required|integer',
            'content' => 'required'
        ]);

        if ($request->input('id')) { //update
            $award_content = AwardContent::find($request->input('id'));
            $award_content->content = $request->input('content');
            $award_content->save();
        } else {
            $award_content = AwardContent::create($request->all());
        }

        $award_content->tags()->sync($request->input('tags'));

        return redirect()->route('awards.edit', $request->input('award_id'))->with('message', 'Subclause content updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Award  $award
     * @return \Illuminate\Http\Response
     */
    public function destroy(Award $award)
    {
        //
    }

    public function downloadPdf(Request $request, $id)
    {
        // dd($id);
        $award = Award::find($id);
        // $file = Storage::get($award->pdf);
        // dd($award->pdf);
        $headers  = array(
            'Content-Type: application/pdf',
        );
        // dd($award->pdf);
        return Storage::download($award->pdf, $award->title . '.pdf', $headers);
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

        $count = Award::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
