if (!app.index) {
    app.index = {};
}

/**
 * Index
 *
 * @param {Object} options
 */
app.index.Index = function Index(options)
{

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

        var data = {
            username: "max",
            email: "max@example.com"
        };

        $.ajax({
            url: $d.getBaseUrl("index/load"),
            type: "POST",
            cache: false,
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(data)
        }).done(function (data) {
            $d.hideLoad();
            $d.log(data);
            $d.notify({
                msg: "<b>Ok</b> " + data.message,
                type: "success",
                position: "center"
            });
        }).fail(function (xhr) {
            $d.hideLoad();
            var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Server error";
            $d.alert(message);
        });
    };

    this.init();
};

$(function () {
    var obj = new app.index.Index();
    obj.load();
});