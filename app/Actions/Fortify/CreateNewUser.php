<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use DB;
use Illuminate\Validation\Rules\Password;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'telephone' => $input['phone'],
            'role' => $input['role']
        ]);

        if ($input['role'] == "Admin"){
            $role = 1;
        } elseif ($input['role'] == "Industrial Officer"){
            $role = 2;
        } else {
            $role = 3;
        }

       //Insert model_has_roles
        DB::table('model_has_roles')->insert(
        ['role_id' => $role,
         'model_type' => "App\Models\User",
         'model_id' => $user->id]);

        return $user;
        
    }
}
