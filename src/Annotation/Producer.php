<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Producer extends AbstractAnnotation
{
    public $instaceId = null;
    
    public $groupId = null;
    /**
     * @var string
     */
    public $topic = '';

    public $tag = '';
    
    public $delayTtl = 0;

    public $transctionCheckTtl = 0;
}
