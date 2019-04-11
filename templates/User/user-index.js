$(function () {
    $('#add-user').on('click', function () {
        alert('click');
    });

    const table = $('#data-table').DataTable({
        'processing': true,
        'serverSide': true,
        'language': {
            'url': __('js/datatable-english.json')
        },
        'ajax': {
            'url': 'users/list',
            'type': 'POST'
        },
        'columns': [
            {'data': 'username'},
            {'data': 'email'},
            {'data': 'first_name'},
            {'data': 'last_name'},
            {'data': 'role'},
            {'data': 'enabled'},
            {'data': 'created_at'},
            {
                'orderable': false,
                'searchable': false,
                'data': null,
                'render': function (data, type, row, meta) {
                    return '<button type="button" class="btn btn-info">' + gh(__('Edit')) + '</button>';
                }
            }
        ],
    });

    $('#data-table tbody').on('click', 'button', table, function () {
        const data = table.row($(this).parents('tr')).data();
        alert('Edit user: ' + data.id);
    });
});
