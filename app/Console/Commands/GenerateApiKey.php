<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key {email : The email of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API key for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        // Generate a secure API key
        $apiKey = 'ek_' . Str::random(40);
        
        $user->update(['api_key' => $apiKey]);
        
        $this->info("API key generated successfully for {$email}:");
        $this->line($apiKey);
        $this->warn("Please save this API key securely. It will not be shown again.");
        
        return 0;
    }
}
