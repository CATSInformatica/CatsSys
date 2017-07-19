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

define(['datatable'], function () {


    var diagramData = [];
    var jobModule = (function () {

        initJobDataTable = function () {
            $('#jobTable').DataTable({
                iDisplayLength: 20,
                dom: 'lftip'
            });
        };
        initDataTable = function () {

            var recruitmentTable = $('#office-table').DataTable({
                iDisplayLength: 100,
                dom: 'lftip',
                ajax: {
                    url: "/recruitment/registration/getRegistrations",
                    type: "POST",
                    data: function () {
                        return {
                            recruitment: $("select[name=recruitment]").val(),
                            registrationStatus: $("select[name=registrationStatus]").val()
                        };
                    },
                    dataSrc: function (data) {
                        var result = [];
                        for (var i = 0; i < data.length; i++) {
                            result.push({
                                DT_RowClass: "cats-row",
                                DT_RowAttr: {
                                    "data-id": data[i].registrationId
                                },
                                0: data[i].registrationNumber,
                                1: data[i].personName,
                                2: data[i].personEmail
                            });
                        }

                        return result;
                    }
                },
                columnDefs: [{targets: 2, className: 'text-center'}]
            });
            $('button[name=submit]').click(function () {
                recruitmentTable.ajax.reload();
            });
        };
        initJobs = function () {

            $.ajax({
                url: "/administrative-structure/job/get-jobs",
                type: "GET",
                success: function (data) {
                    var jobs = data.jobs;
                    for (var i = 0; i < jobs.length; i++) {
                        $("#jobList").append(depthFirstSearchPrint(jobs[i]));
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        };
        depthFirstSearchPrint = function (job) {

            var jobDt = "";
            var childrenDl = "";
            var jobDd = "<dd><label class='checkbox-inline'>" +
                    "<input name='job[]' class='job-checkbox' type='checkbox' value='" +
                    job.id + "'>" + job.name +
                    "</label>";
            if (job.children.length > 0) {

                jobDt = "<dt><a data-toggle='collapse' href='#job-" +
                        job.id +
                        "' aria-expanded='true' aria-controls='job-" + job.id +
                        "'>" + job.department + "</a></dt>";
                childrenDl = "<dl id='job-" + job.id +
                        "' class='collapse collapse-in dl-horizontal'>";
                for (var i = 0; i < job.children.length; i++) {
                    childrenDl += (depthFirstSearchPrint(job.children[i]));
                }

                childrenDl += "</dl>";
            } else {
                jobDt = "<dt>" + job.department + "</dt>";
            }

            jobDd += childrenDl + "</dd>";
            return jobDt + jobDd;
        };
        /**
         * Substituir essas funções por funções definitivas
         * 
         * @returns {undefined}
         */

        testGoogleChart = function () {

            require(['https://www.gstatic.com/charts/loader.js'], function () {
                google.charts.load('current', {packages: ["orgchart"]});
                google.charts.setOnLoadCallback(drawChart);
                function drawChart() {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Name');
                    data.addColumn('string', 'Manager');
                    data.addColumn('string', 'ToolTip');
                    $.ajax({
                        url: "/administrative-structure/job/get-jobs",
                        type: "GET",
                        success: function (data) {
                            var jobs = data.jobs;
                            console.log(data.jobs);
                            for (var i = 0; i < jobs.length; i++) {
                                testGoogleChartMountVector(jobs[i], '');
                            }
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    }).then(function () {

                        // For each orgchart box, provide the name, manager, and tooltip to show.
                        data.addRows(diagramData);
                        // Create the chart.
                        var chart = new google.visualization.OrgChart(document.getElementById('hierarchy'));
                        // Draw the chart, setting the allowHtml option to true for the tooltips.
                        chart.draw(data, {
                            allowHtml: true,
                            allowCollapse: true,
                            nodeClass: 'hierarchy-color',
                            selectedNodeClass: 'hierarchy-color-selected'
                        });
                    });
                }
            });
        };
        testGoogleChartMountVector = function (job, parent) {

            diagramData.push([job.name, parent, '']);
            for (var i = 0; i < job.children.length; i++) {
                testGoogleChartMountVector(job.children[i], job.name);
            }

            return;
        };
        return {
            init: function () {

                // job/index
                if ($("#jobTable").length > 0) {
                    initJobDataTable();
                }

                // job/create
                if ($("textarea[name*=jobDescription]").length > 0) {
                    require(['trumbowyg'], function () {
                        $("textarea[name*=jobDescription]").trumbowyg({
                            autogrow: true,
                            fullscreenable: false,
                            btns: [
                                'btnGrp-design',
                                'btnGrp-justify',
                                'btnGrp-lists',
                                ['horizontalRule']
                            ],
                            btnsGrps: {
                                design: ['bold', 'italic', 'underline'],
                                semantic: ['strong', 'em'],
                                justify: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                lists: ['unorderedList', 'orderedList']
                            }
                        });
                    });
                }

                // job/office-manager
                if ($("#office-table").length > 0) {
                    initDataTable();
                    initJobs();
                }

                //job/hiearchy
                if ($("#hierarchy").length > 0) {
                    testGoogleChart();
                }

            }
            ,
            getDataOf: function (selectedItemId) {
                var jobs = [];
                $(".job-checkbox:checked").each(function () {
                    jobs.push($(this).val());
                });
                var jobs = {
                    jobs: jobs
                };
                return jobs;
            },
            getCallbackOf: function (element) {
                return {
                    exec: function (data) {
                    }
                };
            }
        };
    }());
    return jobModule;
});
