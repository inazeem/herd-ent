<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Administrator (formerly Super Admin)
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'administrator@entclinic.com',
            'password' => Hash::make('password'),
        ])->assignRole('administrator');

        // Create FrontDesk User
        User::factory()->create([
            'name' => 'Front Desk',
            'email' => 'frontdesk@entclinic.com',
            'password' => Hash::make('password'),
        ])->assignRole('frontdesk');

        // Create Clinician User
        User::factory()->create([
            'name' => 'Dr. Smith',
            'email' => 'clinician@entclinic.com',
            'password' => Hash::make('password'),
        ])->assignRole('clinician');

        // Create Biller User
        User::factory()->create([
            'name' => 'Billing Staff',
            'email' => 'biller@entclinic.com',
            'password' => Hash::make('password'),
        ])->assignRole('biller');

        // Create additional clinicians
        User::factory()->count(2)->create()->each(function ($user) {
            $user->name = 'Dr. ' . $user->name;
            $user->save();
            $user->assignRole('clinician');
        });

        // Create additional frontdesk staff
        User::factory()->count(1)->create()->each(function ($user) {
            $user->assignRole('frontdesk');
        });
    }
}
