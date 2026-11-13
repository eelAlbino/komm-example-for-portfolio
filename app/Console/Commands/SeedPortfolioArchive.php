<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedPortfolioArchive extends Command
{
    protected $signature = 'portfolio:seed-archive';
    protected $description = 'Make a synthetic comment attachment and issue a local JWT';

    public function handle(): int
    {
        if (!app()->environment('local')) {
            $this->error('The fixture command requires APP_ENV=local.');
            return self::FAILURE;
        }

        $user = User::factory()->create([
            'name' => 'Portfolio Reviewer',
            'email' => Str::uuid().'@example.test',
            'password' => Hash::make(Str::random(40)),
        ]);
        $project = Project::factory()->create(['created_by' => $user->id]);
        ProjectUser::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'project_role_id' => ProjectRole::where('slug', 'commenter')->firstOrFail()->id,
        ]);
        $comment = Comment::factory()->create([
            'created_by' => $user->id,
            'project_id' => $project->id,
            'is_private' => false,
        ]);
        $comment->uploadFile(UploadedFile::fake()->createWithContent('example.txt', 'Synthetic review attachment'));
        $token = Auth::guard('api')->login($user);

        $this->line('Comment ID '.$comment->id);
        $this->line('Bearer token '.$token);
        return self::SUCCESS;
    }
}
