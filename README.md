# application part of state-monitor

[![Packagist](https://img.shields.io/github/release/orinokko/state-monitor.svg)](https://packagist.org/packages/orinokko/state-monitor)
[![build status](https://circleci.com/gh/orinokko/state-monitor/tree/master.svg?style=svg&circle-token=834d362e516162f821fa93927da3dee174120ed0)](https://circleci.com/gh/orinokko/state-monitor/tree/master)

Collects data about exceptions, manual events and some logs. Supports writing to BigQuery. For further data analysis, you can use Google Data Studio or your own application.

For example:
![Errors](https://raw.githubusercontent.com/orinokko/state-monitor/work/docs/img/1.png)
## Installation

### 1. Require the Package

```bash
composer require orinokko/state-monitor
```

### 2. Add to .env
The service identifier of your application
```
STATE_MONITOR_APP=AppName
```
Whether or not to use mailing for errors
```
STATE_MONITOR_LOCAL_EMAIL=true
STATE_MONITOR_ALERT_EMAIL=mail@example.com
```
Whether or not to use BigQuery
```
STATE_MONITOR_BIGQUERY=true
```
Settings for BigQuery, more [details](https://github.com/googleapis/google-cloud-php/blob/master/AUTHENTICATION.md)
```
STATE_MONITOR_GOOGLE_CLOUD_PROJECT=[monitor-123456]
STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS=[storage/monitor.json]
```
Whether or not to log queries
```
STATE_MONITOR_LOG_QUERIES=false
```

### 3. Run config test and installation

```bash
php artisan monitor:install
```

You must get one of or both lines
```bash
Local email channel activated and recipient address provided.
BigQuery channel activated and connection settings provided.
```
In the process, additional actions will be performed:
1. If sending by email is activated, a test letter will be sent to the specified address (using application mail settings).
2. If saving in BigQuery is activated, the existence of the “monitor” data set will be checked. And it will be created if there is no such data set yet.
Also the existence of tables "errors", "events", "checks", "queries" will be checked. If they are not there they will be created.

Example output after installation:
```bash
public_html$ php artisan monitor:install
Current settings:
STATE_MONITOR_APP=TestAppName
STATE_MONITOR_LOCAL_EMAIL=
STATE_MONITOR_ALERT_EMAIL=mail@example.com
STATE_MONITOR_BIGQUERY=1
STATE_MONITOR_GOOGLE_CLOUD_PROJECT=monitor
STATE_MONITOR_GOOGLE_APPLICATION_CREDENTIALS=/storage/monitor.json
STATE_MONITOR_LOG_QUERIES=1
Local email channel disabled.
BigQuery channel activated and connection settings provided. Try configure the database...
Dataset already exist.
Table for errors already exist.
Table for checks already exist.
Table for events already exist.
Table for queries already exist.
```
### 4. Publish assets

```bash
php artisan vendor:publish --provider='Orinoko\StateMonitor\MonitorServiceProvider' --tag='public' --force
```

## Advices
### If the site package is used in different applications

Ensure that real data is listed in APP_NAME and APP_URL. They are can be used to find the sender.

## Usage
### Exceptions monitoring
Exceptions automatically will be caught on web and api middleware groups.
Also exist middleware for custom routes and other:
```php
->middleware('state-monitor-errors')
```
### Checks monitoring
Called manually
```php
Monitor::validateRequest($type,$url,$method,$params=[],$user='',$domain='')
```
### Events monitoring
Called manually
```php
Monitor::storeEvent($message,$priority=0,$url='',$method='',$params=[],$user='',$domain='')
```
### Database monitoring
If STATE_MONITOR_LOG_QUERIES is enabled, it will automatically log all queries to the appropriate table. 

### Front
After publishing assets add to html
```html
<script src="/vendor/state-monitor/js/monitor.js"></script>
```
Calling
```js
<script>
    if (typeof monitorAddEvent === "function")
        monitorAddEvent('Page http://phplaravel-135581-835906.cloudwaysapps.com loaded');
</script>
```
## Contribution
<a href="https://www.buymeacoffee.com/ZArpFcduz" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: auto !important;width: auto !important;" ></a>
