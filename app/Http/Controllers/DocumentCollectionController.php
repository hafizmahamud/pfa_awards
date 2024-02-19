<?php

namespace App\Http\Controllers;

use App\Jobs\Documents\FileHasUploaded;
use App\Models\Tag;
use Inertia\Inertia;
use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use App\Models\DocumentCollection;
use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use File;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Redirect;

class DocumentCollectionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'show']]);
        $this->middleware('can:document create', ['only' => ['create', 'store']]);
        $this->middleware('can:document edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:document delete', ['only' => ['destroy']]);
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

        $documentCollections = DocumentCollection::query()
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

        if ($request->input('sort_by')) {
            switch ($request->input('sort_by')) {
                case 'oldest':
                    $documentCollections = $documentCollections->oldest();
                    break;

                default:
                    $documentCollections = $documentCollections->latest();
                    break;
            }
        }

        $per_page = $request->input('per_page') ? $request->input('per_page') : 10;

        $documentCollections = $documentCollections->with(['author', 'tags', 'documents'])
            ->paginate($per_page)
            ->withQueryString();

        $authors = DocumentCollection::all()->map(function ($documentCollection, $key) {
            return ['id' => $documentCollection->author->id, 'name' => $documentCollection->author->name];
        })->unique()->sortBy('name');

        $authors = $authors->values()->all();

        return Inertia::render(
            'DocumentCollections/Index',
            [
                'documentCollections' => $documentCollections,
                'authors' => $authors,
                'filters' => $request->only(['search', 'jurisdiction', 'tag', 'author', 'sort_by']),
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
    //$docCollection->load('document','tags','jurisdictions')
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('DocumentCollections/Create', [
            'jurisdictions' => fn () => Jurisdiction::all(),
            'tags' => fn () => Tag::all()
        ]);
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
            'description' => 'required',
            'jurisdictions' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'files' => 'sometimes|array',
            'files.*' => 'mimes:pdf|max:2048'
        ]);

        DB::transaction(function () use ($request) {
            // if (
            //     DocumentCollection::where('title', $request->input('title'))
            //     ->orWhere('slug', Str::slug($request->input('title')))
            //     ->orWhere('content', $request->input('description'))
            //     ->exists()
            // ) {
            //     return back()->with('message', 'Document Collection already exists');
            // }

            $documentCollection = DocumentCollection::create([
                'title' => $request->input('title'),
                'slug' =>  $this->makeSlugFromTitle($request->input('title')),
                'content' => $request->input('description'),
                'user_id' => auth()->user()->id
            ]);

            $documentCollection->jurisdictions()->sync($request->input('jurisdictions'));
            $documentCollection->tags()->sync($request->input('tags'));

            if ($request->has('documents') && $request->hasFile('files')) {
                $files = $request->file('files');

                collect(array_unique($files))->each(function ($file, $index) use ($request, $documentCollection) {
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    if ($this->checkDirectory(storage_path('app/public/documents'))) {
                        $filePath = $file->storeAs('documents', $fileName);
                    }
                    dispatch(new FileHasUploaded($documentCollection, $fileName, $filePath, $request->input('documents')[$index]));
                });
            }
        });

        return redirect()->route('admin.documents')
            ->with('message', 'Document Collection created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DocumentCollection  $documentCollection
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentCollection $documentCollection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DocumentCollection  $documentCollection
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentCollection $documentCollection)
    {
        //$documentUploads = DocumentUpload::where('document_collection_id', $document->id)->get();
        $selected_jurisdictions = $documentCollection->jurisdictions
            ->map(function ($jurisdiction, $key) {
                return $jurisdiction->id;
            });

        $selected_tags = $documentCollection->tags
            ->map(function ($tag, $key) {
                return $tag->id;
            });

        return Inertia::render('DocumentCollections/Edit', [
            'documents' => $documentCollection->load('documents', 'tags', 'jurisdictions'),
            'selected_jurisdictions' => $selected_jurisdictions,
            'selected_tags' => $selected_tags,
            'jurisdictions' => Jurisdiction::all(),
            'tags' => Tag::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentCollection  $documentCollection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentCollection $documentCollection)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'jurisdictions' => 'sometimes|array',
            'tags' => 'sometimes|array',
            'files' => 'sometimes|array',
            'files.*' => 'mimes:pdf|max:2048'
        ]);

        $documentCollection->title = $request->title;
        $documentCollection->content = $request->description;
        $documentCollection->updated_at = now();
        $documentCollection->jurisdictions()->sync($request->input('jurisdictions'));
        $documentCollection->tags()->sync($request->input('tags'));
        $documentCollection->save();

        if ($request->has('documents') && $request->hasFile('files')) {

            $files = $request->file('files');
            collect(array_unique($files))->each(function ($file, $index) use ($request, $documentCollection) {
                $fileName = time() . '_' . $file->getClientOriginalName();

                if ($this->checkDirectory(storage_path('app/public/documents'))) {
                    $filePath = $file->storeAs('documents', $fileName);
                }
                dispatch(new FileHasUploaded($documentCollection, $fileName, $filePath, $request->input('documents')[$index]));
            });
        }

        sleep(1);

        return redirect()->route('admin.documents')->with('message', 'Document Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DocumentCollection  $documentCollection
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentCollection $documentCollection)
    {
        // dd($documentCollection);
        $documentCollection->delete();
        sleep(1);

        return redirect()->route('admin.documents')->with('message', 'Documents deleted successfully.');
    }

    /**
     * Check if directory exist or not and create if not
     *
     * @param string $path
     * @return void
     */
    private function checkDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);

            return true;
        }

        return true;
    }


    /**
     * Zipped all files in directory
     *
     * @param string $path
     * @return void
     */
    public function zipAllFile($id)
    {
        $document = DocumentUpload::where('document_collection_id', $id)->get();

        $documents = [];

        // if(empty($document))
        // {
        //     echo "No Files Found";
        // } else {
        //     dd("There is files");
        // }


        foreach ($document as $doc) {
            $file =  public_path('storage/documents/' . $doc->unique_file_name);

            if (\File::exists(public_path('storage/documents/' . $doc->unique_file_name))) {
                $documents[] = public_path('storage/documents/' . $doc->unique_file_name);
            }
        }



        $result = uniqid();
        $fileName = $result . ".zip";

        $zip = new ZipArchive;
        $zipFile = $zip->open(public_path($fileName), ZipArchive::CREATE);

        if ($zipFile === TRUE) {
            //Checking if document files exists before zip
            if (empty($documents)) {
                //return redirect()->back()->withErrors(['msg' => 'The Message']);
                return redirect()->back()->with('message', 'No document found in this Document Collection');
            } else {
                $files = $documents;
            }
            //$files = $documents;

            //$files = File::files(public_path('storage/documents'));       //all files in storage/documents
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        } else {
            var_dump($zipFile);
        }

        return response()->download(public_path($fileName));
    }


    public function downloadPdf(Request $request, $id)
    {
        $documentUpload = DocumentUpload::find($id);

        $headers  = array(
            'Content-Type: application/pdf',
        );

        return Storage::download($documentUpload->path, $documentUpload->file_name . '.pdf', $headers);
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

        $count = DocumentCollection::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
