<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Страница пользователя :: '.$username;
?>
<div class="site-userpage">
    <div class="form-group row">
        <h1>Добрый день, <?= Html::encode($username) ?>.</h1>
        <div>
            <?= Html::a('Выйти', Url::toRoute('/site/logout'), ['class' => 'btn btn-primary btn-small', 'name' => 'logout-button']) ?>
        </div>
    </div>
</div>
