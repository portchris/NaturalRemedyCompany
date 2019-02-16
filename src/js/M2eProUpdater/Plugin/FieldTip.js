MagentoFieldTip = Class.create();
MagentoFieldTip.prototype = {

    // ---------------------------------------

    initialize: function()
    {
        this.isHideToolTip = false;
    },

    // ---------------------------------------

    onToolTipMouseLeave: function()
    {
        var self = MagentoFieldTipObj;
        var element = this;

        self.isHideToolTip = true;

        setTimeout(function() {
            self.isHideToolTip && element.hide();
        }, 1000);
    },

    onToolTipMouseEnter: function()
    {
        var self = MagentoFieldTipObj;
        self.isHideToolTip = false;
    },

    onToolTipIconMouseLeave: function()
    {
        var self = MagentoFieldTipObj;
        var element = this.up().select('.m2eproupdater-tool-tip-message')[0];

        self.isHideToolTip = true;

        setTimeout(function() {
            self.isHideToolTip && element.hide();
        }, 1000);
    },

    // ---------------------------------------

    showToolTip: function()
    {
        var self = MagentoFieldTipObj;

        self.isHideToolTip = false;

        $$('.m2eproupdater-tool-tip-message').each(function(element) {
            element.hide();
        });

        self.changeToolTipPosition(this);
        this.up().select('.m2eproupdater-tool-tip-message')[0].show();
    },

    // ---------------------------------------

    changeToolTipPosition: function(element)
    {
        var toolTip = element.up().select('.m2eproupdater-tool-tip-message')[0];

        var settings = {
            setHeight: false,
            setWidth: false,
            setLeft: true,
            offsetTop: 25,
            offsetLeft: 0
        };

        if (element.up().getStyle('float') == 'right') {
            settings.offsetLeft += 18;
        }
        if (element.up().match('span')) {
            settings.offsetLeft += 15;
        }

        toolTip.clonePosition(element, settings);

        if (toolTip.hasClassName('tip-left')) {
            toolTip.style.left = (parseInt(toolTip.style.left) - toolTip.getWidth() - 10) + 'px';
        }
    }

    // ---------------------------------------
};