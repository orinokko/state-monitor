<?php

namespace Orinoko\StateMonitor;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Orinoko\StateMonitor\Mail\ErrorHandled;
use Google\Cloud\BigQuery\BigQueryClient;

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

    /**
     * Schema for checks table.
     *
     * @var array
     */
    public static $checksSchema = [
        'fields' => [
            [
                'name' => 'type',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'status',
                'type' => 'BOOLEAN',
                'mode' => 'REQUIRED'
            ],
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
        ]
    ];

    /**
     * Schema for events table.
     *
     * @var array
     */
    public static $eventsSchema = [
        'fields' => [
            [
                'name' => 'message',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
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
                'type' => 'STRING'
            ],
            [
                'name' => 'method',
                'type' => 'STRING'
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
        ]
    ];

    /**
     * Schema for events table.
     *
     * @var array
     */
    public static $queriesSchema = [
        'fields' => [
            [
                'name' => 'query',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'query_time',
                'type' => 'STRING',
                'mode' => 'REQUIRED'
            ],
            [
                'name' => 'bindings',
                'type' => 'RECORD',
                'mode' => 'REPEATED',
                "fields"=> [
                    [ "name"=> "key", "type"=> "STRING"],
                    [ "name"=> "value", "type"=> "STRING"]
                ]
            ],
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
                'type' => 'STRING'
            ],
            [
                'name' => 'method',
                'type' => 'STRING'
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
        ]
    ];

    /**
     * Sample func
     *
     * @return string
     */
    public function checkFacade()
    {
        $result = 'facade exist';
        return $result;
    }

    /**
     * Save data to BigQuery
     * @param  string $dataset_id
     * @param  string $table_id
     * @param  array $data
     * @return array
     */
    public static function storeData($dataset_id,$table_id,$data)
    {
        $bigQuery = new BigQueryClient();
        $dataset = $bigQuery->dataset($dataset_id);
        $table = $dataset->table($table_id);

        $insertResponse = $table->insertRows([
            ['data' => $data],
            // additional rows can go here
        ]);
        if ($insertResponse->isSuccessful()) {
            return ['error'=>0];
        } else {
            return ['error'=>1,'errors'=>$insertResponse->failedRows()];
            /*foreach ($insertResponse->failedRows() as $row) {
                return ['error'=>1,'errors'=>$row['errors']];
            }*/
        }
    }

    /**
     * Prepare request info (for errors)
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Http\Response $response
     * @return void
     */
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

    /**
     * Store request info (for errors)
     * @param  string $time - Current time DATETIME
     * @param  string $app_id - id of current installation
     * @param  string $url - url where was this error
     * @param  string $method - request method
     * @param  array $params - request params, array(key=>value,key2=>value2)
     * @param  string $code - response code
     * @param  string $message - error message
     * @param  string $file - filepath where was an error
     * @param  string $line - line of file where was an error
     * @param  array $headers - request headers, array(key=>value,key2=>value2)
     * @param  array $trace - error trace, array([file=>path,line=>value])
     * @return array
     */
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

        return self::storeData('monitor','errors',$data);
    }

    /**
     * Validate request
     * @param  string $type - request direction (incoming|outgoing)
     * @param  string $url - url of the request
     * @param  string $method - method od the request
     * @param  array $params - params of the request, array(key=>value,key2=>value2)
     * @return bool
     */
    public function validateRequest($type,$url,$method,$params=[])
    {
        $status = true;
        // ToDo: some validation logic for status
        // --
        // store data
        $time = Carbon::now()->toDateTimeString();
        $app_id = config('state-monitor.app-name');
        $data = [
            'time' => $time,
            'app_id' => $app_id,
            'url' => $url,
            'method' => $method,
            'params' => [],
            'type' => $type,
            'status' => $status,
        ];
        foreach ($params as $k=>$v){
            $data['params'][] = ['key'=>$k,'value'=>$v];
        }
        $result = self::storeData('monitor','checks',$data);
        return $status;
    }

    /**
     * Store custom event
     * @param  string $message - event description
     * @param  string $url - url of the request
     * @param  string $method - method od the request
     * @param  array $params - params of the request, array(key=>value,key2=>value2)
     * @return array
     */
    public function storeEvent($message,$url='',$method='',$params=[])
    {
        // store data
        $time = Carbon::now()->toDateTimeString();
        $app_id = config('state-monitor.app-name');
        $data = [
            'time' => $time,
            'app_id' => $app_id,
            'url' => $url,
            'method' => $method,
            'params' => [],
            'message' => $message,
        ];
        foreach ($params as $k=>$v){
            $data['params'][] = ['key'=>$k,'value'=>$v];
        }
        return self::storeData('monitor','events',$data);
    }

    /**
     * Store query to log
     * @param  string $query - sql query
     * @param  array $bindings - params of the query
     * @param  string $query_time - time for execution
     * @return array
     */
    public function storeQuery($query,$bindings,$query_time)
    {
        // store data
        $time = Carbon::now()->toDateTimeString();
        $app_id = config('state-monitor.app-name');
        $url = request()->url();
        $params = request()->all();
        $method = request()->getMethod();
        $data = [
            'time' => $time,
            'app_id' => $app_id,
            'url' => $url,
            'method' => $method,
            'params' => [],
            'query' => $query,
            'bindings' => [],
            'query_time' => $query_time,
        ];
        foreach ($params as $k=>$v){
            $data['params'][] = ['key'=>$k,'value'=>$v];
        }
        foreach ($bindings as $k=>$v){
            $data['params'][] = ['key'=>$k,'value'=>$v];
        }
        return self::storeData('monitor','queries',$data);
    }
}
