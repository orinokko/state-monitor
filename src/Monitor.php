<?php

namespace Orinoko\StateMonitor;

//use GuzzleHttp\RequestOptions;
//use Orinoko\Crawler\CrawlerObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\ErrorHandled;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

class Monitor
{
    /**
     * Schema for errors table.
     *
     * @var array
     */
    public static $errorsSchema = [
        'fields' => [
            [
                'name' => 'time',
                'type' => 'DATETIME',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'app_id',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'url',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'method',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'params',
                'type' => 'RECORD',
                'mode' => 'REPEATED',
                "fields"=> [
                    [ "name"=> "key", "type"=> "STRING"],
                    [ "name"=> "value", "type"=> "STRING"]
                ]
            ],
            [
                'name' => 'code',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'message',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'file',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'line',
                'type' => 'INTEGER',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'headers',
                'type' => 'RECORD',
                'mode' => 'REPEATED',
                "fields"=> [
                    [ "name"=> "key", "type"=> "STRING"],
                    [ "name"=> "value", "type"=> "STRING"]
                ]
            ],
            [
                'name' => 'trace',
                'type' => 'RECORD',
                'mode' => 'REPEATED',
                "fields"=> [
                    [ "name"=> "file", "type"=> "STRING"],
                    [ "name"=> "line", "type"=> "INTEGER"]
                ]
            ],
        ]
    ];

    public function checkFacade()
    {
        $result = 'facade run';
        return $result;
    }

    public function processRequest($request,$response)
    {
        // Same as getallheaders(), just with lowercase keys
        $headers = array_map(function($x){
            return $x[0];
        }, $request->headers->all());

        $url = $request->url();
        $params = $request->all();
        $method = $request->getMethod();
        $code = $response->exception->getCode();
        $message = $response->exception->getMessage();
        $file = $response->exception->getFile();
        $line = $response->exception->getLine();
        $trace = $response->exception->getTrace();
        $traceString = $response->exception->getTraceAsString();
        $time = Carbon::now()->toDateTimeString();
        $app = config('app');
        $app_id = config('state-monitor.app-name');

        if(config('state-monitor.use-local')){
            Mail::to(config('state-monitor.alert-email'))
                ->send(new ErrorHandled(compact('time','app','code','message','file','line','trace','traceString','headers','url','params','method')));
        }else{
            // send to external mailer
        }
        // save to storage
        if(config('state-monitor.use-bigquery')){
            $this->saveError($time,$app_id,$url,$method,$params,$code,$message,$file,$line,$headers,$trace);
        }
    }

    public function saveError($time,$app_id,$url,$method,$params,$code,$message,$file,$line,$headers,$trace)
    {
        // prepare data
        $data = [
            'time' => $time,
            'app_id' => $app_id,
            'url' => $url,
            'method' => $method,
            'params' => [],
            'code' => $code,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'headers' => [],
            'trace' => [],
        ];
        foreach ($params as $k=>$v){
            $data['params'][] = ['key'=>$k,'value'=>$v];
        }
        foreach ($headers as $k=>$v){
            $data['headers'][] = ['key'=>$k,'value'=>$v];
        }
        foreach ($trace as $k=>$v){
            $data['trace'][] = ['file'=>$v['file'],'line'=>$v['line']];
        }

        // client
        $bigQuery = new BigQueryClient();
        $dataset = $bigQuery->dataset('monitor');
        $table = $dataset->table('errors');

        $insertResponse = $table->insertRows([
            ['data' => $data],
            // additional rows can go here
        ]);
        if ($insertResponse->isSuccessful()) {
            return ['error'=>0];
        } else {
            foreach ($insertResponse->failedRows() as $row) {
                return ['error'=>1,'errors'=>$row['errors']];
            }
        }
    }
}
