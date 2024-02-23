<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class Consumer extends AbstractAnnotation
{

    public function __construct(
        public string $topic = '',
        public string $groupId = '',
        public string $name = 'Rocket-Http-Consumer',
        public int $nums = 1,
        public ?bool $enable = null,
        public int $maxConsumption = 1,
        public int $numOfMessages = 1,
        public int $waitSeconds = 5,
        public bool $halfTrans = false,
        public bool $orderly =false
    )
    {

    }
}
