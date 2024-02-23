<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;


class Option
{
    private $accessKeyId = '';

    private $accessKeySecret = '';

    private $instanceId = null;

    /**
     * @return string
     */
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }

    /**
     * @param string $accessKeyId
     */
    public function setAccessKeyId(string $accessKeyId)
    {
        $this->accessKeyId = $accessKeyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessKeySecret(): string
    {
        return $this->accessKeySecret;
    }

    /**
     * @param string $accessKeySecret
     */
    public function setAccessKeySecret(string $accessKeySecret)
    {
        $this->accessKeySecret = $accessKeySecret;
        return $this;
    }

    /**
     * @return null
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param null $instanceId
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
        return $this;
    }
}
