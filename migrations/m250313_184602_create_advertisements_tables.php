<?php

use yii\db\Migration;

/**
 * Class m250313_184602_create_advertisements_tables
 */
class m250313_184602_create_advertisements_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица advertisements
        $this->createTable('{{%advertisements}}', [
            'id'                => $this->primaryKey(),
            'property_type'     => $this->string()->notNull(),
            'trade_type'        => $this->string()->notNull(),
            'source'            => $this->string()->notNull(),
            'realtor_phone'     => $this->string()->null(),
            'address'           => $this->string()->null(),
            'clean_description' => $this->text()->null(),
            'raw_description'   => $this->text()->null(),
            'property_name'     => $this->string()->null(),
            'locality'          => $this->string()->null(),
            'district'          => $this->string()->null(),
            'room_quantity'     => $this->integer()->null()->defaultValue(0),
            'property_area'     => $this->integer()->null()->defaultValue(0),
            'land_area'         => $this->integer()->null()->defaultValue(0),
            'condition'         => $this->string()->null(),
            'price'             => $this->integer()->null()->defaultValue(0),
            'created_at'        => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at'        => $this->dateTime()
                ->defaultExpression('CURRENT_TIMESTAMP')
                ->append('ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Индексы для таблицы advertisements
        $this->createIndex('idx-advertisements-property_type', '{{%advertisements}}', 'property_type');
        $this->createIndex('idx-advertisements-price', '{{%advertisements}}', 'price');
        $this->createIndex('idx-advertisements-updated_at', '{{%advertisements}}', 'updated_at');
        $this->createIndex('idx-advertisements-property_area', '{{%advertisements}}', 'property_area');

        // Таблица advertisement_images
        $this->createTable('{{%advertisement_images}}', [
            'id'               => $this->primaryKey(),
            'advertisement_id' => $this->integer()->notNull(),
            'image'            => $this->string()->notNull(),
        ]);

        // Внешний ключ, обеспечивающий каскадное удаление связанных изображений при удалении объявления
        $this->addForeignKey(
            'fk-advertisement_images-advertisement_id',
            '{{%advertisement_images}}',
            'advertisement_id',
            '{{%advertisements}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-advertisement_images-advertisement_id', '{{%advertisement_images}}');
        $this->dropTable('{{%advertisement_images}}');
        $this->dropTable('{{%advertisements}}');
    }
}
