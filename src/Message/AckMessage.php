<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

use Losingbattle\RocketMqHttp\Exception\MqException;

class AckMessage extends Message implements AckMessageInterface
{
    /**
     * @var string
     */
    protected $receiptHandle;

    /**
     * @var string
     */
    protected $trans;

    public function getTag()
    {
        throw new MqException('redundancy');
    }

    public function setTag($tag)
    {
        throw new MqException('redundancy');
    }
    
    public function setReceiptHandle(string $receiptHandle)
    {
        $this->receiptHandle = $receiptHandle;
        return $this;
    }

    public function getReceiptHandle()
    {
        return $this->receiptHandle;
    }

    public function serialize(): array
    {
        $data['ReceiptHandle'] = $this->getReceiptHandle();

        return $data;
    }

    public function setTrans(string $trans)
    {
        $this->trans = $trans;
        return $this;
    }

    public function getTrans():?string 
    {
        return $this->trans;
    }
}
