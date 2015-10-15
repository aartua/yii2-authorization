<?php

namespace app\models;

use Yii;
use app\components\SourceComponent;

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $authAttempts;
    public $authTimeUnlock;

    private static $source = false;

    public static function getSource()
    {
        if (self::$source === false) {
            self::$source = new SourceComponent();
        }
        return self::$source;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getAuthTimeUnlock()
    {
        return $this->authTimeUnlock;
    }

    public function validAuthAttempts()
    {
        return Yii::$app->params['authAttemptLimit'] > (int)$this->authAttempts;
    }

    public function validAuthTimeUnlock()
    {
        return time() > (int)$this->authTimeUnlock;
    }

    public function getSecondsAuthUnlock()
    {
        return (int)$this->authTimeUnlock > 0 ? (int)$this->authTimeUnlock - time() : 0;
    }

    public static function findIdentity($id)
    {
        $user = self::getSource()->findIdentity($id);
        return $user ? new static($user) : false;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::getSource()->findIdentityByAccessToken($token, $type);
        return $user ? new static($user) : false;
    }

    public static function findByUsername($username)
    {
        $user = self::getSource()->findByUsername($username);
        return $user ? new static($user) : false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public function loginSuccess($userId)
    {
        self::getSource()->loginSuccess($userId);
    }

    public function loginFail($userId)
    {
        self::getSource()->loginFail($userId);
    }
}
