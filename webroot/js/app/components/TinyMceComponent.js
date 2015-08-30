App.Components.TinyMceComponent = Frontend.Component.extend({
    tinyMceConfig: null,
    _componentsInitialized: false,
    locale: null,
    startup: function() {
    },
    initComponents: function() {
        if (this._componentsInitialized) {
            return;
        }
        moxman.Env.apiPageName = '/../../../../moxiemanager/api';
        this.locale = this.Controller.getVar('locale');
        this._initMoxmanLocale();
        this.Controller.MoxmanPicker.initPickers();
        this.setDefaultConfig();
        this._componentsInitialized = true;
    },
    setDefaultConfig: function() {
        this.tinyMceConfig = {
            script_url : '/ck_tools/js/vendor/tinymce/tinymce.min.js',
            language: this.locale,
            menu: {
                file: {title: 'File', items: ''},
                edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
                insert: {title: 'Insert', items: 'link media | template hr'},
                view: {title: 'View', items: ''},
                format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
                table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
                tools: {title: 'Tools', items: 'spellchecker code'}
            },
            toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            toolbar2: "print preview media | forecolor backcolor emoticons",
            external_plugins: {
                'moxiemanager': '/ck_tools/js/vendor/moxiemanager/plugin.js'
            },
            relative_urls: false,
            document_base_url: '/',
            remove_script_host: true
        };
    },
    initEditors: function($dom) {
        $dom.find('textarea.tinymce').each(function(i, element) {
            var $element = $(element);
            this.initEditor($element);
        }.bind(this));
    },
    initEditor: function($element) {
        this.initComponents();
        if ($element.data('tinyMceApplied')) {
            return false;
        }
        var config = this.tinyMceConfig;
        if ($element.hasClass('hidden')) {
            config.setup = function(editor) {
                editor.on('init', function(event) {
                    event.target.hide();
                    if ($(event.target.container).parents('.form-group')) {
                        $(event.target.container).parents('.form-group').hide();
                    }
                });
            }
        }
        $element.tinymce(this.tinyMceConfig);
        $element.data('tinyMceApplied', true);
    },
    _initMoxmanLocale: function() {
        if (this.locale == 'de') {
            $.getScript('/ck_tools/js/vendor/moxiemanager/langs/moxman_de.js');
        }
    }
});
