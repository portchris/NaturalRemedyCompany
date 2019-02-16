// Create main objects
// ---------------------------------------

UrlHandlerObj = new UrlHandler();
TranslatorHandlerObj = new TranslatorHandler();
MagentoFieldTipObj = new MagentoFieldTip();

function initializationMagentoBlocks()
{
    $$('.m2eproupdater-tool-tip-image').each(function(element) {
        element.observe('mouseover', MagentoFieldTipObj.showToolTip);
        element.observe('mouseout', MagentoFieldTipObj.onToolTipIconMouseLeave);
    });

    $$('.m2eproupdater-tool-tip-message').each(function(element) {
        element.observe('mouseout', MagentoFieldTipObj.onToolTipMouseLeave);
        element.observe('mouseover', MagentoFieldTipObj.onToolTipMouseEnter);
    });
}

// Set main observers
// ---------------------------------------
Event.observe(window, 'load', function() {

    initializationMagentoBlocks();

    var ajaxHandler = {
        onComplete: function(transport) {
            if (Ajax.activeRequestCount == 0) {
                initializationMagentoBlocks();
            }
        }
    };

    Ajax.Responders.register(ajaxHandler);
});
// ---------------------------------------