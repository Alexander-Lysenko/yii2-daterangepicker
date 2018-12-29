<?php

namespace linjay\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class DateRangePicker extends InputWidget
{
    public $options = ['class' => 'form-control'];
    public $pluginOptions = [];
    public $pluginEvents = [];
    public $defaultPluginOptions = [
        'autoUpdateInput' => false,
        'alwaysShowCalendars' => true,
        'opens' => 'left',
        'locale' => [
            'format' => 'MM/DD/YYYY',
            'separator' => ' - ',
        ],
        'ranges' => [
            'Today' => ['moment()', 'moment()'],
            'Yesterday' => ["moment().subtract(1, 'days')", "moment().subtract(1, 'days')"],
            'Last 7 Days' => ["moment().subtract(6, 'days')", "moment()"],
            'Last 30 Days' => ["moment().subtract(29, 'days')", "moment()"],
            'This Month' => ["moment().startOf('month')", "moment().endOf('month')"],
            'Last Month' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"]
        ],
    ];
    public $callback;
    public $presetDropdown = false;
    public $calendarIconClass = 'fa fa-calendar';
    public $caretIconClass = 'fa fa-caret-down';


    /* ========== METHODS ========== */
    public function run()
    {
        // Non-recursive merge plugin option
        $this->pluginOptions = array_merge($this->defaultPluginOptions, $this->pluginOptions);

        $this->formatRanges();
        $this->setStartValue();
        $this->registerAssets();
        $this->registerScripts();
        echo $this->renderInputHtml($this->presetDropdown ? 'hidden' : 'text');
    }

    public function registerAssets()
    {
        $view = $this->getView();
        DateRangePickerAsset::register($view);
    }

    private function registerScripts()
    {
        $view = $this->getView();
        $selector = $this->hasModel() ? "daterangepicker-{$this->options['id']}" : "daterangepicker-{$this->name}";
        $pluginOptions = Json::encode($this->pluginOptions);
        $callbackSeparator = $this->callback ? ', ' : '';
        $separator = 'picker.locale.separator';
        $startDateFormat = 'picker.startDate.format(picker.locale.format)';
        $endDateFormat = 'picker.endDate.format(picker.locale.format)';
        $js = "$('#{$selector}').daterangepicker({$pluginOptions}{$callbackSeparator}{$this->callback});";
        $js .= "$('#{$selector}').on('apply.daterangepicker', function(ev, picker) {" .
            "$(this).find('input').val({$startDateFormat} + {$separator} + {$endDateFormat}).trigger('change');
            $(this).find('.daterange-value').text({$startDateFormat} + {$separator} + {$endDateFormat});
        });";
        if (!empty($this->pluginEvents)) {
            foreach ($this->pluginEvents as $event => $handler) {
                $function = $handler instanceof JsExpression ? $handler : new JsExpression($handler);
                $js .= "$('#{$selector}').on('{$event}', {$function});\n";
            }
        }
        $view->registerJs($js);
        $style = '.daterange-value {
            display: inline-block; 
            white-space: nowrap; 
            overflow: hidden; 
            width: calc(100% - 35px); 
            text-overflow: ellipsis;
        }';
        $view->registerCss($style);
    }

    protected function setStartValue()
    {
        $value = $this->hasModel() ? $this->value : '';
        if (isset($this->pluginOptions['startDate']) && isset($this->pluginOptions['endDate'])) {
            $value = $this->pluginOptions['startDate'] .
                ($this->pluginOptions['locale']['separator'] ?? ' - ') .
                $this->pluginOptions['endDate'];
        }
        $this->options = ArrayHelper::merge($this->options, ['value' => $value]);
    }

    private function formatRanges()
    {
        $rawRanges = $this->pluginOptions['ranges'];
        $ranges = $rawRanges;
        foreach ($ranges as $range => $keys) {
            foreach ($keys as $key => $rangeValue) {
                $ranges[$range][$key] = new JsExpression($rangeValue);
            }
        }
        $this->pluginOptions['ranges'] = $ranges;
    }

    protected function renderInputHtml($type)
    {
        $this->options = array_merge($this->options, ['autocomplete' => 'off']);
        $input = $this->hasModel() ? Html::activeInput($type, $this->model, $this->attribute, $this->options) :
            Html::input($type, $this->name, $this->value, $this->options);
        $dropDownContainer = Html::beginTag('div', ['class' => 'dropdown',
                'id' => $this->hasModel() ? "daterangepicker-{$this->options['id']}" : "daterangepicker-{$this->name}"
            ]) .
            Html::beginTag('div', [
                'class' => 'form-control dropdown-toggle',
                'data-toggle' => 'dropdown',
                'aria-expanded' => 'false'
            ]) .
            Html::tag('span', '', [
                'class' => $this->calendarIconClass,
                'style' => 'float:left; margin-top:.15em; padding-right: 5px'
            ]) .
            Html::tag('span', $this->value ?: 'Select range...', [
                'class' => 'daterange-value'
            ]) .
            Html::tag('span', '', [
                'class' => $this->caretIconClass,
                'style' => 'float:right; margin-top:.2em; padding-left:5px']) .
            Html::endTag('div') .
            $input .
            Html::endTag('div');
        $inputContainer = Html::beginTag('div', ['class' => 'input-group',
                'id' => $this->hasModel() ? "daterangepicker-{$this->options['id']}" : "daterangepicker-{$this->name}"
            ]) .
            Html::beginTag('div', ['class' => 'input-group-addon']) .
            Html::tag('span', '', ['class' => $this->calendarIconClass]) .
            Html::endTag('div') .
            $input .
            Html::endTag('div');
        return $this->presetDropdown ? $dropDownContainer : $inputContainer;
    }
}