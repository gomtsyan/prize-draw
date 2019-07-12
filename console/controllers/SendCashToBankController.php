<?php
namespace console\controllers;

use common\models\UserPresent;
use common\models\User;
use yii\console\Controller;

class SendCashToBankController extends Controller
{

    public function actionSend()
    {
        $model = new UserPresent();
        $presents = $model::getPresents();
        $sendData = array();

        if($presents && is_array($presents))
        {
            foreach($presents as $present)
            {
                if($present && is_object($present))
                {

                    $presentTypes = json_decode($present->presents);

                    if(is_object($presentTypes) || is_array($presentTypes))
                    {
                        foreach($presentTypes as $k=>$presentType)
                        {
                            if($k == '1'){
                                if(!$presentType->isReceived){
                                    $sendData[] = ['email' => User::findIdentity($present->userId)->email,
                                                    'money' => $presentType->count
                                                ];
                                }
                            }
                        }
                    }
                }
            }
        }

        $url = "'http://localhost:8000/api/users/addMoney'";

        foreach($sendData as $i=>$data)
        {
            $this->send($url, json_encode($data));
        }
    }

    protected function send($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Connection: Keep-Alive'
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $returned = curl_exec($ch);
        curl_close ($ch);

        return $returned;
    }

}