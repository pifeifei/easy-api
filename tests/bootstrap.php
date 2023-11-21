<?php

declare(strict_types=1);
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'C.UTF-8');
$env = Dotenv::createMutable(dirname(__DIR__))->safeLoad();
