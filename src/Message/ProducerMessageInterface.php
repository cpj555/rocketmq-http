<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

interface ProducerMessageInterface extends MessageInterface
{
    public function setMessageBody($key, $value);

    public function getMessageBody(): array;

    public function payload();

    public function setProperties($key, $value);

    public function getProperties(): array;

    public function setDelayTtl(int $delayTtl);

    public function getDelayTtl(): int;

    public function setTransctionCheckTtl(int $transctionCheckTtl);

    public function getTransctionCheckTtl(): int;

    public function setShardingKey(string $shardingKey);

    public function getShardingKey(): string;
}
