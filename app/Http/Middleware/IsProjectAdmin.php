<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Models\Project;

class IsProjectAdmin
{
    public function handle($request, Closure $next)
    {
        $project = $request->route('project');
        if (is_string($project)) {
            $project = Project::find($project);
        }

        if (!$project) {
            abort(404, 'project not found.');
        }

        if (Auth::user()->hasRole('Admin') || $project->author == Auth::user()) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
