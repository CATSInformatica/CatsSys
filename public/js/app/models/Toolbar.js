/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(['bootbox', 'jquery', 'bootstrap'], function (bootbox) {

    var fnTypes = {};
    var config = {};

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
        var selectedResult = getSelectedItemInfo(toolbarItem, false);
        if (selectedResult !== null) {
            window.open(selectedResult.url, selectedResult.target);
        } else {
            bootbox.alert('Nenhuma item (ex: linha de uma tabela) foi escolhido.');
        }
    };

    fnTypes.selectedAjaxClick = function (toolbarItem) {

        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? (' + toolbarItem.data('title') + ')', function (result) {
            if (result) {
                var selectedResult = getSelectedItemInfo(toolbarItem, false);
                if (selectedResult !== null) {
                    $.ajax({
                        url: selectedResult.url,
                        type: 'GET',
                        success: function (data) {
                            bootbox.alert(data.message);
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
                'esta ação? (' + toolbarItem.data('title') + ')', function (result) {
            if (result) {
                var itemInfo = getItemInfo(toolbarItem, false);

                $.ajax({
                    url: itemInfo.url,
                    type: 'GET',
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

    fnTypes.httpClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? (' + toolbarItem.data('title') + ')', function (result) {
            if (result) {
                var selectedResult = getItemInfo(toolbarItem, false);
                window.open(selectedResult.url, selectedResult.target);
            } else {
                bootbox.alert('Ação abortada.');
            }
        });
    };

    fnTypes.ajaxUrlClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? (' + toolbarItem.data('title') + ')', function (result) {
            if (result) {
                var itemInfo = getItemInfo(toolbarItem, true);

                $.ajax({
                    url: itemInfo.url,
                    type: 'GET',
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

    fnTypes.selectedAjaxUrlClick = function (toolbarItem) {
        bootbox.confirm('Tem certeza que deseja executar ' +
                'esta ação? (' + toolbarItem.data('title') + ')', function (result) {
            if (result) {
                var selectedResult = getSelectedItemInfo(toolbarItem, true);

                if (selectedResult !== null) {

                    $.ajax({
                        url: selectedResult.url,
                        type: 'GET',
                        success: function (data) {
                            bootbox.alert(data.message);
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

    getSelectedItemInfo = function (toolbarItem, buildUrl) {
        if ($(config.toolbarSelectedItem).length > 0) {
            var item = toolbarItem.find('a');

            var params = '';

            if (buildUrl) {
                var urlArr = window.location.pathname.split('/');
                params = '/' + urlArr.slice(4, urlArr.length).join('/');
            }

            var url = item.attr('href');
            var target = item.attr('target');
            url = url.replace('$id', $(config.toolbarSelectedItem).data('id'));

            return {
                url: url + params,
                target: (typeof target !== 'undefined' ? target : '_self')
            };
        }

        return null;
    };

    getItemInfo = function (toolbarItem, buildUrl) {
        var item = toolbarItem.find('a');
        var params = '';

        if (buildUrl) {
            var urlArr = window.location.pathname.split('/');
            params = '/' + urlArr.slice(4, urlArr.length).join('/');
        }

        var url = item.attr('href');
        var target = item.attr('target');

        return {
            url: url + params,
            target: (typeof target !== 'undefined' ? target : '_self')
        };
    };

    initToggle = function () {
        $('table').on('click', '.cats-row', function () {
            $(this).siblings('tr')
                    .removeClass('cats-selected-row')
                    .find('td')
                    .removeClass('cats-selected-bg');

            $(this).toggleClass('cats-selected-row');

            $(this).find('td').toggleClass('cats-selected-bg');

            if ($(this).hasClass('cats-selected-row')) {
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
        closeToolbar: closeToolbar,
        openToolbar: openToolbar
    };

});
        