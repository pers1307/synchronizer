<?php

namespace pers1307\synchronizer;


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

    /**
     * @var array $db
     */
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

    /**
     * @return object
     */
    private function getLocalArticles()
    {
        $command = $this->localConnection->prepare('
                  SELECT 
                    id,
                    path_id,
                    parent,
                    `order`,
                    `name`,
                    code,
                    recomends,
                    visible,
                    parents_visible,
                    lft,
                    rgt,
                    `level`,
                    image     
                  FROM mp_equipment_articles');
        $command->execute();

        return $command->fetchAll();
    }

    /**
     * @var integer $id
     *
     * @return object
     */
    private function getExternalArticles($id)
    {
        $existRow = $this->externalConnection->prepare('
                                    SELECT 
                                    id
                                    FROM mp_equipment_articles
                                    WHERE id = ?');
        $existRow->execute([$id]);

        return $existRow->fetch();
    }

    /**
     * @var array $articleInfo
     *
     */
    private function updateExternalArticles($articleInfo)
    {
        $stmt = $this->externalConnection->prepare('
                                    UPDATE 
                                      mp_equipment_articles
                                    SET
                                        path_id = :path_id,
                                        parent = :parent,
                                        `order` = :order,
                                        `name` = :name,
                                        code = :code,
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
        $stmt->bindParam(':recomends', $articleInfo['recomends']);
        $stmt->bindParam(':visible', $articleInfo['visible']);
        $stmt->bindParam(':parents_visible', $articleInfo['parents_visible']);
        $stmt->bindParam(':lft', $articleInfo['lft']);
        $stmt->bindParam(':rgt', $articleInfo['rgt']);
        $stmt->bindParam(':level', $articleInfo['level']);
        $stmt->bindParam(':image', $articleInfo['image']);
        $stmt->bindParam(':id', $articleInfo['id']);
        $stmt->execute();
    }

    /**
     * @var array $articleInfo
     *
     */
    private function insertExternalArticle($articleInfo)
    {
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
        $stmt->bindParam(':recomends', $articleInfo['recomends']);
        $stmt->bindParam(':visible', $articleInfo['visible']);
        $stmt->bindParam(':parents_visible', $articleInfo['parents_visible']);
        $stmt->bindParam(':lft', $articleInfo['lft']);
        $stmt->bindParam(':rgt', $articleInfo['rgt']);
        $stmt->bindParam(':level', $articleInfo['level']);
        $stmt->bindParam(':image', $articleInfo['image']);
        $stmt->execute();
    }

    /**
     * @return object
     *
     */
    private function getLocalProducts()
    {
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
        $products->execute();
        return $products->fetchAll();
    }

    /**
     * @var integer $id
     *
     * @return object
     */
    private function getExternalProducts($id)
    {
        $existRow = $this->externalConnection->prepare('
                                    SELECT 
                                    id
                                    FROM mp_equipment_items
                                    WHERE id = ?');
        $existRow->execute([$id]);
        return $existRow->fetch();
    }

    /**
     * @var array $productInfo
     *
     */
    private function updateExternalProducts($productInfo)
    {
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
    }

    /**
     * @var array $productInfo
     *
     */
    private function insertExternalProducts($productInfo)
    {
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

    /**
     * @var array $articles
     *
     */
    private function deleteExternalArticlesNotLocal($articles)
    {
        $localIds = array_column($articles, 'id');
        $stmt = $this->externalConnection->prepare('
                SELECT
                  id
                FROM
                  mp_equipment_articles');
        $stmt->execute();
        $externalIds = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
        $idsToDelete = array_diff($externalIds, $localIds);

        if (!empty($idsToDelete)) {

            foreach ($idsToDelete as $id) {
                $this->destroyExternalArticleRows($id);
            }
        }
    }

    /**
     * @var array $products
     *
     */
    private function deleteExternalProductsNotLocal($products)
    {
        $localIds = array_column($products, 'id');
        $stmt = $this->externalConnection->prepare('
                SELECT
                  id
                FROM
                  mp_equipment_items');
        $stmt->execute();
        $externalIds = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
        $idsToDelete = array_diff($externalIds, $localIds);

        if (!empty($idsToDelete)) {

            foreach ($idsToDelete as $id) {
                $this->destroyExternalProductRow($id);
            }
        }
    }

    /**
     * @var integer $id
     *
     */
    private function destroyExternalArticleRows($id)
    {
        $stmt = $this->externalConnection->prepare('
                DELETE FROM
                  mp_equipment_articles
                WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * @var integer $id
     *
     */
    private function destroyExternalProductRow($id)
    {
        $stmt = $this->externalConnection->prepare('
                DELETE FROM
                  mp_equipment_items
                WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function sync()
    {
        $this->createLocalConnection();

        $articles = $this->getLocalArticles();

        if ($articles) {

            foreach ($this->externalDb as $db) {
                $this->createExternalConnection($db);

                foreach ($articles as $articleInfo) {
                    $res = $this->getExternalArticles($articleInfo['id']);

                    if (!empty($res)) {
                        $this->updateExternalArticles($articleInfo);
                    } else {
                        $this->insertExternalArticle($articleInfo);
                    }
                }
                $this->deleteExternalArticlesNotLocal($articles);
            }
        }
        $products = $this->getLocalProducts();

        if ($products) {

            foreach ($this->externalDb as $db) {
                $this->createExternalConnection($db);

                foreach ($products as $productInfo) {

                    $res = $this->getExternalProducts($productInfo['id']);
                  
                    if (!empty($res)) {
                        $this->updateExternalProducts($productInfo);
                    } else {
                        $this->insertExternalProducts($productInfo);
                    }
                }
                $this->deleteExternalProductsNotLocal($products);
            }
        };

    }
}
