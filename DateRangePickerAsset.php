<?php

namespace linjay\widgets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use Yii;

class DateRangePickerAsset extends AssetBundle
{
    public $sourcePath = '@aaa/assets/';

    public $css = [
        'css/daterangepicker.min.css',

    ];
    public $js = [
        'js/moment.min.js',
        'js/daterangepicker.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class
    ];

    public function init()
    {
        Yii::setAlias('@aaa', __DIR__);
//        $this->setSourcePath(__DIR__ . '/assets');
        parent::init();
    }
}
