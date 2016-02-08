/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['jquery'], function () {

    var generate = (function () {

        initDateCopy = function () {
            $('#addAttendanceDate').click(addAttendanceDate);
            $('#removeAttendanceDate').click(removeAttendanceDate);
        };

        addAttendanceDate = function () {
            var currentCount = $('form fieldset > .form-group').length;
            var template = $('form fieldset > span').data('template');
            template = template.replace(/__index__/g, currentCount);
            $('form fieldset').append(template);
        };

        removeAttendanceDate = function () {
            var currentCount = $('form fieldset > .form-group').length;
            if (currentCount > 1) {
                $('form fieldset > .form-group').last().remove();
            }
        };

        return {
            init: function () {
                initDateCopy();
            }
        };
    }());

    return generate;

});