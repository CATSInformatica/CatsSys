/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dropzone', 'bootbox', 'moment', 'masks', 'app/models/Service', 'datetimepicker', 'jquery'], function (Dropzone, bootbox, moment, masks, service) {
    var student = (function () {
        // your module code goes here
        // var config = null;
        /**
         * 
         * private functions
         */
        initImageUpload = function () {
            var profileDropzone = new Dropzone("#profile-img", {
                paramName: 'profilePhoto',
                maxFilesize: 5,
                uploadMultiple: false,
                acceptedFiles: "image/png, image/jpeg",
                url: "/recruitment/registration/changePhoto/" + $("#profile-img").data('id')
            });
            profileDropzone.on("sending", function (file) {
                // Show the total progress bar when upload starts
                $("#total-progress").css('opacity', '1');
            });
            profileDropzone.on("totaluploadprogress", function (progress) {
                $("#total-progress .progress-bar").css('width', progress + '%');
            });
            profileDropzone.on("queuecomplete", function (progress) {
                // Hide the total progress bar when nothing's uploading anymore
                $("#total-progress").css('opacity', '0');
            });
            profileDropzone.on('success', function (file, data) {
                bootbox.alert(data.message);
                if (data.file !== null) {
                    $("#profile-img").attr('src', data.file + '?' + new Date().getTime());
                }
            });
        };

        initDatepickers = function () {
            $('.datepicker').closest('.input-group').datetimepicker({
                format: 'DD/MM/YYYY',
                minDate: moment().subtract(100, 'years'),
                useCurrent: false,
                maxDate: moment().subtract(15, 'years'),
                locale: 'pt-br',
                viewMode: 'years',
                viewDate: moment().subtract(21, 'years')
            });
        };

        initMasks = function () {
            masks.bind({
                phone: "input[name*=personPhone]",
                cpf: "input[name*=personCpf]",
                date: "input[name*=personBirthday]",
                zip: "input[name*=addressPostalCode]"
            });
        };

        initServices = function () {
            service.bindZipService({
                dataHolder: $('input[name*=addressPostalCode]')
            });
        };

        return {
            init: function () {
                initImageUpload();
                initDatepickers();
                initMasks();
                initServices();
            }
        };
    }());

    return student;

});