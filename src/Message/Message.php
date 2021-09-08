<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

use Losingbattle\RocketMqHttp\Exception\MessageException;

abstract class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $topic = '';

    /**
     * @var array|string
     */
    protected $tag = '';

    /**
     * @var null|string
     */
    protected $instanceId;

    /**
     * @var null|string
     */
    protected $groupId;

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setInstanceId(?string $instaceId): self
    {
        $this->instanceId = $instaceId;
        return  $this;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }

    public function setGroupId(?string $groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getGroupId(): ?string
    {
        return  $this->groupId;
    }

    public function serialize(): array
    {
        throw new MessageException('You have to overwrite serialize() method.');
    }

    public function unserialize(string $data)
    {
        throw new MessageException('You have to overwrite unserialize() method.');
    }
}
