"use strict";
/**
 * Storm Ajax Json Communicator
 * Client-side code
 */

var Comm_ServerSide = {
    busy: false,
    currentURL: null,
    history: (window.History.Adapter === undefined ?window.history:window.History),
    /* Define hook bind posts */
    hooks: {
        /* Called when busy gets set to true */
        'onBusy': [],
        /* Called when busy gets set to false */
        'onNotBusy': [],
        /* Called after an successful AJAX call, data is passed */
        'onSuccess': [],
        /* Called after an unsuccessful ajax call, data is passed */
        'onError': [],
        /* Called when the load method was successful, data is passed */
        'onLoad': [],
        /* Called when the load method was unsuccessful, data is passed */
        'onLoadError': [],
        /* Called when the form method was successful, form object and data passed */
        'onFormSuccess': [],
        /* Called when the form method was unsuccessful, form object and data passed */
        'onFormUnsuccessful': [],
        /* Called when one or more flash objects have been received, an array of flash(s) is passed */
        'onFlash': []
    }
};

Comm_ServerSide.setBusy = function (busy) {
    if (this.busy !== busy) {
        this.busy = busy;
        this.apply((busy ?'onBusy':'onNotBusy'));
    }
    
};

/**
 * 
 * @param string|jqueryObject form Provide the forms name if passing a string
 * @param string url
 * @param object data
 * @param callable callback
 * @returns undefined
 */
Comm_ServerSide.form = function (form, url, data, callback) {
    var ctx = this;
    if (form !== null && typeof form === 'object') {
        form = $('form[name=\''+form.replace('\'', '\\\'')+'\']');
    }
    if (url === undefined || url === null) {
        url = form.attr('action');
    }
    this.ajax(
        url,
        data,
        function (data) {
            if (callback !== undefined) {
                callback(data);
            }
            ctx.apply('onFormSuccess', [ form, data ]);
        },
        function (data) {
            ctx.apply('onFormError', [ form, data ]);
        }
    );
};

/**
 * 
 * @param string url
 * @param object data
 * @param callable callback
 * @returns undefined
 */
Comm_ServerSide.load = function (url, data, callback) {
    this.currentURL = url;
    this.history.pushState(null, null, url);
    var ctx = this;
    this.ajax(
        url,
        data,
        function (data) {
            if (callback !== undefined) {
                callback(data);
            }
            ctx.apply('onLoad', [ data ]);
        },
        function (data) {
            ctx.apply('onLoadError', [ data ]);
        }
    );
};

/**
 * 
 * @param callable callback
 * @returns undefined
 */
Comm_ServerSide.reload = function (callback) {
    this.load(this.currentURL, null, callback);
};

/**
 * 
 * @param object callback
 * @returns undefined
 */
Comm_ServerSide.findFlash = function (data) {
    if (data.flash !== undefined && data.flash.length > 0) {
        this.apply('onFlash', [ data.flash ]);
    }
};

/**
 * 
 * @param string url
 * @param object data
 * @param callable successCallback
 * @param callable errorCallback
 * @returns undefined
 */
Comm_ServerSide.ajax = function (url, data, successCallback, errorCallback) {
    if (this.busy === true) {
        return null;
    }
    this.setBusy(true);
    var ctx = this;
    if (data === undefined || data === null) {
        data = {};
    }
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        context: ctx,
        success: function (data) {
            this.setBusy(false);
            successCallback(data);
            this.findFlash(data);
            this.apply('onSuccess', [ data ]);
        },
        error: function (data) {
            this.setBusy(false);
            errorCallback(data);
            this.findFlash(data);
            this.apply('onError', [ data ]);
        }
    });
};

/**
 * 
 * @param string event
 * @param array param
 * @returns undefined
 */
Comm_ServerSide.apply = function (event, args) {
    if (args === undefined || args === null) {
        args = [];
    }
    if (this.hooks[event+''] !== undefined) {
        var funcs = this.hooks[event+''];
        for(var i = 0;i < funcs.length;i++) {
            funcs[i].apply(null, args);
        }
    }
};

/**
 * 
 * @param string event
 * @param callable callback
 * @returns undefined
 */
Comm_ServerSide.hook = function (event, callback) {
    if (this.hooks[event+''] !== undefined) {
        this.hooks[event+''].push(callback);
    }
};

(function(window,undefined){
    if (window.History.Adapter !== undefined) {
        History.Adapter.bind(window,'statechange',function () {
            var url = History.getState().hash;
            if (Comm_ServerSide.currentURL !== url) {
               Comm_ServerSide.load(url);
            }
        });
    } else {
        window.onpopstate = function (event) {
            var hostname = document.location.protocol + '//' + document.location.hostname;
            var url = document.location.href.replace(hostname, '');
            if (Comm_ServerSide.currentURL !== url) {
               Comm_ServerSide.load(url);
            }
        };
    }
})(window);