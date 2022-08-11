<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
 *--------------------------------------------------------------------------
 * Set The Default Timezone
 *--------------------------------------------------------------------------
 *
 * Here we will set the default timezone for PHP. PHP is notoriously mean
 * if the timezone is not explicitly set. This will be used by each of
 * the PHP date and date-time functions throughout the application.
 *
 */

date_default_timezone_set('UTC');

setlocale(LC_ALL, 'C.UTF-8');

// $env = \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$env = \Dotenv\Dotenv::createMutable(dirname(__DIR__))->safeLoad();
// dump($env);

// var_dump($_ENV);
// echo getenv('WECHAT_APP_ID');
