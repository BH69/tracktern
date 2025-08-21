<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user roles in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::all();
        
        $this->info('Current users in the database:');
        $this->table(
            ['ID', 'Name', 'Email', 'Role'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role ?? 'No role set'
                ];
            })
        );
        
        $this->info('Total users: ' . $users->count());
        $this->info('Admin users: ' . $users->where('role', 'admin')->count());
        $this->info('Student users: ' . $users->where('role', 'student')->count());
    }
}
