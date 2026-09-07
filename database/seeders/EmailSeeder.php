<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Email;
use App\Models\User;

class EmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For testing UC03, we need a user. Let's create one if none exist.
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Email::factory()->create([
            'user_id' => $user->id,
            'sender' => 'customer@example.com',
            'subject' => 'Question about my order',
            'content' => "Hello,\n\nI would like to know when my order #12345 will arrive. It has been a week since I placed it.\n\nThank you,\nCustomer",
            'category' => 'important',
            'received_at' => now(),
        ]);
        
        Email::factory()->count(5)->create(['user_id' => $user->id]);
    }
}
