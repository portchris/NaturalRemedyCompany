ConfigurationHandler = Class.create();
ConfigurationHandler.prototype = {

    popupObj: null,

    // ---------------------------------------

    initialize: function() {},

    // ---------------------------------------

    showModuleChangelogPopup: function(module)
    {
        var self = this;

        new Ajax.Request(UrlHandlerObj.get('adminhtml/configuration/getChangeLogHtml'), {
            method: 'post',
            asynchronous: true,
            parameters: {
                module: module
            },
            onSuccess: function(transport) {

                self.popupObj = Dialog.info(null, {
                    draggable: true,
                    resizable: true,
                    closable: true,
                    className: "magento",
                    windowClassName: "popup-window",
                    title: TranslatorHandlerObj.translate('Changelog'),
                    top: 50,
                    height: 300,
                    width: 560,
                    zIndex: 100,
                    hideEffect: Element.hide,
                    showEffect: Element.show
                });

                self.popupObj.options.destroyOnClose = true;

                $('modal_dialog_message').insert(transport.responseText);
                $('modal_dialog_message').innerHTML.evalScripts();

                self.autoHeightFix();
            }
        });
    },

    autoHeightFix: function()
    {
        setTimeout(function() {
            Windows.getFocusedWindow().content.style.height = '';
            Windows.getFocusedWindow().content.style.maxHeight = '650px';
        }, 150);
    }

    // ---------------------------------------
};