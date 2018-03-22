/**
 * Class
 */
var HomeIndex = function () {

    // The current object scope
    var $this = this;

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
            username: "max",
            email: "max@example.com"
        };

        $.ajax({
            url: $d.getBaseUrl("home/load"),
            type: "POST",
            cache: false,
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(params)
        }).done(function (data) {
            $d.hideLoad();
            $d.log(data);
            $d.notify({
                msg: "<b>Ok</b> " + data.message,
                type: "success",
                position: "center"
            });

            // Translations
            data.text = {
                'current_user': __('Current user'),
                'user_id': __('User-ID'),
                'username' : __('Username'),
                'its': __('Its'),
            };

            var template = $('#user-template').html();
            //Mustache.parse(template);
            var output = Mustache.render(template, data);

            $('#content').append(output);

        }).fail(function (xhr) {
            $d.hideLoad();
            var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Server error";
            $d.alert(message);
        });
    };

    this.init();
};

$(function () {
    (new HomeIndex()).load();
});
