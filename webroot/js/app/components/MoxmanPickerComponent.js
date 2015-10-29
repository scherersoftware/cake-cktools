App.Components.MoxmanPickerComponent = Frontend.Component.extend({
    defaultConfig: null,
    startup: function() {
    },
    initComponents: function() {
        if (this._componentsInitialized) {
            return;
        }
        this.setDefaultConfig();
        this._componentsInitialized = true;
    },
    setDefaultConfig: function() {
        this.defaultConfig = {
            relative_urls: false,
            remove_script_host: true,
            path: '/files/',
            document_base_url: '/files/'
        };
    },
    initPickers: function() {
        $('.mox-picker-btn').each(function(i, el) {
            this.initPicker($(el));
        }.bind(this));
    },
    initPicker: function($picker_btn) {
        this.initComponents();
        if ($picker_btn.data('pickerInitialized')) {
            return;
        }
        $picker_btn.on('click', function(ev) {
            ev.preventDefault();
            var config = $.extend({}, this.defaultConfig, {
                oninsert: function(args) {
                    $picker_btn.parents('.input-group').find('.mox-picker').val(args.focusedFile.path);
                }
            });
            moxman.browse(config);
        }.bind(this));
        $picker_btn.data('pickerInitialized', true);
    }
});
