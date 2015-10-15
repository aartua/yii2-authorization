<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user && !$user->validAuthTimeUnlock()) {
                $seconds = $this->getUser()->getSecondsAuthUnlock();
                $this->addError($attribute, 'Попробуйте ещё раз через '.$seconds.' секунд.');
            }
            if (!$this->hasErrors() && (!$user || !$user->validatePassword($this->password))) {
                $this->addError($attribute, 'Неверные данные.');
                if ($user) {
                    $user->loginFail($this->getUser()->getId());
                }
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->loginSuccess($this->getUser()->getId());
            return Yii::$app->user->login($user);
        }
        return false;
    }
}
