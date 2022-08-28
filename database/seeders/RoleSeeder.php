<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = Role::create(['name' => 'Admin']);
        $role2 = Role::create(['name' => 'Moderator']);
        $role3 = Role::create(['name' => 'Tatuador']);
        $role4 = Role::create(['name' => 'Normal']);

        //Roles
        Permission::create([
            'guard_name' => 'api',
            'name' => 'role.index',
            'description' => 'Show list roles'])->syncRoles([$role1]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'role.edit',
            'description' => 'Update roles'])->syncRoles([$role1]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'role.delete',
            'description' => 'Deleted permission role'])->syncRoles([$role1]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'role.create',
            'description' => 'Create role'])->syncRoles([$role1]);

        //Posts
        Permission::create([
            'guard_name' => 'api',
            'name' => 'posts.index',
            'description' => 'Show list roles'])->syncRoles([$role1,$role3,$role2,$role4]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'posts.edit',
            'description' => 'Update roles'])->syncRoles([$role3]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'posts.delete',
            'description' => 'Deleted permission role'])->syncRoles([$role1,$role3,$role2]);
        Permission::create([
            'guard_name' => 'api',
            'name' => 'posts.create',
            'description' => 'Create role'])->syncRoles([$role3]);



    }
}
