<?php

declare(strict_types=1);

return [
    'host' => env('ROCKETMQ_HTTP_HOST'),
    'access_key_id' => env('ROCKETMQ_HTTP_ACCESS_KEY_ID'),
    'access_key_secret' => env('ROCKET_MQ_HTTP_ACCESS_KEY_SECRET'),
    'instance_id' => env('ROCKET_MQ_HTTP_INSTANCE_ID'),
    'concurrent' => [
        'limit' => 15,
    ],
];
