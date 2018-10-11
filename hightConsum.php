<?php

$conf = new RdKafka\Conf();

// Set a rebalance callback to log partition assignments (optional)
// 当有新的消费进程加入或者退出消费组时，kafka 会自动重新分配分区给消费者进程，这里注册了一个回调函数，当分区被重新分配时触发
$conf->setRebalanceCb(function (RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
    switch ($err) {
        case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
            echo "Assign: ";
            var_dump($partitions);
            $kafka->assign($partitions);
            break;

        case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
            echo "Revoke: ";
            var_dump($partitions);
            $kafka->assign(NULL);
            break;

        default:
            throw new \Exception($err);
    }
});

// 配置groud.id 具有相同 group.id 的consumer 将会处理不同分区的消息，所以同一个组内的消费者数量如果订阅了一个topic， 那么消费者进程的数量多于 多于这个topic 分区的数量是没有意义的。
$conf->set('group.id', 'myConsumerGroup');

//添加 kafka集群服务器地址
$conf->set('metadata.broker.list', '127.0.0.1:9092');

$topicConf = new RdKafka\TopicConf();


// Set where to start consuming messages when there is no initial offset in
// offset store or the desired offset is out of range.
// 'smallest': start from the beginning
//当没有初始偏移量时，从哪里开始读取
$topicConf->set('auto.offset.reset', 'smallest');


// Set the configuration to use for subscribed/assigned topics
$conf->setDefaultTopicConf($topicConf);

$consumer = new RdKafka\KafkaConsumer($conf);

// 让消费者订阅log 主题
$consumer->subscribe(['test']);


while (true) {
    $message = $consumer->consume(120*1000);
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            var_dump($message);
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            throw new \Exception($message->errstr(), $message->err);
            break;
    }
}

?>

