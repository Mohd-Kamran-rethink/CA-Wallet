<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MyCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:retentionleads';

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
        $leads = DB::table('leads')
            ->join('sources', 'leads.source_id', '=', 'sources.id')
            ->leftjoin('users', 'leads.agent_id', '=', 'users.id')
            ->where('leads.current_status', '=', 'Deposited')
            ->where('leads.lead_type', '=', 'Retention')
            ->whereDate('leads.updated_at', $threeDaysAgo)
            ->select('leads.*', 'sources.name as source_name', 'users.name as agent_name')
            ->get();

        $retentionAgents = User::where('agent_type', '=', 'Retention')->get();

        foreach ($leads as $lead) {
            $randomAgent = $retentionAgents->random();
            DB::table('leads')
                ->where('id', $lead->id)
                ->update(['agent_id' => $randomAgent->id]);
        }
    }
}
