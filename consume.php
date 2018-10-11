<?php
//https://libraries.io/github/mentionapp/php-rdkafka
function kafka_consume()
{
    try {
        $rcf = new RdKafka\Conf();
        $rcf->set('group.id', 'test');
        $cf = new RdKafka\TopicConf();
        /*
            $cf->set('offset.store.method', 'file');
        */
        $cf->set('auto.offset.reset', 'smallest');
        $cf->set('auto.commit.enable', true);

        $rk = new RdKafka\Consumer($rcf);
        $rk->setLogLevel(LOG_DEBUG);
        $rk->addBrokers("192.168.3.99:9092");
        $topic = $rk->newTopic("test", $cf);
        //$topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
        while (true) {
            $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
            $msg = $topic->consume(0, 1000);
            //var_dump($msg);
            if ($msg->err) {
                echo $msg->errstr(), "\n";
                break;
            } else {
                echo $msg->payload, "\n";
            }
            $topic->consumeStop(0);
            sleep(1);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
kafka_consume();
