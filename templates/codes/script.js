(function(self, jq) {
    const refresh = {
        // The value returned by the javascript setInterval() function
        value: null,
        // The interval between refresh
        interval: 15,
        // The number of seconds left before refreshing
        timer: 0,
    };

    const timerIds = {
        // The id of the HTML element displaying the timer
        timer: "jaxon-supervisor-refresh-timer",
        // The id of the HTML element displaying the do icon
        do: "jaxon-supervisor-refresh-do",
        // The id of the HTML element displaying the enable icon
        enable: "jaxon-supervisor-refresh-enable",
        // The id of the HTML element displaying the disable icon
        disable: "jaxon-supervisor-refresh-disable",
    };

    self.enableRefresh = () => {
        if(refresh.value !== null)
        {
            return;
        }
        refresh.timer = refresh.interval;
        refresh.value = setInterval(self.updateCounter, 1000);

        // jq(timerIds.do).hide();
        jq(timerIds.enable).hide();
        jq(timerIds.disable).show();
    };

    self.disableRefresh = () => {
        jq(timerIds.timer).html('0');
        clearInterval(refresh.value);
        refresh.value = null;

        // jq(timerIds.do).show();
        jq(timerIds.enable).show();
        jq(timerIds.disable).hide();
    };

    self.updateCounter = () => {
        const elt = jq(timerIds.timer);
        if(!elt)
        {
            // Stop the timer if the page is not displayed
            self.disableRefresh();
            return;
        }

        elt.html(refresh.timer);
        --refresh.timer < 0 && self.doRefresh();
    },

    self.doRefresh = () => {
        self.disableRefresh();
        <?php echo $this->rqHome->refresh() ?>;
    }
})({}, jaxon.parser.query);
