<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%presents}}`.
 */
class m190710_091450_create_presents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%presents}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'limitOption' => $this->text()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%presents}}');
    }
}
