<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

use Losingbattle\RocketMqHttp\Contract\PackerInterface;
use Losingbattle\RocketMqHttp\Result;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerInterface;

abstract class ConsumerMessage extends Message implements ConsumerMessageInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;



    /**
     * @var array
     */
    protected $tag = [];

    /**
     * @var int
     */
    protected $waitSeconds;

    /**
     * @var int
     */
    protected $numOfMessages;

    /**
     * @var bool
     */
    protected $enable = true;

    /**
     * @var int
     */
    protected $maxConsumption = 0;

    protected $route = [];
    
    protected $halfTrans = false;

    protected $orderly = false;

    public function consumeMessage($messageBody)
    {
        return Result::ACK;
    }

    public function setGroupId(?string $groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getGroupId(): string
    {
        return  $this->groupId;
    }

    public function getConsumerTag(): string
    {
        return implode('||', (array) $this->getTag());
    }

    public function setTag($tag)
    {
        $this->tag[] = $tag;
        return  $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setNumOfMessages(int $numOfMessages)
    {
        $this->numOfMessages = $numOfMessages;
        return $this;
    }

    public function getNumOfMessages(): int
    {
        return  $this->numOfMessages;
    }

    public function setWaitSeconds(int $waitSeconds)
    {
        $this->waitSeconds = $waitSeconds;
        return $this;
    }

    public function getWaitSeconds(): int
    {
        return  $this->waitSeconds;
    }

    public function getMaxConsumption(): int
    {
        return  $this->maxConsumption;
    }

    public function setMaxConsumption(int $maxConsumption)
    {
        $this->maxConsumption = $maxConsumption;
        return $this;
    }

    public function isEnable(): bool
    {
        return  $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;
        return  $this;
    }

    public function registerRoute(string $tag, callable $callable)
    {
        if (! isset($this->route[$tag])) {
            $this->setTag($tag);
            $this->route[$tag] = $callable;
        }
    }

    public function getRoute()
    {
        return $this->route;
    }
    
    public function setHalfTrans(bool $halfTrans)
    {
        $this->halfTrans = $halfTrans;
        return $this;
    }
    
    public function getHalfTrans(): bool
    {
        return  $this->halfTrans;
    }
    
    public function getOrderly(): bool
    {
        return  $this->orderly;
    }
    
    public function setOrderly(bool $ordely)
    {
        $this->orderly = $ordely;
        return $this;
    }

    public function unserialize(string $data)
    {
        return ApplicationContext::getContainer()->get(PackerInterface::class)->unpack($data);
    }
}
