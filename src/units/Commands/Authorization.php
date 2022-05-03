<?php

namespace Idopin\ApiSupport\Commands;

use Illuminate\Console\Command;

class Authorization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorization:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description
    = 'Run the commands necessary to prepare Passport for use, and create a client of type password for issuing access tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('passport:install');
        $this->call('passport:client', [
            '--password' => true,
            '--name' => 'password_client',
            '--user_id' => null,
            '--provider' => 'users'
        ]);
        return 0;
    }
}
