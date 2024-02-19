<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Inertia\Inertia;
use App\Models\Clause;
use App\Models\Project;
use App\Models\Condition;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use App\Models\ConditionCategory;
use App\Models\DocumentCollection;
use App\Models\ConditionSubcategory;
use App\Models\DashboardPost;
use App\Models\Subclause;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:post create', ['only' => ['create', 'store']]);
        $this->middleware('can:post edit', ['only' => ['edituser', 'edituser']]);
        $this->middleware('can:post delete', ['only' => ['destroy']]);

        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function posts(Request $request)
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

        $posts = $posts->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $posts = $posts->with(['author', 'tags'])
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render(
            'Admin/Posts',
            [
                'posts' => $posts,
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

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function news_announcements(Request $request)
    {
        $posts = DashboardPost::query()
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

        $posts = $posts->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $posts = $posts->with(['author'])
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render(
            'Admin/NewsAnnouncements',
            [
                'posts' => $posts,
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

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function documents(Request $request)
    {
        $documents = DocumentCollection::query()
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

        $documents = $documents->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $documents = $documents->with(['tags'])
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render(
            'Admin/DocumentCollections',
            [
                'documentCollections' => $documents,
                // 'authors' => $authors,
                'filters' => $request->only(['search', 'jurisdiction', 'tag']),
                'jurisdictions' => Jurisdiction::all(),
                'tags' => Tag::all(),
                'can' => [
                    'create' => Auth::user()->can('document create'),
                    'edit' => Auth::user()->can('document edit'),
                    'delete' => Auth::user()->can('document delete'),
                ]
            ]
        );
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function projects(Request $request)
    {
        $projects = Project::query()
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

        $projects = $projects->latest();

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $projects = $projects->with(['tags'])
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render(
            'Admin/Projects',
            [
                'projects' => $projects,
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

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories(Request $request)
    {

        return Inertia::render(
            'Admin/ConditionCategories',
            [
                'conditionCategories' => ConditionCategory::with('conditionSubcategories.conditionTopics')->get(),
                'can' => [
                    'create' => Auth::user()->can('post create'),
                    'edit' => Auth::user()->can('post edit'),
                    'delete' => Auth::user()->can('post delete'),
                ]
            ]
        );
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_categories(Request $request)
    {
        // dd($request->all());
        if ($request->condition_category_id == "") {
            $request->validate([
                'name' => 'required|string',
                'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        } else {
            $request->validate([
                'name' => 'required|string'
            ]);
        }

        if ($request->input('id')) { // update item
            if ($request->input('type') == 'category' && $request->input('condition_category_id') != "") {
                $request->validate([
                    'condition_category_id' => 'required'
                ]);
                $temp = ConditionCategory::find($request->input('id'));
                $temp->delete();

                ConditionSubcategory::create($request->all());
                redirect()->route('admin.categories')->with('message', 'Subcategory updated successfully.');
            } elseif (!($request->input('type') == 'category') && ($request->input('condition_category_id') == "")) {
                $temp = ConditionSubcategory::find($request->input('id'));
                $temp->delete();

                ConditionCategory::create($request->all());
                redirect()->route('admin.categories')->with('message', 'Category updated successfully.');
            } else {
                if ($request->input('type') == 'category') {
                    $item = ConditionCategory::find($request->input('id'));
                } else {
                    $item = ConditionSubcategory::find($request->input('id'));
                    $item->condition_category_id = $request->input('condition_category_id');
                }

                $item->name = $request->input('name');
                $item->save();
                redirect()->route('admin.categories')->with('message', 'Category or Subcategory updated successfully.');
            }
        } else {
            if ($request->input('condition_category_id') != "") {
                ConditionSubcategory::create($request->all());
                redirect()->route('admin.categories')->with('message', 'Subcategory created successfully.');
            } else {
                if ($request->file('file')) {
                    $image = $request->file('file')->store('condition_categories', 'public');
                    $request['image'] = $image;
                }
                ConditionCategory::create($request->all());
                redirect()->route('admin.categories')->with('message', 'Category created successfully.');
            }
        }
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function clauses(Request $request)
    {

        return Inertia::render(
            'Admin/Clauses',
            [
                'clauses' => Clause::with('subclauses.award_contents')->get(),
                'can' => [
                    'create' => Auth::user()->can('post create'),
                    'edit' => Auth::user()->can('post edit'),
                    'delete' => Auth::user()->can('post delete'),
                ]
            ]
        );
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_clauses(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required|string',
        ]);

        if ($request->input('type') == 'clause') {
            if ($request->input('id')) {
                $item = Clause::find($request->input('id'));
                $item->title = $request->input('title');
                $item->save();
                redirect()->route('admin.clauses')->with('message', 'Clause updated successfully.');
            } else {
                Clause::create($request->all());
                redirect()->route('admin.clauses')->with('message', 'Clause created successfully.');
            }
        } else {
            $request->validate([
                'clause_id' => 'required'
            ]);

            if ($request->input('id')) {
                $item = Subclause::find($request->input('id'));
                $item->title = $request->input('title');
                $item->clause_id = $request->input('clause_id');
                $item->save();
                redirect()->route('admin.clauses')->with('message', 'Subclause updated successfully.');
            } else {
                Subclause::create($request->all());
                redirect()->route('admin.clauses')->with('message', 'Subclause created successfully.');
            }
        }
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function jurisdictions(Request $request)
    {

        return Inertia::render(
            'Admin/Jurisdictions',
            [
                'jurisdictions' => Jurisdiction::all(),
                'can' => [
                    'create' => Auth::user()->can('post create'),
                    'edit' => Auth::user()->can('post edit'),
                    'delete' => Auth::user()->can('post delete'),
                ]
            ]
        );
    }

    /**
     * Display a listing of the resource for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function store_jurisdictions(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        if ($request->input('id')) {
            $jurisdiction = Jurisdiction::find($request->input('id'));
            $jurisdiction->name = $request->input('name');

            if ($request->file('logo')) {
                $logo = $request->file('logo')->store('jurisdictions', 'public');
                $jurisdiction->logo = $logo;
            }

            $jurisdiction->save();
            redirect()->route('admin.jurisdictions')->with('message', 'Jurisdiction updated successfully.');
        } else {
            if ($request->file('logo')) {
                $logo = $request->file('logo')->store('jurisdictions', 'public');
                $request['logo'] = $logo;
            }

            Jurisdiction::create($request->all());
            redirect()->route('admin.jurisdictions')->with('message', 'Jurisdiction created successfully.');
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        // dd($request);
        switch ($request->input('type')) {
            case 'category':
                ConditionCategory::find($id)->delete();
                return redirect()->route('admin.categories')->with('message', 'Category deleted successfully.');
                break;

            case 'subcategory':
                ConditionSubcategory::find($id)->delete();
                return redirect()->route('admin.categories')->with('message', 'Subcategory deleted successfully.');
                break;

            case 'clauses':
                Clause::find($id)->delete();
                return redirect()->route('admin.clauses')->with('message', 'Clause deleted successfully.');
                break;

            case 'subclauses':
                Subclause::find($id)->delete();
                return redirect()->route('admin.clauses')->with('message', 'Subclause deleted successfully.');
                break;

            case 'jurisdiction':
                Jurisdiction::find($id)->delete();
                return redirect()->route('admin.jurisdictions')->with('message', 'Jurisdiction deleted successfully.');
                break;

            default:
                # code...
                break;
        }
    }
}
