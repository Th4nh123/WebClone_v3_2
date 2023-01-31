<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\ToolCloneController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ToolComand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $tool = new ToolCloneController();
        $tool->parseURL();
        Log::info('done');
        return 0;
    }
}
