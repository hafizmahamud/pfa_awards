<?php

use App\Models\Post;
use Inertia\Inertia;
use App\Models\Clause;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use App\Models\DashboardPost;
use App\Models\DocumentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Helpers\General\CollectionHelper;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\ProjectPostController;
use App\Http\Controllers\DashboardPostController;
use App\Http\Controllers\AwardsComparisonController;
use App\Http\Controllers\DocumentCollectionController;
use App\Models\AwardContent;
use App\Models\Condition;
use App\Models\ConditionTopic;
use App\Models\Subclause;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/dashboard');
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/test', function () {
    $file = \Illuminate\Http\UploadedFile::fake()->create('test-' . date('Y-m-d') . '-' . date('H-i-s') . '.pdf')->store('pdfs');
    //dd($file);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $posts = Post::latest()->with('tags')->take(5)->get();
        $documents = DocumentCollection::latest()->with('tags')->take(5)->get();

        $news_announcements = DashboardPost::query()
            ->where('status', '=', 'publish')
            ->latest()
            ->take(3)
            ->get();
        // dd($news_announcements);

        $posts_documents = $posts->concat($documents)
            ->sortByDesc('updated_at');
        // dd($posts_documents->take(3));
        return Inertia::render(
            'Dashboard',
            [
                'posts_documents' => $posts_documents->take(3)->values(),
                'news_announcements' => $news_announcements
            ]
        );
    })->name('dashboard');

    Route::resource('awards', AwardController::class);
    Route::post('awards/content', [AwardController::class, 'content_update'])->name('awards.content_update');
    Route::get('/awards/pdf/{id}', [AwardController::class, 'downloadPdf'])->name('awards.pdf');

    Route::resource('awards-comparison', AwardsComparisonController::class);
    Route::get('downloadPdf', [AwardsComparisonController::class, 'downloadPdf'])->name('awards.downloadPdf');

    Route::resource('document-collections', DocumentCollectionController::class);
    Route::get('/documents/pdf/{id}', [DocumentCollectionController::class, 'downloadPdf'])->name('documents.pdf');
    Route::get('documents/{document}/pdfstream', [DocumentCollectionController::class, 'pdfStream'])->name('pdfStream');
    Route::get('documents/{document}/zipAllFile', [DocumentCollectionController::class, 'zipAllFile'])->name('documents.zipAllFile');

    Route::resource('posts', PostController::class);

    Route::group(['middleware' => ['is.project.members']], function () {
        Route::get('projects/{project}/posts/create', [ProjectPostController::class, 'create'])->name('projectposts.create');
        Route::get('projects/{project}/posts/{post}/edit', [ProjectPostController::class, 'edit'])->name('projectposts.edit');
        Route::post('projects/{project}/posts', [ProjectPostController::class, 'store'])->name('projectposts.store');
        Route::put('projects/{project}/posts/{post}', [ProjectPostController::class, 'update'])->name('projectposts.update');
    });

    Route::resource('projects', ProjectController::class);

    Route::resource('dashboard-posts', DashboardPostController::class);

    Route::resource('conditions', ConditionController::class);
    Route::get('/conditions/category/{id}', [ConditionController::class, 'category'])->name('conditions.category');
    Route::get('/conditions/category/pdf/{id}', [ConditionController::class, 'downloadPdf'])->name('conditions.pdf');

    // Route::get('/awards-comparison', function () {
    //     return Inertia::render('AwardsComparison');
    // })->name('awards_comparison');
    // Route::get('/awards-comparison', [AwardsComparisonController::class, 'index'])->name('awards_comparison');

    Route::get('/search', function (Request $request) {
        // dd($request->input('jurisdiction'));

        $search_results = new Collection([]);

        if ($request->input('in')) {
            if (in_array('all', $request->input('in')) || in_array('clauses', $request->input('in'))) {
                // search title
                $results = Subclause::query()
                    ->when($request->input('s'), function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('title', 'like', "%{$search}%");
                        });
                    })
                    ->get()
                    ->map(function ($subclause) use ($request) {
                        return $subclause->award_contents->map(function ($award_content) use ($subclause, $request) {
                            if ($request->input('jurisdiction')) {
                                if ($award_content->award->jurisdiction->id == $request->input('jurisdiction')) {
                                    return [
                                        'id' => $award_content->id,
                                        'title' => $subclause->title,
                                        'content' => $award_content->content,
                                        'updated_at' => $award_content->updated_at,
                                        'award' => $award_content->award->title,
                                        'jurisdictions' => [$award_content->award->jurisdiction],
                                        'attachment' => null,
                                        'link' => route('awards.index', ['jurisdiction' => $award_content->award->jurisdiction->id, 'award_id' => $award_content->award->id, 'clause_id' => $award_content->subclause->clause->id]) . '#view-' . $award_content->subclause->id,
                                        'type' => 'CLAUSE'
                                    ];
                                }
                            } else {
                                return [
                                    'id' => $award_content->id,
                                    'title' => $subclause->title,
                                    'content' => $award_content->content,
                                    'updated_at' => $award_content->updated_at,
                                    'award' => $award_content->award->title,
                                    'jurisdictions' => [$award_content->award->jurisdiction],
                                    'attachment' => null,
                                    'link' => route('awards.index', ['jurisdiction' => $award_content->award->jurisdiction->id, 'award_id' => $award_content->award->id, 'clause_id' => $award_content->subclause->clause->id]) . '#view-' . $award_content->subclause->id,
                                    'type' => 'CLAUSE'
                                ];
                            }
                        });
                    });

                $results = $results->flatten(1)->whereNotNull();

                // search content
                $contents = AwardContent::query()
                    ->when($request->input('s'), function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('content', 'like', "%{$search}%");
                        });
                    })
                    ->get()
                    ->map(function ($award_content) use ($request) {
                        if ($request->input('jurisdiction')) {
                            if ($award_content->award->jurisdiction->id == $request->input('jurisdiction')) {
                                return [
                                    'id' => $award_content->id,
                                    'title' => $award_content->subclause->title,
                                    'content' => $award_content->content,
                                    'updated_at' => $award_content->updated_at,
                                    'award' => $award_content->award->title,
                                    'jurisdictions' => [$award_content->award->jurisdiction],
                                    'attachment' => null,
                                    'link' => route('awards.index', ['jurisdiction' => $award_content->award->jurisdiction->id, 'award_id' => $award_content->award->id, 'clause_id' => $award_content->subclause->clause->id]) . '#view-' . $award_content->subclause->id,
                                    'type' => 'CLAUSE'
                                ];
                            }
                        } else {
                            // dd($award_content->award->jurisdiction);
                            return [
                                'id' => $award_content->id,
                                'title' => $award_content->subclause->title,
                                'content' => $award_content->content,
                                'updated_at' => $award_content->updated_at,
                                'award' => $award_content->award->title,
                                'jurisdictions' => [$award_content->award->jurisdiction],
                                'attachment' => null,
                                'link' => route('awards.index', ['jurisdiction' => $award_content->award->jurisdiction->id, 'award_id' => $award_content->award->id, 'clause_id' => $award_content->subclause->clause->id]) . '#view-' . $award_content->subclause->id,
                                'type' => 'CLAUSE'
                            ];
                        }
                    });

                $contents = $contents->whereNotNull();
                $results = $results->concat($contents)->unique();

                $search_results = $search_results->concat($results);
            }
            if (in_array('all', $request->input('in')) || in_array('conditions', $request->input('in'))) {
                $results = ConditionTopic::query()
                    ->when($request->input('s'), function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->get()
                    ->map(function ($topic) use ($request) {
                        // return $topic->conditions_by_jurisdictions($request);
                        return $topic->conditions
                            ->map(function ($condition) use ($topic, $request) {
                                if ($condition->attachment) {
                                    $attachment = route('conditions.pdf', $condition->id);
                                } else {
                                    $attachment = null;
                                }
                                if ($request->input('jurisdiction')) {
                                    if ($condition->jurisdiction->id == $request->input('jurisdiction')) {
                                        return [
                                            'id' => $condition->id,
                                            'title' => $topic->name,
                                            'content' => $condition->content,
                                            'jurisdictions' => [$condition->jurisdiction],
                                            'attachment' => $attachment,
                                            'updated_at' => $condition->updated_at,
                                            'link' => route('conditions.category', ['id' => $topic->conditionSubcategory->conditionCategory->id, 'subcategory_id' => $topic->conditionSubcategory->id]) . '#condition-' . $condition->id,
                                            'type' => 'CONDITION'
                                        ];
                                    }
                                } else {
                                    return [
                                        'id' => $condition->id,
                                        'title' => $topic->name,
                                        'content' => $condition->content,
                                        'jurisdictions' => [$condition->jurisdiction],
                                        'attachment' => $attachment,
                                        'updated_at' => $condition->updated_at,
                                        'link' => route('conditions.category', ['id' => $topic->conditionSubcategory->conditionCategory->id, 'subcategory_id' => $topic->conditionSubcategory->id]) . '#condition-' . $condition->id,
                                        'type' => 'CONDITION'
                                    ];
                                }
                            });
                    });
                $results = $results->flatten(1)->whereNotNull();
                $conditions = Condition::query()
                    ->when($request->input('s'), function ($query, $search) {
                        $query->where(function ($query) use ($search) {
                            $query->where('content', 'like', "%{$search}%");
                        });
                    })
                    ->when($request->input('jurisdiction'), function ($query, $jurisdiction) {
                        $query->where('jurisdiction_id', '=', $jurisdiction);
                    })
                    ->get()
                    ->map(function ($condition) {
                        if ($condition->attachment) {
                            $attachment = route('conditions.pdf', $condition->id);
                        } else {
                            $attachment = null;
                        }
                        $topic = $condition->conditionTopic;
                        return [
                            'id' => $condition->id,
                            'title' => $condition->conditionTopic->name,
                            'content' => $condition->content,
                            'jurisdictions' => [$condition->jurisdiction],
                            'attachment' => $attachment,
                            'updated_at' => $condition->updated_at,
                            'link' => route('conditions.category', ['id' => $topic->conditionSubcategory->conditionCategory->id, 'subcategory_id' => $topic->conditionSubcategory->id]) . '#condition-' . $condition->id,
                            'type' => 'CONDITION'
                        ];
                    });
                $conditions = $conditions->whereNotNull();
                $results = $results->concat($conditions)->unique();

                $search_results = $search_results->concat($results);
            }
            if (in_array('all', $request->input('in')) || in_array('posts', $request->input('in'))) {
                // code...
                $posts = Post::query()
                    ->when($request->input('s'), function ($query, $search) {
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
                    ->get();

                $result = $posts->map(function ($post) {
                    if ($post->attachment) {
                        $attachment = route('posts.pdf', $post->id);
                    } else {
                        $attachment = null;
                    }
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content,
                        'jurisdictions' => $post->jurisdictions->toArray(),
                        'attachment' => $attachment,
                        'updated_at' => $post->updated_at,
                        'type' => 'POST'
                    ];
                });

                $search_results = $search_results->concat($result);
            }
            if (in_array('all', $request->input('in')) || in_array('documents', $request->input('in'))) {
                // code...
                $documents = DocumentCollection::query()
                    ->when($request->input('s'), function ($query, $search) {
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
                    ->with('documents')
                    ->get();

                // dd($documents);

                $result = $documents->map(function ($document) {
                    return [
                        'id' => $document->id,
                        'title' => $document->title,
                        'content' => $document->content,
                        'jurisdictions' => $document->jurisdictions->toArray(),
                        'attachment' => $document->documents,
                        'updated_at' => $document->updated_at,
                        'type' => 'DOCUMENT'
                    ];
                });

                $search_results = $search_results->concat($result);
            }
        }


        $search_results->sortByDesc('updated_at')->values();
        $search_results = CollectionHelper::paginate($search_results, 5)->withQueryString();
        // dd($search_results->links);
        // dd($search_results);

        return Inertia::render(
            'Search',
            [
                'search_results' => $search_results,
                'jurisdictions' => Jurisdiction::all(),
                'filters' => $request->only(['s', 'jurisdiction', 'in']),
            ]
        );
    })->name('search');
});

//Route for Admin Page
Route::group(['middleware' => 'is.admin'], function () {
    Route::get('admin/news-announcements', [AdminController::class, 'news_announcements'])->name('admin.news_announcements');
    Route::get('admin/posts', [AdminController::class, 'posts'])->name('admin.posts');
    Route::get('admin/documents', [AdminController::class, 'documents'])->name('admin.documents');
    Route::get('admin/projects', [AdminController::class, 'projects'])->name('admin.projects');
    Route::get('admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('admin/categories', [AdminController::class, 'store_categories'])->name('admin.categories.store');
    Route::get('admin/jurisdictions', [AdminController::class, 'jurisdictions'])->name('admin.jurisdictions');
    Route::post('admin/jurisdictions', [AdminController::class, 'store_jurisdictions'])->name('admin.jurisdictions.store');
    Route::get('admin/clauses', [AdminController::class, 'clauses'])->name('admin.clauses');
    Route::post('admin/clauses', [AdminController::class, 'store_clauses'])->name('admin.clauses.store');
    Route::post('admin/delete', [AdminController::class, 'delete'])->name('admin.delete');
    Route::resource('admin/tag', TagController::class);
    Route::resource('admin/user', UserController::class);
    Route::post('user/{user}/approve', [UserController::class, 'approve_user'])->name('user.approve');
    Route::post('user/{user}/decline', [UserController::class, 'decline_user'])->name('user.decline');
});




Route::get('/email/verify', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

//Resend Verification Email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/user/approval', function () {
    return Inertia::render('Approval');
})->middleware('auth')->name('user.approval');

// Route::middleware(['print'])->group(function() {
//     Route::get('downloadPdf', [AwardsComparisonController::class, 'downloadPdf'])->name('awards.downloadPdf');
// });
