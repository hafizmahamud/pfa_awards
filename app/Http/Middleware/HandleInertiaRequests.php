<?php

namespace App\Http\Middleware;

use DateTimeZone;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'global' => [
                'logo' => Storage::url('images\logo.png'),
                'message' => session('message'),
                'greeting' => 'Good' . $this->getGreetingTime(),
            ],
            'auth' => [
                'admin' => $request->user() ? $request->user()->can('user list') : null,
                'name'  => Auth::user() ? Auth::user()->name : 'Guest'
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ]
        ]);
    }

    /**
     * Determine the greeting time.
     *
     * @return string
     */
    private function getGreetingTime(): string
    {
        $date = new \DateTime('now', new DateTimeZone('Australia/Melbourne'));
        $hour = $date->format('H');

        // dd($hour >= 20 || $hour <= 5);
        if ($hour >= 6 && $hour <= 11) {
            return ' Morning';
        } elseif ($hour >= 12 && $hour <= 14) {
            return ' Afternoon';
        } elseif ($hour >= 15 && $hour <= 19) {
            return ' Evening';
        } elseif ($hour >= 20 || $hour <= 5) {
            return ' Night';
        }
    }
}
