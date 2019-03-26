new Vue({
    el: "#app",
    template: '#user-template',

    data: {
        message: __('Loading...'),
        user: {},
        now: null,
        text: null,
    },
    mounted: function () {
        // Now the template is mounted into the dom
        this.fetchData();
    },
    methods: {
        fetchData: function () {
            var $data = this;

            $d.showLoad();

            const params = {
                username: "max",
                email: "max@example.com"
            };
/*
            const data = {
                username: "max2003",
                email: "max@example.com"
            };

            $.ajax({
                url: 'users',
                type: "POST",
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data)
            }).done(function (data) {
                alert('Success');
            }).fail(function (xhr) {
                alert('Server error');
            });
*/


            ajax.post($d.getBaseUrl("home/load"), params).done(function (data) {
                $d.hideLoad();
                $d.log(data);

                $d.notify({
                    msg: "<b>Ok</b> " + data.message,
                    type: "success",
                    position: "center"
                });

                //$data.message = data.message;
                //$d.addText(data.text);

                // set data
                $data = $.extend($data, data);
            }).fail(function (xhr) {
                $d.hideLoad();
                const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Server error";
                //$d.alert(message);
                $data.message = message;
                ajax.handleError(xhr);
            });



        },
        test: function (msg) {
            alert(msg);
        }
    }
});

