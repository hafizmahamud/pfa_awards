<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Clause;
use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use PDF;
use App\Models\User;
use DB;
use URL;

class AwardsComparisonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('jurisdictions')) {
            $jurisdictions = $request->input('jurisdictions');

            $awards = collect($jurisdictions)->map(function ($jurisdiction) use ($request) {
                $award = Award::query()
                    ->whereHas('jurisdiction', function ($query) use ($jurisdiction) {
                        $query->whereId($jurisdiction);
                    })
                    ->with(['jurisdiction'])
                    ->latest()
                    ->first();

                $filter = [];
                if ($request->input('tag')) {
                    $filter['tag'] = $request->input('tag');
                }

                if ($request->input('topic')) {
                    $filter['clause'] = $request->input('topic');
                }
                if ($award != null) {
                    $award->contents = $award->getContent($filter);
                }

                return $award;
            });

            // $awards
            // dd($awards[0]->contents);
            if ($request->input('topic')) {
                $clauses = Clause::where('id', '=', $request->input('topic'))->get();
            } else {
                $clauses = Clause::all();
            }
            $content = [];
            foreach ($clauses as $clause) {
                $subclauses = $clause->subclauses;

                foreach ($subclauses as $subclause) {
                    $content_1 = (isset($awards[0]->contents[$clause->id]['subclauses'][$subclause->id]['content'])) ? $awards[0]->contents[$clause->id]['subclauses'][$subclause->id]['content'] : '';
                    $content_2 = (isset($awards[1]->contents[$clause->id]['subclauses'][$subclause->id]['content'])) ? $awards[1]->contents[$clause->id]['subclauses'][$subclause->id]['content'] : '';

                    if ($content_1 == '' && $content_2 == '') {
                        continue;
                    }
                    $content[] = [
                        'id' => $subclause->id,
                        'category' => $clause->title,
                        'provision' => $subclause->title,
                        'content' => [
                            $content_1,
                            $content_2
                        ]
                    ];
                }
            }
            // dd($content);

            if ($awards[0] == null || $awards[1] == null) {
                $awards = null;
                $comparison_content = [];
            } else {
                $comparison_content = [
                    'jurisdiction' => [
                        $awards[0]->jurisdiction,
                        $awards[1]->jurisdiction,
                    ],
                    'terms' => [
                        '(' . $awards[0]->start_date . ' - ' . $awards[0]->end_date . ')',
                        '(' . $awards[1]->start_date . ' - ' . $awards[1]->end_date . ')',
                    ],
                    'award_title' => [
                        $awards[0]->title,
                        $awards[1]->title,
                    ],
                    'content_comparison' => $content

                ];
            }
        } else {
            $awards = false;
            $comparison_content = [];
        }

        return Inertia::render(
            'AwardsComparison',
            [
                'awards' => $awards,
                'comparison_content' => $comparison_content,
                'filters' => $request->only(['jurisdictions', 'tag', 'topic']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'clauses' => Clause::all()
            ]
        );
    }

    public function create()
    {
        //
    }
}
