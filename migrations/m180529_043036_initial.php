<?php

use yii\db\Migration;

/**
 * Class m180529_043036_initial
 */
class m180529_043036_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('User', [
            'id' => $this->primaryKey(),
            'created' => $this->timestamp(),
            'fullName' => $this->string(500),
            'role' => $this->integer()
        ]);
        $this->createTable('Email', [
            'id' => $this->primaryKey(),
            'idUser' => $this->integer(),
            'created' => $this->timestamp(),
            'email' => $this->string(500),
            'confirmed' => $this->timestamp(),
            'isMain' => $this->boolean()->notNull()->defaultValue(false)
        ]);
        $this->createTable('Product', [
            'id' => $this->primaryKey(),
            'idBrand' => $this->integer(),
            'created' => $this->timestamp(),
            'slug' => $this->string(500),
            'price' => $this->decimal(10, 2),
            'title' => $this->string(500)
        ]);
        $this->createTable('Taxonomy', [
            'id' => $this->primaryKey(),
            'idParent' => $this->integer(),
            'slug' => $this->string(500),
            'type' => $this->integer(),
            'title' => $this->string(500),
            'description' => $this->text()
        ]);
        $this->createTable('TaxonomyType', [
            'id' => $this->primaryKey(),
            'name' => $this->string(500),
            'slug' => $this->string(100),
            'description' => $this->text()
        ]);
        $this->createTable('ProductTaxonomy', [
            'idTaxonomy' => $this->integer()->notNull(),
            'idProduct'  => $this->integer()->notNull()
        ]);
        $this->createTable('Brand', [
            'id' => $this->primaryKey(),
            'shortName' => $this->string(500),
            'fullName' => $this->string(500),
            'slug' => $this->string(500)
        ]);
        $this->addPrimaryKey('ProductTaxonomy_pk',
                                'ProductTaxonomy',
                                ['idTaxonomy','idProduct']);

        $this->createTable('Cart', [
            'id' => $this->primaryKey(),
            'hash' => $this->string(32),
            'created' => $this->timestamp(),
            'total' => $this->decimal(10, 2)
        ]);

        $this->createTable('CartItem', [
            'id' => $this->primaryKey(),
            'idCart' => $this->integer(),
            'idProduct' => $this->integer(),
            'amount' => $this->decimal(10, 3)
        ]);

        $this->createTable('CartItemAttribute', [
            'idCartItem' => $this->integer(),
            'idAttribute' => $this->integer(),
            'attributeValue' => $this->string(1000)
        ]);

        $this->addPrimaryKey('CartItemAttribute_PK', 'CartItemAttribute',
                             ['idCartItem', 'idAttribute']
                             );

        $this->createTable('Shop', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(500),
            'title' => $this->string(500),
            'domain' => $this->string(500),
            'logo' => $this->string(500),
            'created' => $this->datetime()
        ]);

        // type:
        //   1. discount in %
        //   2. more for less (3x2, 2x1)
        //   3. fixed discount ($100)
        //   4. ...
        $this->createTable('Offer', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(500),
            'type' => $this->integer(),
            'begins' => $this->datetime(),
            'ends' => $this->datetime(),
            'factor' => $this->string(500) // 2x1, 50%
        ]);
        $this->createTable('OfferTarget', [
            'id' => $this->primaryKey(),
            'idOffer' => $this->integer(),
            'idTaxonomy' => $this->integer(),
            'idProduct' => $this->integer()
        ]);

        // Create taxonomy
        $this->insert('TaxonomyType', ['id' => 1, 'name' => 'Category']);

        // Create categories
        $this->batchInsert('Taxonomy', 
            ['id', 'type', 'title', 'slug', 'idParent'], [
                [1, 1, 'Main', 'main', null],
                [2, 1, 'Computers', 'computers', 1],
                [3, 1, 'Laptops', 'laptops', 2],
                [4, 2, 'Apple', 'apple', null]
            ]
        );

        // Create brands
        $this->batchInsert('Brand',
            ['id', 'shortName', 'fullName', 'slug'], [
                [1, 'Apple', 'Apple Computers', 'apple'],
                [2, 'Microsoft', 'Microsoft Corporation', 'microsoft'],
                [3, 'Facebook', 'Facebook Inc.', 'facebook'],
                [4, 'DELL', 'DELL Computers', 'dell'],
            ]
        );

        // Create products example
        $this->batchInsert('Product',
            ['id', 'title', 'price', 'idBrand'], [
                [1, 'DELL Laptop N5010 Allow Edition', 15512, 4],
                [2, 'Apple MacBook Pro Late 2015 13"', 29999, 1]
            ]
        );

        // Create taxonomy for this products
        $this->batchInsert('ProductTaxonomy', ['idProduct', 'idTaxonomy'], [
            [1, 3], // DELL is Laptop
            [2, 3], // MacBook is Laptop
        ]);

        // Default shop
        $this->insert('Shop', [
            'id' => 1,
            'title' => 'The RAM Sellre'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ProductTaxonomy');
        $this->dropTable('TaxonomyType');
        $this->dropTable('Taxonomy');
        $this->dropTable('Shop');
        $this->dropTable('Product');
        $this->dropTable('Brand');
        $this->dropTable('Email');
        $this->dropTable('Cart');
        $this->dropTable('CartItem');
        $this->dropTable('CartItemAttribute');
        $this->dropTable('OfferTarget');
        $this->dropTable('Offer');
        $this->dropTable('User');
        return true;
    }

}
