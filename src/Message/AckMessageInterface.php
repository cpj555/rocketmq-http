<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

interface AckMessageInterface extends MessageInterface
{
    public function setReceiptHandle(string $receiptHandle);

    public function getReceiptHandle();

    public function setTrans(string $trans);

    public function getTrans(): ?string;
}
