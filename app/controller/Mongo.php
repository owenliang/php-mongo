<?php
namespace app\controller;

use MongoDB\BSON\ObjectId;

class Mongo
{
    private $mongo = null;
    private $collection = null;

    public function __construct()
    {
        $this->mongo = new \MongoDB\Client('mongodb://localhost:28000/');
        $this->collection = $this->mongo->selectCollection('my_db', 'my_collection');
    }

    // 插入
    public function insert()
    {
        $insertOneResult = $this->collection->insertOne([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());

        var_dump($insertOneResult->getInsertedId());
    }

    // 查找
    public function find()
    {
        $cursor = $this->collection->find(
            ['username' => 'admin'],    // 过滤条件
            ['skip' => 1, 'limit' => 2, 'sort' => ['_id' => -1]]    // 其他参数
        );

        foreach ($cursor as $document) {
            var_dump($document);
        }
    }

    // 更新
    public function update()
    {
        $updateResult =  $this->collection->updateMany(
            ['username' => 'admin'], // 过滤条件
            ['$set' => ['name' => 'owenliang']], // 更新操作
            ['upsert' => true] // 其他参数
        );

        printf("Matched %d document(s)\n", $updateResult->getMatchedCount());
        printf("Modified %d document(s)\n", $updateResult->getModifiedCount());
    }

    // 删除
    public function delete()
    {
        $deleteResult = $this->collection->deleteMany(['name' => 'owenliang']);

        printf("Deleted %d document(s)\n", $deleteResult->getDeletedCount());
    }

    // 这是一个benchmark
    public function bulk()
    {
        $st = microtime(true);

        $batch = [];
        for ($i = 0; $i < 10000000; ++$i) {
            $batch[] = [
                'insertOne' => [
                    [
                        'username' => 'admin',
                        'email' => 'admin@example.com',
                        'name' => 'Admin User',
                    ]
                ]
            ];
            if ($i % 1000 == 0) {
                $this->collection->bulkWrite($batch, ['ordered' => false]);
                $batch = [];
            }
            if ($i % 10000 ==0) {
                echo $i / (microtime(true) - $st) . PHP_EOL;
            }
        }
    }
}