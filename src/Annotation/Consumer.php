<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Consumer extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $topic = '';
    
    /**
     * @var string
     */
    public $groupId = '';

    /**
     * @var string
     */
    public $name = 'Rocket-Http-Consumer';

    /**
     * @var int
     */
    public $nums = 1;

    /**
     * @var null|bool
     */
    public $enable;

    /**
     * @var int
     */
    public $maxConsumption = 0;

    /**
     * @var int
     */
    public $numOfMessages = 1;

    /**
     * @var int
     */
    public $waitSeconds = 5;

    /**
     * @var bool 
     */
    public $halfTrans = false;

    /**
     * @var bool 
     */
    public $orderly = false;
}
