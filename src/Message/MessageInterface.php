<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message;

interface MessageInterface
{
    public function setTopic(string $topic);

    public function getTopic(): string;

    public function setTag($tag);

    public function getTag();

    public function setInstanceId(?string $instanceId);

    public function getInstanceId(): ?string;

    public function setGroupId(string $groupId);

    public function getGroupId(): ?string;

    /**
     * Serialize the message body to a string.
     */
    public function serialize(): array;

    /**
     * Unserialize the message body.
     */
    public function unserialize(string $data);
}
