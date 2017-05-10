/**
 * DataTable jQuery plugin
 * Serverside pagination, sort and search
 *
 * @param {string|object} selector
 * @param {object} options
 * @returns {undefined}
 *
 * @licence MIT
 * @author odan
 */
$d.fn.DataTable = function(selector, options) {

    var $this = this;
    this.el = $(selector);

    // default options
    this.options = $.extend({
        pageSize: 10,
        page: 0,
        search: true,
        paginate: true,
        pages: 0,
        start: 0,
        end: 0,
        dataSource: null,
        autoload: true,
        selectable: false,
        userselect: true,
        checkbox: true,
        toggleSelected: true,
        selectedItemBgcolor: '#337ab7',
        // row data-* attributes
        itemDataAttr: [
            {
                'attr': 'id',
                'property': 'id'
            }, {
                'attr': 'token',
                'property': 'token'
            }
        ],
        columns: {}
    }, options);

    this.init = function() {
        // Remove all event handlers (because of late bindings)
        $(this.el).off();

        // Load template
        $(this.el).html(this.getTemplate());

        $(this.el).find("[data-name=pagesize] .dropdown-menu li a").click(function(e) {
            e.preventDefault();
            var pageSize = $(this).text();
            $this.setPageSize(pageSize);
            $this.loadData();
            return;
            var btn = el.closest('.dropdown').find('.dropdown-toggle');
            var txt = btn.find('[data-value]');
            txt.text(el.text());
            var pageSize = el.attr('data-value');
            $this.options.pageSize = pageSize;
            $this.loadData();
        });

        $(this.el).on('click', 'table thead th', function() {
            var col = $(this);
            var isSortable = col.attr('data-sortable');
            if (isSortable != '1') {
                return;
            }

            var sortProperty = col.attr('data-property');
            var sortDirection = col.attr('data-sortdirection');
            var sortFlag = col.attr('data-sortflag');
            var sortField = col.attr('data-sortfield');
            sortDirection = sortDirection || 'desc';
            sortDirection = (sortDirection === 'desc') ? 'asc' : 'desc';
            col.attr('data-sortdirection', sortDirection);
            col.parent().find('[data-name=sortdirection]').remove();

            var s = '<span class="glyphicon glyphicon-chevron-down pull-right" data-name="sortdirection"></span>';
            if (sortDirection == 'asc') {
                s = '<span class="glyphicon glyphicon-chevron-up pull-right" data-name="sortdirection"></span>';
            }

            $this.options.sortDirection = sortDirection;
            $this.options.sortProperty = empty(sortField) ? sortProperty : sortField;
            $this.options.sortFlag = sortFlag;
            $this.options.sortField = sortField;

            col.append($(s));

            $this.loadData();
        });

        $(this.el).find('[data-name=search]').on('keypress', function(e) {
            if (e.keyCode === 13) {
                $this.loadData();
            }
        });
        $(this.el).find('[data-name=search_button]').on('click', function(e) {
            $this.loadData();
        });
        $(this.el).find('[data-name=page_prev]').on('click', function(e) {
            if ($this.options.page > 1) {
                $this.options.page--;
                $this.loadData();
            }
        });
        $(this.el).find('[data-name=page_next]').on('click', function(e) {
            if ($this.options.page < $this.options.pages) {
                $this.options.page++;
                $this.loadData();
            }
        });
		$(this.el).find('[data-name=page_text]').on('click', $this.promtPageNumber);

        this.renderHeader();

        if (this.options.autoload === true) {
            this.loadData();
        }
    };

    this.setPageSize = function(size) {
        $this.options.pageSize = size;
        var div = $($this.el).find("[data-name=pagesize_span]");
        div.html(size);
        div.attr('data-value', size);
    };

    this.getPageSize = function(size) {
        var div = $($this.el).find("[data-name=pagesize_span]");
        var result = div.attr('data-value');
        return result;
    };

    this.loadData = function() {
        var viewstate = $this.getViewState();
        // callback
        if ($this.options.dataSource) {
            $this.options.dataSource(viewstate, $this.render);
        }
    };

    this.getViewState = function() {
        var options = {};
        // data filtering (todo)
        options.filter = {
            'text': '',
            'value': ''
        };
        options.search = $($this.el).find('[data-name=search]').val();
        // Represents the current page of data
        options.page = $this.options.page;
        // epresenting number of data items to be displayed
        options.pageSize = $this.getPageSize(); // $this.options.pageSize;
        // sort
        options.sortDirection = $this.options.sortDirection;
        options.sortProperty = $this.options.sortProperty;
        options.sortFlag = $this.options.sortFlag;
        options.sortField = $this.options.sortField;
        return options;
    };

    this.setViewState = function(options) {
        if ('search' in options) {
            $($this.el).find('[data-name=search]').val(options.search);
        }
        if ('page' in options) {
            $this.options.page = options.page;
        }
        if ('pageSize' in options) {
            $this.setPageSize(options.pageSize);
        }
        if ('sortDirection' in options) {
            $this.options.sortDirection = options.sortDirection;
        }
        if ('sortProperty' in options) {
            $this.options.sortProperty = options.sortProperty;
        }
        if ('sortFlag' in options) {
            $this.options.sortFlag = options.sortFlag;
        }
        if ('sortField' in options) {
            $this.options.sortField = options.sortField;
        }
    };

    /**
     * Render table
     *
     * @param {object} options
     * @returns {undefined}
     */
    this.render = function(options) {

        if ($this.options.beforeRender) {
            $this.options.beforeRender();
        }
        if (options.columns) {
            if (!$this.equals($this.options.columns, options.columns)) {
                $this.options.columns = options.columns;
                $this.renderHeader();
            }
        }

        // clear table
        var table = $($this.el).find('[data-name=table]');
        table.find('tbody').html('');

        $this.options = $.extend($this.options, options);
        //$d.log(options);
        if ($this.options.pageSize) {
            $this.setPageSize($this.options.pageSize);
        }

        var strPage = __("Page {page} / {pages}");
        strPage = $d.template(strPage, options);
        $($this.el).find('[data-name=page_text]').html(strPage);

        var strPosition = __("{start} - {end} of {count} items");
        strPosition = $d.template(strPosition, options);
        $($this.el).find('[data-name=postion]').html(strPosition);

        $this.renderBody(options);

        if ($this.options.afterRender) {
            $this.options.afterRender();
        }
    };
	
	/**
	 * Show promt for page number.
	 * 
	 * @param {object} e
	 * @returns void
	 */
	this.promtPageNumber = function(e) {
		e.preventDefault();

		var promtSettings = {
			text: __('Please enter the page number'),
			validate: function(value) {
				if (empty(value)) {
					return __('required');
				}
				if (value < 1 || value != parseInt(value, 10)) {
					return __('invalid');
				}
				return true;
			}
		};
		
		$d.prompt(promtSettings, function(page) {
			if (!page) {
				return;
			}
			if ($this.options.page < $this.options.pages) {
                $this.options.page = page;
                $this.loadData();
            }
		});	
	};

    /**
     * Compare objects
     *
     * @param {mixed} a
     * @param {mixed} b
     * @returns {Boolean}
     */
    this.equals = function(a, b) {
        return JSON.stringify(a) === JSON.stringify(b);
    };

    /**
     * Render table body
     *
     * @param {object} options
     * @returns {undefined}
     */
    this.renderBody = function(options) {
        var table = $($this.el).find('[data-name=table]');
        var tbody = table.find('tbody');
        tbody.html('');

        for (var i in options.items) {
            var tr = $('<tr></tr>');
            if (!$this.options.userselect) {
                tr.addClass('d-dt-unselectable');
            }
            var row = options.items[i];
            var selectable = $this.options.selectable;
            if (selectable === 'multi' || selectable === 'single') {
                tr.on('click', $this.onRowClick);
                tr.on('dblclick', $this.onRowDblClick);
                tr.attr('data-selectable', selectable);
                tr.css('cursor', 'pointer');
            }

            // append row data attributes
            if ($this.options.itemDataAttr) {
                for (var i in $this.options.itemDataAttr) {
                    var field = $this.options.itemDataAttr[i];
                    if (field['property'] in row) {
                        var attr = field['attr'];
                        var val = row[field['property']];
                        tr.attr('data-' + attr, val);
                    }
                }
            }

            if ($this.options.onRenderRow) {
                var renderRow = {
                    'item': tr,
                    'row': row
                };
                $this.options.onRenderRow(renderRow);
            }

            for (var j in $this.options.columns) {
                var td = $('<td></td>');
                var col = $this.options.columns[j];

                //if (!(col.property in row)) {
                //	tr.append(td);
                //	continue;
                //}

                if ($this.options.onRenderColumn) {
                    var render = {
                        'item': td,
                        'property': col.property,
                        'value': row[col.property],
                        'name': col.name,
                        'row': row
                    };
                    var isColRendered = $this.options.onRenderColumn(render);
                    if (isColRendered === true) {
                        td.append($(render.value));
                    } else {
                        td.html(gh(render.value));
                    }
                } else {
                    td.html('<span unselectable="on">' + gh(row[col.property]) + '</span>');
                    //td.html(gh(row[col.property]));
                }
                tr.append(td);
            }
            tbody.append(tr);
        }
    };

    this.onRowDblClick = function(e) {
        var tr = $(this);
        if ($this.options.onItemDblClick) {
            $this.options.onItemDblClick({
                item: tr
            });
        }
    };

    this.onRowClick = function(e) {
        var tr = $(this);
        var selectable = tr.attr('data-selectable');
        if (selectable !== 'single' && selectable !== 'multi') {
            return;
        }

        var selected = tr.attr('data-selected');
        if (selectable === 'single' && selected != '1') {
            // deselect all selected rows
            tr.parent().find('tr[data-selected=1]').each(function() {
                var sel = $(this);
                //selected = '0';
                sel.attr('data-selected', '0');
                if ($this.options.checkbox == true) {
                    sel.find('[data-name=selected]').remove();
                } else {
                    sel.css('background-color', '');
                }
            });
        }

        if (selected == '1') {
            if ($this.options.toggleSelected == true) {
                selected = '0';
                tr.attr('data-selected', selected);
                if ($this.options.checkbox == true) {
                    var td = tr.find('td:first');
                    td.find('[data-name=selected]').remove();
                } else {
                    tr.css('background-color', '');
                }
            }
        } else {
            selected = '1';
            tr.attr('data-selected', selected);
            if ($this.options.checkbox == true) {
                var ico = '<span data-name="selected" class="glyphicon glyphicon-ok pull-right"></span>';
                var td = tr.find('td:first');
                td.append(ico);
            } else {
                tr.css('background-color', $this.options.selectedItemBgcolor);
            }
        }
        if ($this.options.onItemSelection) {
            $this.options.onItemSelection({
                item: tr,
                selected: selected
            });
        }
    };

    /**
     * Render table header
     *
     * @returns {undefined}
     */
    this.renderHeader = function() {
        var table = $($this.el).find('[data-name=table]');
        table.find('thead').html('');
        var tr = $('<tr>');
        for (var i in $this.options.columns) {
            var col = $this.options.columns[i];
            var strTh = '<th nowrap>' + gh(col.label) + '</th>';
            var th = $(strTh);
            th.addClass('d-dt-hyphens');
            if ('property' in col) {
                th.attr('data-property', col.property);
            }
            if ('sortable' in col) {
                th.attr('data-sortable', col.sortable ? '1' : '0');
                if (col.sortable) {
                    th.css('cursor', 'pointer');
                }
            }
            if ('sortflag' in col) {
                th.attr('data-sortflag', col.sortflag);
            }
            if ('sortfield' in col) {
                th.attr('data-sortfield', col.sortfield);
            }
            if ('width' in col) {
                th.css('width', col.width);
            }
            tr.append(th);
        }
        table.find('thead').append(tr);
    };

    this.getSelectedItems = function() {
        var table = $($this.el).find('[data-name=table]');
        var rows = table.find('tbody tr[data-selected=1]');
        return rows;
    };

    this.getTemplate = function() {
        var html = '\
        <style>\
            .d-dt-unselectable {\
                -moz-user-select: none;\
                -khtml-user-select: none;\
                -webkit-user-select: none;\
                -o-user-select: none;\
            }\
            .d-dt-hyphens {\
                -ms-word-break: break-all;\
                word-break: break-all;\
                -ms-hyphens: auto;\
                -moz-hyphens: auto;\
                -webkit-hyphens: auto;\
                hyphens: auto;\
            }\
        </style>\
        <div class="row">\
            <div class="col-sm-5">\
                <div class="pull-left">\
                    <div class="input-group">\
                        <input type="text" data-name="search" class="form-control" placeholder="{placeholderSearch}">\
                        <span class="input-group-btn">\
                            <button class="btn btn-default" type="button" data-name="search_button">\
                                <i class="fa fa-search"></i>\
                            </button>\
                        </span>\
                    </div>\
                </div>\
            </div>\
		<div class="col-sm-7">\
                    <div class="pull-right">\
                        <button type="button" class="btn btn-default btn-sm" data-name="page_prev">\
                            <span class="glyphicon glyphicon-chevron-left"></span>\
                        </button>\
                        <label data-name="page_text" style="cursor: pointer;">Page 0 / 0</label>\
                        <button type="button" class="btn btn-default btn-sm" data-name="page_next">\
                            <span class="glyphicon glyphicon-chevron-right"></span>\
                        </button>\
                </div>\
            </div>\
        </div>\
        <div class="row">\
            <p></p>\
        </div>\
        <div class="row">\
            <div class="col-sm-12">\
                <table class="table table-bordered table-striped table-hover" data-name="table">\
                    <thead>\
                         <tr>\
                            <th>&nbsp;\n\
					<!--<span class="glyphicon glyphicon-chevron-down pull-right"></span>-->\
                            </th>\
                        </tr> \
                    </thead>\
                    <tbody>\
                    </tbody>\
                </table>\
            </div>\
        </div>\
        <div class="row">\
            <div class="col-sm-12">\
                <div class="pull-right">\
                    <table>\
                        <tbody>\
                            <tr>\
                                <td><span data-name="postion">{itemsstartend}</span></td>\
                                <td>&nbsp;</td>\
                                <td>\
                                    <div class="dropdown" data-name="pagesize">\
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">\
                                            <span data-name="pagesize_span" data-value="10">10</span>\
                                            <span class="caret"></span>\
                                        </button>\
                                        <ul class="dropdown-menu" style="width: 70px !important;min-width: 70px !important;" role="menu">\
                                            <li><a role="menuitem" data-value="10" href="#">10</a></li>\
                                            <li><a role="menuitem" data-value="30" href="#">30</a></li>\
                                        </ul>\
                                    </div>\
                                </td>\
                                <td>&nbsp;</td>\
                                <td>{perpage}</td>\
                            </tr>\
                        </tbody>\
                    </table>\
                </div>\
            </div>\
        </div>';

        // translate
        html = $d.template(html, {
            placeholderSearch: __('Search'),
            perpage: __('per Page'),
            itemsstartend: __('{start} - {end} of {count} items', {
                'start': 0,
                'end': 0,
                'count': 0
            })
        });
        return html;
    };

    this.init();

};

// jquery plugin adapter (jpa)
$.fn.datatable = function(options) {
    this.each(function() {
        $(this).data('datatable', new $d.fn.DataTable(this, options));
    });
};
