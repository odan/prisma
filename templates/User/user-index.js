$(function() {
    $('#add-user').on('click', function() {
        alert('click');
    });

    $('#data-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "language": {
            "url": __("js/datatable-german.json")
        },
        "ajax": {
            'url': 'users/list',
            'type': 'POST'
        },
        "columns": [
            { "data": "username" },
            { "data": "email" },
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "role" },
            { "data": "enabled" },
            { "data": "created_at" }
        ]
    } );
} );