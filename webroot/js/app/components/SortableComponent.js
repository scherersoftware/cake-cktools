App.Components.SortableComponent = Frontend.Component.extend({
    defaultConfig: null,
    startup: function() {
    },
    initSortableLists: function($dom) {
        $dom.find('table.sortable-list tbody').sortable().disableSelection();
        $dom.find('table.sortable-list tbody').on("sortupdate", this._onSortUpdate.bind(this));
    },
    _onSortUpdate: function(e, ui) {
        var foreignKey = ui.item.data('entity-id');
        var sort = ui.item.index() + 1;

        if (typeof(this.Controller._dom.find('table.sortable-list tbody').children()[sort-1]) == "undefined") {
            sort = 1;
        }
        App.Main.UIBlocker.blockElement(this.Controller._dom);
        App.Main.request(
            {
                plugin: this.Controller._frontendData.request.plugin,
                controller: this.Controller._frontendData.request.controller.replace(/_/g, '-'),
                action: 'sort',
            }, {
                foreignKey: foreignKey,
                sort: sort
            },
            function(data) {
                App.Main.UIBlocker.unblockElement(this.Controller._dom);
                if (data.code == 'success') {
                } else {
                    alert("Error on update sort order! Please reload page.");
                }
            }.bind(this));
    }
});
