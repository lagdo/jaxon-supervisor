jaxon.supervisor = {
    _interval: null, // The value returned by the javascript setInterval() function
    refresh: {
        interval: 15,   // The interval between refresh
        timer: 0        // The number of seconds left before refreshing
    },
    id: {
        timer: "jaxon-supervisor-refresh-timer",    // The id of the HTML element displaying the timer
        do: "jaxon-supervisor-refresh-do",          // The id of the HTML element displaying the do icon
        enable: "jaxon-supervisor-refresh-enable",  // The id of the HTML element displaying the enable icon
        disable: "jaxon-supervisor-refresh-disable" // The id of the HTML element displaying the disable icon
    },
    enableRefresh: function() {
        if(jaxon.supervisor._interval != null)
        {
            return;
        }
        jaxon.supervisor.refresh.timer = jaxon.supervisor.refresh.interval;
        jaxon.supervisor._interval = setInterval(jaxon.supervisor.updateCounter, 1000);
        // jaxon.$(jaxon.supervisor.id.do).style.display = 'none';
        jaxon.$(jaxon.supervisor.id.enable).style.display = 'none';
        jaxon.$(jaxon.supervisor.id.disable).style.display = 'block';
    },
    disableRefresh: function() {
        let elt = jaxon.$(jaxon.supervisor.id.timer);
        if(elt != null)
        {
            elt.innerHTML = "0";
        }
        clearInterval(jaxon.supervisor._interval);
        jaxon.supervisor._interval = null;
        // jaxon.$(jaxon.supervisor.id.do).style.display = 'block';
        jaxon.$(jaxon.supervisor.id.enable).style.display = 'block';
        jaxon.$(jaxon.supervisor.id.disable).style.display = 'none';
    },
    updateCounter: function() {
        let elt = jaxon.$(jaxon.supervisor.id.timer);
        if(!elt)
        {
            // Stop the timer if the page is not displayed
            jaxon.supervisor.disableRefresh();
            return;
        }
        elt.innerHTML = jaxon.supervisor.refresh.timer;
        jaxon.supervisor.refresh.timer--;
        if(jaxon.supervisor.refresh.timer < 0)
        {
            jaxon.supervisor.doRefresh();
        }
    },
    doRefresh: function() {
        jaxon.supervisor.disableRefresh();
        <?php echo $this->rqHome->refresh() ?>;
    }
}
