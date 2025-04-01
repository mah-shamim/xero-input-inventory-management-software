<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class LDTestRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ld:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Browser Test Register User';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->call('test', ['filter'=>'RegistrationTest']);
    }
}
