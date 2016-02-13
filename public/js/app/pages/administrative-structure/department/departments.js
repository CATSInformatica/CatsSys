/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['jquery'], function () {
    var departments = (function () {

        adminStructure = $('#admin-structure');

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
            $("select[name*=parent]").on('change', function () {
                var iconClass = 'fa fa-sticky-note-o';
                if ($(this).val() !== "") {
                    iconClass = $(this).find('option:selected').data('icon');
                }

                $(this)
                        .prev('span')
                        .find('i')
                        .removeClass()
                        .addClass(iconClass);

            });
        };

        getAdministrativeHierarchy = function () {

            if (adminStructure.length === 0) {
                return;
            }

            var params = arguments;

            $.ajax({
                url: '/administrative-structure/department/getDepartments' + (params.length === 1 ? '/' + params[0] : ''),
                success: function (data) {

                    var departments = $("<ul id='department-" + (params.length !== 0 ? params[0] : "root") + "' class='nav active' data-isChild='" +
                            (params.length !== 0) + "' style='display: none;'>");
                    var results = data.results;
                    for (var i = 0; i < results.length; i++) {
                        departments.append(
                                "<li data-active='" + results[i].isActive + "' " +
                                "data-children='" + results[i].numberOfChildren + "' " +
                                "data-id='" + results[i].departmentId + "' " +
                                "id='department-identity-" + results[i].departmentId + "' " +
                                "class='col-lg-3 col-md-4 col-sm-6 col-xs-12 " +
                                "catssys-admin-structure" +
                                (results[i].isActive === false ? " catssys-admin-structure-disabled" : "") + "'>" +
                                "<a href='#department-" + results[i].departmentId + "' data-toggle='tab'>" +
                                "<i class='" + results[i].departmentIcon + "'></i>" +
                                "<h4><strong>" + results[i].departmentName + "</strong></h4>" +
                                "<p class='text-justify'>" + results[i].departmentDescription + "</p>" +
                                (results[i].isActive === false ? "<p><b>Inativa</b></p>" : "") +
                                "</a></li>");
                    }
                    if (results.length > 0) {
                        if (params.length !== 0) {

                            console.log(adminStructure
                                    .find('#department-identity-' + params[0]).closest('ul')
                                    .next('.tab-content').length);

                            adminStructure
                                    .find('#department-identity-' + params[0]).closest('ul')
                                    .next('.tab-content')
                                    .append(departments)
                                    .append("<div class='tab-content'></div>");
                            departments.fadeIn();
                        } else {
                            adminStructure.find(".container")
                                    .append(departments)
                                    .append("<div class='tab-content'></div>");
                            departments.fadeIn();
                        }

                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });

        };

        applyAdministrativeEffects = function () {
            adminStructure.on("click", ".catssys-admin-structure", function () {

                var parentDepartmentId = $(this).data('id');
                $(this)
                        .closest('ul')
                        .next('.tab-content')
                        .find('ul')
                        .not("#department-" + parentDepartmentId)
                        .removeClass('active')
                        .slideUp('slow');

                if ($(this).data('children') > 0) {
                    if ($("#department-" + parentDepartmentId).length === 0) {
                        getAdministrativeHierarchy(parentDepartmentId);
                    } else {
                        if($("#department-" + parentDepartmentId).hasClass('active')) {
                            $("#department-" + parentDepartmentId).removeClass('active').slideUp();
                        } else {
                            $("#department-" + parentDepartmentId).addClass('active').slideDown();
                        }
                    }
                }
            });
        };


        return {
            init: function () {
                initIconPreview();
                addIconsToParents();
                getAdministrativeHierarchy();
                applyAdministrativeEffects();
            }
        };
    }());

    return departments;
});