<?php

declare(strict_types=1);


use Hyperf\Collection\Collection;

class OrderCloseData extends Collection
{
    public function getOrderNo()
    {
        return $this->get('order_no');
    }
}
