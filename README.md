# storage part of state-monitor

[![Packagist](https://img.shields.io/packagist/v/orinokko/state-monitor.svg)](https://packagist.org/packages/orinokko/state-monitor)
[![build status](https://circleci.com/gh/orinokko/state-monitor/tree/master.svg?style=svg&circle-token=834d362e516162f821fa93927da3dee174120ed0)](https://circleci.com/gh/orinokko/state-monitor/tree/master)

## Installation

### 1. Require the Package

```bash
composer require orinokko/state-monitor
```

### 2. Add to .env
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
GOOGLE_CLOUD_PROJECT=[monitor-123456]
GOOGLE_APPLICATION_CREDENTIALS=[storage/monitor.json]
```

### 3. Run config test

```bash
php artisan monitor:check
```

You must get one of or both lines
```bash
Local email channel activated and recipient address provided.
BigQuery channel activated and connection settings provided.
```

## Usage

## Contribution
<a href="https://www.buymeacoffee.com/ZArpFcduz" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: auto !important;width: auto !important;" ></a>
