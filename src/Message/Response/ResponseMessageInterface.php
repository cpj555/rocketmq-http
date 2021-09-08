<?php


namespace Losingbattle\RocketMqHttp\Message\Response;


interface ResponseMessageInterface
{

    public function getMessageId();

    public function setMessageId(string $messageId);

    public function getMessageBodyMD5();

    public function setMessageBodyMD5(string $messageBodyMD5);
}