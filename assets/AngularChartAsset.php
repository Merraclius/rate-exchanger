<?php

namespace app\assets;

use yii\web\AssetBundle;

class AngularChartAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/libs';

    public $js = [
        'Chart.js/2.7.0/Chart.bundle.min.js',
        'angular.chartjs/1.1.1/angular-chart.min.js'
    ];

    public $depends = [
        'app\assets\AngularAsset'
    ];
}