<?php
namespace frontend\controllers;

use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Present;
use common\models\UserPresent;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends MyFrontController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goLogin();
        }

        return $this->render('index');
    }

    protected function getRandomMoney($limitMoney, $name)
    {
        if($limitMoney > 0)
        {
            $maxMoney = ($limitMoney*rand(1, 5))/100; //take a maximum of 5% of the total amount
            if($maxMoney > 1)
            {
                if(is_float($maxMoney))
                {
                    $maxMoney = floor($maxMoney);
                }
                $this->present[$name] = rand(1, $maxMoney);
            }
        }
        return $this->present;
    }

    protected function getRandomThing($things, $name)
    {
        if((is_array($things) || is_object($things)) && count($things) > 0)
        {
            $element = false;
            if(is_array($things))
            {
                $randomKey = array_rand($things,1);
                $element = $things[$randomKey];
            }
            else if(is_object($things))
            {
                $thingsArray = array_values((array)$things);
                $randomKey = array_rand($thingsArray,1);
                $element = $thingsArray[$randomKey];
            }

            $this->present[$name] = $element;
        }
        return $this->present;
    }

    protected function getRandomPoints($name)
    {
        $randomPoints = rand(1, 25);
        $this->present[$name] = $randomPoints;

        return $this->present;
    }

    public function actionStart()
    {
        if(Yii::$app->request->post('start'))
        {
            $presents = Present::find()->all();

            if($presents && is_array($presents))
            {
                foreach($presents as $present)
                {
                    $limitOption = json_decode($present->limitOption);

                    switch ($present->name)
                    {
                        case 'money':
                            if($limitOption->limitMoney && $present->name)
                            {
                                $this->getRandomMoney($limitOption->limitMoney, $present->name);
                            }
                            break;
                        case 'thing':
                            if($limitOption->items && $present->name)
                            {
                                $this->getRandomThing($limitOption->items, $present->name);
                            }

                            break;
                        case 'points':
                            if($present->name)
                            {
                                $this->getRandomPoints($present->name);
                            }
                            break;
                    }
                }
            }
            if(!empty($this->present))
            {
                $randomPresentKey = array_rand($this->present,1);
                echo json_encode(['type'=>$randomPresentKey, 'present'=>$this->present[$randomPresentKey]]);
            }else{
                echo json_encode(['status'=>'error']);
            }
            exit;
        }
    }

    public function actionSavePresent()
    {
        if(Yii::$app->request->post())
        {
            $userId = Yii::$app->request->post('userId');
            $present = Yii::$app->request->post('present');
            $presentName = Yii::$app->request->post('presentType');
            $convertToPoints = Yii::$app->request->post('type');

            if($convertToPoints)
            {
                $convertedPoints = $this->convertMoneyToPoints($present);
                if($convertedPoints)
                {
                    $oldPresent = $present;
                    $present = $convertedPoints;
                    $presentName = 'points';
                }
            }

            $presentId = Present::getIdByName($presentName);
            $userPresent = UserPresent::getByUserId($userId);

            if($userPresent)
            {
                if($userPresent->presents)
                {
                    $presents = json_decode($userPresent->presents);

                    if(is_object($presents) || is_array($presents))
                    {
                        $issetPresentItem = false;

                        foreach($presents as $idPresent => $presentItem)
                        {
                            if($idPresent == $presentId)
                            {
                                $issetPresentItem = true;
                                if($presentName == 'thing')
                                {
                                    array_push($presentItem->items, $present);
                                }
                                else
                                {
                                    $presentItem->count += $present;
                                }

                            }
                        }

                        if(!$issetPresentItem)
                        {
                            $presents = (array) $presents;

                            switch ($presentName)
                            {
                                case 'money':
                                    $presents[$presentId] = ['isReceived'=>0, 'count'=>$present];
                                    break;
                                case 'points':
                                    $presents[$presentId] = ['count'=>$present];
                                    break;
                                case 'thing':
                                    $presents[$presentId] = ['isReceived'=>0, 'items'=>[$present]];
                                    break;
                            }
                        }
                    }
                    else
                    {
                        echo json_encode(['error'=>'Something went wrong']);
                        exit;
                    }

                    $userPresent->presents = json_encode($presents);

                }
                else
                {
                    echo json_encode(['error'=>'Something went wrong']);
                    exit;
                }
            }
            else
            {
                $presents = array();
                switch ($presentName)
                {
                    case 'money':
                        $presents[$presentId] = ['isReceived'=>0, 'count'=>$present];
                        break;
                    case 'thing':
                        $presents[$presentId] = ['isReceived'=>0, 'items'=>[$present]];
                        break;
                    case 'points':
                        $presents[$presentId] = ['count'=>$present];
                        break;
                }
                $userPresent = new UserPresent();
                $userPresent->userId = $userId;
                $userPresent->presents = json_encode($presents);
            }

            if ($userPresent->save())
            {
                if(isset($oldPresent) && $oldPresent)
                {
                    $presentName = 'money';
                    $present = $oldPresent;
                }

                if($presentName != 'points')
                {
                    $this->deductAmount($presentName, $present);
                }
                else
                {
                    echo json_encode(['message'=>'Present successfully added']);
                }
            }
            else
            {
                echo json_encode(['error'=>'Something went wrong']);
            }
            exit;
        }
    }

    protected function deductAmount($presentName, $present)
    {
        $presentObj = Present::getPresentByName($presentName);

        if($presentObj && is_object($presentObj))
        {
            if($presentObj->limitOption)
            {
                $limitOption = json_decode($presentObj->limitOption);

                if($limitOption && is_object($limitOption))
                {
                    switch ($presentName)
                    {
                        case 'money':
                            if(isset($limitOption->limitMoney) && $limitOption->limitMoney)
                            {
                                $limitOption->limitMoney -= $present;
                            }
                            break;
                        case 'thing':
                            if(isset($limitOption->items) && $limitOption->items)
                            {
                                if(is_object($limitOption->items))
                                {
                                    $limitOption->items = array_values((array)$limitOption->items);
                                }
                                if (($key = array_search($present, $limitOption->items)) !== false) {
                                    unset($limitOption->items[$key]);
                                    $limitOption->items = array_values($limitOption->items);
                                }
                            }
                            break;
                    }

                    $presentObj->limitOption = json_encode($limitOption);

                    if($presentObj->save())
                    {
                        echo json_encode(['message'=>'Present successfully added']);
                    }
                    else
                    {
                        echo json_encode(['error'=>'Something went wrong']);
                    }
                }
                else
                {
                    echo json_encode(['error'=>'Something went wrong']);
                }
            }
            else
            {
                echo json_encode(['error'=>'Something went wrong']);
            }
        }
        else
        {
            echo json_encode(['error'=>'Something went wrong']);
        }
        exit;
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();


        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        } else {

            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goLogin();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goLogin();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
