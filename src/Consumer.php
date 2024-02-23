<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Event\AfterConsume;
use Losingbattle\RocketMqHttp\Event\BeforeConsume;
use Losingbattle\RocketMqHttp\Event\FailToConsume;
use Losingbattle\RocketMqHttp\Exception\InvalidArgumentException;
use Losingbattle\RocketMqHttp\Exception\MqException;
use Losingbattle\RocketMqHttp\Message\ConsumerMessageInterface;
use Losingbattle\RocketMqHttp\Message\Response\ConsumeMessageResponse;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\ReflectionManager;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Coroutine\Concurrent;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use function Hyperf\Support\call;


class Consumer extends Client
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var null|EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(ContainerInterface $container, \Closure $clientFactory, Option $option, LoggerInterface $logger = null)
    {
        parent::__construct($clientFactory, $option, $logger);
        $this->container = $container;
        if ($this->container->has(EventDispatcherInterface::class)) {
            $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
        }
    }

    public function consume(ConsumerMessageInterface $consumerMessage)
    {
        if ($consumerMessage->getNumOfMessages() < 0 || $consumerMessage->getNumOfMessages() > 16) {
            throw new InvalidArgumentException('numOfMessages should be 1~16');
        }
        if ($consumerMessage->getWaitSeconds() > 30) {
            throw new InvalidArgumentException('numOfMessages should less then 30');
        }

        $query = [
            'consumer' => $consumerMessage->getGroupId(),
            'numOfMessages' => $consumerMessage->getNumOfMessages(),
        ];

        if ($consumerMessage->getInstanceId()) {
            $query['ns'] = $consumerMessage->getInstanceId();
        }

        $concurrent = $this->getConcurrent();
        $maxConsumption = $consumerMessage->getMaxConsumption();
        $currentConsumption = 0;

        if ($consumerMessage->getConsumerTag()) {
            $query['tag'] = $consumerMessage->getConsumerTag();
        }
        if ($consumerMessage->getWaitSeconds()) {
            $query['waitSeconds'] = $consumerMessage->getWaitSeconds();
        }

        if($consumerMessage->getHalfTrans() && $consumerMessage->getOrderly()){
            throw new MqException('trans can only set one');
        }

        if ($consumerMessage->getHalfTrans()) {
            $query['trans'] = Constants::TRANSACTION_POP;
        }

        if($consumerMessage->getOrderly()){
            $query['trans'] = Constants::TRANSACTION_ORDER;
        }

        while (true) {
            [$content,$statusCode] = $this->request(
                'GET',
                sprintf('/topics/%s/messages', $consumerMessage->getTopic()),
                ['query' => $query]
            );

            if ($statusCode == 200) {
                $messages = Arr::get($content, 'Message');

                if (! is_array(reset($messages))) {
                    $messages = [$messages];
                }
                
                //如果是顺序消息并且存在Concurrent 则以分区起协程
                if($consumerMessage->getOrderly() && $concurrent instanceof Concurrent){
                    $shardKeyGroup = [];
                    //以shardingKey为键将同一分区的消息分组
                    foreach ($messages as $message){
                        $consumeMessageResponse = ConsumeMessageResponse::make($message);
                        $shardKeyGroup[$consumeMessageResponse->getProperties()->getShardingKey()][] = [$consumerMessage,$consumeMessageResponse];
                    }

                    foreach ($shardKeyGroup as $messageGroup){
                        $concurrent->create(function() use ($messageGroup,&$currentConsumption){
                            foreach ($messageGroup as $message){
                                list($consumerMessage,$consumeMessageResponse) = $message;
                                $this->getCallback($consumerMessage, $consumeMessageResponse)();
                                ++$currentConsumption;
                            }
                        });
                    }
                }else{
                    foreach ($messages as $message) {

                        $consumeMessageResponse = ConsumeMessageResponse::make($message);

                        $callback = $this->getCallback($consumerMessage, $consumeMessageResponse);

                        if (! $concurrent instanceof Concurrent) {
                            parallel([$callback]);
                        } else {
                            $concurrent->create($callback);
                        }

                        ++$currentConsumption;
                    }
                }


                if ($maxConsumption > 0 && $currentConsumption > $maxConsumption) {
                    break;
                }
            } else {
                $code = Arr::get($content, 'Code');
                $message = Arr::get($content, 'Message');
                $requestId = Arr::get($content, 'RequestId');
                $hostId = Arr::get($content, 'HostId');
                if ($code == Constants::MESSAGE_NOT_EXIST) {
                    continue;
                }
                throw new MqException(sprintf('Code: %s Message: %s RequestId : %s HostId %s', $code, $message, $requestId, $hostId));
            }
        }

        //当设置了maxConsumption达到最大消费数后自重启后 消费完这批消息再结束
        while ($concurrent && ! $concurrent->isEmpty()) {
            usleep(10 * 1000);
        }
    }

    protected function getCallback(ConsumerMessageInterface $consumerMessage, ConsumeMessageResponse $consumeMessageResponse)
    {
        return function () use ($consumerMessage,$consumeMessageResponse) {
            $messageBody = $consumeMessageResponse->getMessageBody();

            try {
                $this->eventDispatcher && $this->eventDispatcher->dispatch(new BeforeConsume($consumeMessageResponse));

                $messageTag = $consumeMessageResponse->getMessageTag();
                $messageBody = $consumeMessageResponse->getMessageBody();

                $data = $consumerMessage->unserialize($messageBody);

                $route = $consumerMessage->getRoute();
                if ($route && isset($route[$messageTag])) {
                    /* @var callable $callable */
                    $callable = $route[$messageTag];
                    [$c,$method] = $callable;
                    $reflectionMethod = ReflectionManager::reflectMethod(get_class($c),$method);
                    $parameters = $reflectionMethod->getParameters();
                    $parameter = current($parameters);
                    if ($parameter->getType()) {
                        $classname = $parameter->getType()->getName();
                        if ($this->isSubOfCollection($classname)) {
                            /* @var Collection $classname */
                            $data = $classname::make($data);
                        }
                    }
                    $result = call($callable, [$data]);
                } else {
                    $result = $consumerMessage->consumeMessage($data);
                }

                if (is_null($result)) {
                    $result = Result::ACK;
                }
                $this->eventDispatcher && $this->eventDispatcher->dispatch(new AfterConsume($consumeMessageResponse, $result));
            } catch (\Throwable $exception) {
                $this->eventDispatcher && $this->eventDispatcher->dispatch(new FailToConsume($consumeMessageResponse, $exception));
                if ($this->container->has(FormatterInterface::class)) {
                    $formatter = $this->container->get(FormatterInterface::class);
                    $this->logger->error($formatter->format($exception));
                } else {
                    $this->logger->error($exception->getMessage());
                }

                $result = Result::NACK;
            }

            $ackMessage = $this->initAckMessageFromConsumerMessage($consumerMessage, $consumeMessageResponse->getReceiptHandle());

            if ($result == Result::ACK) {
                if ($consumerMessage->getHalfTrans()) {
                    $this->commit($ackMessage);
                } else {
                    $this->ack($ackMessage);
                }
            }

            if ($result == Result::NACK && $consumerMessage->getHalfTrans()) {
                $this->rollback($ackMessage);
            }
        };
    }

    protected function getConcurrent(): ?Concurrent
    {
        $config = $this->container->get(ConfigInterface::class);
        $concurrent = (int) $config->get('rocketmq-http.concurrent.limit', 0);
        if ($concurrent > 1) {
            return new Concurrent($concurrent);
        }

        return null;
    }

    protected function isSubOfCollection(string $classname): bool
    {
        if (class_exists($classname)) {
            return is_subclass_of($classname, Collection::class);
        }
        return false;
    }
}
