<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%userPresents}}`.
 */
class m190710_091852_create_userPresents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%userPresents}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'presents' => $this->text()->notNull(),
        ]);

        $this->createIndex(
            'idx-userPresents-userId',
            '{{%userPresents}}',
            'userId'
        );

        $this->addForeignKey(
            'fk-userPresents-userId',
            '{{%userPresents}}',
            'userId',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey(
            'fk-userPresents-userId',
            '{{%userPresents}}'
        );

        $this->dropIndex(
            'idx-userPresents-userId',
            '{{%userPresents}}'
        );

        $this->dropTable('{{%userPresents}}');
    }
}
