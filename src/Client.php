<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Exception\ClientException;
use Losingbattle\RocketMqHttp\Util\Signature;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Hyperf\Collection\Arr;
use Hyperf\Codec\Xml;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class Client
{
    use AckTrait;
    
    private $clientFactory;

    /**
     * @var string
     */
    private $accessKeyId;

    /**
     * @var string
     */
    private $accessKeySecret;

    private $instanceId;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(\Closure $clientFactory, Option $option, LoggerInterface $logger = null)
    {
        $this->clientFactory = $clientFactory;
        $this->accessKeyId = $option->getAccessKeyId();
        $this->accessKeySecret = $option->getAccessKeySecret();
        $this->instanceId = $option->getInstanceId();
        $this->logger = $logger ?: new NullLogger();
    }

    protected function request(string $method, string $url, array $options = [])
    {
        // Create a HTTP Client by $clientFactory closure.
        $clientFactory = $this->clientFactory;
        $client = $clientFactory($options);
        if (! $client instanceof ClientInterface) {
            throw new ClientException(sprintf('The client factory should create a %s instance.', ClientInterface::class));
        }

        if (! Arr::exists($options['query'], 'ns') && $this->instanceId) {
            $options['query']['ns'] = $this->instanceId;
        }

        $date = gmdate('D, d M Y H:i:s \\G\\M\\T');
        $version = '2015-06-06';
        $contentType = 'text/xml';
        $canonicalizedResource = $url . '?' . http_build_query($options['query']);

        $options['http_errors'] = false;
        $options['headers'] = [
            'Date' => $date,
            'x-mq-version' => $version,
            'Content-Type' => $contentType,
            'Authorization' => sprintf('MQ %s:%s', $this->accessKeyId, Signature::generate($method, $contentType, $date, $version, $canonicalizedResource, $this->accessKeySecret)),
        ];

        $this->logger->info('request:', ['method' => $method, 'url' => $url, 'option' => $options]);
        try {
            $response = $client->request($method, $url, $options);

            $statusCode = $response->getStatusCode();
            $responseBodyContent = $response->getBody()->getContents();

            $this->logger->info('response:', ['statusCode' => $statusCode, 'responseBodyContent' => $responseBodyContent]);

            $content = $responseBodyContent ? Xml::toArray($responseBodyContent) : '';
        } catch (TransferException $e) {
            $content = sprintf('Something went wrong when calling rocketmq (%s).', $e->getMessage());
            $this->logger->error($content);
            throw $e;
        }

        return [$content, $statusCode];
    }
}
