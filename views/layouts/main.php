<?php

use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\icons\Icon;

use app\assets\AppAsset;
use app\widgets\Alert;

/* @var \yii\web\View $this */
/* @var string $content */

AppAsset::register($this);

Icon::map($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <nav class="header container navbar sticky-top">
        <? if (Yii::$app->user->isGuest) { ?>
            <?= AuthChoice::widget([
                'baseAuthUrl' => ['site/auth'],
                'popupMode' => true,
                'options' => [
                    'class' => 'auth-widget-container'
                ]
            ]); ?>
        <? } else { ?>
            <span class="navbar-brand">Logged as <?= Yii::$app->user->identity->username ?></span>
            <span class="navbar-text">
              <a href="site/logout">Logout</a>
            </span>
        <? } ?>
    </nav>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
