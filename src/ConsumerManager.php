<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Annotation\Consumer as ConsumerAnnotation;
use Losingbattle\RocketMqHttp\Message\ConsumerMessageInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessManager;
use Psr\Container\ContainerInterface;
use function Hyperf\Support\make;

class ConsumerManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $classes = AnnotationCollector::getClassesByAnnotation(ConsumerAnnotation::class);
        /**
         * @var string
         * @var ConsumerAnnotation $annotation
         */
        foreach ($classes as $class => $annotation) {
            $instance = make($class);
            if (! $instance instanceof ConsumerMessageInterface) {
                continue;
            }

            $annotation->topic && $instance->setTopic($annotation->topic);
            $annotation->groupId && $instance->setGroupId($annotation->groupId);
            is_int($annotation->numOfMessages) && $instance->setNumOfMessages($annotation->numOfMessages);
            is_int($annotation->waitSeconds) && $instance->setWaitSeconds($annotation->waitSeconds);
            $annotation->halfTrans && $instance->setHalfTrans($annotation->halfTrans);
            $annotation->orderly && $instance->setOrderly($annotation->orderly);
            ! is_null($annotation->enable) && $instance->setEnable($annotation->enable);
            property_exists($instance, 'container') && $instance->container = $this->container;
            $annotation->maxConsumption && $instance->setMaxConsumption($annotation->maxConsumption);
            $nums = $annotation->nums;
            $process = $this->createProcess($instance);
            $process->nums = (int) $nums;
            $process->name = $annotation->name . '-' . $instance->getGroupId();
            ProcessManager::register($process);
        }
    }

    private function createProcess(ConsumerMessageInterface $consumerMessage): AbstractProcess
    {
        return new class($this->container, $consumerMessage) extends AbstractProcess {
            /**
             * @var Consumer
             */
            private $consumer;

            /**
             * @var ConsumerMessageInterface
             */
            private $consumerMessage;

            public function __construct(ContainerInterface $container, ConsumerMessageInterface $consumerMessage)
            {
                parent::__construct($container);
                $this->consumer = $container->get(Consumer::class);
                $this->consumerMessage = $consumerMessage;
            }

            public function handle(): void
            {
                $this->consumer->consume($this->consumerMessage);
            }

            public function getConsumerMessage(): ConsumerMessageInterface
            {
                return $this->consumerMessage;
            }

            public function isEnable($server): bool
            {
                return $this->consumerMessage->isEnable();
            }
        };
    }
}
