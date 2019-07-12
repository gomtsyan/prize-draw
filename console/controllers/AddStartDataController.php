<?php
namespace console\controllers;

use common\models\Present;
use yii\base\Model;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

class AddStartDataController extends Controller
{

    public function actionCreate()
    {
        $data = [
            ['name'=>'money', 'limitOption'=>'{"limitMoney":50000}'],
            ['name'=>'thing', 'limitOption'=>'{"items":["item1", "item2", "item3", "item4", "item5", "item6", "item7", "item8"]}'],
            ['name'=>'points', 'limitOption'=>Null],
        ];

        foreach($data as $name=>$value)
        {
            $model = new Present();
            $model->name = $value['name'];
            $model->limitOption = $value['limitOption'];
            $model->save();
        }

        echo 'initial data added successfully';
    }
}