<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Message\Response;

use Losingbattle\RocketMqHttp\Constants;
use Hyperf\Utils\Collection;

class ConsumeMessageProperties extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    public function getMessageKey()
    {
        $this->get(Constants::MESSAGE_PROPERTIES_MSG_KEY);
    }

    public function getShardingKey()
    {
        return $this->get(Constants::MESSAGE_PROPERTIES_SHARDING,'');
    }

    public function getTransCheckImmunityTime(): int
    {
        return (int) $this->get(Constants::MESSAGE_PROPERTIES_TRANS_CHECK_KEY, 0);
    }

    public function getStartDeliveryTime(): int
    {
        return (int) $this->get(Constants::MESSAGE_PROPERTIES_TIMER_KEY, 0);
    }
}
