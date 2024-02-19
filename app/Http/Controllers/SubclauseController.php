<?php

namespace App\Http\Controllers;

use App\Models\Subclause;
use Illuminate\Http\Request;

class SubclauseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:subclause list', ['only' => ['index', 'show']]);
        $this->middleware('can:subclause create', ['only' => ['create', 'store']]);
        $this->middleware('can:subclause edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:subclause delete', ['only' => ['destroy']]);
    }

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subclause  $subclause
     * @return \Illuminate\Http\Response
     */
    public function show(Subclause $subclause)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subclause  $subclause
     * @return \Illuminate\Http\Response
     */
    public function edit(Subclause $subclause)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subclause  $subclause
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subclause $subclause)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subclause  $subclause
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subclause $subclause)
    {
        //
    }
}
