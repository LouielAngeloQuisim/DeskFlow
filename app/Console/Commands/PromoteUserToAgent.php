<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAgent extends Command
{
    protected $signature = 'deskflow:make-agent {email : Email address of an existing account}';

    protected $description = 'Grant support-agent access to an existing DeskFlow account';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->components->error('No account exists for that email address.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => 'agent'])->save();
        $this->components->info("{$user->email} can now access the support queue.");

        return self::SUCCESS;
    }
}
