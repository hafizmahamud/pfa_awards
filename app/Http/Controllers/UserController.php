<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Jurisdiction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\GeneratePassword;
use App\Mail\UserDetail;
use Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edituser', 'edituser']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);

        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = (new User)->newQuery();
        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $users->orderBy($attribute, $sort_order);
        } else {
            $users->latest();
        }
        $users = $users->paginate(10)->onEachSide(2)->appends(request()->query());

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
            'can' => [
                'create' => Auth::user()->can('user create'),
                'edit' => Auth::user()->can('user edit'),
                'delete' => Auth::user()->can('user delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return Inertia::render('Admin/User/Create', [
            'jurisdictions' => Jurisdiction::all()
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'telephone' => 'required',
            'mobile' => 'required',
            'jurisdiction' => 'required',
            'role' => 'required',
            'password' => ['required', Password::defaults()],
        ]);

        $data = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'telephone' => $request->input('telephone'),
            'mobile' => $request->input('mobile'),
            'jurisdiction' => $request->input('jurisdiction'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ]);

        //Insert model_has_roles
        if ($request->role == "Admin") {
            $role = 1;
        } elseif ($request->role == "Industrial Officer") {
            $role = 2;
        } else {
            $role = 3;
        }

        DB::table('model_has_roles')->insert(
            [
                'role_id' => $role,
                'model_type' => "App\Models\User",
                'model_id' => $data->id
            ]
        );

        if($request->input('isSendEmail')){           
            Mail::to($data->email)->send(new UserDetail(
                [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'telephone' => $request->input('telephone'),
                    'mobile' => $request->input('mobile'),
                    'jurisdiction' => $request->input('jurisdiction'),
                    'password' => $request->input('password'),
                    'role' => $request->input('role')
                ]
            ));
        }

        return redirect()->route('user.index')->with('message', 'User Created Successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $award
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userdata = User::find($id);

        return Inertia::render('Admin/User/Edit', [
            'userdata' => $userdata,
            'roles' => Role::all(),
            'jurisdictions' => Jurisdiction::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $award
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            // 'telephone' => 'required',
            // 'mobile' => 'required',
            // 'jurisdiction' => 'required',
            'role' => 'required',
            //'password' => ['sometimes', Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $user->mobile = $request->mobile;
        $user->jurisdiction = $request->jurisdiction;
        $user->role = $request->role;
        //$user->password = Hash::make($request->password);
        $user->save();
        sleep(1);

        if ($request->role == "Admin") {
            $role = 1;
        } elseif ($request->role == "Industrial Officer") {
            $role = 2;
        } else {
            $role = 3;
        }

        //Update model_has_roles
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->update(['role_id' => $role]);

        if ($request->password != null) {
            $rPassword = str_random(8);
            $gPassword = Hash::make($rPassword);

            User::where('email', $user->email)->update(['password' => $gPassword, 'updated_at' => now()]);

            Mail::to($user->email)->send(new GeneratePassword($rPassword));
        }


        return redirect()->route('user.index')->with('message', 'User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $award
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('user.index')
            ->with('message', __('User Deleted Successfully'));
    }

    public function approve_user(User $user)
    {
        // dd($user);
        $user->approved = true;
        $user->save();

        return redirect()->route('user.index')->with('message', 'User has been approved.');
    }

    public function decline_user(User $user)
    {
        $user->approved = false;
        $user->save();

        return redirect()->route('user.index')->with('message', 'User has been declined.');
    }
}
