<?php

namespace frontend\controllers;

use \yii\web\Controller;

class MyFrontController extends Controller
{

    public $present;

    public function init()
    {
        //echo "<pre>"; var_dump($this); die;


    }

    public function goLogin()
    {

        return $this->redirect('/site/login');

    }


}
