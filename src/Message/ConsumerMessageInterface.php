<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

interface ConsumerMessageInterface extends MessageInterface
{
    public function consumeMessage($messageBody);

    public function setNumOfMessages(int $numOfMessages);

    public function getNumOfMessages(): int;

    public function setWaitSeconds(int $waitSeconds);

    public function getWaitSeconds(): int;

    public function getConsumerTag(): string;

    public function getMaxConsumption(): int;

    public function setMaxConsumption(int $maxConsumption);

    public function isEnable(): bool;

    public function setEnable(bool $enable);

    public function registerRoute(string $tag, callable $callable);

    public function getRoute();

    public function setHalfTrans(bool $halfTrans);

    public function getHalfTrans(): bool;

    public function setOrderly(bool $ordely);

    public function getOrderly(): bool;
}
