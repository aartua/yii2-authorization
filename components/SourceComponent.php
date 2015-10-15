<?php

namespace app\components;

use Yii;

class SourceComponent extends \yii\base\Component
{
    private static $users = false;

    public static function getUsers() {
        if (self::$users === false) {
            $path = self::getSourcePath();
            if (is_file($path)) {
                $content = file_get_contents($path);
                if ($content) {
                    self::$users = json_decode($content, true);
                }
            }
        }
        return self::$users;
    }

    public static function getSourcePath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.Yii::$app->params['usersStorage'];
    }

    public static function findIdentity($id)
    {
        $users = self::getUsers();
        return isset($users[$id]) ? $users[$id] : false;
    }

    public static function findByUsername($username)
    {
        $users = self::getUsers();
        if (is_array($users)) {
            foreach ($users as $user) {
                if (strcasecmp($user['username'], $username) === 0) {
                    return $user;
                }
            }
        } else {
            throw new \Exception('The data source was not defined.');
        }
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $users = self::getUsers();
        if (is_array($users)) {
            foreach ($users as $user) {
                if ($user['accessToken'] === $token) {
                    return $user;
                }
            }
        } else {
            throw new \Exception('The data source was not defined.');
        }
    }

    public function loginSuccess($userId)
    {
        if (isset(self::$users[$userId])) {
            self::$users[$userId]['authAttempts'] = 0;
            self::$users[$userId]['authTimeUnlock'] = 0;
            self::updateUsers();
        }
    }

    public function loginFail($userId)
    {
        if (isset(self::$users[$userId])) {
            if (!isset(self::$users[$userId]['authAttempts'])) {
                self::$users[$userId]['authAttempts'] = 0;
            }
            if (!isset(self::$users[$userId]['authTimeUnlock'])) {
                self::$users[$userId]['authTimeUnlock'] = 0;
            }
            if (self::$users[$userId]['authTimeUnlock'] > 0) {
                self::$users[$userId]['authAttempts'] = 0;
                self::$users[$userId]['authTimeUnlock'] = 0;
            }
            ++self::$users[$userId]['authAttempts'];
            if (self::$users[$userId]['authAttempts'] == Yii::$app->params['authAttemptLimit']) {
                self::$users[$userId]['authTimeUnlock'] = time() + Yii::$app->params['authSecondsLock'];
            }
            self::updateUsers();
        }
    }

    public static function updateUsers()
    {
        $result = false;
        if (is_array(self::$users)) {
            $path = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.Yii::$app->params['usersStorage']);
            $data = json_encode(self::$users);
            if ($data !== false) {
                $result = file_put_contents($path, $data) !== false;
            }
        }
        return $result;
    }
}
