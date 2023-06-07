<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StatusBackToRetention extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:statusBackToNormal';

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
        $threeDaysAgo = Carbon::now()->subDays(3)->toDateString();
      
            
            DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            ->whereDate('leads.updated_at', $threeDaysAgo)
                ->update(['lead_type' => 'Retention']);
    }
}
