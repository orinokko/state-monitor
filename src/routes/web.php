<?php

Route::get('state-monitor-info', 'Orinoko\StateMonitor\Http\MonitorController@index');
Route::post('monitorAddEvent', 'Orinoko\StateMonitor\Http\MonitorController@monitorAddEvent');