<?php

namespace Crater\Console\Commands;

use Crater\Models\AvalaraConfig;
use Illuminate\Console\Command;

class EncryptPasswordAvalaraConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avalara:encrypt-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'encrypt all passwords that are unencrypted de avalara config';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $avalaraConfigs = AvalaraConfig::all();
        foreach ($avalaraConfigs as $avalaraConfig) {
            $avalaraConfig->password = $avalaraConfig->password_decode;
            $avalaraConfig->save();
        }

        return self::SUCCESS;
    }
}
