/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['jquery'], function () {
    var departments = (function () {

        adminStructure = $('#admin-structure');
        departmentObjArray = [];

        initIconPreview = function () {
            var departmentIcon = $("input[name*=departmentIcon]");

            if (departmentIcon.length > 0) {

                if (departmentIcon.val() !== "") {
                    departmentIcon.next('span').find('i').removeClass()
                            .addClass(departmentIcon.val());
                }

                departmentIcon.on('keyup click blur', function () {
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

                    var departments = $("<div id='department-" + (params.length !== 0 ? params[0] : "root") + "' style='display: none;'>");

                    if (params.length !== 0) {
                        departments.append("<br><h3>" +
                                "<i class='" + departmentObjArray[params[0]].departmentIcon + "'></i> " +
                                departmentObjArray[params[0]].departmentName + "</h3><hr>" +
                                "<p class='text-justify'>" + departmentObjArray[params[0]].departmentDescription + "</p><br>");
                    }

                    departments.append("<ul class='nav'></ul>");
                    var results = data.results;

                    for (var i = 0; i < results.length; i++) {

                        departmentObjArray[results[i].departmentId] = {
                            departmentName: results[i].departmentName,
                            departmentIcon: results[i].departmentIcon,
                            departmentDescription: results[i].departmentDescription
                        };

                        departments.find(".nav").append(
                                "<li data-active='" + results[i].isActive + "' " +
                                "data-children='" + results[i].numberOfChildren + "' " +
                                "data-id='" + results[i].departmentId + "' " +
                                "id='department-identity-" + results[i].departmentId + "' " +
                                "class='col-lg-3 col-md-4 col-sm-6 col-xs-12 " +
                                "catssys-admin-structure " +
                                (typeof adminStructure.data('catssys-toolbar') !== 'undefined' ? "cats-row" : "") +
                                (results[i].isActive === false ? " catssys-admin-structure-disabled" : "") + "'>" +
                                "<div href='#department-" + results[i].departmentId + "' data-toggle='tab'>" +
                                "<i class='" + results[i].departmentIcon + "'></i>" +
                                "<h4><strong>" + results[i].departmentName + "</strong></h4>" +
                                "<p class='text-justify'>" +
                                (results[i].departmentDescription.length < 140 ?
                                        results[i].departmentDescription : results[i].departmentDescription.substring(0, 136) + "...") +
                                "</p></div></li>");
                    }

                    departments.append("<div class='tab-content'></div>");

                    if (params.length !== 0) {
                        adminStructure
                                .find('#department-identity-' + params[0])
                                .closest('.nav')
                                .siblings('.tab-content')
                                .first()
                                .append(departments);
                    } else {
                        adminStructure.find(".container")
                                .append(departments);
                    }

                    departments.fadeIn();

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
                        .closest(".nav")
                        .siblings(".tab-content")
                        .first()
                        .find("div[id^='department-']")
                        .not("#department-" + parentDepartmentId)
                        .slideUp(100);

                if ($("#department-" + parentDepartmentId).length === 0) {
                    getAdministrativeHierarchy(parentDepartmentId);
                } else {
                    $("#department-" + parentDepartmentId).slideToggle(100);
                }

            });
        };


        return {
            init: function () {
                initIconPreview();
                addIconsToParents();
                getAdministrativeHierarchy();
                applyAdministrativeEffects();
            },
            getCallbackOf: function (element) {

                return {
                    exec: function (data) {
                        adminStructure.find("#department-identity-" + data.departmentId).remove();
                        adminStructure.find("#department-" + data.departmentId).remove();
                    }
                };
            }
        };
    }());

    return departments;
});