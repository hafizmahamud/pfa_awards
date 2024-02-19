<?php

namespace App\Http\Controllers;

use App\Models\Jurisdiction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurisdictionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:jurisdiction list', ['only' => ['index', 'show']]);
        $this->middleware('can:jurisdiction create', ['only' => ['create', 'store']]);
        $this->middleware('can:jurisdiction edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:jurisdiction delete', ['only' => ['destroy']]);
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
     * @param  \App\Models\Jurisdiction  $jurisdiction
     * @return \Illuminate\Http\Response
     */
    public function show(Jurisdiction $jurisdiction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Jurisdiction  $jurisdiction
     * @return \Illuminate\Http\Response
     */
    public function edit(Jurisdiction $jurisdiction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jurisdiction  $jurisdiction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jurisdiction $jurisdiction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jurisdiction  $jurisdiction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jurisdiction $jurisdiction)
    {
        //
    }
}
