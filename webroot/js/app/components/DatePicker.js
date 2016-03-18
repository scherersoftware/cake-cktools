App.Components.DatePickerComponent = Frontend.Component.extend({
    setup: function($elements) {
        $elements.each(function(i, element) {
            var $container = $(element);

            if ($container.data('datePickerApplied')) {
                return;
            }

            var $selectContainer = $container.find('select:first').parent();
            // Check if the new Markup structure of CakePHP is present and react accordingly
            if ($selectContainer.hasClass('year')) {
                $selectContainer = $selectContainer.parent().parent();
                $selectContainer.find('ul.list-inline').hide();
            } else {
                $container.find('select').hide();
            }

            var pickerMarkup = '<div class="input-group date"><input type="text" class="form-control"/><span class="input-group-addon"><i class="fa fa-calendar-o"></i></span></div>';
            $selectContainer.append(pickerMarkup);

            var type = $container.hasClass('dateTime') ? 'dateTime' : 'date';
            var format = 'DD.MM.YYYY';
            if(type === 'dateTime') {
                format = 'DD.MM.YYYY HH:mm';
            }

            $selectContainer.find('input[type=text]').blur(function (e) {
                // if only day and month were entered, make sure the current year is used.
                if (e.currentTarget.value.substring(6) == '0000') {
                    e.currentTarget.value = e.currentTarget.value.substring(0, 6) + moment().year();
                    $(e.currentTarget).trigger('change');
                }
                // Year was entered in short form, prepend "20"
                else if (e.currentTarget.value.substr(6, 2) == '00') {
                    e.currentTarget.value = e.currentTarget.value.substring(0, 6) + '20' + e.currentTarget.value.substr(8, 2);
                    $(e.currentTarget).trigger('change');
                }
            });

            var $picker = $selectContainer.find('.input-group.date');
            $picker.datetimepicker({
                format: format,
                focusOnShow: !this.Controller.getVar('isMobile'),
                sideBySide: true,
                locale: this.Controller.getVar('locale') || 'en',
                date: this._getDateFromSelects($container),
                icons: {
                    time: 'fa fa-clock',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-o',
                    clear: 'fa fa-trash'
                }
            });


            // Update the selects to the correct values after a datepicker change
            $picker.on('dp.change', function(e) {
                var $container = $(e.currentTarget).parents('.form-group');
                this._updateSelects($container, e.date, $(e.currentTarget));
            }.bind(this));

            $container.data('datePickerApplied', true);
        }.bind(this));
    },
    _updateSelects: function($selectContainer, date, input) {
        if (input.find('input').val().length == 0) {
            $selectContainer.find('select option[selected="selected"]').each(function(i, el) {
                $(el).removeAttr("selected");
            });
            return null;
        }
        $selectContainer.find('select').each(function(i, el) {
            var $select = $(el);
            var previousValue = $select.val();
            if($select.attr('name').indexOf('[year]') > -1) {
                $select.val(date.year());
            }
            if($select.attr('name').indexOf('[month]') > -1) {
                $select.val(date.format('MM'));
            }
            if($select.attr('name').indexOf('[day]') > -1) {
                $select.val(date.format('DD'));
            }
            if($select.attr('name').indexOf('[hour]') > -1) {
                $select.val(date.format('HH'));
            }
            if($select.attr('name').indexOf('[minute]') > -1) {
                $select.val(date.format('mm'));
            }
            if (previousValue != $select.val()) {
                $select.change();
            }
        });
    },
    _getDateFromSelects: function($selectContainer) {
        var date = moment();
        $selectContainer.find('select').each(function(i, el) {
            var $select = $(el);
            var val = parseInt($select.val(), 10);
            if($select.attr('name').indexOf('[year]') > -1) {
                date.year(val);
            }
            if($select.attr('name').indexOf('[month]') > -1) {
                date.month(val - 1);
            }
            if($select.attr('name').indexOf('[day]') > -1) {
                date.date(val);
            }
            if($select.attr('name').indexOf('[hour]') > -1) {
                date.hour(val);
            }
            if($select.attr('name').indexOf('[minute]') > -1) {
                date.minute(val);
            }
        });
        return date;
    }
});
