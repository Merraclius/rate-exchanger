<?php

namespace app\assets;

use yii\web\AssetBundle;

class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $js = [
        'angular/angular.min.js',
        'angular-resource/angular-resource.min.js',
    ];
}