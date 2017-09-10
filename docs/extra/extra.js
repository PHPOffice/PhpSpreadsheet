var nodemcu = nodemcu || {};
(function () {
    'use strict';

    $(document).ready(function () {
        fixSearch();
    });

    /*
     * RTD messes up MkDocs' search feature by tinkering with the search box defined in the theme, see
     * https://github.com/rtfd/readthedocs.org/issues/1088. This function sets up a DOM4 MutationObserver
     * to react to changes to the search form (triggered by RTD on doc ready). It then reverts everything
     * the RTD JS code modified.
     */
    function fixSearch() {
        var target = document.getElementById('rtd-search-form');
        var config = {attributes: true, childList: true};

        var observer = new MutationObserver(function (mutations) {
            // if it isn't disconnected it'll loop infinitely because the observed element is modified
            observer.disconnect();
            var form = $('#rtd-search-form');
            form.empty();
            form.attr('action', 'https://' + window.location.hostname + '/en/' + determineSelectedBranch() + '/search.html');
            $('<input>').attr({
                type: "text",
                name: "q",
                placeholder: "Search docs"
            }).appendTo(form);
        });

        if (window.location.origin.indexOf('readthedocs') > -1) {
            observer.observe(target, config);
        }
    }

    /**
     * Analyzes the URL of the current page to find out what the selected GitHub branch is. It's usually
     * part of the location path. The code needs to distinguish between running MkDocs standalone
     * and docs served from RTD. If no valid branch could be determined 'dev' returned.
     *
     * @returns GitHub branch name
     */
    function determineSelectedBranch() {
        var branch = 'dev', path = window.location.pathname;
        if (window.location.origin.indexOf('readthedocs') > -1) {
            // path is like /en/<branch>/<lang>/build/ -> extract 'lang'
            // split[0] is an '' because the path starts with the separator
            var thirdPathSegment = path.split('/')[2];
            // 'latest' is an alias on RTD for the 'dev' branch - which is the default for 'branch' here
            if (thirdPathSegment !== 'latest') {
                branch = thirdPathSegment;
            }
        }
        return branch;
    }
}());