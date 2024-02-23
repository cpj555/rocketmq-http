<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message\Response;

use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;

class ConsumeMessageResponse extends ResponseMessage
{
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->setProperties(Arr::get($items, 'Properties', ''));
    }
    
    public static function make($items = []): ConsumeMessageResponse
    {
        return parent::make($items); 
    }

    public function getMessageBody(): string
    {
        return $this->get('MessageBody', '');
    }

    public function getReceiptHandle(): string
    {
        return $this->get('ReceiptHandle', '');
    }

    /**
     * 1608025039444.
     */
    public function getPublishTime(): string
    {
        return $this->get('PublishTime', '');
    }

    public function getFirstConsumeTime(): int
    {
        return $this->get('FirstConsumeTime', '');
    }

    public function getNextConsumeTime(): string
    {
        return $this->get('NextConsumeTime', '');
    }

    public function getConsumedTimes()
    {
        return $this->get('ConsumedTimes', 0);
    }

    public function getMessageTag(): string
    {
        return $this->get('MessageTag', '');
    }

    public function setProperties(string $properties)
    {
        $propertiesArr = [];
        $res = explode('|', $properties);

        foreach ($res as $k) {
            $kAndV = explode(':', $k);
            if (sizeof($kAndV) == 2) {
                $propertiesArr[$kAndV[0]] = $kAndV[1];
            }
        }
        $this->put('Properties', ConsumeMessageProperties::make($propertiesArr));
    }

    public function getProperties(): ConsumeMessageProperties
    {
        return $this->get('Properties');
    }
}
