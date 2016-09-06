/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['jquery', 'datatable', 'ekkolightbox'], function () {
    var index = (function () {

        var configTable = $('#config-table');

        initDataTable = function () {
            
            configTable.DataTable({
                dom: 'lftip',
                paging: false
            });
        };
        
        initLightbox = function () {
            $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    loadingMessage: "Carregando..."
                });
            });
        };

        return {
            init: function () {
                initDataTable();
                initLightbox();
            },
            getCallbackOf: function (element) {
                
                return {
                    exec: function (data) {
                        configTable
                                .DataTable()
                                .row('#bg-config-' + data.bgConfigId)
                                .remove()
                                .draw();
                    }
                };

            }
        };

    }());    
    
    return index;
});