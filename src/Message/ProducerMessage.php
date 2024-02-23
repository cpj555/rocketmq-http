<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

use Losingbattle\RocketMqHttp\Constants;
use Losingbattle\RocketMqHttp\Contract\PackerInterface;
use Losingbattle\RocketmqHttp\Exception\MqException;
use Hyperf\Context\ApplicationContext;

abstract class ProducerMessage extends Message implements ProducerMessageInterface
{
    /**
     * @var array
     */
    protected $messageBody = [];

    /**
     * @var string
     */
    protected $tag = '';

    /**
     * @var int
     */
    protected $delayTtl = 0;

    protected $transctionCheckTtl = 0;

    protected $shardingKey = '';
    
    
    /**
     * @var array
     */
    protected $properties
        = [
        ];

    public function setTag($tag)
    {
        $this->tag = $tag;
        return  $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties($key, $value): self
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function setMessageBody($key, $value): self
    {
        if (is_null($key)) {
            $this->messageBody[] = $value;
        } else {
            $this->messageBody[$key] = $value;
        }
        return $this;
    }

    public function getMessageBody(): array
    {
        return $this->messageBody;
    }

    public function setDelayTtl(int $delayTtl)
    {
        if($delayTtl <= 0){
            throw new MqException('__STARTDELIVERTIME must be > 0.');
        }
        $this->delayTtl = $delayTtl;
        return $this;
    }

    public function getDelayTtl(): int
    {
        return $this->delayTtl;
    }

    public function setTransctionCheckTtl(int $transctionCheckTtl)
    {
        if ($transctionCheckTtl < 10 || $transctionCheckTtl > 300) {
            throw new MqException('__TransCheckT must be in 10~300.');
        }
        $this->transctionCheckTtl = $transctionCheckTtl;
        return $this;
    }

    public function getTransctionCheckTtl(): int
    {
        return $this->transctionCheckTtl;
    }

    public function setShardingKey(string $shardingKey)
    {
        $this->shardingKey = $shardingKey;
        return $this;
    }

    public function getShardingKey(): string
    {
        return $this->shardingKey;
    }

    public function payload(): array
    {
        return $this->serialize();
    }

    public function serialize(): array
    {
        $data = [];

        if (! $this->getMessageBody()) {
            throw new MqException('messageBody is empty');
        }
        $packer = ApplicationContext::getContainer()->get(PackerInterface::class);

        $data['MessageBody'] = $packer->pack($this->messageBody);

        if ($this->getTag()) {
            $data['MessageTag'] = $this->getTag();
        }

        if ($this->getDelayTtl()) {
            $this->setProperties(Constants::MESSAGE_PROPERTIES_TIMER_KEY, (time() + $this->getDelayTtl()) * 1000);
        }

        if ($this->getTransctionCheckTtl()) {
            $this->setProperties(Constants::MESSAGE_PROPERTIES_TRANS_CHECK_KEY, $this->getTransctionCheckTtl());
        }

        if($this->getShardingKey()){
            $this->setProperties(Constants::MESSAGE_PROPERTIES_SHARDING,$this->getShardingKey());
        }

        if ($this->getProperties()) {
            $data['Properties'] = implode('|', array_map(function ($v, $k) {
                return $k . ':' . $v;
            }, $this->getProperties(), array_keys($this->getProperties())));
        }

        return $data;
    }
}
