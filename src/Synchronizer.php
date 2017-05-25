<?php

namespace Duras\Synchronizer;


class Synchronizer
{
    /**
     * @var localConnection
     */
    private $localConnection;

    /**
     * @var externalConnection
     */
    private $externalConnection;

    /**
     * @array externalDb
     */
    private $externalDb;

    /**
     * @var localDb
     */
    private $localDb;


    function __construct($external, $local)
    {
        $this->externalDb = $external;
        $this->localDb = $local;
    }

    private function createLocalConnection()
    {
        $this->localConnection = new \PDO(
            'mysql:host=' . $this->localDb['host'] . ';dbname=' . $this->localDb['name'],
            $this->localDb['user'],
            $this->localDb['pass']
        );
    }
    private function createExternalConnection($db)
    {
        try {
            $this->externalConnection = new \PDO(
                'mysql:host=' . $db['host'] . ';dbname=' . $db['name'],
                $db['user'],
                $db['pass'],
                array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING)
            );
        } catch (\PDOException $e) {
            echo 'Соединение оборвалось: ' . $e->getMessage();
            exit;
        }
    }

    public function sync()
    {
        $this->createLocalConnection();

        $command = $this->localConnection->prepare('
                  SELECT 
                    id,
                    path_id,
                    parent,
                    `order`,
                    `name`,
                    code,
                    text,
                    recomends,
                    visible,
                    parents_visible,
                    lft,
                    rgt,
                    `level`,
                    image     
                  FROM mp_equipment_articles');

        if ($command->execute()) {
            foreach ($this->externalDb as $db) {
                $this->createExternalConnection($db);
                $i = 0;
                while ($articleInfo = $command->fetch()) {
                    $i++;
                    if ($i == 1) {
                        $existRow = $this->externalConnection->prepare('
                                    SELECT 
                                    id
                                    FROM mp_equipment_articles
                                    WHERE id = ?');
                        $existRow->execute([$articleInfo['id']]);

                        $res = $existRow->fetch();

                        if (!empty($res)) {
                            $stmt = $this->externalConnection->prepare('
                                    UPDATE 
                                      mp_equipment_articles
                                    SET
                                        path_id = :path_id,
                                        parent = :parent,
                                        `order` = :order,
                                        `name` = :name,
                                        code = :code,
                                        text = :text,
                                        recomends = :recomends,
                                        visible = :visible,
                                        parents_visible = :parents_visible,
                                        lft = :lft,
                                        rgt = :rgt,
                                        level = :level,
                                        image = :image
                                    WHERE id = :id');
                            $stmt->bindParam(':path_id', $articleInfo['path_id']);
                            $stmt->bindParam(':parent', $articleInfo['parent']);
                            $stmt->bindParam(':order', $articleInfo['order']);
                            $stmt->bindParam(':name', $articleInfo['name']);
                            $stmt->bindParam(':code', $articleInfo['code']);
                            $stmt->bindParam(':text', $articleInfo['text']);
                            $stmt->bindParam(':recomends', $articleInfo['recomends']);
                            $stmt->bindParam(':visible', $articleInfo['visible']);
                            $stmt->bindParam(':parents_visible', $articleInfo['parents_visible']);
                            $stmt->bindParam(':lft', $articleInfo['lft']);
                            $stmt->bindParam(':rgt', $articleInfo['rgt']);
                            $stmt->bindParam(':level', $articleInfo['level']);
                            $stmt->bindParam(':image', $articleInfo['image']);
                            $stmt->bindParam(':id', $articleInfo['id']);
                            $stmt->execute();
                        } else {
                            $stmt = $this->externalConnection->prepare('
                                    INSERT INTO 
                                    mp_equipment_articles
                                    (
                                        id,
                                        path_id,
                                        parent,
                                        `order`,
                                        `name`,
                                        code,
                                        text,
                                        recomends,
                                        visible,
                                        parents_visible,
                                        lft,
                                        rgt,
                                        `level`,
                                        image  
                                    ) 
                                    VALUES 
                                    (
                                        :id, 
                                        :path_id,
                                        :parent,
                                        :order,
                                        :name,
                                        :code,
                                        :text,
                                        :recomends,
                                        :visible,
                                        :parents_visible,
                                        :lft,
                                        :rgt,
                                        :level,
                                        :image 
                                    )');
                            $stmt->bindParam(':id', $articleInfo['id']);
                            $stmt->bindParam(':path_id', $articleInfo['path_id']);
                            $stmt->bindParam(':parent', $articleInfo['parent']);
                            $stmt->bindParam(':order', $articleInfo['order']);
                            $stmt->bindParam(':name', $articleInfo['name']);
                            $stmt->bindParam(':code', $articleInfo['code']);
                            $stmt->bindParam(':text', $articleInfo['text']);
                            $stmt->bindParam(':recomends', $articleInfo['recomends']);
                            $stmt->bindParam(':visible', $articleInfo['visible']);
                            $stmt->bindParam(':parents_visible', $articleInfo['parents_visible']);
                            $stmt->bindParam(':lft', $articleInfo['lft']);
                            $stmt->bindParam(':rgt', $articleInfo['rgt']);
                            $stmt->bindParam(':level', $articleInfo['level']);
                            $stmt->bindParam(':image', $articleInfo['image']);
                            $stmt->execute();
                        }
                    }
                }
            }

        }
    }
}