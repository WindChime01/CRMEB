define(function () {
    'use strict';
    var timer;
    var count = 0;
    function isPlainObject(value) {
        return value && value.constructor.name === 'Object';
    }

    function isFunction(value) {
        return typeof value === 'function';
    }

    function stringifyQuery(query) {
        return Object.keys(query).map(function (key) {
            return encodeURIComponent(key) + '=' + encodeURIComponent(query[key]);
        }).join('&');
    }

    function heartbeat(socket) {
        if (timer) {
            clearTimeout(timer);
        }
        timer = setTimeout(function () {
            socket.send({type: 'ping'});
        }, 3000);
    }
    function Socket(options) {
        this.options = options;
        this._init(options);
    }

    Socket.prototype._init = function (options) {
        var self = this;
        var url = window.location.protocol.replace('http', 'ws') + '//' + window.location.hostname;
        var query = '';
        if (isPlainObject(options.query)) {
            query = stringifyQuery(options.query);
        }
        if (options.port) {
            url += ':' + options.port;
        }
        if (query) {
            url += '?' + query;
        }
        this._ws = new WebSocket(url);
        this._ws.onopen = function () {
            count = 0;
            heartbeat(self);
            if (isFunction(options.onopen)) {
                options.onopen();
            }
        };
        this._ws.onmessage = function (event) {
            heartbeat(self);
            if (isFunction(options.onmessage)) {
                options.onmessage(JSON.parse(event.data));
            }
        };
        this._ws.onerror = function (event) {
            if (isFunction(options.onerror)) {
                options.onerror();
            }
        };
        this._ws.onclose = function (event) {
            console.error('WebSocket 断开：' + event.code + ' ' + event.reason);
            count++;
            if (count <= 5) {
                self._init(self.options);
            }
            if (isFunction(options.onclose)) {
                options.onclose();
            }
        };
    };

    Socket.prototype.send = function (data) {
        if (this.state() !== 1) {
            return;
        }
        this._ws.send(JSON.stringify(data));
    };

    Socket.prototype.close = function () {
        this._ws.close();
    };

    Socket.prototype.state = function () {
        return this._ws.readyState;
    };

    return Socket;
});