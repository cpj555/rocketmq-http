<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS)]
class Producer extends AbstractAnnotation
{

    public function __construct(
        public ?string $instaceId = null,
        public ?string $groupId = null,
        public string  $topic = '',
        public string  $tag = '',
        public int     $delayTtl = 0,
        public int     $transctionCheckTtl = 0,
    )
    {
    }

}
