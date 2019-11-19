<?php

namespace Orinoko\StateMonitor\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Google\Cloud\BigQuery\BigQueryClient;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\TestEmail;
use Orinoko\StateMonitor\Monitor;

class CleanAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:cleanall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean All';


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
        $logQueries = config('state-monitor.log-queries');
        $webMiddleware = config('state-monitor.use-middleware-web');
        $apiMiddleware = config('state-monitor.use-middleware-api');
        $this->info('Current settings:');
        $this->line('STATE_MONITOR_APP='.$appName);
        $this->line('STATE_MONITOR_LOCAL_EMAIL='.$useLocal);
        $this->line('STATE_MONITOR_ALERT_EMAIL='.$alertEmail);
        $this->line('STATE_MONITOR_BIGQUERY='.$useBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_CLOUD_PROJECT='.$projectBigQuery);
        $this->line('STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS='.$keyBigQuery);
        $this->line('STATE_MONITOR_LOG_QUERIES='.$logQueries);
        $this->line('STATE_MONITOR_MIDDLEWARE_WEB='.$webMiddleware);
        $this->line('STATE_MONITOR_MIDDLEWARE_API='.$apiMiddleware);

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
        }else{
            $this->info('Local email channel disabled.');
        }
        if($useBigQuery){
            if($projectBigQuery && $keyBigQuery) {
                //putenv('GOOGLE_CLOUD_PROJECT='.config('state-monitor.bigquery-project'));
                //putenv('GOOGLE_APPLICATION_CREDENTIALS='.base_path().config('state-monitor.bigquery-path'));
                try {
                    $bigQuery = new BigQueryClient([
                        'keyFilePath' => base_path().config('state-monitor.bigquery-path')
                    ]);
                } catch (DomainException $exception) {
                    $this->error($exception->getMessage());
                }
                $this->info('BigQuery channel activated and connection settings provided. Try configure the database...');
                // -----------------------------------------------
                // DATASETS
                // -----------------------------------------------
                $datasets = $bigQuery->datasets();
                $monitorDataset = null;
                foreach ($datasets as $dataset) {
                    if($dataset->id()=='monitor'){
                        $this->info('Dataset already exist.');
                        $dataset->delete();
                        $this->info('Dataset deleted.');
                    }
                }
                if(!$monitorDataset){
                    $monitorDataset = $bigQuery->createDataset('monitor');
                    $this->info('Dataset not found - created.');
                }
                // -----------------------------------------------
                // TABLES
                // -----------------------------------------------
                // errors
                $errorTable = null;
                $tables = $monitorDataset->tables();
                foreach ($tables as $table) {
                    if($table->id()=='errors'){
                        $this->info('Table for errors already exist.');
                        $errorTable = $table;
                    }
                }
                if(!$errorTable){
                    $errorTable = $monitorDataset->createTable('errors',['schema' => Monitor::$errorsSchema]);
                    $this->info('Table for errors not found - created.');
                }
                // checks
                $checksTable = null;
                $tables = $monitorDataset->tables();
                foreach ($tables as $table) {
                    if($table->id()=='checks'){
                        $this->info('Table for checks already exist.');
                        $checksTable = $table;
                    }
                }
                if(!$checksTable){
                    $checksTable = $monitorDataset->createTable('checks',['schema' => Monitor::$checksSchema]);
                    $this->info('Table for checks not found - created.');
                }
                // events
                $eventsTable = null;
                $tables = $monitorDataset->tables();
                foreach ($tables as $table) {
                    if($table->id()=='events'){
                        $this->info('Table for events already exist.');
                        $eventsTable = $table;
                    }
                }
                if(!$eventsTable){
                    $eventsTable = $monitorDataset->createTable('events',['schema' => Monitor::$eventsSchema]);
                    $this->info('Table for events not found - created.');
                }
                // events
                $queriesTable = null;
                $tables = $monitorDataset->tables();
                foreach ($tables as $table) {
                    if($table->id()=='queries'){
                        $this->info('Table for queries already exist.');
                        $queriesTable = $table;
                    }
                }
                if(!$queriesTable){
                    $queriesTable = $monitorDataset->createTable('queries',['schema' => Monitor::$queriesSchema]);
                    $this->info('Table for queries not found - created.');
                }
            }else {
                $this->info('BigQuery channel activated, but without connection settings.');
            }
        }else{
            $this->info('BigQuery channel disabled.');
        }
    }
}