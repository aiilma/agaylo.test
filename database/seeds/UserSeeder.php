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
    }
}
