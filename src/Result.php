<?php

declare(strict_types=1);


namespace Losingbattle\RocketMqHttp;

class Result
{
    /**
     * Acknowledge the message.
     */
    const ACK = 'ack';

    /**
     * Unacknowledge the message.
     */
    const NACK = 'nack';


    /**
     * Reject the message and requeue it.
     */
    const REQUEUE = 'requeue';

    /**
     * Reject the message and drop it.
     */
    const DROP = 'drop';
}
