<?php

namespace linjay\widgets;


use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;

class DateRangePicker extends InputWidget
{
    public $options = ['class' => 'form-control'];

    public $pluginOptions = [
        'autoUpdateInput' => false,
        'locale' => [
            'format' => 'Y-MM-DD',
            'separator' => ' - ',
        ],
        'ranges' => [
            'Today' => ["moment()", "moment()"],
            'Yesterday' => ["moment().subtract(1, 'days')", "moment().subtract(1, 'days')"],
            'Last 7 Days' => ["moment().subtract(6, 'days')", "moment()"],
            'Last 30 Days' => ["moment().subtract(29, 'days')", "moment()"],
            'This Month' => ["moment().startOf('month')", "moment().endOf('month')"],
            'Last Month' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"]
        ],
    ];

    public $callback;

    public $convertFormat = false;

    public function run()
    {
        $this->registerAssets();
        $this->registerScripts();
        $this->initializeOptions();
        echo $this->renderInputHtml('text');
    }

    public function initializeOptions()
    {
        if ($this->convertFormat && isset($this->pluginOptions['locale']['format'])) {
            $this->pluginOptions['locale']['format'] = static::convertDateFormat(
                $this->pluginOptions['locale']['format']
            );
        }
        $startDate = $this->pluginOptions['startDate'] ?? date($this->pluginOptions['locale']['format']);
        $endDate = $this->pluginOptions['endDate'] ?? date($this->pluginOptions['locale']['format'], time() + 86400);
        $this->pluginOptions = ArrayHelper::merge($this->pluginOptions, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function renderInputHtml($type)
    {
        $value = '';
        if (isset($this->pluginOptions['startDate']) && isset($this->pluginOptions['endDate'])) {
            $value = "{$this->pluginOptions['startDate']}{$this->pluginOptions['locale']['separator']}{$this->pluginOptions['endDate']}";
        }
        if ($this->hasModel()) {
            return Html::activeInput($type, $this->model, $this->attribute, ArrayHelper::merge($this->options, ['value' => $value]));
        }
        return Html::input($type, $this->name, $this->value, ArrayHelper::merge($this->options, ['value' => $value]));
    }

    public function registerAssets()
    {
        $view = $this->getView();
        DateRangePickerAsset::register($view);
    }

    public function registerScripts()
    {
        $view = $this->getView();
        $selector = $this->options['id'];
        $pluginOptions = Json::encode($this->pluginOptions);
        $callbackSeparator = $this->callback ? ', ' : '';
        $js = "$('#{$selector}').daterangepicker({$pluginOptions}{$callbackSeparator}{$this->callback});";
        $js .= "$('#{$selector}').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('{$this->pluginOptions['locale']['format']}') + 
            '{$this->pluginOptions['locale']['separator']}' + picker.endDate.format('{$this->pluginOptions['locale']['format']}'));
        });";
        $view->registerJs($js);
    }

    protected static function convertDateFormat($format)
    {
        return strtr($format, [
            // meridian lowercase
            'a' => 'p',
            // meridian uppercase
            'A' => 'P',
            // second (with leading zeros)
            's' => 'ss',
            // minute (with leading zeros)
            'i' => 'ii',
            // hour in 12-hour format (no leading zeros)
            'g' => 'H',
            // hour in 24-hour format (no leading zeros)
            'G' => 'h',
            // hour in 12-hour format (with leading zeros)
            'h' => 'HH',
            // hour in 24-hour format (with leading zeros)
            'H' => 'hh',
            // day of month (no leading zero)
            'j' => 'd',
            // day of month (two digit)
            'd' => 'dd',
            // day name short is always 'D'
            // day name long
            'l' => 'DD',
            // month of year (no leading zero)
            'n' => 'm',
            // month of year (two digit)
            'm' => 'mm',
            // month name short is always 'M'
            // month name long
            'F' => 'MM',
            // year (two digit)
            'y' => 'yy',
            // year (four digit)
            'Y' => 'yyyy',
        ]);
    }
}