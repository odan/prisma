var app = {};

var VueGettext = {};
VueGettext.install = function (Vue, options) {
    // 1. add global method or property
    Vue.prototype.__ = __;
};

/**
 * Fix for open modal is shifting body content to the left #9855
 */
//if ($.fn.modal) {
    // Boostrap 3.x
    //$.fn.modal.Constructor.prototype.setScrollbar = function () {};

    // Boostrap 4.x
    //$.fn.modal.Constructor.prototype._setScrollbar = function () {};
//}