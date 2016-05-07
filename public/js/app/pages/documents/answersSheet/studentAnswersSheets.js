/* 
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define(['filesaver'], function () {

    var svgTemplate = null;
    var svgStudents = [];
    var students = [];
    var lastSClass = null;

    var asModule = (function () {

        initTemplate = function () {

            $.get('/img/exam-answers-template.svg', function (data) {
                // Get the SVG tag, ignore the rest
                svgTemplate = jQuery(data).find('svg');

                // Remove any invalid XML tags as per http://validator.w3.org
                svgTemplate = svgTemplate
                        .removeAttr('xmlns:a');

                // Replace image with new SVG
                $("#template-container")
                        .append(svgTemplate)
                        .slideDown("slow");
            }, 'xml');
        };


        addListeners = function () {

            $("#process-template").click(function () {
                if (svgTemplate !== null) {
                    formatTemplate();
                }
            });

            $("#process-students").click(function () {

//                $("#student-progress").width(0);

                var classVal = $("#studentClass").val();

                if (svgTemplate !== null) {
                    if (lastSClass !== classVal) {
                        getStudents(classVal).then(function () {
                            formatTemplate();
                            magic();
                        });
                    } else {
                        formatTemplate();
                        magic();
                    }
                }
            });

            $(".print-sheet").click(function () {
                if (svgTemplate !== null) {

                    switch ($(this).data("type")) {
                        case "template":
                            print([svgTemplate]);
                            break;
                        case "students":
                            print(svgStudents);
                            break;
                    }
                }
            });
        };

        /**
         * Pintar a matrícula, insere o número de matrícula e nome do aluno
         * 
         * @returns {undefined}
         */
        magic = function () {

            $("#students-container").html("");
            var studentAnswersSheet = null;
            svgStudents = [];
//            var progress;

            for (var i = 0; i < students.length; i++) {
                studentAnswersSheet = svgTemplate.clone();
                studentAnswersSheet.attr("id", "template_" +
                        students[i].enrollmentId);
                markEnrollment(studentAnswersSheet,
                        students[i].enrollmentId,
                        students[i].personFirstName + " " + students[i].personLastName
                        );

                svgStudents.push(studentAnswersSheet);

                $("#students-container")
                        .append(
                                $("<div class='col-md-6 col-xs-12'>")
                                .append(studentAnswersSheet)
                                );
//                progress = (i + 1) * 100 / (students.length);
//                $("#student-progress")
//                        .css("width", progress + "%")
//                        .attr('aria-valuenow', progress);
            }

            $("#students-container").slideDown("slow");

        };

        markEnrollment = function (svg, idNumber, name) {

            var idNumber = enrollmentToArray(idNumber);
            svg.data("identity", idNumber.join("") + "_" + name.replace(/ /g, "_"));

            for (var i = 0; i < idNumber.length; i++) {
                svg
                        .find("#id-circle-" + i + "-" + idNumber[i])
                        .attr("style", "fill:#000000;fill-opacity:1;" +
                                "stroke:#000000;stroke-width:0.75569242;" +
                                "stroke-miterlimit:4;stroke-dasharray:none;" +
                                "stroke-opacity:1")
                        .next("text")
                        .remove();
                svg.find("#student-name > tspan").text(name);
                svg.find("#student-id-number > tspan").text(idNumber.join(""));
            }
        };

        enrollmentToArray = function (id)
        {
            var partial = ("" + id);
            return ("00000" + partial).substring(partial.length).split("");
        };

        formatTemplate = function () {
            var subtitle = $("#input-subtitle").val();
            var visibility = $("input[name=input-language]:checked").val();
            var svgWidth = svgTemplate[0].getAttribute("viewBox").split(" ")[2];
            var textElement = svgTemplate.find("#subtitle > tspan");
            textElement.text(subtitle);
            var textWidth = textElement[0].getBoundingClientRect().width;
            var xPosition = (svgWidth / 2) - (textWidth / 2);
            textElement.attr("x", xPosition);

            svgTemplate.find("#language-group").attr("visibility", visibility);
            svgTemplate.find("#language-group").removeAttr("style");

            if (visibility === "hidden") {
                svgTemplate.find("#language-group").attr("style", "display:none;visibility:hidden;");
            }

        };

        print = function (svgs) {

            var blob = null;
            var data = null;
            var documentName = "";

            for (var i = 0; i < svgs.length; i++) {
                data = (new XMLSerializer())
                        .serializeToString(svgs[i][0]);
                blob = new Blob([data], {type: "image/svg+xml;charset=utf-8"});
                documentName = typeof svgs[i].data("identity") !== "undefined"
                        ? svgs[i].data("identity") : "Modelo";
                saveAs(blob, documentName + "-" + (new Date).getTime() +
                        ".svg");
            }

        };

        /**
         * 
         * @param {type} classId
         * @returns {jqXHR} promise
         */
        getStudents = function (classId) {

            return $.ajax({
                url: '/school-management/student-class/get-students',
                type: 'POST',
                data: {
                    id: classId
                },
                success: function (data) {
                    students = data.students;
                    lastSClass = classId;
                },
                error: function (err) {
                    console.log(err);
                    students = [];
                }
            });
        };

        return {
            init: function () {
                initTemplate();
                addListeners();
            }
        };

    }());

    return asModule;

});
