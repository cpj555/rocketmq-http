# ROCKETMQ-HTTP 组件

##安装
```
composer require Losingbattle/rocketmq-http
```

- hyperf框架直接引用即可,使用姿势与官方rabbitmq基本一致
- 其余框架生产者依赖guzzlehttp,psr/container,实现即可,消费者使用了协程消费依赖swoole [example](example/RocketMqFactory.php)
- 阿里云的rocketmq-http本身存在一些问题,当gid+topic+instance过长时消费消息将会报错,所以只能自身在创建时把控(Code: NotSupport Message: the length of GID(CID) and TOPIC is too long, total length(include instance) should not longer than 119, please change another topic or another cid RequestId : 605402BE384531236C9E1205 HostId)
- 普通消息相关已在线上稳定运行一年多

## 默认配置

|       配置                   |  类型  |  默认值   |      备注       |
|:-------------------------:|:-------:|:---------:|:--------------:|
|  host                     | string  |            |   Host        | 
|  access_key_id            | string  |            |               |
|  access_key_secret        | string  |            |               |
|  instance_id              | string  |            |   实例id       |
|  concurrent.limit         | int     |     0      | 同时消费的数量   |

hyperf中使用一下命令初始化即可
```
php bin/hyperf.php vendor:publish losingbattle/rocketmq-http
```


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


#[Producer(topic: "order_center_normal_topic", tag: "order_submit")]
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

#[Consumer(topic: "order_center_normal_topic", groupId: "GID_order_center_status_change", numOfMessages: 16, waitSeconds: 30)]
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
        return Result::ACK;
    }

    public function test($x)
    {
        return Result::ACK;
    }

    public function consumeMessage($consumeMessageResponse)
    {
        //没有指定tag则默认使用consumeMessage
        return Result::ACK;
    }
}

```

## 日志

dependencies.php中添加(建议只在本地调试时使用,本质上是不停的http轮询,影响日志查看)
```php
return [
    RocketMqHttpLoggerFactory::class,//(example文件中,重写loggerfaoctory)
];
```
listener.php 中添加消费listener
```php
return [
    Losingbattle\RocketMqHttp\Listener\ConsumeListener::class,
];
```


## demo

[普通消息生产](example/Producer/OrderSubmitNormalMessage.php)

[普通消息消费](example/Consumer/OrderCenterConsumer.php)

[顺序消息生产](example/Producer/OrderStatusOrderlyMessage.php)

[顺序消息消费](example/Consumer/OrderCenterOrderlyConsumer.php)

[延时消息生产](example/Producer/OrderCloseDelayMessage.php)

延时消息生产与普通消息相比只是在注解上多个一个delayTtl(秒)的属性

[延迟消息消费](example/Consumer/OrderCenterDelayConsumer.php)

延时消息与普通基本在消费形式上没有太大区别,只有在阿里云控制台有区分

[事务消息](example/Producer/OrderCreateTransMessage.php)

[普通消息生产](example/Producer/OrderSubmitNormalMessage.php)
