<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class DeactivateExpiredUsers extends Command
{
    protected $signature   = 'users:deactivate-expired';
    protected $description = 'Deactivate users whose access period has expired.';

    public function __construct(private UserService $userService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking for expired users...');
        $count = $this->userService->deactivateExpiredUsers();
        $this->info("Deactivated {$count} expired user(s).");
        return Command::SUCCESS;
    }
}
