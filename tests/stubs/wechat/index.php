<?php

declare(strict_types=1);

include_once __DIR__ . '/../../bootstrap.php';

$config = (new \Pff\EasyApiTest\Feature\WechatTest())->getConfig();

$client = new \Pff\EasyApiTest\Feature\stubs\WechatClient($config['config']);

if ('GET' === $_SERVER['REQUEST_METHOD']) {
    $client->bind();
} else {
    echo '其他自动响应';
}
