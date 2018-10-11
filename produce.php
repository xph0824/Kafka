<?php
//https://libraries.io/github/mentionapp/php-rdkafka
try {
    $rcf = new RdKafka\Conf();
    $rcf->set('group.id', 'test');
    $cf = new RdKafka\TopicConf();
    $cf->set('offset.store.method', 'broker');
    $cf->set('auto.offset.reset', 'smallest');

    $rk = new RdKafka\Producer($rcf);
    $rk->setLogLevel(LOG_DEBUG);
    $rk->addBrokers("192.168.3.99:9092");
    $topic = $rk->newTopic("test", $cf);
    //for($i = 0; $i < 1000; $i++) {
        //$topic->produce(0,0,'test' . $i);//没有setMessge接口了,使用produce  参考：https://libraries.io/github/mentionapp/php-rdkafka
    //}
    $topic->produce(0,0,'test'.date('Y-m-d H:i:s'));
    echo 'kafka_produce success!!!';
} catch (Exception $e) {
    echo $e->getMessage();
}


/*
function kafka_produce($key=null,$post=null)
{
    $rk = new \RdKafka\producer();
    $rk->setLogLevel(LOG_DEBUG);
    $rk->addBrokers("192.168.3.99:9092");
    $topics = $rk->newTopic('test');
    $topics->produce(0, 0,$post,$key);

    echo 'kafka_produce success!!!';
}
kafka_produce('test',date('Y-m-d H:i:s'));
*/