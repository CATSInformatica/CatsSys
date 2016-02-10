/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['jquery'], function () {
    var departments = (function () {

        initIconPreview = function () {
            var departmentIcon = $("input[name*=departmentIcon]");

            if (departmentIcon.length > 0) {

                if (departmentIcon.val() !== "") {
                    departmentIcon.next('span').find('i').removeClass()
                            .addClass(departmentIcon.val());
                }

                departmentIcon.on('keyup click', function () {
                    $(this)
                            .next('span')
                            .find('i')
                            .removeClass()
                            .addClass($(this).val());
                    ;
                });
            }
        };

        addIconsToParents = function () {
            var departmentParents = $("input[name*=parent]");
            var icon = "";

            if (departmentParents.length > 0) {
                departmentParents.each(function () {
                    icon = $(this).data('icon');
                    $(this)
                            .after(" ")
                            .after($("<i class='" + icon + "'></i>").fadeIn());
                });
            }
        };

        getAdministrativeHierarchy = function () {

            var adminStructure = $('#admin-structure');

            if (adminStructure.length > 0) {
                $.ajax({
                    url: '/administrative-structure/department/getDepartments',
                    success: function (data) {

                        var departments = $("<div class='row' style='display: none;'>");
                        var results = data.results;
                        for (var i = 0; i < results.length; i++) {
                            departments.append(
                                    "<div data-active='" + results[i].isActive + "' " +
                                    "class='col-lg-3 col-md-4 col-sm-6 col-xs-12 " +
                                    "catssys-admin-structure" +
                                    (results[i].isActive === false ? " catssys-admin-structure-disabled" : "") + "'>" +
                                    "<i class='" + results[i].departmentIcon + "'></i>" +
                                    "<h4><strong>" + results[i].departmentName + "</strong></h4>" +
                                    "<p class='text-justify'>" + results[i].departmentDescription + "</p>" +
                                    (results[i].isActive === false ? "<p><b>Inativa</b></p>" : "") +
                                    "</div>");
                        }

                        adminStructure.find(".container")
                                .append(departments);

                        departments.fadeIn();

                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }

        };


        return {
            init: function () {
                initIconPreview();
                addIconsToParents();
                getAdministrativeHierarchy();
            }
        };
    }());

    return departments;
});