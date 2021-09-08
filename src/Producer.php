<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Exception\MqException;
use Losingbattle\RocketMqHttp\Message\ProducerMessageInterface;
use Losingbattle\RocketMqHttp\Message\Response\PublishMessageResponse;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Codec\Xml;

class Producer extends Client
{
    public function produce(ProducerMessageInterface $producerMessage, float $timeout = 5): PublishMessageResponse
    {
        return retry(1, function () use ($producerMessage, $timeout) {
            return $this->produceMessage($producerMessage, $timeout);
        });
    }

    private function produceMessage(ProducerMessageInterface $producerMessage, float $timeout = 5): PublishMessageResponse
    {
        $this->injectMessageProperty($producerMessage);

        if ($producerMessage->getInstanceId()) {
            $query = [
                'ns' => $producerMessage->getInstanceId(),
            ];
        }
        
        //事务性消息发送必须设置group 用于commit和rollback时使用
        if($producerMessage->getTransctionCheckTtl()){
            if(!$producerMessage->getGroupId()){
                throw new MqException("GroupId is null");
            }
        }

        [$responseBodyContent, $statusCode] = $this->request(
            'POST',
            sprintf('/topics/%s/messages', $producerMessage->getTopic()),
            [
                'body' => Xml::toXml($producerMessage->payload(), null, 'Message'),
                'query' => $query ?? [],
                'timeout' => $timeout,
            ]
        );

        if ($statusCode == 201) {
            $publishMessageResponse = new PublishMessageResponse();
            $publishMessageResponse
                ->setMessageId((string) Arr::get($responseBodyContent, 'MessageId'))
                ->setMessageBodyMD5((string) Arr::get($responseBodyContent, 'MessageBodyMD5'))
                ->setReceiptHandle((string) Arr::get($responseBodyContent, 'ReceiptHandle'));
        } else {
            $code = Arr::get($responseBodyContent, 'Code');
            $message = Arr::get($responseBodyContent, 'Message');
            $requestId = Arr::get($responseBodyContent, 'RequestId');
            $hostId = Arr::get($responseBodyContent, 'HostId');
            throw new MqException(sprintf('Code: %s Message: %s RequestId : %s HostId %s', $code, $message, $requestId, $hostId));
        }

        return $publishMessageResponse;
    }

    private function injectMessageProperty(ProducerMessageInterface $producerMessage)
    {
        if (class_exists(AnnotationCollector::class)) {
            /* @var null|\Losingbattle\RocketMqHttp\Annotation\Producer $annotation */
            $annotation = AnnotationCollector::getClassAnnotation(get_class($producerMessage), Annotation\Producer::class);

            if ($annotation) {
                $annotation->instaceId && $producerMessage->setInstanceId($annotation->instaceId);
                $annotation->tag && $producerMessage->setTag($annotation->tag);
                $annotation->topic && $producerMessage->setTopic($annotation->topic);
                $annotation->delayTtl && $producerMessage->setDelayTtl($annotation->delayTtl);
                $annotation->transctionCheckTtl && $producerMessage->setTransctionCheckTtl($annotation->transctionCheckTtl);
                $annotation->groupId && $producerMessage->setGroupId($annotation->groupId);
            }
        }
    }
}
