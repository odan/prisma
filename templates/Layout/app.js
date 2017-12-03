var app = {};

/**
 * Fix for open modal is shifting body content to the left #9855
 */
if ($.fn.modal) {
    $.fn.modal.Constructor.prototype.setScrollbar = function () {
    };
}