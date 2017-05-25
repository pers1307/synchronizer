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

                while ($articleInfo = $command->fetch()) {
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
        $products = $this->localConnection->prepare('
                  SELECT 
                    id,
                    path_id,
                    parent,
                    `order`,
                    title,
                    code,
                    brand,
                    image,
                    description,
                    property,
                    recomends,
                    active,
                    on_main,
                    price,
                    power,
                    date     
                  FROM mp_equipment_items');
        if ($products->execute()) {

            foreach ($this->externalDb as $db) {
                $this->createExternalConnection($db);

                while ($productInfo = $products->fetch()) {

                    $existRow = $this->externalConnection->prepare('
                                    SELECT 
                                    id
                                    FROM mp_equipment_items
                                    WHERE id = ?');
                    $existRow->execute([$productInfo['id']]);
                    $res = $existRow->fetch();
                  
                    if (!empty($res)) {
                        $stmt = $this->externalConnection->prepare('
                                    UPDATE 
                                      mp_equipment_items
                                    SET
                                        path_id = :path_id,
                                        parent = :parent,
                                        `order` = :order,
                                        title = :title,
                                        code = :code,
                                        brand = :brand,
                                        image = :image,
                                        description = :description,
                                        property = :property,
                                        recomends = :recomends,
                                        active = :active,
                                        on_main = :on_main,
                                        price = :price,
                                        power = :power,
                                        date = :date    
                                    WHERE id = :id');
                        $stmt->bindParam(':path_id', $productInfo['path_id']);
                        $stmt->bindParam(':parent', $productInfo['parent']);
                        $stmt->bindParam(':order', $productInfo['order']);
                        $stmt->bindParam(':title', $productInfo['title']);
                        $stmt->bindParam(':code', $productInfo['code']);
                        $stmt->bindParam(':brand', $productInfo['brand']);
                        $stmt->bindParam(':image', $productInfo['image']);
                        $stmt->bindParam(':description', $productInfo['description']);
                        $stmt->bindParam(':property', $productInfo['property']);
                        $stmt->bindParam(':recomends', $productInfo['recomends']);
                        $stmt->bindParam(':active', $productInfo['active']);
                        $stmt->bindParam(':on_main', $productInfo['on_main']);
                        $stmt->bindParam(':price', $productInfo['price']);
                        $stmt->bindParam(':power', $productInfo['power']);
                        $stmt->bindParam(':date', $productInfo['date']);
                        $stmt->bindParam(':id', $productInfo['id']);
                        $stmt->execute();
                    } else {
                        $stmt = $this->externalConnection->prepare('
                                    INSERT INTO 
                                    mp_equipment_items
                                    (
                                        id,
                                        path_id,
                                        parent,
                                        `order`,
                                        title,
                                        code,
                                        brand,
                                        image,
                                        description,
                                        property,
                                        recomends,
                                        active,
                                        on_main,
                                        price,
                                        power,
                                        date   
                                    ) 
                                    VALUES 
                                    (
                                        :id, 
                                        :path_id,
                                        :parent,
                                        :order,
                                        :title,
                                        :code,
                                        :brand,
                                        :image,
                                        :description,
                                        :property,
                                        :recomends,
                                        :active,
                                        :on_main,
                                        :price,
                                        :power,
                                        :date   
                                    )');
                        $stmt->bindParam(':id', $productInfo['id']);
                        $stmt->bindParam(':path_id', $productInfo['path_id']);
                        $stmt->bindParam(':parent', $productInfo['parent']);
                        $stmt->bindParam(':order', $productInfo['order']);
                        $stmt->bindParam(':title', $productInfo['title']);
                        $stmt->bindParam(':code', $productInfo['code']);
                        $stmt->bindParam(':brand', $productInfo['brand']);
                        $stmt->bindParam(':image', $productInfo['image']);
                        $stmt->bindParam(':description', $productInfo['description']);
                        $stmt->bindParam(':property', $productInfo['property']);
                        $stmt->bindParam(':recomends', $productInfo['recomends']);
                        $stmt->bindParam(':active', $productInfo['active']);
                        $stmt->bindParam(':on_main', $productInfo['on_main']);
                        $stmt->bindParam(':price', $productInfo['price']);
                        $stmt->bindParam(':power', $productInfo['power']);
                        $stmt->bindParam(':date', $productInfo['date']);
                        $stmt->execute();
                    }
                }
            }
        };
    }
}