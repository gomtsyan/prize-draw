<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userPresents".
 *
 * @property int $id
 * @property int $userId
 * @property string $presents
 *
 * @property User $user
 */
class UserPresent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userPresents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'presents'], 'required'],
            [['userId'], 'integer'],
            [['presents'], 'string'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'presents' => 'Presents',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    public static function getByUserId($userId)
    {
        return  static::findOne(['userId' => $userId]);
    }

    public static function getUsersPresents()
    {
        return  static::find()->all();
    }

    public static function getPresentsByUserIds($ids)
    {
        return  static::find()->where(['userId'=> $ids])->all();
    }

}
