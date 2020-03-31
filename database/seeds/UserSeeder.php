<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        DB::table('user_roles')->truncate();
        DB::table('roles')->truncate();

        // roles
        DB::table('roles')->insert([
            ['name' => 'client'],
            ['name' => 'manager'],
        ]);

        // manager
        $user = User::create([
            'name' => 'agilo manager',
            'email' => env('MANAGER_EMAIL', 'manager@agilo.test'),
            'password' => Hash::make(env('MANAGER_PASSWORD', 'dvorak')),
        ]);

        // role binding
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => 2,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
