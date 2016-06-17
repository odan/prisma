if (!app.index) {
    app.index = {};
}

/**
 * Index
 *
 * @param {Object} options
 */
app.index.Index = function Index(options) {

    // This object
    var $this = this;

    /** @returns {app.index.Index} */
    var $this = $.extend(this, new app.Page());

    // Options
    this.options = $.extend({}, options);

    /**
     * Init
     *
     * @returns {boolean}
     */
    this.init = function() {
        return true;
    };

    /**
     * Load content
     *
     * @returns {undefined}
     */
    this.load = function() {
        $d.showLoad();
        $d.rpc('Index.load', null, function(res) {
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

$(function(params) {
    var obj = new app.index.Index();
    obj.load();
});