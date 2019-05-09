# application part of state-monitor

[![Packagist](https://img.shields.io/github/release/orinokko/state-monitor.svg)](https://packagist.org/packages/orinokko/state-monitor)
[![build status](https://circleci.com/gh/orinokko/state-monitor/tree/master.svg?style=svg&circle-token=834d362e516162f821fa93927da3dee174120ed0)](https://circleci.com/gh/orinokko/state-monitor/tree/master)

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
Whether or not to use mailing
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
Also the existence of tables "errors", "events", "checks" will be checked. If they are not there they will be created.



## Advices
### If the site package is used in different applications

Ensure that real data is listed in APP_NAME and APP_URL. They are can be used to find the sender.

## Usage
### Exceptions monitoring
Exceptions automatically will be caught on web and api middleware groups.
Also exist middleware for custom routes and other:
```php
->middleware('state-monitor-errorss')
```
### Events monitoring
later
### Database monitoring
later
## Contribution
<a href="https://www.buymeacoffee.com/ZArpFcduz" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: auto !important;width: auto !important;" ></a>
