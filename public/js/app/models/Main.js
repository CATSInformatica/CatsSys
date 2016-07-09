/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define(['bootbox', 'jquery', 'bootstrap'], function (bootbox) {

    var fnTypes = {
        selectedHttpClick: {
            selectionRequired: true,
            allowMultiple: false,
            messageRequired: false
        },
        selectedAjaxClick: {
            selectionRequired: true,
            allowMultiple: true,
            messageRequired: true
        },
        ajaxPostSelectedClick: {
            selectionRequired: true,
            allowMultiple: true,
            messageRequired: true
        },
        ajaxClick: {
            selectionRequired: false,
            allowMultiple: false,
            messageRequired: true
        },
        ajaxPostClick: {
            selectionRequired: false,
            allowMultiple: false,
            messageRequired: true
        },
        httpClick: {
            selectionRequired: false,
            allowMultiple: false,
            messageRequired: false
        }
    };
    var config = {};
    var pageConfig = {};
    var numberOfSelectedElements = 0;

    var resultMessages = {
        header: "<b>Executando operações</b>",
        alert: null,
        setAlert: function (alert) {
            this.alert = alert;
        },
        addLine: function (text) {
            this.notify("<p>" + text + "<p>");
        },
        notify: function (text) {
            this.alert.find(".bootbox-body").append(text);
        },
        clear: function () {
            this.message = null;
            this.alert = null;
        },
        getHeader: function () {
            return this.header;
        }
    };

    // execute only defined actions in fnTypes
    initClick = function () {

        var selectedSuffixInfo = "";
        var itemConfig;
        var selectedItemUrl;
        $(config.toolbarElement).on('click', config.toolbarItem, function (e) {
            e.preventDefault();
            itemConfig = getItemInfo($(this));
            if (fnTypes.hasOwnProperty(itemConfig.fnType)) {

                // ação exige que o usuário selecione algum elemento na interface
                if (fnTypes[itemConfig.fnType].selectionRequired) {

                    if (numberOfSelectedElements === 0) {
                        return bootbox.alert("Nenhum elemento de seleção (ex: linha de uma tabela) foi escolhido.");
                    }

                    if (!fnTypes[itemConfig.fnType].allowMultiple && numberOfSelectedElements > 1) {
                        return bootbox.alert("Esta ação não permite a seleção de múltiplos itens " +
                                " (" + numberOfSelectedElements + " foram selecionados).\n\ Por favor, escolha apenas um.");
                    }

                    selectedSuffixInfo = numberOfSelectedElements + (numberOfSelectedElements > 1 ? " itens foram selecionados." : " item foi selecionado.");
                }

//                bootbox.confirm("Tem certeza que deseja executar " +
//                        "esta ação? [" + itemConfig.title + "]. " + selectedSuffixInfo, function (result) {

                // o usuário desistiu de executar a ação
//                            if (!result) {
//                                return bootbox.alert('Ação abortada.');
//                            }

                var selected = $(document).find(".cats-selected-row");
                var deferreds = [];

                // need message? show alert!
                if (fnTypes[itemConfig.fnType].messageRequired) {
                    resultMessages.clear();
                    resultMessages.setAlert(bootbox.alert(resultMessages.getHeader()));
                }

                if (fnTypes[itemConfig.fnType].selectionRequired) {
                    selectedItemUrl = itemConfig.url;
                    if (fnTypes[itemConfig.fnType].allowMultiple) {
                        selected.each(function (e) {
                            itemConfig.url = selectedItemUrl.replace('$id', $(this).data("id"));
                            deferreds.push(fnTypes[itemConfig.fnType].fn(itemConfig));
                        });
                    } else {
                        itemConfig.url = selectedItemUrl.replace("$id", selected.data("id"));
                        deferreds.push(fnTypes[itemConfig.fnType].fn(itemConfig));
                    }
                } else {
                    deferreds.push(fnTypes[itemConfig.fnType].fn(itemConfig));
                }

                $.when.apply(null, deferreds).then(function () {
                    // need message? append complete!
                    if (fnTypes[itemConfig.fnType].messageRequired) {
                        resultMessages.addLine("<b>Resultado:</b><br>- Sucesso<br><br><b>Concluído</b>.");
                    }
                }, function () {
                    // need message? append complete!
                    if (fnTypes[itemConfig.fnType].messageRequired) {
                        resultMessages.addLine("<b>Resultado:</b><br>- Uma ou mais ações retornaram erros.<br><br><b>Concluído</b>.");
                    }
                });
//                        });
            } else {
                bootbox.alert('fnType: [' + itemConfig.fnType + '] não existe.');
            }
        });
    };
    // select an element for page reload or new tab or ...
    fnTypes.selectedHttpClick.fn = function (itemConfig) {
        return fnTypes.httpClick.fn(itemConfig);
    };
    // select an element and the page will send a post without data
    fnTypes.selectedAjaxClick.fn = function (itemConfig) {

        var message;
        return $.ajax({
            url: itemConfig.url,
            type: "POST",
            success: function (data) {
                if (typeof data.message === "undefined") {
                    message = "- A requisição foi executada com sucesso, no entanto, nenhuma mensagem foi especificada pelo servidor.<br>";
                } else {
                    message = data.message + "<br>";
                }

                /**
                 * callback on page config
                 */

                if (typeof data.callback !== "undefined") {
                    if (typeof pageConfig.getCallbackOf !== "undefined") {
                        pageConfig.getCallbackOf(itemConfig.id).exec(data.callback);
                    } else {
                        message += "- O servidor retornou um parâmetro de <em>callback</em>, mas a função <code>getCallbackOf(selectedItemId)</code> não foi encontrada.";
                    }
                }

                if (itemConfig.hideOnSuccess === true) {
                    closeToolbar();
                }

                resultMessages.addLine(message);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                resultMessages.addLine(textStatus);
            }
        });
    };
    // send a post with previous selection and with particular data
    fnTypes.ajaxPostSelectedClick.fn = function (itemConfig) {

        var defer = $.Deferred();
        var message;
        if (typeof pageConfig.getDataOf === "undefined") {

            defer.then(null, function () {
                resultMessages.addLine("- Ações do tipo <code>" + itemConfig.fnType + "</code> " +
                        "exigem que a função <code>getDataOf(selectedItemId)</code>" +
                        " seja implementada.");
            });

            defer.reject();
            return defer;
        }

        var dataToSend = pageConfig.getDataOf(itemConfig.id);
        if (typeof dataToSend !== "object") {

            defer.then(null, function () {
                resultMessages.addLine("- A função <code>getDataOf</code> deve retornar um objeto," +
                        " <code>undefined</code> encontrado.");
            });

            defer.reject();
            return defer;
        }

        return $.ajax({
            url: itemConfig.url,
            type: 'POST',
            data: dataToSend,
            success: function (data) {
                if (typeof data.message === "undefined") {
                    message = "- A requisição foi executada com sucesso, no entanto, nenhuma mensagem foi especificada pelo servidor.<br>";
                } else {
                    message = data.message;
                }

                /**
                 * callback on page config
                 */

                if (typeof data.callback !== "undefined") {
                    if (typeof pageConfig.getCallbackOf !== "undefined") {
                        pageConfig.getCallbackOf(itemConfig.id).exec(data.callback);
                    } else {
                        message += "- O servidor retornou um parâmetro de <em>callback</em>, mas a função <code>getCallbackOf(selectedItemId)</code> não foi encontrada.";
                    }
                }

                if (itemConfig.hideOnSuccess === true) {
                    closeToolbar();
                }

                resultMessages.addLine(message);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                resultMessages.addLine(textStatus);
            }
        });
    };
    // click and a post will be sent without any previous selection and without data
    fnTypes.ajaxClick.fn = function (itemConfig) {

        var message;

        return $.ajax({
            url: itemConfig.url,
            type: 'POST',
            success: function (data) {
                if (typeof data.message === "undefined") {
                    message = "- A requisição foi executada com sucesso, no entanto, nenhuma mensagem foi especificada pelo servidor.";
                } else {
                    message = data.message;
                }

                resultMessages.addLine(message);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                resultMessages.addLine(textStatus);
            }
        });
    };
    // click and a post will be sent without any previous selection and with particular data
    fnTypes.ajaxPostClick.fn = function (itemConfig) {

        var defer = $.Deferred();
        var message;
        if (typeof pageConfig.getDataOf === "undefined") {

            defer.then(null, function () {
                resultMessages.addLine("- Ações do tipo <code>" + itemConfig.fnType + "</code> " +
                        "exigem que a função <code>getDataOf(selectedItemId)</code>" +
                        " seja implementada.");
            });

            defer.reject();
            return defer;
        }

        var dataToSend = pageConfig.getDataOf(itemConfig.id);
        if (typeof dataToSend !== "object") {

            defer.then(null, function () {
                resultMessages.addLine("- A função <code>getDataOf</code> deve retornar um objeto," +
                        " <code>undefined</code> encontrado.");
            });

            defer.reject();
            return defer;
        }

        return $.ajax({
            url: itemConfig.url,
            type: 'POST',
            data: dataToSend,
            success: function (data) {

                if (typeof data.message === "undefined") {
                    message = "- A requisição foi executada com sucesso, no entanto, nenhuma mensagem foi especificada pelo servidor.<br>";
                } else {
                    message = data.message;
                }

                /**
                 * callback on page config
                 */

                if (typeof data.callback !== "undefined") {
                    if (typeof pageConfig.getCallbackOf !== "undefined") {
                        pageConfig.getCallbackOf(itemConfig.id).exec(data.callback);
                    } else {
                        message += "- O servidor retornou um parâmetro de <em>callback</em>, mas a função <code>getCallbackOf(selectedItemId)</code> não foi encontrada.";
                    }
                }

                if (itemConfig.hideOnSuccess === true) {
                    closeToolbar();
                }

                resultMessages.addLine(message);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                resultMessages.addLine(textStatus);
            }
        });
    };
    // reload the page or open a new tab without any previous selection
    fnTypes.httpClick.fn = function (itemConfig) {
        var def = $.Deferred();
        window.open(itemConfig.url, itemConfig.target);
        def.resolve();
        return def;
    };
    getItemInfo = function (toolbarItem) {

        var item = toolbarItem.find("a");
        var url = item.attr("href");
        var target = item.attr("target");
        return {
            id: toolbarItem.attr("id"),
            title: toolbarItem.data("title"),
            fnType: toolbarItem.data("fntype"),
            hideOnSuccess: toolbarItem.data("hideonsuccess"),
            url: url,
            target: (typeof target !== "undefined" ? target : "_self")
        };
    };
    initToggle = function () {
        $(".content").on("click", ".cats-row", function (e) {
            var selectedElement = $(this);
            if (selectedElement.is('tr')) {
                selectedElement.find('td').toggleClass('cats-selected-bg');
            } else {
                selectedElement
                        .toggleClass('cats-selected-bg');
            }

            selectedElement.toggleClass('cats-selected-row');
            setNumberOfSelectedElements();

            if (e.ctrlKey) {
                if (selectedElement.hasClass('cats-selected-row')) {
                    openToolbar();
                } else if (numberOfSelectedElements === 0) {
                    closeToolbar();
                }
            }
        });
        $(document).keyup(function (e) {

            if (e.keyCode === 27) {
                $(this)
                        .find(".cats-selected-bg, .cats-selected-row")
                        .removeClass("cats-selected-bg cats-selected-row");
                closeToolbar();
                setNumberOfSelectedElements();
            }
        });
    };
    setNumberOfSelectedElements = function () {
        numberOfSelectedElements = $(document).find(".cats-selected-row").length;
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
