<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Util;

class Signature
{
    public static function generate(string $method, string $contentType, string $date, string $version, string $canonicalizedResource, string $accessKeySecret): string
    {
        $stringToSign = $method . "\n\n" . $contentType . "\n" . $date . "\n" . 'x-mq-version:' . $version . "\n" . $canonicalizedResource;
        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret, true));
    }
}
