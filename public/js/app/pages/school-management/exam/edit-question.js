/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define([], function () {
    var edit = (function () {
        
        return {
            init: function () {
                require(['app/pages/school-management/exam/add-question'], function (QuestionModule) {
                    QuestionModule.init();
                });
            }
        };

    }());

    return edit;
});