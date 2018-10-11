整个系统运行的顺序：

- 启劢 zookeeper 的 server
- 启劢 kafka 的 server
- Producer 如果生产了数据，会先通过 zookeeper 找刡 broker，然后将数据存放迕 broker
- Consumer 如果要消费数据，会先通过 zookeeper 找对应的 broker，然后消费。
--------------------- 

1. 安装kafka
##### (先去kafka官网现在最新的kafka安装包)
- 	[root@bogon software]# wget http://apache.fayea.com/kafka/0.10.2.1/kafka_2.10-0.10.2.1.tgz
##### 然后把kafka解压到安装目录下
- 	[root@bogon software]# tar -zvxf kafka_2.10-0.10.2.1.tgz -C /usr/local/kafka/
#####  	重命名kafka安装包，方便配置
- 	[root@bogon kafka]# mv kafka_2.0-0.10.2.1 kafk
#####  	启动zookeeper
- 	[root@bogon bin]# sh zookeeper-server-start.sh -daemon ../config/zookeeper.properties
#####  	启动kafka服务
- 	[root@bogon bin]# sh kafka-server-start.sh ../config/server.properties
#####  	如果出现错误,这时候我们需要修改下主机名
- 	[root@xg sysconfig]# hostname localhost
- 	[root@xg sysconfig]# su
#####  	再次执行
- 	[root@localhost bin]# sh kafka-server-start.sh ../config/server.properties
#####  	最后看到kafka成功启动 
#####   输入jps可以查看启动情况

2. 测试
##### 创建一个topic
[root@localhost bin]# sh kafka-topics.sh --create --zookeeper localhost:2181 --replication-factor 1 --partitions 1 --topic test

Created topic "test".

##### topic创建成功，下面我查看一下

- [root@localhost bin]# sh kafka-topics.sh --list --zookeeper localhost:2181

test

##### 发送消息

- [root@localhost bin]# sh kafka-console-producer.sh --broker-list localhost:9092 --topic test

hello test,haha

this is a test message

##### 客户端接收消息

[root@localhost bin]# sh kafka-console-consumer.sh --zookeeper localhost:2181 --topic test --from-beginning

Using the ConsoleConsumer with old consumer is deprecated and will be removed in a future major release. Consider using the new consumer by passing [bootstrap-server] instead of [zookeeper].

hello test,haha

this is a test message
-----------------------------------------------------------------------

1. 首先php要有kafka扩展，在命令行中输入 php -m  看是否有rdkafka 

#### - 没有的话需要安装配置下：

--------------- kafka php客户端安装(php-rdkafka) --------------
- 1.安装 librdkafka

cd /usr/local

git clone https://github.com/edenhill/librdkafka

cd librdkafka

./configure		#配置

make  #编译

make install  #安装


- 2.安装php-rdkafka

cd /usr/local

git clone https://github.com/arnaud-lb/php-rdkafka.git

cd php-rdkafka

/usr/local/php/bin/phpize  #加载php扩展模块

./configure --enable-kafka --with-php-config=/usr/local/php/bin/php-config
#配置

make  #编译

make install  #安装


vi /usr/local/lib/php.ini或者/usr/local/php/etc/php.ini

加入 extension=rdkafka.so

php -m  看是否有rdkafka


2.期间遇到几个坑 ?前边的步骤都做完后 发现就是扩展没有正常加载上 ，很奇葩 后来看了 php的错误日志 是找不到librdkafka.so.1这个文件，librdkafka安装都是正常的， 百度后 找到了解决方法
php加载?librdkafka的时候 会在?/usr/lib/下找对应的文件 没有找到回报错，所以加个软链是最简单的方法

ln -s /usr/local/lib/librdkafka.so.1 /usr/lib/

加了软链后记得一定 更新软链才会生效

ldconfig

然后再重启php-fpm 一切都ok啦 ~~ 。


