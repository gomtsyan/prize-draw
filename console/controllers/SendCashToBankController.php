<?php
namespace console\controllers;

use common\models\UserPresent;
use common\models\User;
use yii\console\Controller;

class SendCashToBankController extends Controller
{
    protected $userIds = array();
    protected $url = "http://localhost:8000/api/users/addMoney";

    public function actionSend($limit)
    {
        if(intval($limit)){
            $sendData = $this->getSendData();
            $limit = intval($limit);

            if($sendData && is_array($sendData))
            {
                $totalCount = count($sendData);
                $countOfSend = ceil($totalCount/$limit);
                $offset = 0;
                $i = 1;
                while ($i <= $countOfSend)
                {
                    $offset = ($i - 1)*$limit;
                    $currentSendData = array_slice($sendData, $offset, $limit);
                    $this->sendCurl($this->url, json_encode($currentSendData));
                    $i++;
                }
                $this->deductUsersAmount();
            }
        }else{
            echo "Argument is not correct";
        }
    }

    protected function deductUsersAmount()
    {
        $usersPresents = UserPresent::getPresentsByUserIds($this->userIds);

        if($usersPresents && is_array($usersPresents))
        {
            foreach($usersPresents as $userPresents)
            {
                $present = json_decode($userPresents->presents);

                if(is_object($present))
                {
                    foreach($present as $k=>$presentItem)
                    {
                        if($k == '1')
                        {
                            $presentItem->isReceived = 1;
                            $presentItem->count = 0;
                        }
                    }
                }
                $userPresents->presents = json_encode($present);
                try{
                    $userPresents->save();
                }catch (Exception $e){
                    echo $e->getMessage();
                }
            }
        }
    }

    protected function getSendData()
    {
        $model = new UserPresent();
        $usersPresents = $model::getUsersPresents();
        $sendData = array();

        if($usersPresents && is_array($usersPresents))
        {
            foreach($usersPresents as $userPresents)
            {
                if($userPresents && is_object($userPresents))
                {
                    $userPresentTypes = json_decode($userPresents->presents);

                    if(is_object($userPresentTypes) || is_array($userPresentTypes))
                    {
                        foreach($userPresentTypes as $k=>$userPresentType)
                        {
                            if($k == '1'){
                                if(!$userPresentType->isReceived){
                                    $sendData[] = [
                                        'email' => User::findIdentity($userPresents->userId)->email,
                                        'money' => $userPresentType->count
                                    ];
                                    $this->userIds[] = $userPresents->userId;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $sendData;
    }

    protected function sendCurl($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

}