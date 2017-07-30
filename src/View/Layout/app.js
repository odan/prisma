var app = {};

/**
 * Base class for all pages
 *
 * @param {Object} options
 * @returns {Object}
 */
app.Page = function (options) {

    // This object
    var $this = this;

    // Options
    this.options = $.extend({}, options);

    // Url query parameter
    this.query = $d.urlParams();

    // Templates
    this.tpl = {};

    /**
     * Constructor
     *
     * @returns {undefined}
     */
    this.init = function () {
    };

    /**
     * Load page content
     *
     * @returns {undefined}
     */
    this.load = function () {
    };

    /**
     * Add (cut) template
     *
     * @param {string} selector
     * @param {string} strName
     * @returns {undefined}
     */
    this.setTemplate = function (selector, strName) {
        var el = $(selector);
        if (el.length) {
            $this.tpl[strName] = el.html();
            el.html('');
        }
    };

    /**
     * Return template content
     *
     * @param {string} strName
     * @returns {string}
     */
    this.getTemplate = function (strName) {
        return $this.tpl[strName];
    };

    this.init();
};

/**
 * Fix for open modal is shifting body content to the left #9855
 */
if ($.fn.modal) {
    $.fn.modal.Constructor.prototype.setScrollbar = function () {
    };
}