<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\User;

class SiteController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Url::toRoute('site/user'), 302);
        }
        $loginForm = new LoginForm();
        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
            $this->redirect(Url::toRoute('site/user'), 302);
        }
        return $this->render('index', [
            'loginForm' => $loginForm,
        ]);
    }

    public function actionUser() {
        if (Yii::$app->user->isGuest) {
            $this->goHome();
        }
        $user = User::findIdentity(Yii::$app->getUser()->getId());
        return $this->render('user', [
            'username' => $user->getUsername()
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        $this->goHome();
    }
}
