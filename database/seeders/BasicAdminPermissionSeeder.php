<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BasicAdminPermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        $permissions = [
            'permission list',
            'permission create',
            'permission edit',
            'permission delete',
            'role list',
            'role create',
            'role edit',
            'role delete',
            'user list',
            'user create',
            'user edit',
            'user delete',
            'post list',
            'post create',
            'post edit',
            'post delete',
            'project list',
            'project create',
            'project edit',
            'project delete',
            'award list',
            'award create',
            'award edit',
            'award delete',
            'jurisdiction list',
            'jurisdiction create',
            'jurisdiction edit',
            'jurisdiction delete',
            'document list',
            'document create',
            'document edit',
            'document delete',
            'clause list',
            'clause create',
            'clause edit',
            'clause delete',
            'subclause list',
            'subclause create',
            'subclause edit',
            'subclause delete',
            'condition list',
            'condition create',
            'condition edit',
            'condition delete',
            'tag list',
            'tag create',
            'tag edit',
            'tag delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Admin']);
        foreach ($permissions as $permission) {
            $role1->givePermissionTo($permission);
        }

        $role2 = Role::create(['name' => 'Industrial Officer']);
        $role2->givePermissionTo('post list');
        $role2->givePermissionTo('project list');
        $role2->givePermissionTo('award list');
        $role2->givePermissionTo('jurisdiction list');
        $role2->givePermissionTo('document list');
        $role2->givePermissionTo('condition list');
        $role2->givePermissionTo('clause list');
        $role2->givePermissionTo('subclause list');
        $role2->givePermissionTo('tag list');


        $role3 = Role::create(['name' => 'Researcher']);
        $role3->givePermissionTo('post list');
        $role3->givePermissionTo('project list');
        $role3->givePermissionTo('award list');
        $role3->givePermissionTo('jurisdiction list');
        $role3->givePermissionTo('document list');
        $role3->givePermissionTo('condition list');
        $role3->givePermissionTo('clause list');
        $role3->givePermissionTo('subclause list');
        $role3->givePermissionTo('tag list');

        // gets all permissions via Gate::before rule; see AuthServiceProvider
        // create demo users
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'dev@osky.com.au',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'approved' => true
        ]);
        $user->assignRole($role1);
        $user = User::factory()->create([
            'name' => 'Industrial Officer',
            'email' => 'io@osky.com.au',
            'password' => Hash::make('password'),
            'role' => 'Industrial Officer',
            'approved' => true
        ]);
        $user->assignRole($role2);
        $user = User::factory()->create([
            'name' => 'Researcher',
            'email' => 'researcher@osky.com.au',
            'password' => Hash::make('password'),
            'role' => 'Researcher',
            'approved' => true
        ]);
        $user->assignRole($role3);
    }
}
