# 步骤记录


## 搭建config server：

```
rs.initiate(
   {
      _id: "csRS",
      version: 1,
    configsvr: true, 
      members: [
         { _id: 0, host : "localhost:27000" },
         { _id: 1, host : "localhost:27001" },
         { _id: 2, host : "localhost:27002" }
      ]
   }
)
```

## 搭建mongos

## 搭建shard:

```
rs.initiate(
   {
      _id: "shard0",
      version: 1,
      members: [
         { _id: 0, host : "localhost:29000" },
         { _id: 1, host : "localhost:29001" },
         { _id: 2, host : "localhost:29002" }
      ]
   }
)

rs.initiate(
   {
      _id: "shard1",
      version: 1,
      members: [
         { _id: 0, host : "localhost:30000" },
         { _id: 1, host : "localhost:30001" },
         { _id: 2, host : "localhost:30002" }
      ]
   }
)
```

## 在mongos中添加shard:

sh.addShard("shard0/localhost:29000,localhost:29001,localhost:29002")


## 安装php mongodb扩展

https://pecl.php.net/package/mongodb

## 安装php composer sdk

composer require mongodb/mongodb

## 切换database

use my_db

## 允许database下的collection使用sharding特性
sh.enableSharding('my_db')

## 创建collection

https://docs.mongodb.com/manual/reference/method/db.createCollection/index.html

db.createCollection("my_collection" )

## 让collection使用sharding机制

（range模式只能伴随chunk分裂，而hash模式是可以预分配chunk的）

sh.shardCollection("my_db.my_collection", { _id: "hashed" },  false, { numInitialChunks: 1024}  )


## 创建索引

Hashed shard key只能指定一个字段建立唯一索引，支持预建chunk

非hashed shard key支持多个字段联合唯一索引，不支持预见chunk。

db.my_collection.createIndex({username: 1, email: 1})，

注意集群无法保证shard key之外的index是unique唯一的，所以无法在shard key之外建立其他唯一键。

哈希shard key

https://docs.mongodb.com/manual/reference/method/db.collection.createIndexes/

## 调试SQL

db.my_collection.explain().find({username: "admin"})

## 批量写入性能

wiredTiger引擎真的很不错，通过配置可以轻松的控制内存使用量，4年前的mmap引擎简直太渣了。

如下配置可以控制wiredTiger的内存使用量（包括用于写入的buffer和读取的cache），从压测来看非常稳定有效。

更大的cache带来更好的写入和读取性能，当然也要花更多的钱买更多的内存。

```
storage:
   wiredTiger:
      engineConfig:
         cacheSizeGB: 2
```

## wiredTiger引擎调优

除了上面说的cacheSizeGB，还有一个限制每秒事务数量的参数值得关注：

wiredTigerConcurrentReadTransactions


具体链接见：
https://juejin.im/entry/58c2330344d9040068e7eac9