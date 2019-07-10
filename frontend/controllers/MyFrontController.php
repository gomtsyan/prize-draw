<?php

namespace frontend\controllers;

use \yii\web\Controller;

class MyFrontController extends Controller
{

    public $coefficientMtoP = 0.2;

    protected $present = array();

    public function init()
    {
        //echo "<pre>"; var_dump($this); die;

    }

    public function convertMoneyToPoints($money)
    {
        $points = 0;

        if($money < 0.01 and $money > -0.01) return 0;

        $points = $money * $this->coefficientMtoP;

        $points = round($points, 2);

        $points = number_format($points, 2, '.', '');

        return $points;
    }

    public function goLogin()
    {
        return $this->redirect('/site/login');
    }


}
