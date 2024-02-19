<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\DashboardPost;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardPostController extends Controller
{
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
        return Inertia::render(
            'DashboardPosts/Create',
            [
                'types' => [
                    'news',
                    'announcement'
                ],
                'statuses' => [
                    'publish',
                    'draft'
                ]
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
            'title' => [
                'required', 'string', 'max:255'
            ],
            'content' => [
                'required'
            ]
        ]);

        $request['user_id'] = auth()->user()->id;

        DashboardPost::create($request->all());

        return redirect()->route('admin.news_announcements')->with('message', 'News and Announcement Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DashboardPost  $dashboardPost
     * @return \Illuminate\Http\Response
     */
    public function show(DashboardPost $dashboardPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DashboardPost  $dashboardPost
     * @return \Illuminate\Http\Response
     */
    public function edit(DashboardPost $dashboardPost)
    {
        return Inertia::render(
            'DashboardPosts/Edit',
            [
                'post' => $dashboardPost,
                'types' => [
                    'news',
                    'announcement'
                ],
                'statuses' => [
                    'publish',
                    'draft'
                ]
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DashboardPost  $dashboardPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DashboardPost $dashboardPost)
    {
        $request->validate([
            'title' => [
                'required', 'string', 'max:255'
            ],
            'content' => [
                'required'
            ]
        ]);

        $dashboardPost->title = $request->input('title');
        $dashboardPost->content = $request->input('content');
        $dashboardPost->type = $request->input('type');
        $dashboardPost->status = $request->input('status');

        $dashboardPost->save();

        return redirect()->route('admin.news_announcements')->with('message', 'News and Announcement Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DashboardPost  $dashboardPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(DashboardPost $dashboardPost)
    {
        $dashboardPost->delete();
        sleep(1);

        return redirect()->route('admin.news_announcements')->with('message', 'Post Delete Successfully');
    }
}
