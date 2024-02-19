<?php

namespace App\Http\Controllers;

use App\Models\Clause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClauseController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:clause list', ['only' => ['index', 'show']]);
        $this->middleware('can:clause create', ['only' => ['create', 'store']]);
        $this->middleware('can:clause edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:clause delete', ['only' => ['destroy']]);
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
     * @param  \App\Models\Clause  $clause
     * @return \Illuminate\Http\Response
     */
    public function show(Clause $clause)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Clause  $clause
     * @return \Illuminate\Http\Response
     */
    public function edit(Clause $clause)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Clause  $clause
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clause $clause)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Clause  $clause
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clause $clause)
    {
        //
    }
}
