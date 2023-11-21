<?php

declare(strict_types=1);
use Pff\EasyApiTest\Feature\stubs\WechatClient;
use Pff\EasyApiTest\Feature\WechatTest;

include_once __DIR__ . '/../../bootstrap.php';

$config = (new WechatTest())->getConfig();

$client = new WechatClient($config['config']);

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    $client->bind();
} else {
    echo '其他自动响应';
}
