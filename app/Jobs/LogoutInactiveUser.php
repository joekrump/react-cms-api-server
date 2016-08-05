<?php

namespace App\Jobs;


use App\User;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogoutInactiveUser extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    
    /**
     * Create a new job instance.
     *
     * @param  $user - The user that should be logged out
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Log out the user by setting `logged_in` to false;
     *
     * @return void
     */
    public function handle()
    {
        $this->user->logged_in = false;
        $this->user->save();
    }
}
