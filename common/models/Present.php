<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "presents".
 *
 * @property int $id
 * @property string $name
 * @property string $limitOption
 */
class Present extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'presents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['limitOption'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'limitOption' => 'Limit Option',
        ];
    }
}
