## DateRangePicker (Yii2 extension)
This is a simple adaptation of [Bootstrap Date Range Picker](http://www.daterangepicker.com) for Yii2 applications.
#### Requirements
* Yii2 (yiisoft/yii2 v.2.0.0 or higher)
* Yii2-Bootstrap (yiisoft/yii2-bootstrap v.2.0.0 or higher) (bootstrap 3.3.x, not optimized for Bootstrap 4.x)
* Font Awesome Icons (You can change icons' classes to yours if you don't use FA)

#### Installation 
``

#### Usage
##### Simple usage ()with default configuration)
```php
// With model
<?= $form->field($model, 'time')->label('Time')->widget(\linjay\widgets\DateRangePicker::class, [])?>

// Without model
<?= \linjay\widgets\DateRangePicker::widget([])?>
```
##### Usage with model:
```php
<?= $form->field($model, 'time')->label('Time')->widget(\linjay\widgets\DateRangePicker::class, [
    'presetDropdown' => false, //render as dropdown with hidden input or text input with addon
    'calendarIconClass' => 'fa fa-calendar', // calendar icon CSS class
    'caretIconClass' => 'fa fa-caret-down', // caret icon CSS class (for dropdown)
    'pluginOptions' => [ // presets for Date Range Picker javascript library
        'autoUpdateInput' => false,
        'startDate'=> '10/01/2018',
        'endDate' => '10/31/2018',
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
            'Last Month' => [
                "moment().subtract(1, 'month').startOf('month')",
                 "moment().subtract(1, 'month').endOf('month')"
            ]
        ],
    ],
    'pluginEvents' => [
        'cancel.daterangepicker' => "function() {
            $(this).val('');
        }",
    ],
    // callback function (if needed)
    'callback' => "function(start, end, label) {
         console.log('New date range selected: ' + 
         start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + 
         ' (predefined range: ' + label + ')');
    }"
]) ?>
```
##### Usage without model:
```php
<?= \linjay\widgets\DateRangePicker::widget([
    'presetDropdown' => true, //render as dropdown with hidden input or text input with addon
    'calendarIconClass' => 'fa fa-calendar', // calendar icon CSS class
    'caretIconClass' => 'fa fa-caret-down', // caret icon CSS class (for dropdown)
    'pluginOptions' => [ // presets for Date Range Picker javascript library
        'autoUpdateInput' => false,
        'startDate'=> '10/01/2018',
        'endDate' => '10/31/2018',
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
            'Last Month' => [
                "moment().subtract(1, 'month').startOf('month')",
                 "moment().subtract(1, 'month').endOf('month')"
            ]
        ],
    ],
    'pluginEvents' => [
        'cancel.daterangepicker' => "function() {
          $(this).val('');
        }",
    ],
    // callback function (if needed)
    'callback' => "function(start, end, label) {
         console.log('New date range selected: ' + 
         start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + 
         ' (predefined range: ' + label + ')');
    }"
])?>
```
