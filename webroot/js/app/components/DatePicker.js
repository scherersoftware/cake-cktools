App.Components.DatePickerComponent = Frontend.Component.extend({
    setup: function ($elements) {
        $elements.each(function (i, element) {
            var $container = $(element);

            if ($container.data('datePickerApplied')) {
                return;
            }

            var $input = $container.find('input');
            var $inputContainer = $input.hide().parent();
            
            var pickerMarkup = '<div class="input-group date"><input type="text" class="form-control"/><span class="input-group-addon"><i class="fal fa-calendar-alt"></i></span></div>';
            $inputContainer.prepend(pickerMarkup);

            var type = $container.hasClass('dateTime') ? 'dateTime' : 'date';
            var format = 'DD.MM.YYYY';
            if (type === 'dateTime') {
                format = 'DD.MM.YYYY HH:mm';
            }

            $inputContainer.find('.input-group input[type=text]').blur(function (e) {
                // if only day and month were entered, make sure the current year is used.
                if (e.currentTarget.value.substring(6) === '0000') {
                    e.currentTarget.value = e.currentTarget.value.substring(0, 6) + new Date().getFullYear();
                    $(e.currentTarget).trigger('change');
                }
                // Year was entered in short form, prepend "20"
                else if (e.currentTarget.value.substr(6, 2) === '00') {
                    e.currentTarget.value = e.currentTarget.value.substring(0, 6) + '20' + e.currentTarget.value.substr(8, 2);
                    $(e.currentTarget).trigger('change');
                }
            });

            var $picker = $inputContainer.find('.input-group.date');
            $picker.datetimepicker({
                format: format,
                focusOnShow: !this.Controller.getVar('isMobile'),
                sideBySide: true,
                locale: this.Controller.getVar('locale') || 'en',
                daysOfWeekDisabled: this.Controller.getVar('daysOfWeekDisabled') || [],
                date: new Date($input.val()),
                icons: {
                    time: 'fal fa-clock',
                    date: 'fal fa-calendar',
                    up: 'fal fa-chevron-up',
                    down: 'fal fa-chevron-down',
                    previous: 'fal fa-chevron-left',
                    next: 'fal fa-chevron-right',
                    today: 'fal fa-calendar-alt',
                    clear: 'fal fa-trash',
                }
            });

            // Update the date input to the correct value after a datepicker change
            $picker.on('dp.change', function (e) {
                this._updateInput($input, $(e.currentTarget), e.date);
            }.bind(this));

            // Since dp.change is only called on blur, we need to make sure the date input is
            // updated as soon as the input is cleared.
            $picker.on('input', function (e) {
                if ($(e.currentTarget).find('input').val().length === 0) {
                    this._updateInput($input, $(e.currentTarget), null);
                }
            }.bind(this));

            // Initially update input
            this._updateInput($input, $picker, $picker.data('DateTimePicker').date());

            $container.data('datePickerApplied', true);
        }.bind(this));
    },
    _updateInput: function ($input, $picker, date) {
        var previousValue = $input.val();
        if ($picker.find('input').val().length === 0) {
            $input.val('');
        } else {
            var format = 'YYYY-MM-DD';
            if ($input.attr('type') !== 'date') {
                format = 'YYYY-MM-DDThh:mm';
            }

            $input.val(date.format(format));
        }

        if (previousValue !== $input.val()) {
            $input.change();
        }
    },
});
