<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Condition;
use Illuminate\Support\Str;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use App\Models\ConditionTopic;
use App\Models\ConditionCategory;
use App\Models\ConditionSubcategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConditionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'show']]);
        $this->middleware('can:condition create', ['only' => ['create', 'store']]);
        $this->middleware('can:condition edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:condition delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $categories = ConditionCategory::all();

        return Inertia::render(
            'Conditions/Index',
            [
                'categories' => $categories,
            ]
        );
    }

    public function category(Request $request, $id)
    {
        $subcategories = ConditionSubcategory::whereBelongsTo(ConditionCategory::find($id))->get();

        if (!count($subcategories)) {
            return Inertia::render(
                'Conditions/Compare',
                [
                    'compare' => [],
                    'category' => $id,
                    'filters' => $request->only(['search', 'jurisdictions', 'tag', 'subcategory_id']),
                    'jurisdictions' => Jurisdiction::all(),
                    'tags' => Tag::all()
                ]
            );
        }
        $topics = ConditionTopic::whereBelongsTo($subcategories)
            ->when($request->input('tag'), function ($query, $tag) {
                $query->whereHas('tags', function ($query) use ($tag) {
                    $query->whereId($tag);
                });
            })
            ->get();
        $conditions = Condition::query()
            ->when($request->input('jurisdictions'), function ($query) use ($request) {
                $query->whereIn('jurisdiction_id', $request->input('jurisdictions'));
            })
            ->with('conditionTopic.conditionSubcategory', 'jurisdiction', 'conditionTopic.tags')
            ->whereBelongsTo($topics)
            ->get();

        // dd($conditions);

        $compare = [];

        if ($conditions->count()) {
            foreach ($conditions as $condition) {
                $subcategory = $condition->conditionTopic->conditionSubcategory;
                if (!isset($compare[$subcategory->id])) {
                    $compare[$subcategory->id] = [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name
                    ];
                }

                $topic = $condition->conditionTopic;

                if (!isset($compare[$subcategory->id]['topics'][$topic->id])) {
                    $compare[$subcategory->id]['topics'][$topic->id] = [
                        'id' => $topic->id,
                        'name' => $topic->name,
                        'tags' => $topic->tags
                    ];
                }

                $compare[$subcategory->id]['topics'][$topic->id]['jurisdictions'][$condition->jurisdiction_id] = [
                    'id' => $condition->id,
                    'jurisdiction' => $condition->jurisdiction->name,
                    'content' => $condition->content,
                    'attachment' => $condition->attachment,
                    'attachmentUrl' => Storage::url('public/' . $condition->attachment),
                    'author' => $condition->author
                ];
            }
        }

        return Inertia::render(
            'Conditions/Compare',
            [
                'compare' => $compare,
                'category' => $id,
                'filters' => $request->only(['search', 'jurisdictions', 'tag', 'subcategory_id']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all()
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
        $subcategories = null;
        $topics = null;
        $condition = null;

        if ($request->input('category_id')) {
            $subcategories = ConditionSubcategory::where('condition_category_id', '=', $request->input('category_id'))->get();
            // dd($subcategories);
        }

        if ($request->input('subcategory_id')) {
            $topics = ConditionTopic::where('condition_subcategory_id', '=', $request->input('subcategory_id'))->get();
        }

        if ($request->input('topic_id') && $request->input('jurisdiction_id')) {
            $condition = Condition::where('condition_topic_id', '=', $request->input('topic_id'))
                ->where('jurisdiction_id', '=', $request->input('jurisdiction_id'))
                ->with(['conditionTopic.tags'])
                ->first();

            // dd

            $condition->conditionTopic->tags_array = $condition->conditionTopic->tags->map(function ($tag) {
                return $tag->id;
            });
            // dd($condition);
        }

        return Inertia::render(
            'Conditions/Create',
            [
                'jurisdictions' => Jurisdiction::all(),
                'categories' => ConditionCategory::all(),
                'filters' => $request->only(['category_id', 'subcategory_id', 'topic_id', 'jurisdiction_id']),
                'subcategories' => $subcategories,
                'topics' => $topics,
                'condition' => $condition,
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
        //
        $request->validate([
            'name' => 'required',
            'jurisdiction_id' => 'required',
            'condition_topic_id' => 'required',
            'content' => 'required',
        ]);
        // dd($request);

        if ($request->file('file')) {
            $attachment = $request->file('file')->storeAs('conditions', $request->file('file')->getClientOriginalName(), 'public');
            $request['attachment'] = $attachment;
        } else {
            $attachment = null;
        }

        // create or update topic
        // dd($request->input('condition_topic_id'));
        if ($request->input('condition_topic_id')) {
            // update topic
            $topic = ConditionTopic::find($request->input('condition_topic_id'));
            $topic->name = $request->input('name');
            $topic->save();

            $topic->tags()->sync($request->input('tags'));
        } else {
            // create topic
            $topic = new ConditionTopic();
            $topic->conditionSubcategory = $request->input('subcategory_id');
            $topic->name = $request->input('name');

            $topic->save();

            $topic->tags()->sync($request->input('tags'));

            $request['condition_topic_id'] = $topic->id;
        }
        $parameters = [
            'category_id' => $request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id'),
            'topic_id' => $request->input('condition_topic_id'),
            'jurisdiction_id' => $request->input('jurisdiction_id'),
        ];

        // dd($parameters);

        $request['user_id'] = auth()->user()->id;

        if ($request->input('condition_id')) { //update
            $condition = Condition::find($request->input('condition_id'));
            $condition->content = $request->input('content');
            $condition->attachment = $attachment;
            $condition->save();
        } else {
            $condition = Condition::create($request->all());
        }

        sleep(1);

        return redirect()->route('conditions.create', $parameters)->with('message', 'Condition content updated successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Condition  $condition
     * @return \Illuminate\Http\Response
     */
    public function show(Condition $condition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Condition  $condition
     * @return \Illuminate\Http\Response
     */
    public function edit(Condition $condition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Condition  $condition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Condition $condition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Condition  $condition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Condition $condition)
    {
        //
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

        $count = Condition::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function downloadPdf(Request $request, $id)
    {
        // dd($id);
        $condition = Condition::find($id);
        $headers  = array(
            'Content-Type: application/pdf',
        );
        // dd($award->pdf);
        return Storage::download($condition->attachment, $condition->conditionTopic->name . '-' . $condition->jurisdiction->name . '.pdf', $headers);
    }
}
