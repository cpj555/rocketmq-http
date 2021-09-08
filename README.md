# ROCKETMQ-HTTP 组件

##安装
```
composer require Losingbattle/rocketmq-http
```

## 默认配置

|       配置                   |  类型  |  默认值   |      备注       |
|:-------------------------:|:-------:|:---------:|:--------------:|
|  host                     | string  |            |   Host        | 
|  access_key_id            | string  |            |               |
|  access_key_secret        | string  |            |               |
|  instance_id              | string  |            |   实例id       |
|  concurrent.limit         | int     |     0      | 同时消费的数量   |



## 投递消息

在 DemoProducer 文件中，我们可以修改 `@Producer` 注解对应的字段来替换对应的 `topic` 和 `tag`。
其中 `MessageBody` 就是最终投递到消息队列中的数据。
示例如下。

> 使用 `@Producer` 注解时需 `use Losingbattle\RocketMqHttp\Annotation\Producer;` 命名空间；   


```php
<?php


namespace App\Rocketmq\Producer;


use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;

/**
 * @Producer(topic="order_normal_topic",tag="tag_default_share-order_C_TERMINAL_updateDraftOrderStatus",delayTtl=1)
 */
class TestMessage extends ProducerMessage
{

    public function setBizId($bizId)
    {
        $this->setMessageBody('bizId', $bizId);
        return $this;
    }
}

```


```php
<?php
use Losingbattle\RocketMqHttp\Producer;
use App\Rocketmq\Producer\TestMessage;
use Hyperf\Utils\ApplicationContext;

$producer = $this->container->get(Producer::class);
$testMessage = new TestMessage();
$testMessage->setOrderSn("1")->setType(1)->setUserId(1)->setBizId(1);
$m = $producer->produce($testMessage,1);

```

## 消费消息

```php
<?php

declare(strict_types=1);

namespace App\Rocketmq\Consumer;

use Losingbattle\RocketMqHttp\Annotation\Consumer;
use Losingbattle\RocketMqHttp\Message\ConsumerMessage;
use Losingbattle\RocketMqHttp\Result;

/**
 * @Consumer(groupId="GID_hyperf", topic="order_normal_topic", numOfMessages=16, waitSeconds=30, maxConsumption=5)
 */
class OrderCenterConsumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->registerRoute('tag_default_share-order_C_TERMINAL_updateDraftOrderStatus', [$this, 'updateOrderStatus']);
        $this->registerRoute('tag_test', [$this, 'test']);
    }

    public function isEnable(): bool
    {
        return false;
    }

    public function updateOrderStatus($x)
    {
        dd('xxx');
        dd($x);
        return Result::ACK;
    }

    public function test($x)
    {
        dd('test');
        dd($x);
        return Result::ACK;
    }

    public function consumeMessage($consumeMessageResponse)
    {
        dd('11111111');
        dd($consumeMessageResponse);
        sleep(5);
        dd('2222222');

        return Result::ACK;
    }
}

```