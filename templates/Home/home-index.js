$.fn.homeIndex = function () {
    const $this = this;

    this.init = function () {
        $this.fetchData();
    };

    this.fetchData = function () {
        $d.showLoad();

        const params = {
            username: "max",
            email: "max@example.com"
        };

        ajax.post($d.getBaseUrl('home/load'), params).done(function (data) {
            $d.hideLoad();

            $d.notify({
                msg: '<b>Ok</b> ' + data.message,
                type: 'success',
                position: 'center'
            });

            // set data
            const userTemplate = $('#user-template').html();
            $this.html($d.template(userTemplate, data));

        }).fail(function (xhr) {
            $d.hideLoad();
            ajax.handleError(xhr);
        });
    };

    this.init();
};

$(function () {
    $('#app').homeIndex();
});

