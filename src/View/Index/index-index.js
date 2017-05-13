if (!app.index) {
    app.index = {};
}

/**
 * Index
 *
 * @param {Object} options
 */
app.index.Index = function Index(options) {

    /** @returns {app.index.Index} */
    var $this = $.extend(this, new app.Page());

    // Options
    this.options = $.extend({}, options);

    /**
     * Init
     *
     * @returns {boolean}
     */
    this.init = function () {
        return true;
    };

    /**
     * Load content
     *
     * @returns {undefined}
     */
    this.load = function () {
        $d.showLoad();
        var params = {
            'hello': 'world'
        };
        $d.call('Index.load', params, function (res) {
            if (!$d.handleResponse(res)) {
                return;
            }

            // load table rows
            if (res.result.status === 1) {
                $d.notify({
                    msg: "<b>Ok</b> " + __('Loaded successfully!'),
                    type: "success",
                    position: "center"
                });
            } else {
                $d.alert('Server error');
            }
        });
    };

    this.init();
};

$(function () {
    var obj = new app.index.Index();
    obj.load();
});