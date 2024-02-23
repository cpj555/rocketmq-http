<?php
declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Contract;


interface RequestIdInterface
{
    public function generate(): string;
}