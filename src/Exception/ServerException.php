<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Exception;

use GuzzleHttp\Exception\GuzzleException;

class ServerException extends \RuntimeException implements GuzzleException
{
}
