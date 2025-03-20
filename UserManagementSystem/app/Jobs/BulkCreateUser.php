<?php

namespace App\Jobs;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class BulkCreateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $usersData;


    /**
     * Create a new job instance.
     */
    public function __construct($usersData)
    {
        $this->usersData = $usersData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->usersData as $userData){
            User::create([
                'name'=>$userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role']
            ]);
        }
    }
}
