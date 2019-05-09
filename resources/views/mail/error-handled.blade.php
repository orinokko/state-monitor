Time: {{ $data['time'] }} <br>
App Name: {{ $data['app']['name'] }} <br>
App Url: {{ $data['app']['url'] }} <br>
<hr>
Current Url: {{ $data['url'] }} <br>
Method: {{ $data['method'] }} <br>
@if($data['params'])
Params: <br>
@foreach($data['params'] as $k=>$v)
    {{ $k }}: {{ $v }} <br>
@endforeach
@endif
<hr>
Code: {{ $data['code'] }} <br>
Message: {{ $data['message'] }} <br>
File: {{ $data['file'] }} <br>
Line: {{ $data['line'] }} <br>
<hr>
<h3>Request Headers</h3>
@foreach($data['headers'] as $k=>$v)
    {{ $k }}: {{ $v }} <br>
@endforeach
<hr>
<h3>Trace</h3>
{!! nl2br($data['traceString']) !!}