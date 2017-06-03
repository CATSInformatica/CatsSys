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

        initDataTable = function () {

            var table = $('#volunteer-table').DataTable({
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
                table.ajax.reload();
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
        
        return {
            init: function () {
                // documents/index
                if ($("#volunteer-table").length > 0) {
                    initDataTable();
                }
            },
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
