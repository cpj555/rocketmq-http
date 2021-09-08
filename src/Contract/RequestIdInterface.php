<?php


namespace Losingbattle\RocketMqHttp\Contract;


interface RequestIdInterface
{
    public function generate(): string;
}