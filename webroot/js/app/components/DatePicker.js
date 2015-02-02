App.Components.DatePickerComponent = Frontend.Component.extend({
    setup: function($elements) {
        $elements.each(function(i, element) {
            var $container = $(element);
            var $selectContainer = $container.find('select.form-control:first').parent();
            //$container.find('select.form-control').hide();
            
            var pickerMarkup = '<div class="input-group date"><input type="text" class="form-control"/><span class="input-group-addon"><i class="fa fa-calendar-o"></i></span></div>';
            $selectContainer.append(pickerMarkup);

            
            var type = $container.hasClass('dateTime') ? 'dateTime' : 'date';
            var format = 'DD.MM.YYYY';
            if(type === 'dateTime') {
                format = 'DD.MM.YYYY HH:mm';
            }

            var $picker = $selectContainer.find('.input-group.date');
            $picker.datetimepicker({
                format: format,
                sideBySide: true,
                locale: this.Controller.getVar('locale') || 'en'
            });
            $picker.on('dp.change', function(e) {
                var $p = $(e.currentTarget);
                var $container = $p.parents('.form-group');
                var type = $container.hasClass('dateTime') ? 'dateTime' : 'date';

                $container.find('select.form-control').each(function(i, el) {
                    var $select = $(el);
                    if($select.attr('name').indexOf('[year]') > -1) {
                        
                    }
                    
                });

            });
        }.bind(this));
    }
});