<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

            $user=User::create([
            'name' => 'Mahar Nisar',
            'email' => 'maharnisar@akhuwat.org.pk',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'image'=>'dummy.png',
            'remember_token' => Str::random(10),
            ]);
        $user->assignRole('super-admin');
    }
}
