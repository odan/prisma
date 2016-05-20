/**
 * JavaScript file upload library
 *
 * @author odan https://github.com/odan
 * @licence MIT
 */

if (!$d) {
    $d = {};
}

$d.Upload = function() {

    var $this = this;

    /**
     * File upload
     *
     * @param {object} options
     * @returns {undefined}
     */
    this.uploadFile = function(options) {

        // allowed filetypes
        var fileTypes = {};

        options = $.extend({
            onupload: $this.onFileUpload,
            onfileload: options.onfileload || function() {
            },
            maxfilesize: 3 * 1024 * 1024,
            global: false,
            filetype: fileTypes,
            'plugin': {
                filereader: 'assets/js/filereader.swf',
                extensions: "*.*"
            }
        }, options);

        // check for fileReader polyfill (plugin)
        // https://github.com/Jahdrien/FileReader
        if ($.fn.fileReader) {
            $(options.input).fileReader(options.plugin);
        }

        options.input.on('change', function(e) {
            var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
            options.onupload(files, options);
            // reset
            $(options.input).val('');
        });
    };

    /**
     * Image uploader
     * http://hayageek.com/drag-and-drop-file-upload-jquery/
     *
     * @param {object} options
     * options.image {object} the image html elemen
     * options.onupload	{object} callback function
     * options.onimageload {object} callback function
     * options.input {object} the htlm input upload elemement
     * options.global {boolean}
     *
     * @returns {undefined}
     */
    this.uploadImage = function(options) {

        // allowed filetypes
        var fileTypes = {
            'image/jpeg': 'jpg',
            'image/jpg': 'jpg',
            'image/gif': 'gif',
            'image/png': 'png'
        };

        options = $.extend({
            image: null,
            onupload: $this.onImageUpload,
            onimageload: $this.onImageLoad,
            maxfilesize: 3 * 1024 * 1024,
            global: false,
            filetype: fileTypes,
            'plugin': {
                filereader: 'assets/js/filereader.swf',
                extensions: "*.jpg;*.gif;*.png"
            }
        }, options);

        // check for fileReader polyfill (plugin)
        // https://github.com/Jahdrien/FileReader
        if ($.fn.fileReader) {
            $(options.input).fileReader(options.plugin);
        }

        // Handle drag and drop events
        options.image.on('dragenter', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '2px solid #0B85A1');
        });

        options.image.on('dragover', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });

        options.image.on('dragleave', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border', '');
        });

        options.image.on('drop', function(e) {
            e.preventDefault();
            //$(this).css('border', '2px dotted #0B85A1');
            $(this).css('border', '');

            var files = e.originalEvent.dataTransfer.files;
            options.onupload(files, options);
        });

        // Handle click event with file open dialog
        options.image.on('click', function() {
            $(options.input).trigger('click');
        });

        options.input.on('change', function(e) {
            var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
            options.onupload(files, options);
            // reset
            $(options.input).val('');
        });

        // If the files are dropped outside the div, file is opened in the
        // browser window. To avoid that we can prevent ‘drop’ event on document.
        if (options.global === true) {
            $(document).on('dragenter', function(e) {
                e.stopPropagation();
                e.preventDefault();
            });

            $(document).on('dragover', function(e) {
                e.stopPropagation();
                e.preventDefault();
                //elImage.css('border', '2px dotted #0B85A1');
            });

            $(document).on('drop', function(e) {
                e.stopPropagation();
                e.preventDefault();

                var files = e.originalEvent.dataTransfer.files;
                options.onupload(files, options);

                // Prevent double handler
                e.preventDefault();
            });
        }
    };

    /**
     * Event onImageUpload
     *
     * @param {object} files
     * @param {object} obj
     * @returns {undefined}
     */
    this.onImageUpload = function(files, options) {

        if (!files || files.length < 1) {
            return;
        }

        var readers = {};
        for (var i in files)
        {
            var file = files[i];
            if (typeof file !== 'object') {
                continue;
            }
            readers[i] = new FileReader();
            readers[i].file = file;

            //var strFileName = file.name;
            var strFileType = file.type;
            if (!empty(options.filetype) && !(strFileType in options.filetype)) {
                $d.alert(__('The filetype is invalid'));
                return;
            }

            if (file.size > options.maxfilesize) {
                $d.alert(__('The file size exceeds the limit allowed'));
                return;
            }

            readers[i].onerror = function(event) {
                $d.alert(__("The file could not be opened.") + ' ' + event.target.error.code);
            };

            readers[i].onload = function(e) {
                options.onimageload(e, this.file, options);
            };
            readers[i].readAsDataURL(file);
        }
    };

    /**
     * If image is loaded
     *
     * @param {object} e
     * @param {object} file
     * @returns {undefined}
     */
    this.onImageLoad = function(e, file, options) {
        var strData = e.target.result;
        // change image
        $(options.image).attr('src', strData);
    };

    /**
     * Event onFileUpload
     *
     * @param {object} files
     * @param {object} obj
     * @returns {undefined}
     */
    this.onFileUpload = function(files, options) {

        if (!files || files.length < 1) {
            return;
        }
        var readers = {};
        for (var i in files)
        {
            var file = files[i];
            if (typeof file !== 'object') {
                continue;
            }
            readers[i] = new FileReader();
            readers[i].file = file;

            //var strFileName = file.name;
            var strFileType = file.type;
            if (!empty(options.filetype) && !(strFileType in options.filetype)) {
                $d.alert(__('The filetype is invalid'));
                return;
            }

            if (file.size > options.maxfilesize) {
                $d.alert(__('The file size exceeds the limit allowed'));
                return;
            }

            readers[i].onerror = function(event) {
                $d.alert(__("The file could not be opened.") + ' ' + event.target.error.code);
            };

            readers[i].onload = function(e) {
                options.onfileload(e, this.file, options);
            };
            readers[i].readAsDataURL(file);
        }
    };

};
