#errors
```php
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
```

# validations
```php
    /**
     * Validate request
     * @param  string $type - request direction (incoming|outgoing)
     * @param  string $url - url of the request
     * @param  string $method - method od the request
     * @param  array $params - params of the request, array(key=>value,key2=>value2)
     * @return bool
     */
    public function validateRequest($type,$url,$method,$params=[])
```

# validations
```php
    /**
     * Store custom event
     * @param  string $message - event description
     * @param  string $url - url of the request
     * @param  string $method - method od the request
     * @param  array $params - params of the request, array(key=>value,key2=>value2)
     * @return array
     */
    public function storeEvent($message,$url='',$method='',$params=[])
```

# queries (disabled by default)
```php
    /**
     * Store query to log
     * @param  string $query - sql query
     * @param  array $bindings - params of the query
     * @param  string $query_time - time for execution
     * @return array
     */
    public function storeQuery($query,$bindings,$query_time)
```