<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommentStatus;

class CommentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // To discuss
        CommentStatus::firstOrCreate([
            'name' => 'To discuss',
            'slug' => 'to-discuss',
            'color' => '#ffa200'
        ]);
        // In progress
        CommentStatus::firstOrCreate([
            'name' => 'In progress',
            'slug' => 'in-progress',
            'color' => '#7300ff'
        ]);
        // Rejected
        CommentStatus::firstOrCreate([
            'name' => 'Rejected',
            'slug' => 'rejected',
            'color' => '#e83055'
        ]);
        // Resolved
        CommentStatus::firstOrCreate([
            'name' => 'Resolved',
            'slug' => 'resolved',
            'color' => '#1ce97f'
        ]);
    }
}
