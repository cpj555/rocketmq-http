<?php

declare(strict_types=1);


namespace Example\Consumer\MessageData;

use Hyperf\Utils\Collection;

class OrderCloseData extends Collection
{
    public function getOrderNo()
    {
        return $this->get('order_no');
    }
}
