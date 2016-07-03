/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'jmaskedinput'], function () {

    bind = function (masks) {

        if (masks.hasOwnProperty("phone")) {
            $(masks.phone).mask("(99) 9999-9999?9")
                    .focusout(function (event) {
                        var target, phone, element;
                        target = (event.currentTarget) ? event.currentTarget : event.srcElement;
                        phone = target.value.replace(/\D/g, '');
                        element = $(target);
                        element.unmask();
                        if (phone.length > 10) {
                            element.mask("(99) 99999-999?9");
                        } else {
                            element.mask("(99) 9999-9999?9");
                        }
                    });
        }

        if (masks.hasOwnProperty("zip")) {
            $(masks.zip).mask("99999-999");
        }

        if (masks.hasOwnProperty("cpf")) {
            $(masks.cpf).mask("999.999.999-99");
        }

        if (masks.hasOwnProperty("date")) {
            $(masks.date).mask("99/99/9999");
        }

        if (masks.hasOwnProperty("datetime")) {
            $(masks.datetime).mask("99/99/9999 99:99:99");
        }

        if (masks.hasOwnProperty("datetimeNoSeconds")) {
            $(masks.datetimeNoSeconds).mask("99/99/9999 99:99");
        }

        if (masks.hasOwnProperty("timeNoSeconds")) {
            $(masks.timeNoSeconds).mask("99:99");
        }

    };

    return {
        bind: bind
    };

});