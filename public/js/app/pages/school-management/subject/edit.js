/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery'], function () {
    var edit = (function () {

        var initParentField = function () {
            var parent = $('#data').attr("data-parent-id");
            if (jQuery.isNumeric(parent) && parent > 0) {
                $('#subject-parent').val(parent);
            }
            $('#subject-parent').change(function() {
               if ($('#subject-parent').val() === $('#data').attr("data-id")) {
                   $('#subject-parent').val(0);
               }
            });
        };

        return {
            init: function () {
                initParentField();
            }
        };

    }());

    return edit;
});