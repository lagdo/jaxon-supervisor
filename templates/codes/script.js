/**
 * Class: jaxon.supervisor
 */

jaxon.supervisor = {};
(function(self, query) {
    const refresh = {
        // The interval between refresh
        interval: 15,
        // The number of seconds left before refreshing
        countdown: 0,
        // The object returned by the javascript setInterval() function
        timer: null,
    };

    const selector = {
        // The HTML element displaying the countdown
        countdown: "#jaxon-supervisor-refresh-countdown",
        // The HTML element to be displayed when refresh is enabled
        enabled: ".jaxon-supervisor-refresh-enabled",
        // The HTML element to be displayed when refresh is disabled
        disabled: ".jaxon-supervisor-refresh-disabled",
    };

    const decrementCoundown = () => {
        const elt = query.jq(selector.countdown);
        if (!elt.length) {
            // Stop the timer if the page is not displayed
            self.disableRefresh();
            return;
        }

        elt.html(--refresh.countdown);
        if (refresh.countdown <= 0) {
            self.disableRefresh();
            <?php echo $this->rqHome->refresh(true) ?>;
        }
    };

    self.enableRefresh = () => {
        if (refresh.timer !== null) {
            // There's already a timer. Do nothing.
            return;
        }

        refresh.countdown = refresh.interval;
        refresh.timer = setInterval(decrementCoundown, 1000);

        query.jq(selector.countdown).html(refresh.countdown);
        query.jq(selector.enabled).show();
        query.jq(selector.disabled).hide();
    };

    self.disableRefresh = () => {
        if (refresh.timer === null) {
            // There's no timer. Do nothing.
            return;
        }

        clearInterval(refresh.timer);
        refresh.timer = null;

        query.jq(selector.countdown).html('0');
        query.jq(selector.disabled).show();
        query.jq(selector.enabled).hide();
    };
})(jaxon.supervisor, jaxon.parser.query);
