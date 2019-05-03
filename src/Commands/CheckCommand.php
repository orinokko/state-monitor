<?php

namespace Orinoko\StateMonitor\Commands;

use Illuminate\Console\Command;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\TestEmail;

class CheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check settings';

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
     * @return mixed
     */
    public function handle()
    {
        $useLocal = config('state-monitor.use-local');
        $alertEmail = config('state-monitor.alert-email');
        $useBigQuery = config('state-monitor.use-bigquery');
        $projectBigQuery = config('state-monitor.bigquery-project');
        $keyBigQuery = config('state-monitor.bigquery-path');
        $this->info('Current settings:');
        $this->line('STATE_MONITOR_LOCAL_EMAIL='.$useLocal);
        $this->line('STATE_MONITOR_ALERT_EMAIL='.$alertEmail);
        $this->line('STATE_MONITOR_BIGQUERY='.$useBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_CLOUD_PROJECT='.$projectBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS='.$keyBigQuery);

        if(!$useLocal && !$useBigQuery){
            $this->error('All outgoing channels are turned off.');
        }
        if($useLocal){
            if(!$alertEmail){
                $this->error('Local email channel activated, but without recipient address.');
            }else{
                $this->info('Local email channel activated and recipient address provided.');
                Mail::to($alertEmail)
                    ->send(new TestEmail());
                $this->info('Test email sent to specified address.');
            }
        }
        if($useBigQuery){
            if($projectBigQuery && $keyBigQuery) {
                try {
                    $bigQuery = new BigQueryClient();
                } catch (DomainException $exception) {
                    $this->error($exception->getMessage());
                }
                $this->info('BigQuery channel activated and connection settings provided.');
            }else {
                $this->info('BigQuery channel activated, but without connection settings.');
            }
        }
    }
}