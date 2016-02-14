/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['bootbox', 'dropzone', 'jquery'], function (bootbox, Dropzone) {
    var files = (function () {
        // your module code goes here
        // var config = null;

        /**
         * private functions
         */

        initFileUploadElements = function () {

            Dropzone.autoDiscover = false;
            var dropElements = {};
            $(".info-box-icon").each(function () {
                var element = $(this);
                var id = element.data('id');

                dropElements[id] = new Dropzone("#" + id, {
                    paramName: id,
                    maxFilesize: 15,
                    uploadMultiple: false,
                    acceptedFiles: "application/pdf",
                    url: "/recruitment/pre-interview/studentFileUpload/" + id,
                    sending: function (file) {
                        // Show the total progress bar when upload starts
                        element.siblings(".info-box-content")
                                .find(".progress-bar").css("opacity", "1");
                    },
                    totaluploadprogress: function (progress) {
                        element.siblings(".info-box-content")
                                .find('.progress-bar').css("width", progress + "%");
                        element.siblings(".info-box-content")
                                .find('.progress-description').text("Enviando (" + progress + "%)");
                    },
                    queuecomplete: function (progress) {
                        // Hide the total progress bar when nothing's uploading anymore
                        element.siblings(".info-box-content")
                                .find(".progress-description").text("Concluído");
                    },
                    success: function (file, data) {
                        bootbox.alert(data.message);
                        element.closest('.info-box')
                                .removeClass("bg-red")
                                .removeClass("bg-green")
                                .addClass("bg-green");

                        element.siblings(".info-box-content")
                                .find('.info-box-number').html('<small>' +
                                '<a href="/recruitment/pre-interview/getUploadedFile/' + id + '" target="_blank">' +
                                file.name +
                                '</a>' +
                                '</small>');

                    },
                    error: function (file, response) {
                        bootbox.alert(response);
                    },
                    previewsContainer: false
                });
            });

            // hack to trigger dropElements on child click
            $('.info-box-icon').on('click', '*', function (e) {
                e.stopPropagation();
                $(this).closest('.info-box-icon').trigger('click');
            });
        };

        return {
            init: function () {
                initFileUploadElements();
            }
        };
    }());

    return files;
});
    