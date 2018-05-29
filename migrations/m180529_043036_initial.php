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
            'slug' => $this->integer(),
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ProductTaxonomy');
        $this->dropTable('TaxonomyType');
        $this->dropTable('Taxonomy');
        $this->dropTable('Product');
        $this->dropTable('Brand');
        $this->dropTable('Email');
        $this->dropTable('User');
        return true;
    }

}
