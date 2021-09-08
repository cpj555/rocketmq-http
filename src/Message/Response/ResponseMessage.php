<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message\Response;

use Hyperf\Utils\Collection;

abstract class ResponseMessage extends Collection implements ResponseMessageInterface
{
    public function getMessageId()
    {
        return $this->get('MessageId');
    }

    public function setMessageId(string $messageId)
    {
        $this->put('MessageId', $messageId);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageBodyMD5()
    {
        return $this->get('MessageBodyMD5');
    }

    /**
     * @return ResponseMessage
     */
    public function setMessageBodyMD5(string $messageBodyMD5)
    {
        return  $this->put('MessageBodyMD5', $messageBodyMD5);
    }

    /**
     * @return string
     */
    public function getReceiptHandle()
    {
        return $this->get('ReceiptHandle','');
    }

    public function setReceiptHandle(string $receiptHandle)
    {
        return $this->put('ReceiptHandle', $receiptHandle);
    }
}
