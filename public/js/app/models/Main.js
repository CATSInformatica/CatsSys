/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['bootbox', 'jquery', 'bootstrap'], function (bootbox) {

    var fnTypes = {};
    var config = {};
    var pageConfig = {};

    initClick = function () {
        $(config.toolbarElement).on('click', config.toolbarItem, function (e) {
            // nenhuma ação é executada se não estiver especificada abaixo
            e.preventDefault();
            var fnType = $(this).data('fntype');
            if (fnTypes.hasOwnProperty(fnType)) {
                fnTypes[fnType].call(this, $(this));
            } else {
                bootbox.alert('fnType: (' + fnType + ') não definida.');
            }
        });
    };

    fnTypes.selectedHttpClick = function (toolbarItem) {
        var selectedResult = getSelectedItemInfo(toolbarItem);
        if (selectedResult !== null) {
            window.open(selectedResult.url, selectedResult.target);
        } else {
            bootbox.alert('Nenhuma item (ex: linha de uma tabela) foi escolhido.');
        }
    };

    fnTypes.selectedAjaxClick = function (toolbarItem) {

        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? [' + toolbarItem.data('title') + ']', function (result) {
            if (result) {
                var selectedResult = getSelectedItemInfo(toolbarItem);
                if (selectedResult !== null) {
                    $.ajax({
                        url: selectedResult.url,
                        type: 'POST',
                        success: function (data) {
                            bootbox.alert(data.message);

                            /**
                             * callback on page config
                             */

                            if (typeof pageConfig.getCallbackOf(toolbarItem.attr('id')) !== 'undefined') {
                                pageConfig.getCallbackOf(toolbarItem.attr('id')).exec(data.callback);
                            }


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            bootbox.alert(textStatus);
                        }
                    });
                } else {
                    bootbox.alert('Nenhuma item (ex: linha de uma tabela) foi escolhido.');
                }
            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };

    fnTypes.ajaxClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? [' + toolbarItem.data('title') + ']', function (result) {
            if (result) {
                var itemInfo = getItemInfo(toolbarItem);

                $.ajax({
                    url: itemInfo.url,
                    type: 'POST',
                    success: function (data) {
                        bootbox.alert(data.message);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        bootbox.alert(textStatus);
                    }
                });

            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };

    fnTypes.ajaxPostClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? [' + toolbarItem.data('title') + ']', function (result) {
            if (result) {
                var itemInfo = getItemInfo(toolbarItem);
                $.ajax({
                    url: itemInfo.url,
                    type: 'POST',
                    data: pageConfig.getDataOf(toolbarItem.attr('id')),
                    success: function (data) {
                        bootbox.alert(data.message);

                        /**
                         * callback on page config
                         */

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        bootbox.alert(textStatus);
                    }
                });

            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };

    fnTypes.ajaxPostSelectedClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? [' + toolbarItem.data('title') + ']', function (result) {
            if (result) {
                var selectedResult = getSelectedItemInfo(toolbarItem);
                if (selectedResult !== null) {
                    $.ajax({
                        url: selectedResult.url,
                        type: 'POST',
                        data: pageConfig.getDataOf(toolbarItem.attr('id')),
                        success: function (data) {
                            bootbox.alert(data.message);

                            /**
                             * callback on page config
                             */

                            if (typeof pageConfig.getCallbackOf(toolbarItem.attr('id')) !== 'undefined') {
                                pageConfig.getCallbackOf(toolbarItem.attr('id')).exec(data.callback);
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            bootbox.alert(textStatus);
                        }
                    });
                } else {
                    bootbox.alert('Nenhuma item (ex: linha de uma tabela) foi escolhido.');
                }
            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };



    fnTypes.httpClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? [' + toolbarItem.data('title') + ']', function (result) {
            if (result) {
                var selectedResult = getItemInfo(toolbarItem);
                window.open(selectedResult.url, selectedResult.target);
            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };

    getSelectedItemInfo = function (toolbarItem) {
        if ($(config.toolbarSelectedItem).length > 0) {
            var item = toolbarItem.find('a');

            var url = item.attr('href');
            var target = item.attr('target');
            url = url.replace('$id', $(config.toolbarSelectedItem).data('id'));

            return {
                url: url,
                target: (typeof target !== 'undefined' ? target : '_self')
            };
        }

        return null;
    };

    getItemInfo = function (toolbarItem) {
        var item = toolbarItem.find('a');
        var url = item.attr('href');
        var target = item.attr('target');

        return {
            url: url,
            target: (typeof target !== 'undefined' ? target : '_self')
        };
    };

    initToggle = function () {
        $('section').on('click', '.cats-row', function () {
            var selectedElement = $(this);

            if (selectedElement.is('tr')) {
                selectedElement.siblings('tr')
                        .removeClass('cats-selected-row')
                        .find('td')
                        .removeClass('cats-selected-bg');
                selectedElement.find('td').toggleClass('cats-selected-bg');
            } else {
                selectedElement
                        .siblings(".cats-row")
                        .removeClass('cats-selected-row')
                        .removeClass('cats-selected-bg');

                selectedElement
                        .toggleClass('cats-selected-bg')
                        .closest('.container')
                        .find('.cats-row')
                        .not(this)
                        .removeClass('cats-selected-row')
                        .removeClass('cats-selected-bg');
            }

            selectedElement.toggleClass('cats-selected-row');

            if (selectedElement.hasClass('cats-selected-row')) {
                openToolbar();
            } else {
                closeToolbar();
            }
        });
    };

    closeToolbar = function () {
        $(config.toolbarContainer).removeClass(config.toolbarContainerOpen);
    };

    openToolbar = function () {

        if (!$(config.toolbarContainer).hasClass(
                config.toolbarContainerOpen)) {
            $(config.toolbarContainer).addClass(
                    config.toolbarContainerOpen);
        }
    };

    return {
        init: function () {
            if (config.toolbarElement === '' ||
                    config.toolbarItem === '' ||
                    config.toolbarSelectedItem === '' ||
                    config.toolbarContainer === '' ||
                    config.toolbarContainerOpen === ''
                    ) {
                throw 'Config must have the properties `toolbarElement`,' +
                        ' `toolbarItem`, `toolbarSelectedItem`,' +
                        ' `toolbarContainer`';
            }

            initClick();
            initToggle();
        },
        setConfig: function (conf) {
            config.toolbarElement = conf.toolbarElement || '';
            config.toolbarItem = conf.toolbarItem || '';
            config.toolbarSelectedItem = conf.toolbarSelectedItem || '';
            config.toolbarContainer = conf.toolbarContainer || '';
            config.toolbarContainerOpen = conf.toolbarContainerOpen || '';
        },
        setPageConfig: function (pageConf) {
            pageConfig = pageConf;
        },
        closeToolbar: closeToolbar,
        openToolbar: openToolbar
    };

});