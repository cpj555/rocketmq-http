<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Exception\MqException;
use Losingbattle\RocketMqHttp\Message\AckMessage;
use Losingbattle\RocketMqHttp\Message\AckMessageInterface;
use Losingbattle\RocketMqHttp\Message\ConsumerMessageInterface;
use Losingbattle\RocketMqHttp\Message\ProducerMessageInterface;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Codec\Xml;

trait AckTrait
{
    public function ack(AckMessageInterface $ackMessage)
    {
        $query = [
            'consumer' => $ackMessage->getGroupId(),
        ];

        if ($ackMessage->getInstanceId()) {
            $query['ns'] = $ackMessage->getInstanceId();
        }

        if ($ackMessage->getTrans()) {
            $query['trans'] = $ackMessage->getTrans();
        }

        [$content, $statusCode] = $this->request(
            'DELETE',
            sprintf('/topics/%s/messages', $ackMessage->getTopic()),
            [
                'body' => Xml::toXml($ackMessage->serialize(), null, 'ReceiptHandles'),
                'query' => $query,
            ]
        );

        if ($statusCode != 204) {
            $code = Arr::get($content, 'Code');
            $message = Arr::get($content, 'Message');
            $requestId = Arr::get($content, 'RequestId');
            $hostId = Arr::get($content, 'HostId');
            throw new MqException(sprintf('Code: %s Message: %s RequestId : %s HostId %s', $code, $message, $requestId, $hostId));
        }
    }

    public function commitSuccess(ProducerMessageInterface $producerMessage, string $receiptHandle)
    {
        $ackMessage = $this->initAckMessageFromProducerMessage($producerMessage, $receiptHandle);
        $this->commit($ackMessage);
    }

    public function rollbackSuccess(ProducerMessageInterface $producerMessage, string $receiptHandle)
    {
        $ackMessage = $this->initAckMessageFromProducerMessage($producerMessage, $receiptHandle);
        $this->rollback($ackMessage);
    }

    public function commit(AckMessageInterface $ackMessage)
    {
        $ackMessage->setTrans(Constants::TRANSACTION_COMMIT);
        $this->ack($ackMessage);
    }

    public function rollback(AckMessageInterface $ackMessage)
    {
        $ackMessage->setTrans(Constants::TRANSACTION_ROLLBACK);
        $this->ack($ackMessage);
    }

    protected function initAckMessageFromConsumerMessage(ConsumerMessageInterface $consumerMessage, string $receiptHandle)
    {
        $ackMessge = new AckMessage();
        $ackMessge->setTopic($consumerMessage->getTopic())
            ->setInstanceId($consumerMessage->getInstanceId())
            ->setGroupId($consumerMessage->getGroupId())
            ->setReceiptHandle($receiptHandle);
        return $ackMessge;
    }

    protected function initAckMessageFromProducerMessage(ProducerMessageInterface $producerMessage, string $receiptHandle)
    {
        $ackMessge = new AckMessage();
        $ackMessge->setTopic($producerMessage->getTopic())
            ->setInstanceId($producerMessage->getInstanceId())
            ->setGroupId($producerMessage->getGroupId())
            ->setReceiptHandle($receiptHandle);
        return $ackMessge;
    }
}
