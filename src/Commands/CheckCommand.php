<?php

namespace Orinoko\StateMonitor\Commands;

use Illuminate\Console\Command;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\TestEmail;
use Orinoko\StateMonitor\Monitor;

class CheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:install';

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
        $appName = config('state-monitor.app-name');
        $useLocal = config('state-monitor.use-local');
        $alertEmail = config('state-monitor.alert-email');
        $useBigQuery = config('state-monitor.use-bigquery');
        $projectBigQuery = config('state-monitor.bigquery-project');
        $keyBigQuery = config('state-monitor.bigquery-path');
        $this->info('Current settings:');
        $this->line('STATE_MONITOR_APP='.$appName);
        $this->line('STATE_MONITOR_LOCAL_EMAIL='.$useLocal);
        $this->line('STATE_MONITOR_ALERT_EMAIL='.$alertEmail);
        $this->line('STATE_MONITOR_BIGQUERY='.$useBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_CLOUD_PROJECT='.$projectBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS='.$keyBigQuery);

        if(!$appName){
            $this->error('You need to provide app identifier in STATE_MONITOR_APP.');
        }
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
                $this->info('BigQuery channel activated and connection settings provided. to configure the database...');
                // -----------------------------------------------
                // DATASETS
                // -----------------------------------------------
                $datasets = $bigQuery->datasets();
                $monitorDataset = null;
                foreach ($datasets as $dataset) {
                    if($dataset->id()=='monitor'){
                        $this->info('Dataset already exist.');
                        $monitorDataset = $dataset;
                    }
                }
                if(!$monitorDataset){
                    $monitorDataset = $bigQuery->createDataset('monitor');
                    $this->info('Dataset not found - created.');
                }
                // -----------------------------------------------
                // TABLES
                // -----------------------------------------------
                $errorTable = null;
                $tables = $monitorDataset->tables();
                foreach ($tables as $table) {
                    if($table->id()=='errors'){
                        $this->info('Table for errors already exist.');
                        $errorTable = $table;
                    }
                }
                if(!$errorTable){
                    //print_r(json_encode(Monitor::$errorsSchema));
                    $errorTable = $monitorDataset->createTable('errors',['schema' => Monitor::$errorsSchema]);
                    $this->info('Table for errors not found - created.');
                }
            }else {
                $this->info('BigQuery channel activated, but without connection settings.');
            }
        }
    }
}