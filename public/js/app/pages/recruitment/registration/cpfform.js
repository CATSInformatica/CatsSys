/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['masks', 'jquery'], function (masks) {
    var student = (function () {

        initMasks = function () {
            masks.bind({
                cpf: "input[name=person_cpf]"
            });
        };

        return {
            init: function () {
                initMasks();
            }
        };
    }());

    return student;

});