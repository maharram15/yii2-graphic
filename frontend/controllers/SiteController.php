<?php

namespace frontend\controllers;

use backend\models\ServerMonitoring;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
        $lastWeek = date("Y-m-d", strtotime("-7 days"));
        $getOnline = ServerMonitoring::find()->select(['date'])->distinct("date")->where(['>=', 'date', $lastWeek])->orderBy(['date' => SORT_ASC])->all();
        $model = new ServerMonitoring();
        $value = [5, 100, 150, 200, 250, 300, 350, 400, 450, 500, 5, 100, 150, 43, 54, 53, 111, 141, 450, 500, 100, 150, 200, 250, 300, 350, 400, 450, 500, 5, 100, 150, 43, 54, 53, 111, 141, 450, 500];
        $value2 = [];
        $value3 = [];
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $yesterdayData = ServerMonitoring::find()->where(['date' => $yesterday])->max("online");
        // $max = ServerMonitoring::find()->where(['date' => date("Y-m-d")])->max('online');
        $lastID = ServerMonitoring::find()->where(['date' => date("Y-m-d")])->max('id');
        $getTime = ServerMonitoring::find()->where(['id' => $lastID])->one();
        $data = ServerMonitoring::find()->where(['date' => date("Y-m-d")])->andWhere(["server" => 1])->orderBy(["time" => SORT_ASC])->all();
        $data2 = ServerMonitoring::find()->where(['date' => date("Y-m-d")])->andWhere(["server" => 2])->orderBy(["time" => SORT_ASC])->all();
        $data3 = ServerMonitoring::find()->where(['date' => date("Y-m-d")])->andWhere(["server" => 3])->orderBy(["time" => SORT_ASC])->all();
        $id = 0;
        $lastOnline = ServerMonitoring::find()->where(['date' => date("Y-m-d"), "time" => "00:00"])->all();
        for ($i = 0; $i < count($data); $i++) {
            array_push($value, $data[$i]['online']);
        }
        for ($a = 0; $a < count($data2); $a++) {
            array_push($value2, $data2[$a]['online']);
        }
        for ($z = 0; $z < count($data3); $z++) {
            array_push($value3, $data3[$z]['online']);
        }
        return $this->render('index', [
            'value' => $value,
            // 'max' => $max,
            'value2' => $value2,
            'value3' => $value3,
            'options' => $getOnline,
            'id' => $id,
            'lastOnline' => $lastOnline,
            'getTime' => $getTime['time']
        ]);
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

        return $this->goHome();
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
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
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
            return $this->goHome();
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

    public function actionGetgraphic()
    {
        $this->layout = false;
        $model = new ServerMonitoring();
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request;
        if (Yii::$app->request->isAjax && $post->post("graphicRequest") == true) {
            $max = ServerMonitoring::find()->where(['date' => $post->post("graphicData")])->max('online');
            $graphic = ServerMonitoring::find()->where(['date' => $post->post("graphicData")])->andWhere(["server" => 1])->orderBy(["time" => SORT_ASC])->all();
            $graphic2 = ServerMonitoring::find()->where(['date' => $post->post("graphicData")])->andWhere(["server" => 2])->orderBy(["time" => SORT_ASC])->all();
            $graphic3 = ServerMonitoring::find()->where(['date' => $post->post("graphicData")])->andWhere(["server" => 3])->orderBy(["time" => SORT_ASC])->all();
            $graphArray1 = [];
            $graphArray2 = [];
            $graphArray3 = [];
            $date = date_create($post->post("graphicData"));
            $interval = date_interval_create_from_date_string('1 day');
            $res = date_add($date, $interval);
            $getLastOnline1 = ServerMonitoring::find()->where(['date' => $res->format('Y-m-d'), 'time' => "00:00"])->andWhere(["server" => 1])->one();
            $getLastOnline2 = ServerMonitoring::find()->where(['date' => $res->format('Y-m-d'), 'time' => "00:00"])->andWhere(["server" => 2])->one();
            $getLastOnline3 = ServerMonitoring::find()->where(['date' => $res->format('Y-m-d'), 'time' => "00:00"])->andWhere(["server" => 3])->one();

            for ($i = 0; $i < count($graphic); $i++) {
                array_push($graphArray1, $graphic[$i]['online']);
            }
            for ($a = 0; $a < count($graphic2); $a++) {
                array_push($graphArray2, $graphic2[$a]['online']);
            }
            for ($z = 0; $z < count($graphic3); $z++) {
                array_push($graphArray3, $graphic3[$z]['online']);
            }
            if ($graphic) {
                $model->refresh();
                $text = $this->render('index', [
                    'graphic' => $graphArray1,
                    'graphic2' => $graphArray2,
                    'graphic3' => $graphArray3,
                    'getLastOnline1' => $getLastOnline1,
                    'getLastOnline2' => $getLastOnline2,
                    'getLastOnline3' => $getLastOnline3
                ]);
                return [
                    'html' => $text,
                ];
            }
        }
    }
}
