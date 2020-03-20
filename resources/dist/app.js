!(function(e) {
    var t = {};
    function n(i) {
        if (t[i]) return t[i].exports;
        var o = (t[i] = { i: i, l: !1, exports: {} });
        return e[i].call(o.exports, o, o.exports, n), (o.l = !0), o.exports;
    }
    (n.m = e),
        (n.c = t),
        (n.d = function(e, t, i) {
            n.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: i });
        }),
        (n.r = function(e) {
            'undefined' != typeof Symbol &&
                Symbol.toStringTag &&
                Object.defineProperty(e, Symbol.toStringTag, { value: 'Module' }),
                Object.defineProperty(e, '__esModule', { value: !0 });
        }),
        (n.t = function(e, t) {
            if ((1 & t && (e = n(e)), 8 & t)) return e;
            if (4 & t && 'object' == typeof e && e && e.__esModule) return e;
            var i = Object.create(null);
            if (
                (n.r(i),
                Object.defineProperty(i, 'default', { enumerable: !0, value: e }),
                2 & t && 'string' != typeof e)
            )
                for (var o in e)
                    n.d(
                        i,
                        o,
                        function(t) {
                            return e[t];
                        }.bind(null, o)
                    );
            return i;
        }),
        (n.n = function(e) {
            var t =
                e && e.__esModule
                    ? function() {
                          return e.default;
                      }
                    : function() {
                          return e;
                      };
            return n.d(t, 'a', t), t;
        }),
        (n.o = function(e, t) {
            return Object.prototype.hasOwnProperty.call(e, t);
        }),
        (n.p = '/'),
        n((n.s = 5));
})([
    function(e, t, n) {
        'use strict';
        function i(e, t) {
            var n;
            return function() {
                for (var i = arguments.length, o = new Array(i), r = 0; r < i; r++) o[r] = arguments[r];
                clearTimeout(n),
                    (n = setTimeout(function() {
                        return e.apply(void 0, o);
                    }, t));
            };
        }
        function o(e) {
            return (arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : document).querySelector(e);
        }
        function r(e) {
            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : document;
            return Array.from(t.querySelectorAll(e));
        }
        var a = [
            'a[href]:not([disabled]):not([tabindex="-1"])',
            'button:not([disabled]):not([tabindex="-1"])',
            'textarea:not([disabled]):not([tabindex="-1"])',
            'input:not([type="hidden"]):not([disabled]):not([tabindex="-1"])',
            'select:not([disabled]):not([tabindex="-1"])',
        ];
        function s(e) {
            var t = r(a.join(', '), e),
                n = t[0],
                i = t[t.length - 1];
            function o(e) {
                'Tab' === e.key &&
                    (e.shiftKey
                        ? document.activeElement === n && (e.preventDefault(), i.focus())
                        : document.activeElement === i && (e.preventDefault(), n.focus()));
            }
            return (
                n && n.focus(),
                window.addEventListener('keydown', o),
                function() {
                    window.removeEventListener('keydown', o);
                }
            );
        }
        function c(e, t, n) {
            document.addEventListener(e, function(e) {
                var i = e.target.closest(t);
                i && n({ event: e, target: i });
            });
        }
        function l() {}
        function u(e) {
            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 'transition';
            return new Promise(function(n) {
                e.classList.remove('hidden'),
                    e.classList.add(''.concat(t, '-enter')),
                    e.classList.add(''.concat(t, '-enter-active')),
                    f(function() {
                        e.classList.remove(''.concat(t, '-enter')),
                            h(e, function() {
                                e.classList.remove(''.concat(t, '-enter-active')),
                                    f(function() {
                                        n(e);
                                    });
                            });
                    });
            });
        }
        function d(e) {
            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 'transition';
            return new Promise(function(n) {
                e.classList.add(''.concat(t, '-leave-active')),
                    f(function() {
                        e.classList.add(''.concat(t, '-leave')),
                            h(e, function() {
                                e.classList.remove(''.concat(t, '-leave-active')),
                                    e.classList.remove(''.concat(t, '-leave')),
                                    e.classList.add('hidden'),
                                    f(function() {
                                        n(e);
                                    });
                            });
                    });
            });
        }
        function h(e, t) {
            var n = 1e3 * Number(getComputedStyle(e).transitionDuration.replace('s', ''));
            setTimeout(t, n);
        }
        function f(e) {
            requestAnimationFrame(function() {
                return requestAnimationFrame(e);
            });
        }
        n.d(t, 'c', function() {
            return i;
        }),
            n.d(t, 'h', function() {
                return s;
            }),
            n.d(t, 'f', function() {
                return c;
            }),
            n.d(t, 'g', function() {
                return l;
            }),
            n.d(t, 'a', function() {
                return o;
            }),
            n.d(t, 'b', function() {
                return r;
            }),
            n.d(t, 'd', function() {
                return u;
            }),
            n.d(t, 'e', function() {
                return d;
            });
    },
    function(e, t, n) {
        'use strict';
        n.r(t),
            n.d(t, 'showModal', function() {
                return o;
            });
        var i = n(0);
        function o(e) {
            var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : {},
                n = t.onConfirm,
                o = void 0 === n ? i.g : n,
                a = t.onDismiss,
                s = void 0 === a ? i.g : a,
                c = Object(i.a)('[data-modal="'.concat(e, '"]'));
            Object(i.d)(c, 'fade'), r(c, { onConfirm: o, onDismiss: s, onClose: Object(i.h)(c) });
        }
        function r(e, t) {
            var n = t.onConfirm,
                o = t.onDismiss,
                r = t.onClose;
            function a(t) {
                'Escape' === t.key && e.dispatchEvent(new Event('dismiss'));
            }
            function s() {
                n(), l(), Object(i.e)(e, 'fade');
            }
            function c() {
                o(), l(), Object(i.e)(e, 'fade');
            }
            function l() {
                r(),
                    window.removeEventListener('keydown', a),
                    e.removeEventListener('confirm', s),
                    e.removeEventListener('dismiss', c);
            }
            window.addEventListener('keydown', a), e.addEventListener('confirm', s), e.addEventListener('dismiss', c);
        }
        Object(i.f)('click', '[data-modal-trigger]', function(e) {
            o(e.target.dataset.modalTrigger);
        }),
            Object(i.f)('click', '[data-modal-confirm]', function(e) {
                e.target.closest('[data-modal]').dispatchEvent(new Event('confirm'));
            }),
            Object(i.f)('click', '[data-modal-dismiss]', function(e) {
                e.target.closest('[data-modal]').dispatchEvent(new Event('dismiss'));
            }),
            Object(i.f)('click', '[data-modal-backdrop]', function(e) {
                var t = e.event,
                    n = e.target;
                t.target === n && n.closest('[data-modal]').dispatchEvent(new Event('dismiss'));
            }),
            document.addEventListener('turbolinks:load', function() {
                Object(i.b)('[data-modal]')
                    .filter(function(e) {
                        return !e.classList.contains('hidden');
                    })
                    .forEach(function(e) {
                        r(e, { onConfirm: i.g, onDismiss: i.g, onClose: i.g });
                    });
            });
    },
    function(e, t, n) {
        var i, o;
        (function() {
            (function() {
                (function() {
                    this.Turbolinks = {
                        supported:
                            null != window.history.pushState &&
                            null != window.requestAnimationFrame &&
                            null != window.addEventListener,
                        visit: function(e, t) {
                            return r.controller.visit(e, t);
                        },
                        clearCache: function() {
                            return r.controller.clearCache();
                        },
                        setProgressBarDelay: function(e) {
                            return r.controller.setProgressBarDelay(e);
                        },
                    };
                }.call(this));
            }.call(this));
            var r = this.Turbolinks;
            (function() {
                (function() {
                    var e,
                        t,
                        n,
                        i = [].slice;
                    (r.copyObject = function(e) {
                        var t, n, i;
                        for (t in ((n = {}), e)) (i = e[t]), (n[t] = i);
                        return n;
                    }),
                        (r.closest = function(t, n) {
                            return e.call(t, n);
                        }),
                        (e = (function() {
                            var e;
                            return null != (e = document.documentElement.closest)
                                ? e
                                : function(e) {
                                      var n;
                                      for (n = this; n; ) {
                                          if (n.nodeType === Node.ELEMENT_NODE && t.call(n, e)) return n;
                                          n = n.parentNode;
                                      }
                                  };
                        })()),
                        (r.defer = function(e) {
                            return setTimeout(e, 1);
                        }),
                        (r.throttle = function(e) {
                            var t;
                            return (
                                (t = null),
                                function() {
                                    var n;
                                    return (
                                        (n = 1 <= arguments.length ? i.call(arguments, 0) : []),
                                        null != t
                                            ? t
                                            : (t = requestAnimationFrame(
                                                  (function(i) {
                                                      return function() {
                                                          return (t = null), e.apply(i, n);
                                                      };
                                                  })(this)
                                              ))
                                    );
                                }
                            );
                        }),
                        (r.dispatch = function(e, t) {
                            var i, o, r, a, s, c;
                            return (
                                (c = (s = null != t ? t : {}).target),
                                (i = s.cancelable),
                                (o = s.data),
                                (r = document.createEvent('Events')).initEvent(e, !0, !0 === i),
                                (r.data = null != o ? o : {}),
                                r.cancelable &&
                                    !n &&
                                    ((a = r.preventDefault),
                                    (r.preventDefault = function() {
                                        return (
                                            this.defaultPrevented ||
                                                Object.defineProperty(this, 'defaultPrevented', {
                                                    get: function() {
                                                        return !0;
                                                    },
                                                }),
                                            a.call(this)
                                        );
                                    })),
                                (null != c ? c : document).dispatchEvent(r),
                                r
                            );
                        }),
                        (n = (function() {
                            var e;
                            return (
                                (e = document.createEvent('Events')).initEvent('test', !0, !0),
                                e.preventDefault(),
                                e.defaultPrevented
                            );
                        })()),
                        (r.match = function(e, n) {
                            return t.call(e, n);
                        }),
                        (t = (function() {
                            var e, t, n, i;
                            return null !=
                                (t =
                                    null !=
                                    (n =
                                        null != (i = (e = document.documentElement).matchesSelector)
                                            ? i
                                            : e.webkitMatchesSelector)
                                        ? n
                                        : e.msMatchesSelector)
                                ? t
                                : e.mozMatchesSelector;
                        })()),
                        (r.uuid = function() {
                            var e, t, n;
                            for (n = '', e = t = 1; 36 >= t; e = ++t)
                                n +=
                                    9 === e || 14 === e || 19 === e || 24 === e
                                        ? '-'
                                        : 15 === e
                                        ? '4'
                                        : 20 === e
                                        ? (Math.floor(4 * Math.random()) + 8).toString(16)
                                        : Math.floor(15 * Math.random()).toString(16);
                            return n;
                        });
                }.call(this),
                    function() {
                        r.Location = (function() {
                            function e(e) {
                                var t, n;
                                null == e && (e = ''),
                                    ((n = document.createElement('a')).href = e.toString()),
                                    (this.absoluteURL = n.href),
                                    2 > (t = n.hash.length)
                                        ? (this.requestURL = this.absoluteURL)
                                        : ((this.requestURL = this.absoluteURL.slice(0, -t)),
                                          (this.anchor = n.hash.slice(1)));
                            }
                            var t, n, i, o;
                            return (
                                (e.wrap = function(e) {
                                    return e instanceof this ? e : new this(e);
                                }),
                                (e.prototype.getOrigin = function() {
                                    return this.absoluteURL.split('/', 3).join('/');
                                }),
                                (e.prototype.getPath = function() {
                                    var e, t;
                                    return null !=
                                        (e =
                                            null != (t = this.requestURL.match(/\/\/[^\/]*(\/[^?;]*)/)) ? t[1] : void 0)
                                        ? e
                                        : '/';
                                }),
                                (e.prototype.getPathComponents = function() {
                                    return this.getPath()
                                        .split('/')
                                        .slice(1);
                                }),
                                (e.prototype.getLastPathComponent = function() {
                                    return this.getPathComponents().slice(-1)[0];
                                }),
                                (e.prototype.getExtension = function() {
                                    var e, t;
                                    return null !=
                                        (e =
                                            null != (t = this.getLastPathComponent().match(/\.[^.]*$/)) ? t[0] : void 0)
                                        ? e
                                        : '';
                                }),
                                (e.prototype.isHTML = function() {
                                    return this.getExtension().match(/^(?:|\.(?:htm|html|xhtml))$/);
                                }),
                                (e.prototype.isPrefixedBy = function(e) {
                                    var t;
                                    return (t = n(e)), this.isEqualTo(e) || o(this.absoluteURL, t);
                                }),
                                (e.prototype.isEqualTo = function(e) {
                                    return this.absoluteURL === (null != e ? e.absoluteURL : void 0);
                                }),
                                (e.prototype.toCacheKey = function() {
                                    return this.requestURL;
                                }),
                                (e.prototype.toJSON = function() {
                                    return this.absoluteURL;
                                }),
                                (e.prototype.toString = function() {
                                    return this.absoluteURL;
                                }),
                                (e.prototype.valueOf = function() {
                                    return this.absoluteURL;
                                }),
                                (n = function(e) {
                                    return t(e.getOrigin() + e.getPath());
                                }),
                                (t = function(e) {
                                    return i(e, '/') ? e : e + '/';
                                }),
                                (o = function(e, t) {
                                    return e.slice(0, t.length) === t;
                                }),
                                (i = function(e, t) {
                                    return e.slice(-t.length) === t;
                                }),
                                e
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.HttpRequest = (function() {
                            function t(t, n, i) {
                                (this.delegate = t),
                                    (this.requestCanceled = e(this.requestCanceled, this)),
                                    (this.requestTimedOut = e(this.requestTimedOut, this)),
                                    (this.requestFailed = e(this.requestFailed, this)),
                                    (this.requestLoaded = e(this.requestLoaded, this)),
                                    (this.requestProgressed = e(this.requestProgressed, this)),
                                    (this.url = r.Location.wrap(n).requestURL),
                                    (this.referrer = r.Location.wrap(i).absoluteURL),
                                    this.createXHR();
                            }
                            return (
                                (t.NETWORK_FAILURE = 0),
                                (t.TIMEOUT_FAILURE = -1),
                                (t.timeout = 60),
                                (t.prototype.send = function() {
                                    var e;
                                    return this.xhr && !this.sent
                                        ? (this.notifyApplicationBeforeRequestStart(),
                                          this.setProgress(0),
                                          this.xhr.send(),
                                          (this.sent = !0),
                                          'function' == typeof (e = this.delegate).requestStarted
                                              ? e.requestStarted()
                                              : void 0)
                                        : void 0;
                                }),
                                (t.prototype.cancel = function() {
                                    return this.xhr && this.sent ? this.xhr.abort() : void 0;
                                }),
                                (t.prototype.requestProgressed = function(e) {
                                    return e.lengthComputable ? this.setProgress(e.loaded / e.total) : void 0;
                                }),
                                (t.prototype.requestLoaded = function() {
                                    return this.endRequest(
                                        (function(e) {
                                            return function() {
                                                var t;
                                                return 200 <= (t = e.xhr.status) && 300 > t
                                                    ? e.delegate.requestCompletedWithResponse(
                                                          e.xhr.responseText,
                                                          e.xhr.getResponseHeader('Turbolinks-Location')
                                                      )
                                                    : ((e.failed = !0),
                                                      e.delegate.requestFailedWithStatusCode(
                                                          e.xhr.status,
                                                          e.xhr.responseText
                                                      ));
                                            };
                                        })(this)
                                    );
                                }),
                                (t.prototype.requestFailed = function() {
                                    return this.endRequest(
                                        (function(e) {
                                            return function() {
                                                return (
                                                    (e.failed = !0),
                                                    e.delegate.requestFailedWithStatusCode(
                                                        e.constructor.NETWORK_FAILURE
                                                    )
                                                );
                                            };
                                        })(this)
                                    );
                                }),
                                (t.prototype.requestTimedOut = function() {
                                    return this.endRequest(
                                        (function(e) {
                                            return function() {
                                                return (
                                                    (e.failed = !0),
                                                    e.delegate.requestFailedWithStatusCode(
                                                        e.constructor.TIMEOUT_FAILURE
                                                    )
                                                );
                                            };
                                        })(this)
                                    );
                                }),
                                (t.prototype.requestCanceled = function() {
                                    return this.endRequest();
                                }),
                                (t.prototype.notifyApplicationBeforeRequestStart = function() {
                                    return r.dispatch('turbolinks:request-start', {
                                        data: { url: this.url, xhr: this.xhr },
                                    });
                                }),
                                (t.prototype.notifyApplicationAfterRequestEnd = function() {
                                    return r.dispatch('turbolinks:request-end', {
                                        data: { url: this.url, xhr: this.xhr },
                                    });
                                }),
                                (t.prototype.createXHR = function() {
                                    return (
                                        (this.xhr = new XMLHttpRequest()),
                                        this.xhr.open('GET', this.url, !0),
                                        (this.xhr.timeout = 1e3 * this.constructor.timeout),
                                        this.xhr.setRequestHeader('Accept', 'text/html, application/xhtml+xml'),
                                        this.xhr.setRequestHeader('Turbolinks-Referrer', this.referrer),
                                        (this.xhr.onprogress = this.requestProgressed),
                                        (this.xhr.onload = this.requestLoaded),
                                        (this.xhr.onerror = this.requestFailed),
                                        (this.xhr.ontimeout = this.requestTimedOut),
                                        (this.xhr.onabort = this.requestCanceled)
                                    );
                                }),
                                (t.prototype.endRequest = function(e) {
                                    return this.xhr
                                        ? (this.notifyApplicationAfterRequestEnd(),
                                          null != e && e.call(this),
                                          this.destroy())
                                        : void 0;
                                }),
                                (t.prototype.setProgress = function(e) {
                                    var t;
                                    return (
                                        (this.progress = e),
                                        'function' == typeof (t = this.delegate).requestProgressed
                                            ? t.requestProgressed(this.progress)
                                            : void 0
                                    );
                                }),
                                (t.prototype.destroy = function() {
                                    var e;
                                    return (
                                        this.setProgress(1),
                                        'function' == typeof (e = this.delegate).requestFinished && e.requestFinished(),
                                        (this.delegate = null),
                                        (this.xhr = null)
                                    );
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.ProgressBar = (function() {
                            function t() {
                                (this.trickle = e(this.trickle, this)),
                                    (this.stylesheetElement = this.createStylesheetElement()),
                                    (this.progressElement = this.createProgressElement());
                            }
                            var n;
                            return (
                                (n = 300),
                                (t.defaultCSS =
                                    '.turbolinks-progress-bar {\n  position: fixed;\n  display: block;\n  top: 0;\n  left: 0;\n  height: 3px;\n  background: #0076ff;\n  z-index: 9999;\n  transition: width 300ms ease-out, opacity 150ms 150ms ease-in;\n  transform: translate3d(0, 0, 0);\n}'),
                                (t.prototype.show = function() {
                                    return this.visible
                                        ? void 0
                                        : ((this.visible = !0),
                                          this.installStylesheetElement(),
                                          this.installProgressElement(),
                                          this.startTrickling());
                                }),
                                (t.prototype.hide = function() {
                                    return this.visible && !this.hiding
                                        ? ((this.hiding = !0),
                                          this.fadeProgressElement(
                                              (function(e) {
                                                  return function() {
                                                      return (
                                                          e.uninstallProgressElement(),
                                                          e.stopTrickling(),
                                                          (e.visible = !1),
                                                          (e.hiding = !1)
                                                      );
                                                  };
                                              })(this)
                                          ))
                                        : void 0;
                                }),
                                (t.prototype.setValue = function(e) {
                                    return (this.value = e), this.refresh();
                                }),
                                (t.prototype.installStylesheetElement = function() {
                                    return document.head.insertBefore(this.stylesheetElement, document.head.firstChild);
                                }),
                                (t.prototype.installProgressElement = function() {
                                    return (
                                        (this.progressElement.style.width = 0),
                                        (this.progressElement.style.opacity = 1),
                                        document.documentElement.insertBefore(this.progressElement, document.body),
                                        this.refresh()
                                    );
                                }),
                                (t.prototype.fadeProgressElement = function(e) {
                                    return (this.progressElement.style.opacity = 0), setTimeout(e, 450);
                                }),
                                (t.prototype.uninstallProgressElement = function() {
                                    return this.progressElement.parentNode
                                        ? document.documentElement.removeChild(this.progressElement)
                                        : void 0;
                                }),
                                (t.prototype.startTrickling = function() {
                                    return null != this.trickleInterval
                                        ? this.trickleInterval
                                        : (this.trickleInterval = setInterval(this.trickle, n));
                                }),
                                (t.prototype.stopTrickling = function() {
                                    return clearInterval(this.trickleInterval), (this.trickleInterval = null);
                                }),
                                (t.prototype.trickle = function() {
                                    return this.setValue(this.value + Math.random() / 100);
                                }),
                                (t.prototype.refresh = function() {
                                    return requestAnimationFrame(
                                        (function(e) {
                                            return function() {
                                                return (e.progressElement.style.width = 10 + 90 * e.value + '%');
                                            };
                                        })(this)
                                    );
                                }),
                                (t.prototype.createStylesheetElement = function() {
                                    var e;
                                    return (
                                        ((e = document.createElement('style')).type = 'text/css'),
                                        (e.textContent = this.constructor.defaultCSS),
                                        e
                                    );
                                }),
                                (t.prototype.createProgressElement = function() {
                                    var e;
                                    return (
                                        ((e = document.createElement('div')).className = 'turbolinks-progress-bar'), e
                                    );
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.BrowserAdapter = (function() {
                            function t(t) {
                                (this.controller = t),
                                    (this.showProgressBar = e(this.showProgressBar, this)),
                                    (this.progressBar = new r.ProgressBar());
                            }
                            var n, i, o;
                            return (
                                (o = r.HttpRequest),
                                (n = o.NETWORK_FAILURE),
                                (i = o.TIMEOUT_FAILURE),
                                (t.prototype.visitProposedToLocationWithAction = function(e, t) {
                                    return this.controller.startVisitToLocationWithAction(e, t);
                                }),
                                (t.prototype.visitStarted = function(e) {
                                    return e.issueRequest(), e.changeHistory(), e.loadCachedSnapshot();
                                }),
                                (t.prototype.visitRequestStarted = function(e) {
                                    return (
                                        this.progressBar.setValue(0),
                                        e.hasCachedSnapshot() || 'restore' !== e.action
                                            ? this.showProgressBarAfterDelay()
                                            : this.showProgressBar()
                                    );
                                }),
                                (t.prototype.visitRequestProgressed = function(e) {
                                    return this.progressBar.setValue(e.progress);
                                }),
                                (t.prototype.visitRequestCompleted = function(e) {
                                    return e.loadResponse();
                                }),
                                (t.prototype.visitRequestFailedWithStatusCode = function(e, t) {
                                    switch (t) {
                                        case n:
                                        case i:
                                            return this.reload();
                                        default:
                                            return e.loadResponse();
                                    }
                                }),
                                (t.prototype.visitRequestFinished = function(e) {
                                    return this.hideProgressBar();
                                }),
                                (t.prototype.visitCompleted = function(e) {
                                    return e.followRedirect();
                                }),
                                (t.prototype.pageInvalidated = function() {
                                    return this.reload();
                                }),
                                (t.prototype.showProgressBarAfterDelay = function() {
                                    return (this.progressBarTimeout = setTimeout(
                                        this.showProgressBar,
                                        this.controller.progressBarDelay
                                    ));
                                }),
                                (t.prototype.showProgressBar = function() {
                                    return this.progressBar.show();
                                }),
                                (t.prototype.hideProgressBar = function() {
                                    return this.progressBar.hide(), clearTimeout(this.progressBarTimeout);
                                }),
                                (t.prototype.reload = function() {
                                    return window.location.reload();
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.History = (function() {
                            function t(t) {
                                (this.delegate = t),
                                    (this.onPageLoad = e(this.onPageLoad, this)),
                                    (this.onPopState = e(this.onPopState, this));
                            }
                            return (
                                (t.prototype.start = function() {
                                    return this.started
                                        ? void 0
                                        : (addEventListener('popstate', this.onPopState, !1),
                                          addEventListener('load', this.onPageLoad, !1),
                                          (this.started = !0));
                                }),
                                (t.prototype.stop = function() {
                                    return this.started
                                        ? (removeEventListener('popstate', this.onPopState, !1),
                                          removeEventListener('load', this.onPageLoad, !1),
                                          (this.started = !1))
                                        : void 0;
                                }),
                                (t.prototype.push = function(e, t) {
                                    return (e = r.Location.wrap(e)), this.update('push', e, t);
                                }),
                                (t.prototype.replace = function(e, t) {
                                    return (e = r.Location.wrap(e)), this.update('replace', e, t);
                                }),
                                (t.prototype.onPopState = function(e) {
                                    var t, n, i, o;
                                    return this.shouldHandlePopState() &&
                                        (o = null != (n = e.state) ? n.turbolinks : void 0)
                                        ? ((t = r.Location.wrap(window.location)),
                                          (i = o.restorationIdentifier),
                                          this.delegate.historyPoppedToLocationWithRestorationIdentifier(t, i))
                                        : void 0;
                                }),
                                (t.prototype.onPageLoad = function(e) {
                                    return r.defer(
                                        (function(e) {
                                            return function() {
                                                return (e.pageLoaded = !0);
                                            };
                                        })(this)
                                    );
                                }),
                                (t.prototype.shouldHandlePopState = function() {
                                    return this.pageIsLoaded();
                                }),
                                (t.prototype.pageIsLoaded = function() {
                                    return this.pageLoaded || 'complete' === document.readyState;
                                }),
                                (t.prototype.update = function(e, t, n) {
                                    var i;
                                    return (
                                        (i = { turbolinks: { restorationIdentifier: n } }),
                                        history[e + 'State'](i, null, t)
                                    );
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        r.HeadDetails = (function() {
                            function e(e) {
                                var t, n, i, a, s;
                                for (this.elements = {}, n = 0, a = e.length; a > n; n++)
                                    (s = e[n]).nodeType === Node.ELEMENT_NODE &&
                                        ((i = s.outerHTML),
                                        (null != (t = this.elements)[i]
                                            ? t[i]
                                            : (t[i] = { type: r(s), tracked: o(s), elements: [] })
                                        ).elements.push(s));
                            }
                            var t, n, i, o, r;
                            return (
                                (e.fromHeadElement = function(e) {
                                    var t;
                                    return new this(null != (t = null != e ? e.childNodes : void 0) ? t : []);
                                }),
                                (e.prototype.hasElementWithKey = function(e) {
                                    return e in this.elements;
                                }),
                                (e.prototype.getTrackedElementSignature = function() {
                                    var e;
                                    return function() {
                                        var t, n;
                                        for (e in ((n = []), (t = this.elements))) t[e].tracked && n.push(e);
                                        return n;
                                    }
                                        .call(this)
                                        .join('');
                                }),
                                (e.prototype.getScriptElementsNotInDetails = function(e) {
                                    return this.getElementsMatchingTypeNotInDetails('script', e);
                                }),
                                (e.prototype.getStylesheetElementsNotInDetails = function(e) {
                                    return this.getElementsMatchingTypeNotInDetails('stylesheet', e);
                                }),
                                (e.prototype.getElementsMatchingTypeNotInDetails = function(e, t) {
                                    var n, i, o, r, a, s;
                                    for (i in ((a = []), (o = this.elements)))
                                        (s = (r = o[i]).type),
                                            (n = r.elements),
                                            s !== e || t.hasElementWithKey(i) || a.push(n[0]);
                                    return a;
                                }),
                                (e.prototype.getProvisionalElements = function() {
                                    var e, t, n, i, o, r, a;
                                    for (t in ((n = []), (i = this.elements)))
                                        (a = (o = i[t]).type),
                                            (r = o.tracked),
                                            (e = o.elements),
                                            null != a || r
                                                ? e.length > 1 && n.push.apply(n, e.slice(1))
                                                : n.push.apply(n, e);
                                    return n;
                                }),
                                (e.prototype.getMetaValue = function(e) {
                                    var t;
                                    return null != (t = this.findMetaElementByName(e))
                                        ? t.getAttribute('content')
                                        : void 0;
                                }),
                                (e.prototype.findMetaElementByName = function(e) {
                                    var n, i, o, r;
                                    for (o in ((n = void 0), (r = this.elements)))
                                        (i = r[o].elements), t(i[0], e) && (n = i[0]);
                                    return n;
                                }),
                                (r = function(e) {
                                    return n(e) ? 'script' : i(e) ? 'stylesheet' : void 0;
                                }),
                                (o = function(e) {
                                    return 'reload' === e.getAttribute('data-turbolinks-track');
                                }),
                                (n = function(e) {
                                    return 'script' === e.tagName.toLowerCase();
                                }),
                                (i = function(e) {
                                    var t;
                                    return (
                                        'style' === (t = e.tagName.toLowerCase()) ||
                                        ('link' === t && 'stylesheet' === e.getAttribute('rel'))
                                    );
                                }),
                                (t = function(e, t) {
                                    return 'meta' === e.tagName.toLowerCase() && e.getAttribute('name') === t;
                                }),
                                e
                            );
                        })();
                    }.call(this),
                    function() {
                        r.Snapshot = (function() {
                            function e(e, t) {
                                (this.headDetails = e), (this.bodyElement = t);
                            }
                            return (
                                (e.wrap = function(e) {
                                    return e instanceof this
                                        ? e
                                        : 'string' == typeof e
                                        ? this.fromHTMLString(e)
                                        : this.fromHTMLElement(e);
                                }),
                                (e.fromHTMLString = function(e) {
                                    var t;
                                    return (
                                        ((t = document.createElement('html')).innerHTML = e), this.fromHTMLElement(t)
                                    );
                                }),
                                (e.fromHTMLElement = function(e) {
                                    var t, n, i;
                                    return (
                                        (n = e.querySelector('head')),
                                        (t =
                                            null != (i = e.querySelector('body')) ? i : document.createElement('body')),
                                        new this(r.HeadDetails.fromHeadElement(n), t)
                                    );
                                }),
                                (e.prototype.clone = function() {
                                    return new this.constructor(this.headDetails, this.bodyElement.cloneNode(!0));
                                }),
                                (e.prototype.getRootLocation = function() {
                                    var e, t;
                                    return (t = null != (e = this.getSetting('root')) ? e : '/'), new r.Location(t);
                                }),
                                (e.prototype.getCacheControlValue = function() {
                                    return this.getSetting('cache-control');
                                }),
                                (e.prototype.getElementForAnchor = function(e) {
                                    try {
                                        return this.bodyElement.querySelector("[id='" + e + "'], a[name='" + e + "']");
                                    } catch (e) {}
                                }),
                                (e.prototype.getPermanentElements = function() {
                                    return this.bodyElement.querySelectorAll('[id][data-turbolinks-permanent]');
                                }),
                                (e.prototype.getPermanentElementById = function(e) {
                                    return this.bodyElement.querySelector('#' + e + '[data-turbolinks-permanent]');
                                }),
                                (e.prototype.getPermanentElementsPresentInSnapshot = function(e) {
                                    var t, n, i, o, r;
                                    for (r = [], n = 0, i = (o = this.getPermanentElements()).length; i > n; n++)
                                        (t = o[n]), e.getPermanentElementById(t.id) && r.push(t);
                                    return r;
                                }),
                                (e.prototype.findFirstAutofocusableElement = function() {
                                    return this.bodyElement.querySelector('[autofocus]');
                                }),
                                (e.prototype.hasAnchor = function(e) {
                                    return null != this.getElementForAnchor(e);
                                }),
                                (e.prototype.isPreviewable = function() {
                                    return 'no-preview' !== this.getCacheControlValue();
                                }),
                                (e.prototype.isCacheable = function() {
                                    return 'no-cache' !== this.getCacheControlValue();
                                }),
                                (e.prototype.isVisitable = function() {
                                    return 'reload' !== this.getSetting('visit-control');
                                }),
                                (e.prototype.getSetting = function(e) {
                                    return this.headDetails.getMetaValue('turbolinks-' + e);
                                }),
                                e
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = [].slice;
                        r.Renderer = (function() {
                            function t() {}
                            var n;
                            return (
                                (t.render = function() {
                                    var t, n, i;
                                    return (
                                        (n = arguments[0]),
                                        (t = arguments[1]),
                                        ((i = (function(e, t, n) {
                                            n.prototype = e.prototype;
                                            var i = new n(),
                                                o = e.apply(i, t);
                                            return Object(o) === o ? o : i;
                                        })(
                                            this,
                                            3 <= arguments.length ? e.call(arguments, 2) : [],
                                            function() {}
                                        )).delegate = n),
                                        i.render(t),
                                        i
                                    );
                                }),
                                (t.prototype.renderView = function(e) {
                                    return (
                                        this.delegate.viewWillRender(this.newBody),
                                        e(),
                                        this.delegate.viewRendered(this.newBody)
                                    );
                                }),
                                (t.prototype.invalidateView = function() {
                                    return this.delegate.viewInvalidated();
                                }),
                                (t.prototype.createScriptElement = function(e) {
                                    var t;
                                    return 'false' === e.getAttribute('data-turbolinks-eval')
                                        ? e
                                        : (((t = document.createElement('script')).textContent = e.textContent),
                                          (t.async = !1),
                                          n(t, e),
                                          t);
                                }),
                                (n = function(e, t) {
                                    var n, i, o, r, a, s, c;
                                    for (s = [], n = 0, i = (r = t.attributes).length; i > n; n++)
                                        (o = (a = r[n]).name), (c = a.value), s.push(e.setAttribute(o, c));
                                    return s;
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        var e,
                            t,
                            n = function(e, t) {
                                function n() {
                                    this.constructor = e;
                                }
                                for (var o in t) i.call(t, o) && (e[o] = t[o]);
                                return (
                                    (n.prototype = t.prototype), (e.prototype = new n()), (e.__super__ = t.prototype), e
                                );
                            },
                            i = {}.hasOwnProperty;
                        (r.SnapshotRenderer = (function(i) {
                            function o(e, t, n) {
                                (this.currentSnapshot = e),
                                    (this.newSnapshot = t),
                                    (this.isPreview = n),
                                    (this.currentHeadDetails = this.currentSnapshot.headDetails),
                                    (this.newHeadDetails = this.newSnapshot.headDetails),
                                    (this.currentBody = this.currentSnapshot.bodyElement),
                                    (this.newBody = this.newSnapshot.bodyElement);
                            }
                            return (
                                n(o, i),
                                (o.prototype.render = function(e) {
                                    return this.shouldRender()
                                        ? (this.mergeHead(),
                                          this.renderView(
                                              (function(t) {
                                                  return function() {
                                                      return (
                                                          t.replaceBody(),
                                                          t.isPreview || t.focusFirstAutofocusableElement(),
                                                          e()
                                                      );
                                                  };
                                              })(this)
                                          ))
                                        : this.invalidateView();
                                }),
                                (o.prototype.mergeHead = function() {
                                    return (
                                        this.copyNewHeadStylesheetElements(),
                                        this.copyNewHeadScriptElements(),
                                        this.removeCurrentHeadProvisionalElements(),
                                        this.copyNewHeadProvisionalElements()
                                    );
                                }),
                                (o.prototype.replaceBody = function() {
                                    var e;
                                    return (
                                        (e = this.relocateCurrentBodyPermanentElements()),
                                        this.activateNewBodyScriptElements(),
                                        this.assignNewBody(),
                                        this.replacePlaceholderElementsWithClonedPermanentElements(e)
                                    );
                                }),
                                (o.prototype.shouldRender = function() {
                                    return this.newSnapshot.isVisitable() && this.trackedElementsAreIdentical();
                                }),
                                (o.prototype.trackedElementsAreIdentical = function() {
                                    return (
                                        this.currentHeadDetails.getTrackedElementSignature() ===
                                        this.newHeadDetails.getTrackedElementSignature()
                                    );
                                }),
                                (o.prototype.copyNewHeadStylesheetElements = function() {
                                    var e, t, n, i, o;
                                    for (
                                        o = [], t = 0, n = (i = this.getNewHeadStylesheetElements()).length;
                                        n > t;
                                        t++
                                    )
                                        (e = i[t]), o.push(document.head.appendChild(e));
                                    return o;
                                }),
                                (o.prototype.copyNewHeadScriptElements = function() {
                                    var e, t, n, i, o;
                                    for (o = [], t = 0, n = (i = this.getNewHeadScriptElements()).length; n > t; t++)
                                        (e = i[t]), o.push(document.head.appendChild(this.createScriptElement(e)));
                                    return o;
                                }),
                                (o.prototype.removeCurrentHeadProvisionalElements = function() {
                                    var e, t, n, i, o;
                                    for (
                                        o = [], t = 0, n = (i = this.getCurrentHeadProvisionalElements()).length;
                                        n > t;
                                        t++
                                    )
                                        (e = i[t]), o.push(document.head.removeChild(e));
                                    return o;
                                }),
                                (o.prototype.copyNewHeadProvisionalElements = function() {
                                    var e, t, n, i, o;
                                    for (
                                        o = [], t = 0, n = (i = this.getNewHeadProvisionalElements()).length;
                                        n > t;
                                        t++
                                    )
                                        (e = i[t]), o.push(document.head.appendChild(e));
                                    return o;
                                }),
                                (o.prototype.relocateCurrentBodyPermanentElements = function() {
                                    var n, i, o, r, a, s, c;
                                    for (
                                        c = [], n = 0, i = (s = this.getCurrentBodyPermanentElements()).length;
                                        i > n;
                                        n++
                                    )
                                        (r = s[n]),
                                            (a = e(r)),
                                            (o = this.newSnapshot.getPermanentElementById(r.id)),
                                            t(r, a.element),
                                            t(o, r),
                                            c.push(a);
                                    return c;
                                }),
                                (o.prototype.replacePlaceholderElementsWithClonedPermanentElements = function(e) {
                                    var n, i, o, r, a, s;
                                    for (s = [], o = 0, r = e.length; r > o; o++)
                                        (i = (a = e[o]).element),
                                            (n = a.permanentElement.cloneNode(!0)),
                                            s.push(t(i, n));
                                    return s;
                                }),
                                (o.prototype.activateNewBodyScriptElements = function() {
                                    var e, n, i, o, r, a;
                                    for (a = [], n = 0, o = (r = this.getNewBodyScriptElements()).length; o > n; n++)
                                        (i = r[n]), (e = this.createScriptElement(i)), a.push(t(i, e));
                                    return a;
                                }),
                                (o.prototype.assignNewBody = function() {
                                    return (document.body = this.newBody);
                                }),
                                (o.prototype.focusFirstAutofocusableElement = function() {
                                    var e;
                                    return null != (e = this.newSnapshot.findFirstAutofocusableElement())
                                        ? e.focus()
                                        : void 0;
                                }),
                                (o.prototype.getNewHeadStylesheetElements = function() {
                                    return this.newHeadDetails.getStylesheetElementsNotInDetails(
                                        this.currentHeadDetails
                                    );
                                }),
                                (o.prototype.getNewHeadScriptElements = function() {
                                    return this.newHeadDetails.getScriptElementsNotInDetails(this.currentHeadDetails);
                                }),
                                (o.prototype.getCurrentHeadProvisionalElements = function() {
                                    return this.currentHeadDetails.getProvisionalElements();
                                }),
                                (o.prototype.getNewHeadProvisionalElements = function() {
                                    return this.newHeadDetails.getProvisionalElements();
                                }),
                                (o.prototype.getCurrentBodyPermanentElements = function() {
                                    return this.currentSnapshot.getPermanentElementsPresentInSnapshot(this.newSnapshot);
                                }),
                                (o.prototype.getNewBodyScriptElements = function() {
                                    return this.newBody.querySelectorAll('script');
                                }),
                                o
                            );
                        })(r.Renderer)),
                            (e = function(e) {
                                var t;
                                return (
                                    (t = document.createElement('meta')).setAttribute(
                                        'name',
                                        'turbolinks-permanent-placeholder'
                                    ),
                                    t.setAttribute('content', e.id),
                                    { element: t, permanentElement: e }
                                );
                            }),
                            (t = function(e, t) {
                                var n;
                                return (n = e.parentNode) ? n.replaceChild(t, e) : void 0;
                            });
                    }.call(this),
                    function() {
                        var e = function(e, n) {
                                function i() {
                                    this.constructor = e;
                                }
                                for (var o in n) t.call(n, o) && (e[o] = n[o]);
                                return (
                                    (i.prototype = n.prototype), (e.prototype = new i()), (e.__super__ = n.prototype), e
                                );
                            },
                            t = {}.hasOwnProperty;
                        r.ErrorRenderer = (function(t) {
                            function n(e) {
                                var t;
                                ((t = document.createElement('html')).innerHTML = e),
                                    (this.newHead = t.querySelector('head')),
                                    (this.newBody = t.querySelector('body'));
                            }
                            return (
                                e(n, t),
                                (n.prototype.render = function(e) {
                                    return this.renderView(
                                        (function(t) {
                                            return function() {
                                                return t.replaceHeadAndBody(), t.activateBodyScriptElements(), e();
                                            };
                                        })(this)
                                    );
                                }),
                                (n.prototype.replaceHeadAndBody = function() {
                                    var e, t;
                                    return (
                                        (t = document.head),
                                        (e = document.body),
                                        t.parentNode.replaceChild(this.newHead, t),
                                        e.parentNode.replaceChild(this.newBody, e)
                                    );
                                }),
                                (n.prototype.activateBodyScriptElements = function() {
                                    var e, t, n, i, o, r;
                                    for (r = [], t = 0, n = (i = this.getScriptElements()).length; n > t; t++)
                                        (o = i[t]),
                                            (e = this.createScriptElement(o)),
                                            r.push(o.parentNode.replaceChild(e, o));
                                    return r;
                                }),
                                (n.prototype.getScriptElements = function() {
                                    return document.documentElement.querySelectorAll('script');
                                }),
                                n
                            );
                        })(r.Renderer);
                    }.call(this),
                    function() {
                        r.View = (function() {
                            function e(e) {
                                (this.delegate = e), (this.htmlElement = document.documentElement);
                            }
                            return (
                                (e.prototype.getRootLocation = function() {
                                    return this.getSnapshot().getRootLocation();
                                }),
                                (e.prototype.getElementForAnchor = function(e) {
                                    return this.getSnapshot().getElementForAnchor(e);
                                }),
                                (e.prototype.getSnapshot = function() {
                                    return r.Snapshot.fromHTMLElement(this.htmlElement);
                                }),
                                (e.prototype.render = function(e, t) {
                                    var n, i, o;
                                    return (
                                        (o = e.snapshot),
                                        (n = e.error),
                                        (i = e.isPreview),
                                        this.markAsPreview(i),
                                        null != o ? this.renderSnapshot(o, i, t) : this.renderError(n, t)
                                    );
                                }),
                                (e.prototype.markAsPreview = function(e) {
                                    return e
                                        ? this.htmlElement.setAttribute('data-turbolinks-preview', '')
                                        : this.htmlElement.removeAttribute('data-turbolinks-preview');
                                }),
                                (e.prototype.renderSnapshot = function(e, t, n) {
                                    return r.SnapshotRenderer.render(
                                        this.delegate,
                                        n,
                                        this.getSnapshot(),
                                        r.Snapshot.wrap(e),
                                        t
                                    );
                                }),
                                (e.prototype.renderError = function(e, t) {
                                    return r.ErrorRenderer.render(this.delegate, t, e);
                                }),
                                e
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.ScrollManager = (function() {
                            function t(t) {
                                (this.delegate = t),
                                    (this.onScroll = e(this.onScroll, this)),
                                    (this.onScroll = r.throttle(this.onScroll));
                            }
                            return (
                                (t.prototype.start = function() {
                                    return this.started
                                        ? void 0
                                        : (addEventListener('scroll', this.onScroll, !1),
                                          this.onScroll(),
                                          (this.started = !0));
                                }),
                                (t.prototype.stop = function() {
                                    return this.started
                                        ? (removeEventListener('scroll', this.onScroll, !1), (this.started = !1))
                                        : void 0;
                                }),
                                (t.prototype.scrollToElement = function(e) {
                                    return e.scrollIntoView();
                                }),
                                (t.prototype.scrollToPosition = function(e) {
                                    var t, n;
                                    return (t = e.x), (n = e.y), window.scrollTo(t, n);
                                }),
                                (t.prototype.onScroll = function(e) {
                                    return this.updatePosition({ x: window.pageXOffset, y: window.pageYOffset });
                                }),
                                (t.prototype.updatePosition = function(e) {
                                    var t;
                                    return (
                                        (this.position = e),
                                        null != (t = this.delegate) ? t.scrollPositionChanged(this.position) : void 0
                                    );
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        r.SnapshotCache = (function() {
                            function e(e) {
                                (this.size = e), (this.keys = []), (this.snapshots = {});
                            }
                            var t;
                            return (
                                (e.prototype.has = function(e) {
                                    return t(e) in this.snapshots;
                                }),
                                (e.prototype.get = function(e) {
                                    var t;
                                    if (this.has(e)) return (t = this.read(e)), this.touch(e), t;
                                }),
                                (e.prototype.put = function(e, t) {
                                    return this.write(e, t), this.touch(e), t;
                                }),
                                (e.prototype.read = function(e) {
                                    var n;
                                    return (n = t(e)), this.snapshots[n];
                                }),
                                (e.prototype.write = function(e, n) {
                                    var i;
                                    return (i = t(e)), (this.snapshots[i] = n);
                                }),
                                (e.prototype.touch = function(e) {
                                    var n, i;
                                    return (
                                        (i = t(e)),
                                        (n = this.keys.indexOf(i)) > -1 && this.keys.splice(n, 1),
                                        this.keys.unshift(i),
                                        this.trim()
                                    );
                                }),
                                (e.prototype.trim = function() {
                                    var e, t, n, i, o;
                                    for (o = [], e = 0, n = (i = this.keys.splice(this.size)).length; n > e; e++)
                                        (t = i[e]), o.push(delete this.snapshots[t]);
                                    return o;
                                }),
                                (t = function(e) {
                                    return r.Location.wrap(e).toCacheKey();
                                }),
                                e
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.Visit = (function() {
                            function t(t, n, i) {
                                (this.controller = t),
                                    (this.action = i),
                                    (this.performScroll = e(this.performScroll, this)),
                                    (this.identifier = r.uuid()),
                                    (this.location = r.Location.wrap(n)),
                                    (this.adapter = this.controller.adapter),
                                    (this.state = 'initialized'),
                                    (this.timingMetrics = {});
                            }
                            var n;
                            return (
                                (t.prototype.start = function() {
                                    return 'initialized' === this.state
                                        ? (this.recordTimingMetric('visitStart'),
                                          (this.state = 'started'),
                                          this.adapter.visitStarted(this))
                                        : void 0;
                                }),
                                (t.prototype.cancel = function() {
                                    var e;
                                    return 'started' === this.state
                                        ? (null != (e = this.request) && e.cancel(),
                                          this.cancelRender(),
                                          (this.state = 'canceled'))
                                        : void 0;
                                }),
                                (t.prototype.complete = function() {
                                    var e;
                                    return 'started' === this.state
                                        ? (this.recordTimingMetric('visitEnd'),
                                          (this.state = 'completed'),
                                          'function' == typeof (e = this.adapter).visitCompleted &&
                                              e.visitCompleted(this),
                                          this.controller.visitCompleted(this))
                                        : void 0;
                                }),
                                (t.prototype.fail = function() {
                                    var e;
                                    return 'started' === this.state
                                        ? ((this.state = 'failed'),
                                          'function' == typeof (e = this.adapter).visitFailed
                                              ? e.visitFailed(this)
                                              : void 0)
                                        : void 0;
                                }),
                                (t.prototype.changeHistory = function() {
                                    var e, t;
                                    return this.historyChanged
                                        ? void 0
                                        : ((e = this.location.isEqualTo(this.referrer) ? 'replace' : this.action),
                                          (t = n(e)),
                                          this.controller[t](this.location, this.restorationIdentifier),
                                          (this.historyChanged = !0));
                                }),
                                (t.prototype.issueRequest = function() {
                                    return this.shouldIssueRequest() && null == this.request
                                        ? ((this.progress = 0),
                                          (this.request = new r.HttpRequest(this, this.location, this.referrer)),
                                          this.request.send())
                                        : void 0;
                                }),
                                (t.prototype.getCachedSnapshot = function() {
                                    var e;
                                    return !(e = this.controller.getCachedSnapshotForLocation(this.location)) ||
                                        (null != this.location.anchor && !e.hasAnchor(this.location.anchor)) ||
                                        ('restore' !== this.action && !e.isPreviewable())
                                        ? void 0
                                        : e;
                                }),
                                (t.prototype.hasCachedSnapshot = function() {
                                    return null != this.getCachedSnapshot();
                                }),
                                (t.prototype.loadCachedSnapshot = function() {
                                    var e, t;
                                    return (t = this.getCachedSnapshot())
                                        ? ((e = this.shouldIssueRequest()),
                                          this.render(function() {
                                              var n;
                                              return (
                                                  this.cacheSnapshot(),
                                                  this.controller.render(
                                                      { snapshot: t, isPreview: e },
                                                      this.performScroll
                                                  ),
                                                  'function' == typeof (n = this.adapter).visitRendered &&
                                                      n.visitRendered(this),
                                                  e ? void 0 : this.complete()
                                              );
                                          }))
                                        : void 0;
                                }),
                                (t.prototype.loadResponse = function() {
                                    return null != this.response
                                        ? this.render(function() {
                                              var e, t;
                                              return (
                                                  this.cacheSnapshot(),
                                                  this.request.failed
                                                      ? (this.controller.render(
                                                            { error: this.response },
                                                            this.performScroll
                                                        ),
                                                        'function' == typeof (e = this.adapter).visitRendered &&
                                                            e.visitRendered(this),
                                                        this.fail())
                                                      : (this.controller.render(
                                                            { snapshot: this.response },
                                                            this.performScroll
                                                        ),
                                                        'function' == typeof (t = this.adapter).visitRendered &&
                                                            t.visitRendered(this),
                                                        this.complete())
                                              );
                                          })
                                        : void 0;
                                }),
                                (t.prototype.followRedirect = function() {
                                    return this.redirectedToLocation && !this.followedRedirect
                                        ? ((this.location = this.redirectedToLocation),
                                          this.controller.replaceHistoryWithLocationAndRestorationIdentifier(
                                              this.redirectedToLocation,
                                              this.restorationIdentifier
                                          ),
                                          (this.followedRedirect = !0))
                                        : void 0;
                                }),
                                (t.prototype.requestStarted = function() {
                                    var e;
                                    return (
                                        this.recordTimingMetric('requestStart'),
                                        'function' == typeof (e = this.adapter).visitRequestStarted
                                            ? e.visitRequestStarted(this)
                                            : void 0
                                    );
                                }),
                                (t.prototype.requestProgressed = function(e) {
                                    var t;
                                    return (
                                        (this.progress = e),
                                        'function' == typeof (t = this.adapter).visitRequestProgressed
                                            ? t.visitRequestProgressed(this)
                                            : void 0
                                    );
                                }),
                                (t.prototype.requestCompletedWithResponse = function(e, t) {
                                    return (
                                        (this.response = e),
                                        null != t && (this.redirectedToLocation = r.Location.wrap(t)),
                                        this.adapter.visitRequestCompleted(this)
                                    );
                                }),
                                (t.prototype.requestFailedWithStatusCode = function(e, t) {
                                    return (this.response = t), this.adapter.visitRequestFailedWithStatusCode(this, e);
                                }),
                                (t.prototype.requestFinished = function() {
                                    var e;
                                    return (
                                        this.recordTimingMetric('requestEnd'),
                                        'function' == typeof (e = this.adapter).visitRequestFinished
                                            ? e.visitRequestFinished(this)
                                            : void 0
                                    );
                                }),
                                (t.prototype.performScroll = function() {
                                    return this.scrolled
                                        ? void 0
                                        : ('restore' === this.action
                                              ? this.scrollToRestoredPosition() || this.scrollToTop()
                                              : this.scrollToAnchor() || this.scrollToTop(),
                                          (this.scrolled = !0));
                                }),
                                (t.prototype.scrollToRestoredPosition = function() {
                                    var e, t;
                                    return null != (e = null != (t = this.restorationData) ? t.scrollPosition : void 0)
                                        ? (this.controller.scrollToPosition(e), !0)
                                        : void 0;
                                }),
                                (t.prototype.scrollToAnchor = function() {
                                    return null != this.location.anchor
                                        ? (this.controller.scrollToAnchor(this.location.anchor), !0)
                                        : void 0;
                                }),
                                (t.prototype.scrollToTop = function() {
                                    return this.controller.scrollToPosition({ x: 0, y: 0 });
                                }),
                                (t.prototype.recordTimingMetric = function(e) {
                                    var t;
                                    return null != (t = this.timingMetrics)[e] ? t[e] : (t[e] = new Date().getTime());
                                }),
                                (t.prototype.getTimingMetrics = function() {
                                    return r.copyObject(this.timingMetrics);
                                }),
                                (n = function(e) {
                                    switch (e) {
                                        case 'replace':
                                            return 'replaceHistoryWithLocationAndRestorationIdentifier';
                                        case 'advance':
                                        case 'restore':
                                            return 'pushHistoryWithLocationAndRestorationIdentifier';
                                    }
                                }),
                                (t.prototype.shouldIssueRequest = function() {
                                    return 'restore' !== this.action || !this.hasCachedSnapshot();
                                }),
                                (t.prototype.cacheSnapshot = function() {
                                    return this.snapshotCached
                                        ? void 0
                                        : (this.controller.cacheSnapshot(), (this.snapshotCached = !0));
                                }),
                                (t.prototype.render = function(e) {
                                    return (
                                        this.cancelRender(),
                                        (this.frame = requestAnimationFrame(
                                            (function(t) {
                                                return function() {
                                                    return (t.frame = null), e.call(t);
                                                };
                                            })(this)
                                        ))
                                    );
                                }),
                                (t.prototype.cancelRender = function() {
                                    return this.frame ? cancelAnimationFrame(this.frame) : void 0;
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        var e = function(e, t) {
                            return function() {
                                return e.apply(t, arguments);
                            };
                        };
                        r.Controller = (function() {
                            function t() {
                                (this.clickBubbled = e(this.clickBubbled, this)),
                                    (this.clickCaptured = e(this.clickCaptured, this)),
                                    (this.pageLoaded = e(this.pageLoaded, this)),
                                    (this.history = new r.History(this)),
                                    (this.view = new r.View(this)),
                                    (this.scrollManager = new r.ScrollManager(this)),
                                    (this.restorationData = {}),
                                    this.clearCache(),
                                    this.setProgressBarDelay(500);
                            }
                            return (
                                (t.prototype.start = function() {
                                    return r.supported && !this.started
                                        ? (addEventListener('click', this.clickCaptured, !0),
                                          addEventListener('DOMContentLoaded', this.pageLoaded, !1),
                                          this.scrollManager.start(),
                                          this.startHistory(),
                                          (this.started = !0),
                                          (this.enabled = !0))
                                        : void 0;
                                }),
                                (t.prototype.disable = function() {
                                    return (this.enabled = !1);
                                }),
                                (t.prototype.stop = function() {
                                    return this.started
                                        ? (removeEventListener('click', this.clickCaptured, !0),
                                          removeEventListener('DOMContentLoaded', this.pageLoaded, !1),
                                          this.scrollManager.stop(),
                                          this.stopHistory(),
                                          (this.started = !1))
                                        : void 0;
                                }),
                                (t.prototype.clearCache = function() {
                                    return (this.cache = new r.SnapshotCache(10));
                                }),
                                (t.prototype.visit = function(e, t) {
                                    var n, i;
                                    return (
                                        null == t && (t = {}),
                                        (e = r.Location.wrap(e)),
                                        this.applicationAllowsVisitingLocation(e)
                                            ? this.locationIsVisitable(e)
                                                ? ((n = null != (i = t.action) ? i : 'advance'),
                                                  this.adapter.visitProposedToLocationWithAction(e, n))
                                                : (window.location = e)
                                            : void 0
                                    );
                                }),
                                (t.prototype.startVisitToLocationWithAction = function(e, t, n) {
                                    var i;
                                    return r.supported
                                        ? ((i = this.getRestorationDataForIdentifier(n)),
                                          this.startVisit(e, t, { restorationData: i }))
                                        : (window.location = e);
                                }),
                                (t.prototype.setProgressBarDelay = function(e) {
                                    return (this.progressBarDelay = e);
                                }),
                                (t.prototype.startHistory = function() {
                                    return (
                                        (this.location = r.Location.wrap(window.location)),
                                        (this.restorationIdentifier = r.uuid()),
                                        this.history.start(),
                                        this.history.replace(this.location, this.restorationIdentifier)
                                    );
                                }),
                                (t.prototype.stopHistory = function() {
                                    return this.history.stop();
                                }),
                                (t.prototype.pushHistoryWithLocationAndRestorationIdentifier = function(e, t) {
                                    return (
                                        (this.restorationIdentifier = t),
                                        (this.location = r.Location.wrap(e)),
                                        this.history.push(this.location, this.restorationIdentifier)
                                    );
                                }),
                                (t.prototype.replaceHistoryWithLocationAndRestorationIdentifier = function(e, t) {
                                    return (
                                        (this.restorationIdentifier = t),
                                        (this.location = r.Location.wrap(e)),
                                        this.history.replace(this.location, this.restorationIdentifier)
                                    );
                                }),
                                (t.prototype.historyPoppedToLocationWithRestorationIdentifier = function(e, t) {
                                    var n;
                                    return (
                                        (this.restorationIdentifier = t),
                                        this.enabled
                                            ? ((n = this.getRestorationDataForIdentifier(this.restorationIdentifier)),
                                              this.startVisit(e, 'restore', {
                                                  restorationIdentifier: this.restorationIdentifier,
                                                  restorationData: n,
                                                  historyChanged: !0,
                                              }),
                                              (this.location = r.Location.wrap(e)))
                                            : this.adapter.pageInvalidated()
                                    );
                                }),
                                (t.prototype.getCachedSnapshotForLocation = function(e) {
                                    var t;
                                    return null != (t = this.cache.get(e)) ? t.clone() : void 0;
                                }),
                                (t.prototype.shouldCacheSnapshot = function() {
                                    return this.view.getSnapshot().isCacheable();
                                }),
                                (t.prototype.cacheSnapshot = function() {
                                    var e, t;
                                    return this.shouldCacheSnapshot()
                                        ? (this.notifyApplicationBeforeCachingSnapshot(),
                                          (t = this.view.getSnapshot()),
                                          (e = this.lastRenderedLocation),
                                          r.defer(
                                              (function(n) {
                                                  return function() {
                                                      return n.cache.put(e, t.clone());
                                                  };
                                              })(this)
                                          ))
                                        : void 0;
                                }),
                                (t.prototype.scrollToAnchor = function(e) {
                                    var t;
                                    return (t = this.view.getElementForAnchor(e))
                                        ? this.scrollToElement(t)
                                        : this.scrollToPosition({ x: 0, y: 0 });
                                }),
                                (t.prototype.scrollToElement = function(e) {
                                    return this.scrollManager.scrollToElement(e);
                                }),
                                (t.prototype.scrollToPosition = function(e) {
                                    return this.scrollManager.scrollToPosition(e);
                                }),
                                (t.prototype.scrollPositionChanged = function(e) {
                                    return (this.getCurrentRestorationData().scrollPosition = e);
                                }),
                                (t.prototype.render = function(e, t) {
                                    return this.view.render(e, t);
                                }),
                                (t.prototype.viewInvalidated = function() {
                                    return this.adapter.pageInvalidated();
                                }),
                                (t.prototype.viewWillRender = function(e) {
                                    return this.notifyApplicationBeforeRender(e);
                                }),
                                (t.prototype.viewRendered = function() {
                                    return (
                                        (this.lastRenderedLocation = this.currentVisit.location),
                                        this.notifyApplicationAfterRender()
                                    );
                                }),
                                (t.prototype.pageLoaded = function() {
                                    return (
                                        (this.lastRenderedLocation = this.location),
                                        this.notifyApplicationAfterPageLoad()
                                    );
                                }),
                                (t.prototype.clickCaptured = function() {
                                    return (
                                        removeEventListener('click', this.clickBubbled, !1),
                                        addEventListener('click', this.clickBubbled, !1)
                                    );
                                }),
                                (t.prototype.clickBubbled = function(e) {
                                    var t, n, i;
                                    return this.enabled &&
                                        this.clickEventIsSignificant(e) &&
                                        (n = this.getVisitableLinkForNode(e.target)) &&
                                        (i = this.getVisitableLocationForLink(n)) &&
                                        this.applicationAllowsFollowingLinkToLocation(n, i)
                                        ? (e.preventDefault(),
                                          (t = this.getActionForLink(n)),
                                          this.visit(i, { action: t }))
                                        : void 0;
                                }),
                                (t.prototype.applicationAllowsFollowingLinkToLocation = function(e, t) {
                                    return !this.notifyApplicationAfterClickingLinkToLocation(e, t).defaultPrevented;
                                }),
                                (t.prototype.applicationAllowsVisitingLocation = function(e) {
                                    return !this.notifyApplicationBeforeVisitingLocation(e).defaultPrevented;
                                }),
                                (t.prototype.notifyApplicationAfterClickingLinkToLocation = function(e, t) {
                                    return r.dispatch('turbolinks:click', {
                                        target: e,
                                        data: { url: t.absoluteURL },
                                        cancelable: !0,
                                    });
                                }),
                                (t.prototype.notifyApplicationBeforeVisitingLocation = function(e) {
                                    return r.dispatch('turbolinks:before-visit', {
                                        data: { url: e.absoluteURL },
                                        cancelable: !0,
                                    });
                                }),
                                (t.prototype.notifyApplicationAfterVisitingLocation = function(e) {
                                    return r.dispatch('turbolinks:visit', { data: { url: e.absoluteURL } });
                                }),
                                (t.prototype.notifyApplicationBeforeCachingSnapshot = function() {
                                    return r.dispatch('turbolinks:before-cache');
                                }),
                                (t.prototype.notifyApplicationBeforeRender = function(e) {
                                    return r.dispatch('turbolinks:before-render', { data: { newBody: e } });
                                }),
                                (t.prototype.notifyApplicationAfterRender = function() {
                                    return r.dispatch('turbolinks:render');
                                }),
                                (t.prototype.notifyApplicationAfterPageLoad = function(e) {
                                    return (
                                        null == e && (e = {}),
                                        r.dispatch('turbolinks:load', {
                                            data: { url: this.location.absoluteURL, timing: e },
                                        })
                                    );
                                }),
                                (t.prototype.startVisit = function(e, t, n) {
                                    var i;
                                    return (
                                        null != (i = this.currentVisit) && i.cancel(),
                                        (this.currentVisit = this.createVisit(e, t, n)),
                                        this.currentVisit.start(),
                                        this.notifyApplicationAfterVisitingLocation(e)
                                    );
                                }),
                                (t.prototype.createVisit = function(e, t, n) {
                                    var i, o, a, s, c;
                                    return (
                                        (s = (o = null != n ? n : {}).restorationIdentifier),
                                        (a = o.restorationData),
                                        (i = o.historyChanged),
                                        ((c = new r.Visit(this, e, t)).restorationIdentifier =
                                            null != s ? s : r.uuid()),
                                        (c.restorationData = r.copyObject(a)),
                                        (c.historyChanged = i),
                                        (c.referrer = this.location),
                                        c
                                    );
                                }),
                                (t.prototype.visitCompleted = function(e) {
                                    return this.notifyApplicationAfterPageLoad(e.getTimingMetrics());
                                }),
                                (t.prototype.clickEventIsSignificant = function(e) {
                                    return !(
                                        e.defaultPrevented ||
                                        e.target.isContentEditable ||
                                        e.which > 1 ||
                                        e.altKey ||
                                        e.ctrlKey ||
                                        e.metaKey ||
                                        e.shiftKey
                                    );
                                }),
                                (t.prototype.getVisitableLinkForNode = function(e) {
                                    return this.nodeIsVisitable(e)
                                        ? r.closest(e, 'a[href]:not([target]):not([download])')
                                        : void 0;
                                }),
                                (t.prototype.getVisitableLocationForLink = function(e) {
                                    var t;
                                    return (
                                        (t = new r.Location(e.getAttribute('href'))),
                                        this.locationIsVisitable(t) ? t : void 0
                                    );
                                }),
                                (t.prototype.getActionForLink = function(e) {
                                    var t;
                                    return null != (t = e.getAttribute('data-turbolinks-action')) ? t : 'advance';
                                }),
                                (t.prototype.nodeIsVisitable = function(e) {
                                    var t;
                                    return (
                                        !(t = r.closest(e, '[data-turbolinks]')) ||
                                        'false' !== t.getAttribute('data-turbolinks')
                                    );
                                }),
                                (t.prototype.locationIsVisitable = function(e) {
                                    return e.isPrefixedBy(this.view.getRootLocation()) && e.isHTML();
                                }),
                                (t.prototype.getCurrentRestorationData = function() {
                                    return this.getRestorationDataForIdentifier(this.restorationIdentifier);
                                }),
                                (t.prototype.getRestorationDataForIdentifier = function(e) {
                                    var t;
                                    return null != (t = this.restorationData)[e] ? t[e] : (t[e] = {});
                                }),
                                t
                            );
                        })();
                    }.call(this),
                    function() {
                        !(function() {
                            var e, t;
                            if ((e = t = document.currentScript) && !t.hasAttribute('data-turbolinks-suppress-warning'))
                                for (; (e = e.parentNode); )
                                    if (e === document.body)
                                        return console.warn(
                                            'You are loading Turbolinks from a <script> element inside the <body> element. This is probably not what you meant to do!\n\nLoad your application’s JavaScript bundle inside the <head> element instead. <script> elements in <body> are evaluated with each page change.\n\nFor more information, see: https://github.com/turbolinks/turbolinks#working-with-script-elements\n\n——\nSuppress this warning by adding a `data-turbolinks-suppress-warning` attribute to: %s',
                                            t.outerHTML
                                        );
                        })();
                    }.call(this),
                    function() {
                        var e, t, n;
                        (r.start = function() {
                            return t() ? (null == r.controller && (r.controller = e()), r.controller.start()) : void 0;
                        }),
                            (t = function() {
                                return null == window.Turbolinks && (window.Turbolinks = r), n();
                            }),
                            (e = function() {
                                var e;
                                return ((e = new r.Controller()).adapter = new r.BrowserAdapter(e)), e;
                            }),
                            (n = function() {
                                return window.Turbolinks === r;
                            })() && r.start();
                    }.call(this));
            }.call(this),
                e.exports
                    ? (e.exports = r)
                    : void 0 === (o = 'function' == typeof (i = r) ? i.call(t, n, t, e) : i) || (e.exports = o));
        }.call(this));
    },
    function(e, t, n) {
        e.exports = (function() {
            'use strict';
            var e = function() {
                    return (e =
                        Object.assign ||
                        function(e) {
                            for (var t, n = 1, i = arguments.length; n < i; n++)
                                for (var o in (t = arguments[n]))
                                    Object.prototype.hasOwnProperty.call(t, o) && (e[o] = t[o]);
                            return e;
                        }).apply(this, arguments);
                },
                t = [
                    'onChange',
                    'onClose',
                    'onDayCreate',
                    'onDestroy',
                    'onKeyDown',
                    'onMonthChange',
                    'onOpen',
                    'onParseConfig',
                    'onReady',
                    'onValueUpdate',
                    'onYearChange',
                    'onPreCalendarPosition',
                ],
                n = {
                    _disable: [],
                    _enable: [],
                    allowInput: !1,
                    altFormat: 'F j, Y',
                    altInput: !1,
                    altInputClass: 'form-control input',
                    animate: 'object' == typeof window && -1 === window.navigator.userAgent.indexOf('MSIE'),
                    ariaDateFormat: 'F j, Y',
                    clickOpens: !0,
                    closeOnSelect: !0,
                    conjunction: ', ',
                    dateFormat: 'Y-m-d',
                    defaultHour: 12,
                    defaultMinute: 0,
                    defaultSeconds: 0,
                    disable: [],
                    disableMobile: !1,
                    enable: [],
                    enableSeconds: !1,
                    enableTime: !1,
                    errorHandler: function(e) {
                        return 'undefined' != typeof console && console.warn(e);
                    },
                    getWeek: function(e) {
                        var t = new Date(e.getTime());
                        t.setHours(0, 0, 0, 0), t.setDate(t.getDate() + 3 - ((t.getDay() + 6) % 7));
                        var n = new Date(t.getFullYear(), 0, 4);
                        return 1 + Math.round(((t.getTime() - n.getTime()) / 864e5 - 3 + ((n.getDay() + 6) % 7)) / 7);
                    },
                    hourIncrement: 1,
                    ignoredFocusElements: [],
                    inline: !1,
                    locale: 'default',
                    minuteIncrement: 5,
                    mode: 'single',
                    monthSelectorType: 'dropdown',
                    nextArrow:
                        "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>",
                    noCalendar: !1,
                    now: new Date(),
                    onChange: [],
                    onClose: [],
                    onDayCreate: [],
                    onDestroy: [],
                    onKeyDown: [],
                    onMonthChange: [],
                    onOpen: [],
                    onParseConfig: [],
                    onReady: [],
                    onValueUpdate: [],
                    onYearChange: [],
                    onPreCalendarPosition: [],
                    plugins: [],
                    position: 'auto',
                    positionElement: void 0,
                    prevArrow:
                        "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>",
                    shorthandCurrentMonth: !1,
                    showMonths: 1,
                    static: !1,
                    time_24hr: !1,
                    weekNumbers: !1,
                    wrap: !1,
                },
                i = {
                    weekdays: {
                        shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                        longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        longhand: [
                            'January',
                            'February',
                            'March',
                            'April',
                            'May',
                            'June',
                            'July',
                            'August',
                            'September',
                            'October',
                            'November',
                            'December',
                        ],
                    },
                    daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
                    firstDayOfWeek: 0,
                    ordinal: function(e) {
                        var t = e % 100;
                        if (t > 3 && t < 21) return 'th';
                        switch (t % 10) {
                            case 1:
                                return 'st';
                            case 2:
                                return 'nd';
                            case 3:
                                return 'rd';
                            default:
                                return 'th';
                        }
                    },
                    rangeSeparator: ' to ',
                    weekAbbreviation: 'Wk',
                    scrollTitle: 'Scroll to increment',
                    toggleTitle: 'Click to toggle',
                    amPM: ['AM', 'PM'],
                    yearAriaLabel: 'Year',
                    hourAriaLabel: 'Hour',
                    minuteAriaLabel: 'Minute',
                    time_24hr: !1,
                },
                o = function(e) {
                    return ('0' + e).slice(-2);
                },
                r = function(e) {
                    return !0 === e ? 1 : 0;
                };
            function a(e, t, n) {
                var i;
                return (
                    void 0 === n && (n = !1),
                    function() {
                        var o = this,
                            r = arguments;
                        null !== i && clearTimeout(i),
                            (i = window.setTimeout(function() {
                                (i = null), n || e.apply(o, r);
                            }, t)),
                            n && !i && e.apply(o, r);
                    }
                );
            }
            var s = function(e) {
                return e instanceof Array ? e : [e];
            };
            function c(e, t, n) {
                if (!0 === n) return e.classList.add(t);
                e.classList.remove(t);
            }
            function l(e, t, n) {
                var i = window.document.createElement(e);
                return (t = t || ''), (n = n || ''), (i.className = t), void 0 !== n && (i.textContent = n), i;
            }
            function u(e) {
                for (; e.firstChild; ) e.removeChild(e.firstChild);
            }
            function d(e, t) {
                var n = l('div', 'numInputWrapper'),
                    i = l('input', 'numInput ' + e),
                    o = l('span', 'arrowUp'),
                    r = l('span', 'arrowDown');
                if (
                    (-1 === navigator.userAgent.indexOf('MSIE 9.0')
                        ? (i.type = 'number')
                        : ((i.type = 'text'), (i.pattern = '\\d*')),
                    void 0 !== t)
                )
                    for (var a in t) i.setAttribute(a, t[a]);
                return n.appendChild(i), n.appendChild(o), n.appendChild(r), n;
            }
            var h = function() {},
                f = function(e, t, n) {
                    return n.months[t ? 'shorthand' : 'longhand'][e];
                },
                p = {
                    D: h,
                    F: function(e, t, n) {
                        e.setMonth(n.months.longhand.indexOf(t));
                    },
                    G: function(e, t) {
                        e.setHours(parseFloat(t));
                    },
                    H: function(e, t) {
                        e.setHours(parseFloat(t));
                    },
                    J: function(e, t) {
                        e.setDate(parseFloat(t));
                    },
                    K: function(e, t, n) {
                        e.setHours((e.getHours() % 12) + 12 * r(new RegExp(n.amPM[1], 'i').test(t)));
                    },
                    M: function(e, t, n) {
                        e.setMonth(n.months.shorthand.indexOf(t));
                    },
                    S: function(e, t) {
                        e.setSeconds(parseFloat(t));
                    },
                    U: function(e, t) {
                        return new Date(1e3 * parseFloat(t));
                    },
                    W: function(e, t, n) {
                        var i = parseInt(t),
                            o = new Date(e.getFullYear(), 0, 2 + 7 * (i - 1), 0, 0, 0, 0);
                        return o.setDate(o.getDate() - o.getDay() + n.firstDayOfWeek), o;
                    },
                    Y: function(e, t) {
                        e.setFullYear(parseFloat(t));
                    },
                    Z: function(e, t) {
                        return new Date(t);
                    },
                    d: function(e, t) {
                        e.setDate(parseFloat(t));
                    },
                    h: function(e, t) {
                        e.setHours(parseFloat(t));
                    },
                    i: function(e, t) {
                        e.setMinutes(parseFloat(t));
                    },
                    j: function(e, t) {
                        e.setDate(parseFloat(t));
                    },
                    l: h,
                    m: function(e, t) {
                        e.setMonth(parseFloat(t) - 1);
                    },
                    n: function(e, t) {
                        e.setMonth(parseFloat(t) - 1);
                    },
                    s: function(e, t) {
                        e.setSeconds(parseFloat(t));
                    },
                    u: function(e, t) {
                        return new Date(parseFloat(t));
                    },
                    w: h,
                    y: function(e, t) {
                        e.setFullYear(2e3 + parseFloat(t));
                    },
                },
                m = {
                    D: '(\\w+)',
                    F: '(\\w+)',
                    G: '(\\d\\d|\\d)',
                    H: '(\\d\\d|\\d)',
                    J: '(\\d\\d|\\d)\\w+',
                    K: '',
                    M: '(\\w+)',
                    S: '(\\d\\d|\\d)',
                    U: '(.+)',
                    W: '(\\d\\d|\\d)',
                    Y: '(\\d{4})',
                    Z: '(.+)',
                    d: '(\\d\\d|\\d)',
                    h: '(\\d\\d|\\d)',
                    i: '(\\d\\d|\\d)',
                    j: '(\\d\\d|\\d)',
                    l: '(\\w+)',
                    m: '(\\d\\d|\\d)',
                    n: '(\\d\\d|\\d)',
                    s: '(\\d\\d|\\d)',
                    u: '(.+)',
                    w: '(\\d\\d|\\d)',
                    y: '(\\d{2})',
                },
                v = {
                    Z: function(e) {
                        return e.toISOString();
                    },
                    D: function(e, t, n) {
                        return t.weekdays.shorthand[v.w(e, t, n)];
                    },
                    F: function(e, t, n) {
                        return f(v.n(e, t, n) - 1, !1, t);
                    },
                    G: function(e, t, n) {
                        return o(v.h(e, t, n));
                    },
                    H: function(e) {
                        return o(e.getHours());
                    },
                    J: function(e, t) {
                        return void 0 !== t.ordinal ? e.getDate() + t.ordinal(e.getDate()) : e.getDate();
                    },
                    K: function(e, t) {
                        return t.amPM[r(e.getHours() > 11)];
                    },
                    M: function(e, t) {
                        return f(e.getMonth(), !0, t);
                    },
                    S: function(e) {
                        return o(e.getSeconds());
                    },
                    U: function(e) {
                        return e.getTime() / 1e3;
                    },
                    W: function(e, t, n) {
                        return n.getWeek(e);
                    },
                    Y: function(e) {
                        return e.getFullYear();
                    },
                    d: function(e) {
                        return o(e.getDate());
                    },
                    h: function(e) {
                        return e.getHours() % 12 ? e.getHours() % 12 : 12;
                    },
                    i: function(e) {
                        return o(e.getMinutes());
                    },
                    j: function(e) {
                        return e.getDate();
                    },
                    l: function(e, t) {
                        return t.weekdays.longhand[e.getDay()];
                    },
                    m: function(e) {
                        return o(e.getMonth() + 1);
                    },
                    n: function(e) {
                        return e.getMonth() + 1;
                    },
                    s: function(e) {
                        return e.getSeconds();
                    },
                    u: function(e) {
                        return e.getTime();
                    },
                    w: function(e) {
                        return e.getDay();
                    },
                    y: function(e) {
                        return String(e.getFullYear()).substring(2);
                    },
                },
                g = function(e) {
                    var t = e.config,
                        o = void 0 === t ? n : t,
                        r = e.l10n,
                        a = void 0 === r ? i : r;
                    return function(e, t, n) {
                        var i = n || a;
                        return void 0 !== o.formatDate
                            ? o.formatDate(e, t, i)
                            : t
                                  .split('')
                                  .map(function(t, n, r) {
                                      return v[t] && '\\' !== r[n - 1] ? v[t](e, i, o) : '\\' !== t ? t : '';
                                  })
                                  .join('');
                    };
                },
                y = function(e) {
                    var t = e.config,
                        o = void 0 === t ? n : t,
                        r = e.l10n,
                        a = void 0 === r ? i : r;
                    return function(e, t, i, r) {
                        if (0 === e || e) {
                            var s,
                                c = r || a,
                                l = e;
                            if (e instanceof Date) s = new Date(e.getTime());
                            else if ('string' != typeof e && void 0 !== e.toFixed) s = new Date(e);
                            else if ('string' == typeof e) {
                                var u = t || (o || n).dateFormat,
                                    d = String(e).trim();
                                if ('today' === d) (s = new Date()), (i = !0);
                                else if (/Z$/.test(d) || /GMT$/.test(d)) s = new Date(e);
                                else if (o && o.parseDate) s = o.parseDate(e, u);
                                else {
                                    s =
                                        o && o.noCalendar
                                            ? new Date(new Date().setHours(0, 0, 0, 0))
                                            : new Date(new Date().getFullYear(), 0, 1, 0, 0, 0, 0);
                                    for (var h = void 0, f = [], v = 0, g = 0, y = ''; v < u.length; v++) {
                                        var b = u[v],
                                            w = '\\' === b,
                                            E = '\\' === u[v - 1] || w;
                                        if (m[b] && !E) {
                                            y += m[b];
                                            var _ = new RegExp(y).exec(e);
                                            _ &&
                                                (h = !0) &&
                                                f['Y' !== b ? 'push' : 'unshift']({ fn: p[b], val: _[++g] });
                                        } else w || (y += '.');
                                        f.forEach(function(e) {
                                            var t = e.fn,
                                                n = e.val;
                                            return (s = t(s, n, c) || s);
                                        });
                                    }
                                    s = h ? s : void 0;
                                }
                            }
                            if (s instanceof Date && !isNaN(s.getTime())) return !0 === i && s.setHours(0, 0, 0, 0), s;
                            o.errorHandler(new Error('Invalid date provided: ' + l));
                        }
                    };
                };
            function b(e, t, n) {
                return (
                    void 0 === n && (n = !0),
                    !1 !== n
                        ? new Date(e.getTime()).setHours(0, 0, 0, 0) - new Date(t.getTime()).setHours(0, 0, 0, 0)
                        : e.getTime() - t.getTime()
                );
            }
            var w = function(e, t, n) {
                    return e > Math.min(t, n) && e < Math.max(t, n);
                },
                E = { DAY: 864e5 };
            'function' != typeof Object.assign &&
                (Object.assign = function(e) {
                    for (var t = [], n = 1; n < arguments.length; n++) t[n - 1] = arguments[n];
                    if (!e) throw TypeError('Cannot convert undefined or null to object');
                    for (
                        var i = function(t) {
                                t &&
                                    Object.keys(t).forEach(function(n) {
                                        return (e[n] = t[n]);
                                    });
                            },
                            o = 0,
                            r = t;
                        o < r.length;
                        o++
                    ) {
                        var a = r[o];
                        i(a);
                    }
                    return e;
                });
            var _ = 300;
            function C(h, p) {
                var v = { config: e({}, n, D.defaultConfig), l10n: i };
                function C(e) {
                    return e.bind(v);
                }
                function S() {
                    var e = v.config;
                    (!1 === e.weekNumbers && 1 === e.showMonths) ||
                        (!0 !== e.noCalendar &&
                            window.requestAnimationFrame(function() {
                                if (
                                    (void 0 !== v.calendarContainer &&
                                        ((v.calendarContainer.style.visibility = 'hidden'),
                                        (v.calendarContainer.style.display = 'block')),
                                    void 0 !== v.daysContainer)
                                ) {
                                    var t = (v.days.offsetWidth + 1) * e.showMonths;
                                    (v.daysContainer.style.width = t + 'px'),
                                        (v.calendarContainer.style.width =
                                            t + (void 0 !== v.weekWrapper ? v.weekWrapper.offsetWidth : 0) + 'px'),
                                        v.calendarContainer.style.removeProperty('visibility'),
                                        v.calendarContainer.style.removeProperty('display');
                                }
                            }));
                }
                function I(e) {
                    0 === v.selectedDates.length && oe(),
                        void 0 !== e &&
                            'blur' !== e.type &&
                            (function(e) {
                                e.preventDefault();
                                var t = 'keydown' === e.type,
                                    n = e.target;
                                void 0 !== v.amPM &&
                                    e.target === v.amPM &&
                                    (v.amPM.textContent = v.l10n.amPM[r(v.amPM.textContent === v.l10n.amPM[0])]);
                                var i = parseFloat(n.getAttribute('min')),
                                    a = parseFloat(n.getAttribute('max')),
                                    s = parseFloat(n.getAttribute('step')),
                                    c = parseInt(n.value, 10),
                                    l = e.delta || (t ? (38 === e.which ? 1 : -1) : 0),
                                    u = c + s * l;
                                if (void 0 !== n.value && 2 === n.value.length) {
                                    var d = n === v.hourElement,
                                        h = n === v.minuteElement;
                                    u < i
                                        ? ((u = a + u + r(!d) + (r(d) && r(!v.amPM))),
                                          h && j(void 0, -1, v.hourElement))
                                        : u > a &&
                                          ((u = n === v.hourElement ? u - a - r(!v.amPM) : i),
                                          h && j(void 0, 1, v.hourElement)),
                                        v.amPM &&
                                            d &&
                                            (1 === s ? u + c === 23 : Math.abs(u - c) > s) &&
                                            (v.amPM.textContent =
                                                v.l10n.amPM[r(v.amPM.textContent === v.l10n.amPM[0])]),
                                        (n.value = o(u));
                                }
                            })(e);
                    var t = v._input.value;
                    T(), be(), v._input.value !== t && v._debouncedChange();
                }
                function T() {
                    if (void 0 !== v.hourElement && void 0 !== v.minuteElement) {
                        var e,
                            t,
                            n = (parseInt(v.hourElement.value.slice(-2), 10) || 0) % 24,
                            i = (parseInt(v.minuteElement.value, 10) || 0) % 60,
                            o = void 0 !== v.secondElement ? (parseInt(v.secondElement.value, 10) || 0) % 60 : 0;
                        void 0 !== v.amPM &&
                            ((e = n), (t = v.amPM.textContent), (n = (e % 12) + 12 * r(t === v.l10n.amPM[1])));
                        var a =
                            void 0 !== v.config.minTime ||
                            (v.config.minDate &&
                                v.minDateHasTime &&
                                v.latestSelectedDateObj &&
                                0 === b(v.latestSelectedDateObj, v.config.minDate, !0));
                        if (
                            void 0 !== v.config.maxTime ||
                            (v.config.maxDate &&
                                v.maxDateHasTime &&
                                v.latestSelectedDateObj &&
                                0 === b(v.latestSelectedDateObj, v.config.maxDate, !0))
                        ) {
                            var s = void 0 !== v.config.maxTime ? v.config.maxTime : v.config.maxDate;
                            (n = Math.min(n, s.getHours())) === s.getHours() && (i = Math.min(i, s.getMinutes())),
                                i === s.getMinutes() && (o = Math.min(o, s.getSeconds()));
                        }
                        if (a) {
                            var c = void 0 !== v.config.minTime ? v.config.minTime : v.config.minDate;
                            (n = Math.max(n, c.getHours())) === c.getHours() && (i = Math.max(i, c.getMinutes())),
                                i === c.getMinutes() && (o = Math.max(o, c.getSeconds()));
                        }
                        O(n, i, o);
                    }
                }
                function L(e) {
                    var t = e || v.latestSelectedDateObj;
                    t && O(t.getHours(), t.getMinutes(), t.getSeconds());
                }
                function M() {
                    var e = v.config.defaultHour,
                        t = v.config.defaultMinute,
                        n = v.config.defaultSeconds;
                    if (void 0 !== v.config.minDate) {
                        var i = v.config.minDate.getHours(),
                            o = v.config.minDate.getMinutes();
                        (e = Math.max(e, i)) === i && (t = Math.max(o, t)),
                            e === i && t === o && (n = v.config.minDate.getSeconds());
                    }
                    if (void 0 !== v.config.maxDate) {
                        var r = v.config.maxDate.getHours(),
                            a = v.config.maxDate.getMinutes();
                        (e = Math.min(e, r)) === r && (t = Math.min(a, t)),
                            e === r && t === a && (n = v.config.maxDate.getSeconds());
                    }
                    O(e, t, n);
                }
                function O(e, t, n) {
                    void 0 !== v.latestSelectedDateObj && v.latestSelectedDateObj.setHours(e % 24, t, n || 0, 0),
                        v.hourElement &&
                            v.minuteElement &&
                            !v.isMobile &&
                            ((v.hourElement.value = o(v.config.time_24hr ? e : ((12 + e) % 12) + 12 * r(e % 12 == 0))),
                            (v.minuteElement.value = o(t)),
                            void 0 !== v.amPM && (v.amPM.textContent = v.l10n.amPM[r(e >= 12)]),
                            void 0 !== v.secondElement && (v.secondElement.value = o(n)));
                }
                function x(e) {
                    var t = parseInt(e.target.value) + (e.delta || 0);
                    (t / 1e3 > 1 || ('Enter' === e.key && !/[^\d]/.test(t.toString()))) && Z(t);
                }
                function A(e, t, n, i) {
                    return t instanceof Array
                        ? t.forEach(function(t) {
                              return A(e, t, n, i);
                          })
                        : e instanceof Array
                        ? e.forEach(function(e) {
                              return A(e, t, n, i);
                          })
                        : (e.addEventListener(t, n, i),
                          void v._handlers.push({ element: e, event: t, handler: n, options: i }));
                }
                function k(e) {
                    return function(t) {
                        1 === t.which && e(t);
                    };
                }
                function P() {
                    pe('onChange');
                }
                function N(e, t) {
                    var n =
                            void 0 !== e
                                ? v.parseDate(e)
                                : v.latestSelectedDateObj ||
                                  (v.config.minDate && v.config.minDate > v.now
                                      ? v.config.minDate
                                      : v.config.maxDate && v.config.maxDate < v.now
                                      ? v.config.maxDate
                                      : v.now),
                        i = v.currentYear,
                        o = v.currentMonth;
                    try {
                        void 0 !== n && ((v.currentYear = n.getFullYear()), (v.currentMonth = n.getMonth()));
                    } catch (e) {
                        (e.message = 'Invalid date supplied: ' + n), v.config.errorHandler(e);
                    }
                    t && v.currentYear !== i && (pe('onYearChange'), K()),
                        !t || (v.currentYear === i && v.currentMonth === o) || pe('onMonthChange'),
                        v.redraw();
                }
                function F(e) {
                    ~e.target.className.indexOf('arrow') && j(e, e.target.classList.contains('arrowUp') ? 1 : -1);
                }
                function j(e, t, n) {
                    var i = e && e.target,
                        o = n || (i && i.parentNode && i.parentNode.firstChild),
                        r = me('increment');
                    (r.delta = t), o && o.dispatchEvent(r);
                }
                function R(e, t, n, i) {
                    var o = Q(t, !0),
                        r = l('span', 'flatpickr-day ' + e, t.getDate().toString());
                    return (
                        (r.dateObj = t),
                        (r.$i = i),
                        r.setAttribute('aria-label', v.formatDate(t, v.config.ariaDateFormat)),
                        -1 === e.indexOf('hidden') &&
                            0 === b(t, v.now) &&
                            ((v.todayDateElem = r), r.classList.add('today'), r.setAttribute('aria-current', 'date')),
                        o
                            ? ((r.tabIndex = -1),
                              ve(t) &&
                                  (r.classList.add('selected'),
                                  (v.selectedDateElem = r),
                                  'range' === v.config.mode &&
                                      (c(r, 'startRange', v.selectedDates[0] && 0 === b(t, v.selectedDates[0], !0)),
                                      c(r, 'endRange', v.selectedDates[1] && 0 === b(t, v.selectedDates[1], !0)),
                                      'nextMonthDay' === e && r.classList.add('inRange'))))
                            : r.classList.add('flatpickr-disabled'),
                        'range' === v.config.mode &&
                            (function(e) {
                                return (
                                    !('range' !== v.config.mode || v.selectedDates.length < 2) &&
                                    b(e, v.selectedDates[0]) >= 0 &&
                                    b(e, v.selectedDates[1]) <= 0
                                );
                            })(t) &&
                            !ve(t) &&
                            r.classList.add('inRange'),
                        v.weekNumbers &&
                            1 === v.config.showMonths &&
                            'prevMonthDay' !== e &&
                            n % 7 == 1 &&
                            v.weekNumbers.insertAdjacentHTML(
                                'beforeend',
                                "<span class='flatpickr-day'>" + v.config.getWeek(t) + '</span>'
                            ),
                        pe('onDayCreate', r),
                        r
                    );
                }
                function H(e) {
                    e.focus(), 'range' === v.config.mode && ne(e);
                }
                function B(e) {
                    for (
                        var t = e > 0 ? 0 : v.config.showMonths - 1, n = e > 0 ? v.config.showMonths : -1, i = t;
                        i != n;
                        i += e
                    )
                        for (
                            var o = v.daysContainer.children[i],
                                r = e > 0 ? 0 : o.children.length - 1,
                                a = e > 0 ? o.children.length : -1,
                                s = r;
                            s != a;
                            s += e
                        ) {
                            var c = o.children[s];
                            if (-1 === c.className.indexOf('hidden') && Q(c.dateObj)) return c;
                        }
                }
                function q(e, t) {
                    var n = ee(document.activeElement || document.body),
                        i =
                            void 0 !== e
                                ? e
                                : n
                                ? document.activeElement
                                : void 0 !== v.selectedDateElem && ee(v.selectedDateElem)
                                ? v.selectedDateElem
                                : void 0 !== v.todayDateElem && ee(v.todayDateElem)
                                ? v.todayDateElem
                                : B(t > 0 ? 1 : -1);
                    return void 0 === i
                        ? v._input.focus()
                        : n
                        ? void (function(e, t) {
                              for (
                                  var n = -1 === e.className.indexOf('Month') ? e.dateObj.getMonth() : v.currentMonth,
                                      i = t > 0 ? v.config.showMonths : -1,
                                      o = t > 0 ? 1 : -1,
                                      r = n - v.currentMonth;
                                  r != i;
                                  r += o
                              )
                                  for (
                                      var a = v.daysContainer.children[r],
                                          s = n - v.currentMonth === r ? e.$i + t : t < 0 ? a.children.length - 1 : 0,
                                          c = a.children.length,
                                          l = s;
                                      l >= 0 && l < c && l != (t > 0 ? c : -1);
                                      l += o
                                  ) {
                                      var u = a.children[l];
                                      if (
                                          -1 === u.className.indexOf('hidden') &&
                                          Q(u.dateObj) &&
                                          Math.abs(e.$i - l) >= Math.abs(t)
                                      )
                                          return H(u);
                                  }
                              v.changeMonth(o), q(B(o), 0);
                          })(i, t)
                        : H(i);
                }
                function V(e, t) {
                    for (
                        var n = (new Date(e, t, 1).getDay() - v.l10n.firstDayOfWeek + 7) % 7,
                            i = v.utils.getDaysInMonth((t - 1 + 12) % 12),
                            o = v.utils.getDaysInMonth(t),
                            r = window.document.createDocumentFragment(),
                            a = v.config.showMonths > 1,
                            s = a ? 'prevMonthDay hidden' : 'prevMonthDay',
                            c = a ? 'nextMonthDay hidden' : 'nextMonthDay',
                            u = i + 1 - n,
                            d = 0;
                        u <= i;
                        u++, d++
                    )
                        r.appendChild(R(s, new Date(e, t - 1, u), u, d));
                    for (u = 1; u <= o; u++, d++) r.appendChild(R('', new Date(e, t, u), u, d));
                    for (var h = o + 1; h <= 42 - n && (1 === v.config.showMonths || d % 7 != 0); h++, d++)
                        r.appendChild(R(c, new Date(e, t + 1, h % o), h, d));
                    var f = l('div', 'dayContainer');
                    return f.appendChild(r), f;
                }
                function Y() {
                    if (void 0 !== v.daysContainer) {
                        u(v.daysContainer), v.weekNumbers && u(v.weekNumbers);
                        for (var e = document.createDocumentFragment(), t = 0; t < v.config.showMonths; t++) {
                            var n = new Date(v.currentYear, v.currentMonth, 1);
                            n.setMonth(v.currentMonth + t), e.appendChild(V(n.getFullYear(), n.getMonth()));
                        }
                        v.daysContainer.appendChild(e),
                            (v.days = v.daysContainer.firstChild),
                            'range' === v.config.mode && 1 === v.selectedDates.length && ne();
                    }
                }
                function K() {
                    if (!(v.config.showMonths > 1 || 'dropdown' !== v.config.monthSelectorType)) {
                        var e = function(e) {
                            return !(
                                (void 0 !== v.config.minDate &&
                                    v.currentYear === v.config.minDate.getFullYear() &&
                                    e < v.config.minDate.getMonth()) ||
                                (void 0 !== v.config.maxDate &&
                                    v.currentYear === v.config.maxDate.getFullYear() &&
                                    e > v.config.maxDate.getMonth())
                            );
                        };
                        (v.monthsDropdownContainer.tabIndex = -1), (v.monthsDropdownContainer.innerHTML = '');
                        for (var t = 0; t < 12; t++)
                            if (e(t)) {
                                var n = l('option', 'flatpickr-monthDropdown-month');
                                (n.value = new Date(v.currentYear, t).getMonth().toString()),
                                    (n.textContent = f(t, v.config.shorthandCurrentMonth, v.l10n)),
                                    (n.tabIndex = -1),
                                    v.currentMonth === t && (n.selected = !0),
                                    v.monthsDropdownContainer.appendChild(n);
                            }
                    }
                }
                function U() {
                    var e,
                        t = l('div', 'flatpickr-month'),
                        n = window.document.createDocumentFragment();
                    v.config.showMonths > 1 || 'static' === v.config.monthSelectorType
                        ? (e = l('span', 'cur-month'))
                        : ((v.monthsDropdownContainer = l('select', 'flatpickr-monthDropdown-months')),
                          A(v.monthsDropdownContainer, 'change', function(e) {
                              var t = e.target,
                                  n = parseInt(t.value, 10);
                              v.changeMonth(n - v.currentMonth), pe('onMonthChange');
                          }),
                          K(),
                          (e = v.monthsDropdownContainer));
                    var i = d('cur-year', { tabindex: '-1' }),
                        o = i.getElementsByTagName('input')[0];
                    o.setAttribute('aria-label', v.l10n.yearAriaLabel),
                        v.config.minDate && o.setAttribute('min', v.config.minDate.getFullYear().toString()),
                        v.config.maxDate &&
                            (o.setAttribute('max', v.config.maxDate.getFullYear().toString()),
                            (o.disabled =
                                !!v.config.minDate &&
                                v.config.minDate.getFullYear() === v.config.maxDate.getFullYear()));
                    var r = l('div', 'flatpickr-current-month');
                    return (
                        r.appendChild(e),
                        r.appendChild(i),
                        n.appendChild(r),
                        t.appendChild(n),
                        { container: t, yearElement: o, monthElement: e }
                    );
                }
                function W() {
                    u(v.monthNav),
                        v.monthNav.appendChild(v.prevMonthNav),
                        v.config.showMonths && ((v.yearElements = []), (v.monthElements = []));
                    for (var e = v.config.showMonths; e--; ) {
                        var t = U();
                        v.yearElements.push(t.yearElement),
                            v.monthElements.push(t.monthElement),
                            v.monthNav.appendChild(t.container);
                    }
                    v.monthNav.appendChild(v.nextMonthNav);
                }
                function G() {
                    v.weekdayContainer ? u(v.weekdayContainer) : (v.weekdayContainer = l('div', 'flatpickr-weekdays'));
                    for (var e = v.config.showMonths; e--; ) {
                        var t = l('div', 'flatpickr-weekdaycontainer');
                        v.weekdayContainer.appendChild(t);
                    }
                    return z(), v.weekdayContainer;
                }
                function z() {
                    if (v.weekdayContainer) {
                        var e = v.l10n.firstDayOfWeek,
                            t = v.l10n.weekdays.shorthand.slice();
                        e > 0 && e < t.length && (t = t.splice(e, t.length).concat(t.splice(0, e)));
                        for (var n = v.config.showMonths; n--; )
                            v.weekdayContainer.children[n].innerHTML =
                                "\n      <span class='flatpickr-weekday'>\n        " +
                                t.join("</span><span class='flatpickr-weekday'>") +
                                '\n      </span>\n      ';
                    }
                }
                function J(e, t) {
                    void 0 === t && (t = !0);
                    var n = t ? e : e - v.currentMonth;
                    (n < 0 && !0 === v._hidePrevMonthArrow) ||
                        (n > 0 && !0 === v._hideNextMonthArrow) ||
                        ((v.currentMonth += n),
                        (v.currentMonth < 0 || v.currentMonth > 11) &&
                            ((v.currentYear += v.currentMonth > 11 ? 1 : -1),
                            (v.currentMonth = (v.currentMonth + 12) % 12),
                            pe('onYearChange'),
                            K()),
                        Y(),
                        pe('onMonthChange'),
                        ge());
                }
                function X(e) {
                    return !(!v.config.appendTo || !v.config.appendTo.contains(e)) || v.calendarContainer.contains(e);
                }
                function $(e) {
                    if (v.isOpen && !v.config.inline) {
                        var t = 'function' == typeof (a = e).composedPath ? a.composedPath()[0] : a.target,
                            n = X(t),
                            i =
                                t === v.input ||
                                t === v.altInput ||
                                v.element.contains(t) ||
                                (e.path && e.path.indexOf && (~e.path.indexOf(v.input) || ~e.path.indexOf(v.altInput))),
                            o =
                                'blur' === e.type
                                    ? i && e.relatedTarget && !X(e.relatedTarget)
                                    : !i && !n && !X(e.relatedTarget),
                            r = !v.config.ignoredFocusElements.some(function(e) {
                                return e.contains(t);
                            });
                        o &&
                            r &&
                            (void 0 !== v.timeContainer &&
                                void 0 !== v.minuteElement &&
                                void 0 !== v.hourElement &&
                                I(),
                            v.close(),
                            'range' === v.config.mode && 1 === v.selectedDates.length && (v.clear(!1), v.redraw()));
                    }
                    var a;
                }
                function Z(e) {
                    if (
                        !(
                            !e ||
                            (v.config.minDate && e < v.config.minDate.getFullYear()) ||
                            (v.config.maxDate && e > v.config.maxDate.getFullYear())
                        )
                    ) {
                        var t = e,
                            n = v.currentYear !== t;
                        (v.currentYear = t || v.currentYear),
                            v.config.maxDate && v.currentYear === v.config.maxDate.getFullYear()
                                ? (v.currentMonth = Math.min(v.config.maxDate.getMonth(), v.currentMonth))
                                : v.config.minDate &&
                                  v.currentYear === v.config.minDate.getFullYear() &&
                                  (v.currentMonth = Math.max(v.config.minDate.getMonth(), v.currentMonth)),
                            n && (v.redraw(), pe('onYearChange'), K());
                    }
                }
                function Q(e, t) {
                    void 0 === t && (t = !0);
                    var n = v.parseDate(e, void 0, t);
                    if (
                        (v.config.minDate && n && b(n, v.config.minDate, void 0 !== t ? t : !v.minDateHasTime) < 0) ||
                        (v.config.maxDate && n && b(n, v.config.maxDate, void 0 !== t ? t : !v.maxDateHasTime) > 0)
                    )
                        return !1;
                    if (0 === v.config.enable.length && 0 === v.config.disable.length) return !0;
                    if (void 0 === n) return !1;
                    for (
                        var i = v.config.enable.length > 0,
                            o = i ? v.config.enable : v.config.disable,
                            r = 0,
                            a = void 0;
                        r < o.length;
                        r++
                    ) {
                        if ('function' == typeof (a = o[r]) && a(n)) return i;
                        if (a instanceof Date && void 0 !== n && a.getTime() === n.getTime()) return i;
                        if ('string' == typeof a && void 0 !== n) {
                            var s = v.parseDate(a, void 0, !0);
                            return s && s.getTime() === n.getTime() ? i : !i;
                        }
                        if (
                            'object' == typeof a &&
                            void 0 !== n &&
                            a.from &&
                            a.to &&
                            n.getTime() >= a.from.getTime() &&
                            n.getTime() <= a.to.getTime()
                        )
                            return i;
                    }
                    return !i;
                }
                function ee(e) {
                    return (
                        void 0 !== v.daysContainer &&
                        -1 === e.className.indexOf('hidden') &&
                        v.daysContainer.contains(e)
                    );
                }
                function te(e) {
                    var t = e.target === v._input,
                        n = v.config.allowInput,
                        i = v.isOpen && (!n || !t),
                        o = v.config.inline && t && !n;
                    if (13 === e.keyCode && t) {
                        if (n)
                            return (
                                v.setDate(
                                    v._input.value,
                                    !0,
                                    e.target === v.altInput ? v.config.altFormat : v.config.dateFormat
                                ),
                                e.target.blur()
                            );
                        v.open();
                    } else if (X(e.target) || i || o) {
                        var r = !!v.timeContainer && v.timeContainer.contains(e.target);
                        switch (e.keyCode) {
                            case 13:
                                r ? (e.preventDefault(), I(), le()) : ue(e);
                                break;
                            case 27:
                                e.preventDefault(), le();
                                break;
                            case 8:
                            case 46:
                                t && !v.config.allowInput && (e.preventDefault(), v.clear());
                                break;
                            case 37:
                            case 39:
                                if (r || t) v.hourElement && v.hourElement.focus();
                                else if (
                                    (e.preventDefault(),
                                    void 0 !== v.daysContainer &&
                                        (!1 === n || (document.activeElement && ee(document.activeElement))))
                                ) {
                                    var a = 39 === e.keyCode ? 1 : -1;
                                    e.ctrlKey ? (e.stopPropagation(), J(a), q(B(1), 0)) : q(void 0, a);
                                }
                                break;
                            case 38:
                            case 40:
                                e.preventDefault();
                                var s = 40 === e.keyCode ? 1 : -1;
                                (v.daysContainer && void 0 !== e.target.$i) ||
                                e.target === v.input ||
                                e.target === v.altInput
                                    ? e.ctrlKey
                                        ? (e.stopPropagation(), Z(v.currentYear - s), q(B(1), 0))
                                        : r || q(void 0, 7 * s)
                                    : e.target === v.currentYearElement
                                    ? Z(v.currentYear - s)
                                    : v.config.enableTime &&
                                      (!r && v.hourElement && v.hourElement.focus(), I(e), v._debouncedChange());
                                break;
                            case 9:
                                if (r) {
                                    var c = [v.hourElement, v.minuteElement, v.secondElement, v.amPM]
                                            .concat(v.pluginElements)
                                            .filter(function(e) {
                                                return e;
                                            }),
                                        l = c.indexOf(e.target);
                                    if (-1 !== l) {
                                        var u = c[l + (e.shiftKey ? -1 : 1)];
                                        e.preventDefault(), (u || v._input).focus();
                                    }
                                } else
                                    !v.config.noCalendar &&
                                        v.daysContainer &&
                                        v.daysContainer.contains(e.target) &&
                                        e.shiftKey &&
                                        (e.preventDefault(), v._input.focus());
                        }
                    }
                    if (void 0 !== v.amPM && e.target === v.amPM)
                        switch (e.key) {
                            case v.l10n.amPM[0].charAt(0):
                            case v.l10n.amPM[0].charAt(0).toLowerCase():
                                (v.amPM.textContent = v.l10n.amPM[0]), T(), be();
                                break;
                            case v.l10n.amPM[1].charAt(0):
                            case v.l10n.amPM[1].charAt(0).toLowerCase():
                                (v.amPM.textContent = v.l10n.amPM[1]), T(), be();
                        }
                    (t || X(e.target)) && pe('onKeyDown', e);
                }
                function ne(e) {
                    if (
                        1 === v.selectedDates.length &&
                        (!e || (e.classList.contains('flatpickr-day') && !e.classList.contains('flatpickr-disabled')))
                    ) {
                        for (
                            var t = e ? e.dateObj.getTime() : v.days.firstElementChild.dateObj.getTime(),
                                n = v.parseDate(v.selectedDates[0], void 0, !0).getTime(),
                                i = Math.min(t, v.selectedDates[0].getTime()),
                                o = Math.max(t, v.selectedDates[0].getTime()),
                                r = !1,
                                a = 0,
                                s = 0,
                                c = i;
                            c < o;
                            c += E.DAY
                        )
                            Q(new Date(c), !0) ||
                                ((r = r || (c > i && c < o)),
                                c < n && (!a || c > a) ? (a = c) : c > n && (!s || c < s) && (s = c));
                        for (var l = 0; l < v.config.showMonths; l++)
                            for (
                                var u = v.daysContainer.children[l],
                                    d = function(i, o) {
                                        var c = u.children[i],
                                            l = c.dateObj.getTime(),
                                            d = (a > 0 && l < a) || (s > 0 && l > s);
                                        return d
                                            ? (c.classList.add('notAllowed'),
                                              ['inRange', 'startRange', 'endRange'].forEach(function(e) {
                                                  c.classList.remove(e);
                                              }),
                                              'continue')
                                            : r && !d
                                            ? 'continue'
                                            : (['startRange', 'inRange', 'endRange', 'notAllowed'].forEach(function(e) {
                                                  c.classList.remove(e);
                                              }),
                                              void (
                                                  void 0 !== e &&
                                                  (e.classList.add(
                                                      t <= v.selectedDates[0].getTime() ? 'startRange' : 'endRange'
                                                  ),
                                                  n < t && l === n
                                                      ? c.classList.add('startRange')
                                                      : n > t && l === n && c.classList.add('endRange'),
                                                  l >= a &&
                                                      (0 === s || l <= s) &&
                                                      w(l, n, t) &&
                                                      c.classList.add('inRange'))
                                              ));
                                    },
                                    h = 0,
                                    f = u.children.length;
                                h < f;
                                h++
                            )
                                d(h);
                    }
                }
                function ie() {
                    !v.isOpen || v.config.static || v.config.inline || se();
                }
                function oe() {
                    v.setDate(void 0 !== v.config.minDate ? new Date(v.config.minDate.getTime()) : new Date(), !0),
                        M(),
                        be();
                }
                function re(e) {
                    return function(t) {
                        var n = (v.config['_' + e + 'Date'] = v.parseDate(t, v.config.dateFormat)),
                            i = v.config['_' + ('min' === e ? 'max' : 'min') + 'Date'];
                        void 0 !== n &&
                            (v['min' === e ? 'minDateHasTime' : 'maxDateHasTime'] =
                                n.getHours() > 0 || n.getMinutes() > 0 || n.getSeconds() > 0),
                            v.selectedDates &&
                                ((v.selectedDates = v.selectedDates.filter(function(e) {
                                    return Q(e);
                                })),
                                v.selectedDates.length || 'min' !== e || L(n),
                                be()),
                            v.daysContainer &&
                                (ce(),
                                void 0 !== n
                                    ? (v.currentYearElement[e] = n.getFullYear().toString())
                                    : v.currentYearElement.removeAttribute(e),
                                (v.currentYearElement.disabled =
                                    !!i && void 0 !== n && i.getFullYear() === n.getFullYear()));
                    };
                }
                function ae() {
                    'object' != typeof v.config.locale &&
                        void 0 === D.l10ns[v.config.locale] &&
                        v.config.errorHandler(new Error('flatpickr: invalid locale ' + v.config.locale)),
                        (v.l10n = e(
                            {},
                            D.l10ns.default,
                            'object' == typeof v.config.locale
                                ? v.config.locale
                                : 'default' !== v.config.locale
                                ? D.l10ns[v.config.locale]
                                : void 0
                        )),
                        (m.K =
                            '(' +
                            v.l10n.amPM[0] +
                            '|' +
                            v.l10n.amPM[1] +
                            '|' +
                            v.l10n.amPM[0].toLowerCase() +
                            '|' +
                            v.l10n.amPM[1].toLowerCase() +
                            ')'),
                        void 0 === e({}, p, JSON.parse(JSON.stringify(h.dataset || {}))).time_24hr &&
                            void 0 === D.defaultConfig.time_24hr &&
                            (v.config.time_24hr = v.l10n.time_24hr),
                        (v.formatDate = g(v)),
                        (v.parseDate = y({ config: v.config, l10n: v.l10n }));
                }
                function se(e) {
                    if (void 0 !== v.calendarContainer) {
                        pe('onPreCalendarPosition');
                        var t = e || v._positionElement,
                            n = Array.prototype.reduce.call(
                                v.calendarContainer.children,
                                function(e, t) {
                                    return e + t.offsetHeight;
                                },
                                0
                            ),
                            i = v.calendarContainer.offsetWidth,
                            o = v.config.position.split(' '),
                            r = o[0],
                            a = o.length > 1 ? o[1] : null,
                            s = t.getBoundingClientRect(),
                            l = window.innerHeight - s.bottom,
                            u = 'above' === r || ('below' !== r && l < n && s.top > n),
                            d = window.pageYOffset + s.top + (u ? -n - 2 : t.offsetHeight + 2);
                        if (
                            (c(v.calendarContainer, 'arrowTop', !u),
                            c(v.calendarContainer, 'arrowBottom', u),
                            !v.config.inline)
                        ) {
                            var h = window.pageXOffset + s.left - (null != a && 'center' === a ? (i - s.width) / 2 : 0),
                                f = window.document.body.offsetWidth - (window.pageXOffset + s.right),
                                p = h + i > window.document.body.offsetWidth,
                                m = f + i > window.document.body.offsetWidth;
                            if ((c(v.calendarContainer, 'rightMost', p), !v.config.static))
                                if (((v.calendarContainer.style.top = d + 'px'), p))
                                    if (m) {
                                        var g = document.styleSheets[0];
                                        if (void 0 === g) return;
                                        var y = window.document.body.offsetWidth,
                                            b = Math.max(0, y / 2 - i / 2),
                                            w = g.cssRules.length,
                                            E = '{left:' + s.left + 'px;right:auto;}';
                                        c(v.calendarContainer, 'rightMost', !1),
                                            c(v.calendarContainer, 'centerMost', !0),
                                            g.insertRule(
                                                '.flatpickr-calendar.centerMost:before,.flatpickr-calendar.centerMost:after' +
                                                    E,
                                                w
                                            ),
                                            (v.calendarContainer.style.left = b + 'px'),
                                            (v.calendarContainer.style.right = 'auto');
                                    } else
                                        (v.calendarContainer.style.left = 'auto'),
                                            (v.calendarContainer.style.right = f + 'px');
                                else
                                    (v.calendarContainer.style.left = h + 'px'),
                                        (v.calendarContainer.style.right = 'auto');
                        }
                    }
                }
                function ce() {
                    v.config.noCalendar || v.isMobile || (ge(), Y());
                }
                function le() {
                    v._input.focus(),
                        -1 !== window.navigator.userAgent.indexOf('MSIE') || void 0 !== navigator.msMaxTouchPoints
                            ? setTimeout(v.close, 0)
                            : v.close();
                }
                function ue(e) {
                    e.preventDefault(), e.stopPropagation();
                    var t = (function e(t, n) {
                        return n(t) ? t : t.parentNode ? e(t.parentNode, n) : void 0;
                    })(e.target, function(e) {
                        return (
                            e.classList &&
                            e.classList.contains('flatpickr-day') &&
                            !e.classList.contains('flatpickr-disabled') &&
                            !e.classList.contains('notAllowed')
                        );
                    });
                    if (void 0 !== t) {
                        var n = t,
                            i = (v.latestSelectedDateObj = new Date(n.dateObj.getTime())),
                            o =
                                (i.getMonth() < v.currentMonth ||
                                    i.getMonth() > v.currentMonth + v.config.showMonths - 1) &&
                                'range' !== v.config.mode;
                        if (((v.selectedDateElem = n), 'single' === v.config.mode)) v.selectedDates = [i];
                        else if ('multiple' === v.config.mode) {
                            var r = ve(i);
                            r ? v.selectedDates.splice(parseInt(r), 1) : v.selectedDates.push(i);
                        } else
                            'range' === v.config.mode &&
                                (2 === v.selectedDates.length && v.clear(!1, !1),
                                (v.latestSelectedDateObj = i),
                                v.selectedDates.push(i),
                                0 !== b(i, v.selectedDates[0], !0) &&
                                    v.selectedDates.sort(function(e, t) {
                                        return e.getTime() - t.getTime();
                                    }));
                        if ((T(), o)) {
                            var a = v.currentYear !== i.getFullYear();
                            (v.currentYear = i.getFullYear()),
                                (v.currentMonth = i.getMonth()),
                                a && (pe('onYearChange'), K()),
                                pe('onMonthChange');
                        }
                        if (
                            (ge(),
                            Y(),
                            be(),
                            v.config.enableTime &&
                                setTimeout(function() {
                                    return (v.showTimeInput = !0);
                                }, 50),
                            o || 'range' === v.config.mode || 1 !== v.config.showMonths
                                ? void 0 !== v.selectedDateElem &&
                                  void 0 === v.hourElement &&
                                  v.selectedDateElem &&
                                  v.selectedDateElem.focus()
                                : H(n),
                            void 0 !== v.hourElement && void 0 !== v.hourElement && v.hourElement.focus(),
                            v.config.closeOnSelect)
                        ) {
                            var s = 'single' === v.config.mode && !v.config.enableTime,
                                c = 'range' === v.config.mode && 2 === v.selectedDates.length && !v.config.enableTime;
                            (s || c) && le();
                        }
                        P();
                    }
                }
                (v.parseDate = y({ config: v.config, l10n: v.l10n })),
                    (v._handlers = []),
                    (v.pluginElements = []),
                    (v.loadedPlugins = []),
                    (v._bind = A),
                    (v._setHoursFromDate = L),
                    (v._positionCalendar = se),
                    (v.changeMonth = J),
                    (v.changeYear = Z),
                    (v.clear = function(e, t) {
                        void 0 === e && (e = !0),
                            void 0 === t && (t = !0),
                            (v.input.value = ''),
                            void 0 !== v.altInput && (v.altInput.value = ''),
                            void 0 !== v.mobileInput && (v.mobileInput.value = ''),
                            (v.selectedDates = []),
                            (v.latestSelectedDateObj = void 0),
                            !0 === t &&
                                ((v.currentYear = v._initialDate.getFullYear()),
                                (v.currentMonth = v._initialDate.getMonth())),
                            (v.showTimeInput = !1),
                            !0 === v.config.enableTime && M(),
                            v.redraw(),
                            e && pe('onChange');
                    }),
                    (v.close = function() {
                        (v.isOpen = !1),
                            v.isMobile ||
                                (void 0 !== v.calendarContainer && v.calendarContainer.classList.remove('open'),
                                void 0 !== v._input && v._input.classList.remove('active')),
                            pe('onClose');
                    }),
                    (v._createElement = l),
                    (v.destroy = function() {
                        void 0 !== v.config && pe('onDestroy');
                        for (var e = v._handlers.length; e--; ) {
                            var t = v._handlers[e];
                            t.element.removeEventListener(t.event, t.handler, t.options);
                        }
                        if (((v._handlers = []), v.mobileInput))
                            v.mobileInput.parentNode && v.mobileInput.parentNode.removeChild(v.mobileInput),
                                (v.mobileInput = void 0);
                        else if (v.calendarContainer && v.calendarContainer.parentNode)
                            if (v.config.static && v.calendarContainer.parentNode) {
                                var n = v.calendarContainer.parentNode;
                                if ((n.lastChild && n.removeChild(n.lastChild), n.parentNode)) {
                                    for (; n.firstChild; ) n.parentNode.insertBefore(n.firstChild, n);
                                    n.parentNode.removeChild(n);
                                }
                            } else v.calendarContainer.parentNode.removeChild(v.calendarContainer);
                        v.altInput &&
                            ((v.input.type = 'text'),
                            v.altInput.parentNode && v.altInput.parentNode.removeChild(v.altInput),
                            delete v.altInput),
                            v.input &&
                                ((v.input.type = v.input._type),
                                v.input.classList.remove('flatpickr-input'),
                                v.input.removeAttribute('readonly'),
                                (v.input.value = '')),
                            [
                                '_showTimeInput',
                                'latestSelectedDateObj',
                                '_hideNextMonthArrow',
                                '_hidePrevMonthArrow',
                                '__hideNextMonthArrow',
                                '__hidePrevMonthArrow',
                                'isMobile',
                                'isOpen',
                                'selectedDateElem',
                                'minDateHasTime',
                                'maxDateHasTime',
                                'days',
                                'daysContainer',
                                '_input',
                                '_positionElement',
                                'innerContainer',
                                'rContainer',
                                'monthNav',
                                'todayDateElem',
                                'calendarContainer',
                                'weekdayContainer',
                                'prevMonthNav',
                                'nextMonthNav',
                                'monthsDropdownContainer',
                                'currentMonthElement',
                                'currentYearElement',
                                'navigationCurrentMonth',
                                'selectedDateElem',
                                'config',
                            ].forEach(function(e) {
                                try {
                                    delete v[e];
                                } catch (e) {}
                            });
                    }),
                    (v.isEnabled = Q),
                    (v.jumpToDate = N),
                    (v.open = function(e, t) {
                        if ((void 0 === t && (t = v._positionElement), !0 === v.isMobile))
                            return (
                                e && (e.preventDefault(), e.target && e.target.blur()),
                                void 0 !== v.mobileInput && (v.mobileInput.focus(), v.mobileInput.click()),
                                void pe('onOpen')
                            );
                        if (!v._input.disabled && !v.config.inline) {
                            var n = v.isOpen;
                            (v.isOpen = !0),
                                n ||
                                    (v.calendarContainer.classList.add('open'),
                                    v._input.classList.add('active'),
                                    pe('onOpen'),
                                    se(t)),
                                !0 === v.config.enableTime &&
                                    !0 === v.config.noCalendar &&
                                    (0 === v.selectedDates.length && oe(),
                                    !1 !== v.config.allowInput ||
                                        (void 0 !== e && v.timeContainer.contains(e.relatedTarget)) ||
                                        setTimeout(function() {
                                            return v.hourElement.select();
                                        }, 50));
                        }
                    }),
                    (v.redraw = ce),
                    (v.set = function(e, n) {
                        if (null !== e && 'object' == typeof e)
                            for (var i in (Object.assign(v.config, e), e))
                                void 0 !== de[i] &&
                                    de[i].forEach(function(e) {
                                        return e();
                                    });
                        else
                            (v.config[e] = n),
                                void 0 !== de[e]
                                    ? de[e].forEach(function(e) {
                                          return e();
                                      })
                                    : t.indexOf(e) > -1 && (v.config[e] = s(n));
                        v.redraw(), be(!1);
                    }),
                    (v.setDate = function(e, t, n) {
                        if (
                            (void 0 === t && (t = !1),
                            void 0 === n && (n = v.config.dateFormat),
                            (0 !== e && !e) || (e instanceof Array && 0 === e.length))
                        )
                            return v.clear(t);
                        he(e, n),
                            (v.showTimeInput = v.selectedDates.length > 0),
                            (v.latestSelectedDateObj = v.selectedDates[v.selectedDates.length - 1]),
                            v.redraw(),
                            N(),
                            L(),
                            0 === v.selectedDates.length && v.clear(!1),
                            be(t),
                            t && pe('onChange');
                    }),
                    (v.toggle = function(e) {
                        if (!0 === v.isOpen) return v.close();
                        v.open(e);
                    });
                var de = { locale: [ae, z], showMonths: [W, S, G], minDate: [N], maxDate: [N] };
                function he(e, t) {
                    var n = [];
                    if (e instanceof Array)
                        n = e.map(function(e) {
                            return v.parseDate(e, t);
                        });
                    else if (e instanceof Date || 'number' == typeof e) n = [v.parseDate(e, t)];
                    else if ('string' == typeof e)
                        switch (v.config.mode) {
                            case 'single':
                            case 'time':
                                n = [v.parseDate(e, t)];
                                break;
                            case 'multiple':
                                n = e.split(v.config.conjunction).map(function(e) {
                                    return v.parseDate(e, t);
                                });
                                break;
                            case 'range':
                                n = e.split(v.l10n.rangeSeparator).map(function(e) {
                                    return v.parseDate(e, t);
                                });
                        }
                    else v.config.errorHandler(new Error('Invalid date supplied: ' + JSON.stringify(e)));
                    (v.selectedDates = n.filter(function(e) {
                        return e instanceof Date && Q(e, !1);
                    })),
                        'range' === v.config.mode &&
                            v.selectedDates.sort(function(e, t) {
                                return e.getTime() - t.getTime();
                            });
                }
                function fe(e) {
                    return e
                        .slice()
                        .map(function(e) {
                            return 'string' == typeof e || 'number' == typeof e || e instanceof Date
                                ? v.parseDate(e, void 0, !0)
                                : e && 'object' == typeof e && e.from && e.to
                                ? { from: v.parseDate(e.from, void 0), to: v.parseDate(e.to, void 0) }
                                : e;
                        })
                        .filter(function(e) {
                            return e;
                        });
                }
                function pe(e, t) {
                    if (void 0 !== v.config) {
                        var n = v.config[e];
                        if (void 0 !== n && n.length > 0)
                            for (var i = 0; n[i] && i < n.length; i++) n[i](v.selectedDates, v.input.value, v, t);
                        'onChange' === e && (v.input.dispatchEvent(me('change')), v.input.dispatchEvent(me('input')));
                    }
                }
                function me(e) {
                    var t = document.createEvent('Event');
                    return t.initEvent(e, !0, !0), t;
                }
                function ve(e) {
                    for (var t = 0; t < v.selectedDates.length; t++) if (0 === b(v.selectedDates[t], e)) return '' + t;
                    return !1;
                }
                function ge() {
                    v.config.noCalendar ||
                        v.isMobile ||
                        !v.monthNav ||
                        (v.yearElements.forEach(function(e, t) {
                            var n = new Date(v.currentYear, v.currentMonth, 1);
                            n.setMonth(v.currentMonth + t),
                                v.config.showMonths > 1 || 'static' === v.config.monthSelectorType
                                    ? (v.monthElements[t].textContent =
                                          f(n.getMonth(), v.config.shorthandCurrentMonth, v.l10n) + ' ')
                                    : (v.monthsDropdownContainer.value = n.getMonth().toString()),
                                (e.value = n.getFullYear().toString());
                        }),
                        (v._hidePrevMonthArrow =
                            void 0 !== v.config.minDate &&
                            (v.currentYear === v.config.minDate.getFullYear()
                                ? v.currentMonth <= v.config.minDate.getMonth()
                                : v.currentYear < v.config.minDate.getFullYear())),
                        (v._hideNextMonthArrow =
                            void 0 !== v.config.maxDate &&
                            (v.currentYear === v.config.maxDate.getFullYear()
                                ? v.currentMonth + 1 > v.config.maxDate.getMonth()
                                : v.currentYear > v.config.maxDate.getFullYear())));
                }
                function ye(e) {
                    return v.selectedDates
                        .map(function(t) {
                            return v.formatDate(t, e);
                        })
                        .filter(function(e, t, n) {
                            return 'range' !== v.config.mode || v.config.enableTime || n.indexOf(e) === t;
                        })
                        .join('range' !== v.config.mode ? v.config.conjunction : v.l10n.rangeSeparator);
                }
                function be(e) {
                    void 0 === e && (e = !0),
                        void 0 !== v.mobileInput &&
                            v.mobileFormatStr &&
                            (v.mobileInput.value =
                                void 0 !== v.latestSelectedDateObj
                                    ? v.formatDate(v.latestSelectedDateObj, v.mobileFormatStr)
                                    : ''),
                        (v.input.value = ye(v.config.dateFormat)),
                        void 0 !== v.altInput && (v.altInput.value = ye(v.config.altFormat)),
                        !1 !== e && pe('onValueUpdate');
                }
                function we(e) {
                    var t = v.prevMonthNav.contains(e.target),
                        n = v.nextMonthNav.contains(e.target);
                    t || n
                        ? J(t ? -1 : 1)
                        : v.yearElements.indexOf(e.target) >= 0
                        ? e.target.select()
                        : e.target.classList.contains('arrowUp')
                        ? v.changeYear(v.currentYear + 1)
                        : e.target.classList.contains('arrowDown') && v.changeYear(v.currentYear - 1);
                }
                return (
                    (function() {
                        (v.element = v.input = h),
                            (v.isOpen = !1),
                            (function() {
                                var i = [
                                        'wrap',
                                        'weekNumbers',
                                        'allowInput',
                                        'clickOpens',
                                        'time_24hr',
                                        'enableTime',
                                        'noCalendar',
                                        'altInput',
                                        'shorthandCurrentMonth',
                                        'inline',
                                        'static',
                                        'enableSeconds',
                                        'disableMobile',
                                    ],
                                    o = e({}, p, JSON.parse(JSON.stringify(h.dataset || {}))),
                                    r = {};
                                (v.config.parseDate = o.parseDate),
                                    (v.config.formatDate = o.formatDate),
                                    Object.defineProperty(v.config, 'enable', {
                                        get: function() {
                                            return v.config._enable;
                                        },
                                        set: function(e) {
                                            v.config._enable = fe(e);
                                        },
                                    }),
                                    Object.defineProperty(v.config, 'disable', {
                                        get: function() {
                                            return v.config._disable;
                                        },
                                        set: function(e) {
                                            v.config._disable = fe(e);
                                        },
                                    });
                                var a = 'time' === o.mode;
                                if (!o.dateFormat && (o.enableTime || a)) {
                                    var c = D.defaultConfig.dateFormat || n.dateFormat;
                                    r.dateFormat =
                                        o.noCalendar || a
                                            ? 'H:i' + (o.enableSeconds ? ':S' : '')
                                            : c + ' H:i' + (o.enableSeconds ? ':S' : '');
                                }
                                if (o.altInput && (o.enableTime || a) && !o.altFormat) {
                                    var l = D.defaultConfig.altFormat || n.altFormat;
                                    r.altFormat =
                                        o.noCalendar || a
                                            ? 'h:i' + (o.enableSeconds ? ':S K' : ' K')
                                            : l + ' h:i' + (o.enableSeconds ? ':S' : '') + ' K';
                                }
                                o.altInputClass ||
                                    (v.config.altInputClass = v.input.className + ' ' + v.config.altInputClass),
                                    Object.defineProperty(v.config, 'minDate', {
                                        get: function() {
                                            return v.config._minDate;
                                        },
                                        set: re('min'),
                                    }),
                                    Object.defineProperty(v.config, 'maxDate', {
                                        get: function() {
                                            return v.config._maxDate;
                                        },
                                        set: re('max'),
                                    });
                                var u = function(e) {
                                    return function(t) {
                                        v.config['min' === e ? '_minTime' : '_maxTime'] = v.parseDate(t, 'H:i:S');
                                    };
                                };
                                Object.defineProperty(v.config, 'minTime', {
                                    get: function() {
                                        return v.config._minTime;
                                    },
                                    set: u('min'),
                                }),
                                    Object.defineProperty(v.config, 'maxTime', {
                                        get: function() {
                                            return v.config._maxTime;
                                        },
                                        set: u('max'),
                                    }),
                                    'time' === o.mode && ((v.config.noCalendar = !0), (v.config.enableTime = !0)),
                                    Object.assign(v.config, r, o);
                                for (var d = 0; d < i.length; d++)
                                    v.config[i[d]] = !0 === v.config[i[d]] || 'true' === v.config[i[d]];
                                for (
                                    t
                                        .filter(function(e) {
                                            return void 0 !== v.config[e];
                                        })
                                        .forEach(function(e) {
                                            v.config[e] = s(v.config[e] || []).map(C);
                                        }),
                                        v.isMobile =
                                            !v.config.disableMobile &&
                                            !v.config.inline &&
                                            'single' === v.config.mode &&
                                            !v.config.disable.length &&
                                            !v.config.enable.length &&
                                            !v.config.weekNumbers &&
                                            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                                                navigator.userAgent
                                            ),
                                        d = 0;
                                    d < v.config.plugins.length;
                                    d++
                                ) {
                                    var f = v.config.plugins[d](v) || {};
                                    for (var m in f)
                                        t.indexOf(m) > -1
                                            ? (v.config[m] = s(f[m])
                                                  .map(C)
                                                  .concat(v.config[m]))
                                            : void 0 === o[m] && (v.config[m] = f[m]);
                                }
                                pe('onParseConfig');
                            })(),
                            ae(),
                            (v.input = v.config.wrap ? h.querySelector('[data-input]') : h),
                            v.input
                                ? ((v.input._type = v.input.type),
                                  (v.input.type = 'text'),
                                  v.input.classList.add('flatpickr-input'),
                                  (v._input = v.input),
                                  v.config.altInput &&
                                      ((v.altInput = l(v.input.nodeName, v.config.altInputClass)),
                                      (v._input = v.altInput),
                                      (v.altInput.placeholder = v.input.placeholder),
                                      (v.altInput.disabled = v.input.disabled),
                                      (v.altInput.required = v.input.required),
                                      (v.altInput.tabIndex = v.input.tabIndex),
                                      (v.altInput.type = 'text'),
                                      v.input.setAttribute('type', 'hidden'),
                                      !v.config.static &&
                                          v.input.parentNode &&
                                          v.input.parentNode.insertBefore(v.altInput, v.input.nextSibling)),
                                  v.config.allowInput || v._input.setAttribute('readonly', 'readonly'),
                                  (v._positionElement = v.config.positionElement || v._input))
                                : v.config.errorHandler(new Error('Invalid input element specified')),
                            (function() {
                                (v.selectedDates = []), (v.now = v.parseDate(v.config.now) || new Date());
                                var e =
                                    v.config.defaultDate ||
                                    (('INPUT' !== v.input.nodeName && 'TEXTAREA' !== v.input.nodeName) ||
                                    !v.input.placeholder ||
                                    v.input.value !== v.input.placeholder
                                        ? v.input.value
                                        : null);
                                e && he(e, v.config.dateFormat),
                                    (v._initialDate =
                                        v.selectedDates.length > 0
                                            ? v.selectedDates[0]
                                            : v.config.minDate && v.config.minDate.getTime() > v.now.getTime()
                                            ? v.config.minDate
                                            : v.config.maxDate && v.config.maxDate.getTime() < v.now.getTime()
                                            ? v.config.maxDate
                                            : v.now),
                                    (v.currentYear = v._initialDate.getFullYear()),
                                    (v.currentMonth = v._initialDate.getMonth()),
                                    v.selectedDates.length > 0 && (v.latestSelectedDateObj = v.selectedDates[0]),
                                    void 0 !== v.config.minTime &&
                                        (v.config.minTime = v.parseDate(v.config.minTime, 'H:i')),
                                    void 0 !== v.config.maxTime &&
                                        (v.config.maxTime = v.parseDate(v.config.maxTime, 'H:i')),
                                    (v.minDateHasTime =
                                        !!v.config.minDate &&
                                        (v.config.minDate.getHours() > 0 ||
                                            v.config.minDate.getMinutes() > 0 ||
                                            v.config.minDate.getSeconds() > 0)),
                                    (v.maxDateHasTime =
                                        !!v.config.maxDate &&
                                        (v.config.maxDate.getHours() > 0 ||
                                            v.config.maxDate.getMinutes() > 0 ||
                                            v.config.maxDate.getSeconds() > 0)),
                                    Object.defineProperty(v, 'showTimeInput', {
                                        get: function() {
                                            return v._showTimeInput;
                                        },
                                        set: function(e) {
                                            (v._showTimeInput = e),
                                                v.calendarContainer && c(v.calendarContainer, 'showTimeInput', e),
                                                v.isOpen && se();
                                        },
                                    });
                            })(),
                            (v.utils = {
                                getDaysInMonth: function(e, t) {
                                    return (
                                        void 0 === e && (e = v.currentMonth),
                                        void 0 === t && (t = v.currentYear),
                                        1 === e && ((t % 4 == 0 && t % 100 != 0) || t % 400 == 0)
                                            ? 29
                                            : v.l10n.daysInMonth[e]
                                    );
                                },
                            }),
                            v.isMobile ||
                                (function() {
                                    var e = window.document.createDocumentFragment();
                                    if (
                                        ((v.calendarContainer = l('div', 'flatpickr-calendar')),
                                        (v.calendarContainer.tabIndex = -1),
                                        !v.config.noCalendar)
                                    ) {
                                        if (
                                            (e.appendChild(
                                                ((v.monthNav = l('div', 'flatpickr-months')),
                                                (v.yearElements = []),
                                                (v.monthElements = []),
                                                (v.prevMonthNav = l('span', 'flatpickr-prev-month')),
                                                (v.prevMonthNav.innerHTML = v.config.prevArrow),
                                                (v.nextMonthNav = l('span', 'flatpickr-next-month')),
                                                (v.nextMonthNav.innerHTML = v.config.nextArrow),
                                                W(),
                                                Object.defineProperty(v, '_hidePrevMonthArrow', {
                                                    get: function() {
                                                        return v.__hidePrevMonthArrow;
                                                    },
                                                    set: function(e) {
                                                        v.__hidePrevMonthArrow !== e &&
                                                            (c(v.prevMonthNav, 'flatpickr-disabled', e),
                                                            (v.__hidePrevMonthArrow = e));
                                                    },
                                                }),
                                                Object.defineProperty(v, '_hideNextMonthArrow', {
                                                    get: function() {
                                                        return v.__hideNextMonthArrow;
                                                    },
                                                    set: function(e) {
                                                        v.__hideNextMonthArrow !== e &&
                                                            (c(v.nextMonthNav, 'flatpickr-disabled', e),
                                                            (v.__hideNextMonthArrow = e));
                                                    },
                                                }),
                                                (v.currentYearElement = v.yearElements[0]),
                                                ge(),
                                                v.monthNav)
                                            ),
                                            (v.innerContainer = l('div', 'flatpickr-innerContainer')),
                                            v.config.weekNumbers)
                                        ) {
                                            var t = (function() {
                                                    v.calendarContainer.classList.add('hasWeeks');
                                                    var e = l('div', 'flatpickr-weekwrapper');
                                                    e.appendChild(
                                                        l('span', 'flatpickr-weekday', v.l10n.weekAbbreviation)
                                                    );
                                                    var t = l('div', 'flatpickr-weeks');
                                                    return e.appendChild(t), { weekWrapper: e, weekNumbers: t };
                                                })(),
                                                n = t.weekWrapper,
                                                i = t.weekNumbers;
                                            v.innerContainer.appendChild(n), (v.weekNumbers = i), (v.weekWrapper = n);
                                        }
                                        (v.rContainer = l('div', 'flatpickr-rContainer')),
                                            v.rContainer.appendChild(G()),
                                            v.daysContainer ||
                                                ((v.daysContainer = l('div', 'flatpickr-days')),
                                                (v.daysContainer.tabIndex = -1)),
                                            Y(),
                                            v.rContainer.appendChild(v.daysContainer),
                                            v.innerContainer.appendChild(v.rContainer),
                                            e.appendChild(v.innerContainer);
                                    }
                                    v.config.enableTime &&
                                        e.appendChild(
                                            (function() {
                                                v.calendarContainer.classList.add('hasTime'),
                                                    v.config.noCalendar &&
                                                        v.calendarContainer.classList.add('noCalendar'),
                                                    (v.timeContainer = l('div', 'flatpickr-time')),
                                                    (v.timeContainer.tabIndex = -1);
                                                var e = l('span', 'flatpickr-time-separator', ':'),
                                                    t = d('flatpickr-hour', { 'aria-label': v.l10n.hourAriaLabel });
                                                v.hourElement = t.getElementsByTagName('input')[0];
                                                var n = d('flatpickr-minute', { 'aria-label': v.l10n.minuteAriaLabel });
                                                if (
                                                    ((v.minuteElement = n.getElementsByTagName('input')[0]),
                                                    (v.hourElement.tabIndex = v.minuteElement.tabIndex = -1),
                                                    (v.hourElement.value = o(
                                                        v.latestSelectedDateObj
                                                            ? v.latestSelectedDateObj.getHours()
                                                            : v.config.time_24hr
                                                            ? v.config.defaultHour
                                                            : (function(e) {
                                                                  switch (e % 24) {
                                                                      case 0:
                                                                      case 12:
                                                                          return 12;
                                                                      default:
                                                                          return e % 12;
                                                                  }
                                                              })(v.config.defaultHour)
                                                    )),
                                                    (v.minuteElement.value = o(
                                                        v.latestSelectedDateObj
                                                            ? v.latestSelectedDateObj.getMinutes()
                                                            : v.config.defaultMinute
                                                    )),
                                                    v.hourElement.setAttribute(
                                                        'step',
                                                        v.config.hourIncrement.toString()
                                                    ),
                                                    v.minuteElement.setAttribute(
                                                        'step',
                                                        v.config.minuteIncrement.toString()
                                                    ),
                                                    v.hourElement.setAttribute('min', v.config.time_24hr ? '0' : '1'),
                                                    v.hourElement.setAttribute('max', v.config.time_24hr ? '23' : '12'),
                                                    v.minuteElement.setAttribute('min', '0'),
                                                    v.minuteElement.setAttribute('max', '59'),
                                                    v.timeContainer.appendChild(t),
                                                    v.timeContainer.appendChild(e),
                                                    v.timeContainer.appendChild(n),
                                                    v.config.time_24hr && v.timeContainer.classList.add('time24hr'),
                                                    v.config.enableSeconds)
                                                ) {
                                                    v.timeContainer.classList.add('hasSeconds');
                                                    var i = d('flatpickr-second');
                                                    (v.secondElement = i.getElementsByTagName('input')[0]),
                                                        (v.secondElement.value = o(
                                                            v.latestSelectedDateObj
                                                                ? v.latestSelectedDateObj.getSeconds()
                                                                : v.config.defaultSeconds
                                                        )),
                                                        v.secondElement.setAttribute(
                                                            'step',
                                                            v.minuteElement.getAttribute('step')
                                                        ),
                                                        v.secondElement.setAttribute('min', '0'),
                                                        v.secondElement.setAttribute('max', '59'),
                                                        v.timeContainer.appendChild(
                                                            l('span', 'flatpickr-time-separator', ':')
                                                        ),
                                                        v.timeContainer.appendChild(i);
                                                }
                                                return (
                                                    v.config.time_24hr ||
                                                        ((v.amPM = l(
                                                            'span',
                                                            'flatpickr-am-pm',
                                                            v.l10n.amPM[
                                                                r(
                                                                    (v.latestSelectedDateObj
                                                                        ? v.hourElement.value
                                                                        : v.config.defaultHour) > 11
                                                                )
                                                            ]
                                                        )),
                                                        (v.amPM.title = v.l10n.toggleTitle),
                                                        (v.amPM.tabIndex = -1),
                                                        v.timeContainer.appendChild(v.amPM)),
                                                    v.timeContainer
                                                );
                                            })()
                                        ),
                                        c(v.calendarContainer, 'rangeMode', 'range' === v.config.mode),
                                        c(v.calendarContainer, 'animate', !0 === v.config.animate),
                                        c(v.calendarContainer, 'multiMonth', v.config.showMonths > 1),
                                        v.calendarContainer.appendChild(e);
                                    var a = void 0 !== v.config.appendTo && void 0 !== v.config.appendTo.nodeType;
                                    if (
                                        (v.config.inline || v.config.static) &&
                                        (v.calendarContainer.classList.add(v.config.inline ? 'inline' : 'static'),
                                        v.config.inline &&
                                            (!a && v.element.parentNode
                                                ? v.element.parentNode.insertBefore(
                                                      v.calendarContainer,
                                                      v._input.nextSibling
                                                  )
                                                : void 0 !== v.config.appendTo &&
                                                  v.config.appendTo.appendChild(v.calendarContainer)),
                                        v.config.static)
                                    ) {
                                        var s = l('div', 'flatpickr-wrapper');
                                        v.element.parentNode && v.element.parentNode.insertBefore(s, v.element),
                                            s.appendChild(v.element),
                                            v.altInput && s.appendChild(v.altInput),
                                            s.appendChild(v.calendarContainer);
                                    }
                                    v.config.static ||
                                        v.config.inline ||
                                        (void 0 !== v.config.appendTo
                                            ? v.config.appendTo
                                            : window.document.body
                                        ).appendChild(v.calendarContainer);
                                })(),
                            (function() {
                                if (
                                    (v.config.wrap &&
                                        ['open', 'close', 'toggle', 'clear'].forEach(function(e) {
                                            Array.prototype.forEach.call(
                                                v.element.querySelectorAll('[data-' + e + ']'),
                                                function(t) {
                                                    return A(t, 'click', v[e]);
                                                }
                                            );
                                        }),
                                    v.isMobile)
                                )
                                    !(function() {
                                        var e = v.config.enableTime
                                            ? v.config.noCalendar
                                                ? 'time'
                                                : 'datetime-local'
                                            : 'date';
                                        (v.mobileInput = l('input', v.input.className + ' flatpickr-mobile')),
                                            (v.mobileInput.step = v.input.getAttribute('step') || 'any'),
                                            (v.mobileInput.tabIndex = 1),
                                            (v.mobileInput.type = e),
                                            (v.mobileInput.disabled = v.input.disabled),
                                            (v.mobileInput.required = v.input.required),
                                            (v.mobileInput.placeholder = v.input.placeholder),
                                            (v.mobileFormatStr =
                                                'datetime-local' === e
                                                    ? 'Y-m-d\\TH:i:S'
                                                    : 'date' === e
                                                    ? 'Y-m-d'
                                                    : 'H:i:S'),
                                            v.selectedDates.length > 0 &&
                                                (v.mobileInput.defaultValue = v.mobileInput.value = v.formatDate(
                                                    v.selectedDates[0],
                                                    v.mobileFormatStr
                                                )),
                                            v.config.minDate &&
                                                (v.mobileInput.min = v.formatDate(v.config.minDate, 'Y-m-d')),
                                            v.config.maxDate &&
                                                (v.mobileInput.max = v.formatDate(v.config.maxDate, 'Y-m-d')),
                                            (v.input.type = 'hidden'),
                                            void 0 !== v.altInput && (v.altInput.type = 'hidden');
                                        try {
                                            v.input.parentNode &&
                                                v.input.parentNode.insertBefore(v.mobileInput, v.input.nextSibling);
                                        } catch (e) {}
                                        A(v.mobileInput, 'change', function(e) {
                                            v.setDate(e.target.value, !1, v.mobileFormatStr),
                                                pe('onChange'),
                                                pe('onClose');
                                        });
                                    })();
                                else {
                                    var e = a(ie, 50);
                                    (v._debouncedChange = a(P, _)),
                                        v.daysContainer &&
                                            !/iPhone|iPad|iPod/i.test(navigator.userAgent) &&
                                            A(v.daysContainer, 'mouseover', function(e) {
                                                'range' === v.config.mode && ne(e.target);
                                            }),
                                        A(window.document.body, 'keydown', te),
                                        v.config.inline || v.config.static || A(window, 'resize', e),
                                        void 0 !== window.ontouchstart
                                            ? A(window.document, 'touchstart', $)
                                            : A(window.document, 'mousedown', k($)),
                                        A(window.document, 'focus', $, { capture: !0 }),
                                        !0 === v.config.clickOpens &&
                                            (A(v._input, 'focus', v.open), A(v._input, 'mousedown', k(v.open))),
                                        void 0 !== v.daysContainer &&
                                            (A(v.monthNav, 'mousedown', k(we)),
                                            A(v.monthNav, ['keyup', 'increment'], x),
                                            A(v.daysContainer, 'mousedown', k(ue))),
                                        void 0 !== v.timeContainer &&
                                            void 0 !== v.minuteElement &&
                                            void 0 !== v.hourElement &&
                                            (A(v.timeContainer, ['increment'], I),
                                            A(v.timeContainer, 'blur', I, { capture: !0 }),
                                            A(v.timeContainer, 'mousedown', k(F)),
                                            A([v.hourElement, v.minuteElement], ['focus', 'click'], function(e) {
                                                return e.target.select();
                                            }),
                                            void 0 !== v.secondElement &&
                                                A(v.secondElement, 'focus', function() {
                                                    return v.secondElement && v.secondElement.select();
                                                }),
                                            void 0 !== v.amPM &&
                                                A(
                                                    v.amPM,
                                                    'mousedown',
                                                    k(function(e) {
                                                        I(e), P();
                                                    })
                                                ));
                                }
                            })(),
                            (v.selectedDates.length || v.config.noCalendar) &&
                                (v.config.enableTime &&
                                    L(v.config.noCalendar ? v.latestSelectedDateObj || v.config.minDate : void 0),
                                be(!1)),
                            S(),
                            (v.showTimeInput = v.selectedDates.length > 0 || v.config.noCalendar);
                        var i = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
                        !v.isMobile && i && se(), pe('onReady');
                    })(),
                    v
                );
            }
            function S(e, t) {
                for (
                    var n = Array.prototype.slice.call(e).filter(function(e) {
                            return e instanceof HTMLElement;
                        }),
                        i = [],
                        o = 0;
                    o < n.length;
                    o++
                ) {
                    var r = n[o];
                    try {
                        if (null !== r.getAttribute('data-fp-omit')) continue;
                        void 0 !== r._flatpickr && (r._flatpickr.destroy(), (r._flatpickr = void 0)),
                            (r._flatpickr = C(r, t || {})),
                            i.push(r._flatpickr);
                    } catch (e) {
                        console.error(e);
                    }
                }
                return 1 === i.length ? i[0] : i;
            }
            'undefined' != typeof HTMLElement &&
                'undefined' != typeof HTMLCollection &&
                'undefined' != typeof NodeList &&
                ((HTMLCollection.prototype.flatpickr = NodeList.prototype.flatpickr = function(e) {
                    return S(this, e);
                }),
                (HTMLElement.prototype.flatpickr = function(e) {
                    return S([this], e);
                }));
            var D = function(e, t) {
                return 'string' == typeof e
                    ? S(window.document.querySelectorAll(e), t)
                    : e instanceof Node
                    ? S([e], t)
                    : S(e, t);
            };
            return (
                (D.defaultConfig = {}),
                (D.l10ns = { en: e({}, i), default: e({}, i) }),
                (D.localize = function(t) {
                    D.l10ns.default = e({}, D.l10ns.default, t);
                }),
                (D.setDefaults = function(t) {
                    D.defaultConfig = e({}, D.defaultConfig, t);
                }),
                (D.parseDate = y({})),
                (D.formatDate = g({})),
                (D.compareDates = b),
                'undefined' != typeof jQuery &&
                    void 0 !== jQuery.fn &&
                    (jQuery.fn.flatpickr = function(e) {
                        return S(this, e);
                    }),
                (Date.prototype.fp_incr = function(e) {
                    return new Date(
                        this.getFullYear(),
                        this.getMonth(),
                        this.getDate() + ('string' == typeof e ? parseInt(e, 10) : e)
                    );
                }),
                'undefined' != typeof window && (window.flatpickr = D),
                D
            );
        })();
    },
    function(e, t, n) {
        var i;
        window,
            (i = function() {
                return (function(e) {
                    var t = {};
                    function n(i) {
                        if (t[i]) return t[i].exports;
                        var o = (t[i] = { i: i, l: !1, exports: {} });
                        return e[i].call(o.exports, o, o.exports, n), (o.l = !0), o.exports;
                    }
                    return (
                        (n.m = e),
                        (n.c = t),
                        (n.d = function(e, t, i) {
                            n.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: i });
                        }),
                        (n.r = function(e) {
                            'undefined' != typeof Symbol &&
                                Symbol.toStringTag &&
                                Object.defineProperty(e, Symbol.toStringTag, { value: 'Module' }),
                                Object.defineProperty(e, '__esModule', { value: !0 });
                        }),
                        (n.t = function(e, t) {
                            if ((1 & t && (e = n(e)), 8 & t)) return e;
                            if (4 & t && 'object' == typeof e && e && e.__esModule) return e;
                            var i = Object.create(null);
                            if (
                                (n.r(i),
                                Object.defineProperty(i, 'default', { enumerable: !0, value: e }),
                                2 & t && 'string' != typeof e)
                            )
                                for (var o in e)
                                    n.d(
                                        i,
                                        o,
                                        function(t) {
                                            return e[t];
                                        }.bind(null, o)
                                    );
                            return i;
                        }),
                        (n.n = function(e) {
                            var t =
                                e && e.__esModule
                                    ? function() {
                                          return e.default;
                                      }
                                    : function() {
                                          return e;
                                      };
                            return n.d(t, 'a', t), t;
                        }),
                        (n.o = function(e, t) {
                            return Object.prototype.hasOwnProperty.call(e, t);
                        }),
                        (n.p = '/public/assets/scripts/'),
                        n((n.s = 4))
                    );
                })([
                    function(e, t, n) {
                        'use strict';
                        var i = function(e) {
                                return (
                                    (function(e) {
                                        return !!e && 'object' == typeof e;
                                    })(e) &&
                                    !(function(e) {
                                        var t = Object.prototype.toString.call(e);
                                        return (
                                            '[object RegExp]' === t ||
                                            '[object Date]' === t ||
                                            (function(e) {
                                                return e.$$typeof === o;
                                            })(e)
                                        );
                                    })(e)
                                );
                            },
                            o = 'function' == typeof Symbol && Symbol.for ? Symbol.for('react.element') : 60103;
                        function r(e, t) {
                            return !1 !== t.clone && t.isMergeableObject(e)
                                ? l(((n = e), Array.isArray(n) ? [] : {}), e, t)
                                : e;
                            var n;
                        }
                        function a(e, t, n) {
                            return e.concat(t).map(function(e) {
                                return r(e, n);
                            });
                        }
                        function s(e) {
                            return Object.keys(e).concat(
                                (function(e) {
                                    return Object.getOwnPropertySymbols
                                        ? Object.getOwnPropertySymbols(e).filter(function(t) {
                                              return e.propertyIsEnumerable(t);
                                          })
                                        : [];
                                })(e)
                            );
                        }
                        function c(e, t, n) {
                            var i = {};
                            return (
                                n.isMergeableObject(e) &&
                                    s(e).forEach(function(t) {
                                        i[t] = r(e[t], n);
                                    }),
                                s(t).forEach(function(o) {
                                    (function(e, t) {
                                        try {
                                            return (
                                                t in e &&
                                                !(
                                                    Object.hasOwnProperty.call(e, t) &&
                                                    Object.propertyIsEnumerable.call(e, t)
                                                )
                                            );
                                        } catch (e) {
                                            return !1;
                                        }
                                    })(e, o) ||
                                        (n.isMergeableObject(t[o]) && e[o]
                                            ? (i[o] = (function(e, t) {
                                                  if (!t.customMerge) return l;
                                                  var n = t.customMerge(e);
                                                  return 'function' == typeof n ? n : l;
                                              })(o, n)(e[o], t[o], n))
                                            : (i[o] = r(t[o], n)));
                                }),
                                i
                            );
                        }
                        function l(e, t, n) {
                            ((n = n || {}).arrayMerge = n.arrayMerge || a),
                                (n.isMergeableObject = n.isMergeableObject || i),
                                (n.cloneUnlessOtherwiseSpecified = r);
                            var o = Array.isArray(t);
                            return o === Array.isArray(e) ? (o ? n.arrayMerge(e, t, n) : c(e, t, n)) : r(t, n);
                        }
                        l.all = function(e, t) {
                            if (!Array.isArray(e)) throw new Error('first argument should be an array');
                            return e.reduce(function(e, n) {
                                return l(e, n, t);
                            }, {});
                        };
                        var u = l;
                        e.exports = u;
                    },
                    function(e, t, n) {
                        'use strict';
                        (function(e, i) {
                            var o,
                                r = n(3);
                            o =
                                'undefined' != typeof self
                                    ? self
                                    : 'undefined' != typeof window
                                    ? window
                                    : void 0 !== e
                                    ? e
                                    : i;
                            var a = Object(r.a)(o);
                            t.a = a;
                        }.call(this, n(5), n(6)(e)));
                    },
                    function(e, t, n) {
                        e.exports = (function(e) {
                            var t = {};
                            function n(i) {
                                if (t[i]) return t[i].exports;
                                var o = (t[i] = { i: i, l: !1, exports: {} });
                                return e[i].call(o.exports, o, o.exports, n), (o.l = !0), o.exports;
                            }
                            return (
                                (n.m = e),
                                (n.c = t),
                                (n.d = function(e, t, i) {
                                    n.o(e, t) || Object.defineProperty(e, t, { enumerable: !0, get: i });
                                }),
                                (n.r = function(e) {
                                    'undefined' != typeof Symbol &&
                                        Symbol.toStringTag &&
                                        Object.defineProperty(e, Symbol.toStringTag, { value: 'Module' }),
                                        Object.defineProperty(e, '__esModule', { value: !0 });
                                }),
                                (n.t = function(e, t) {
                                    if ((1 & t && (e = n(e)), 8 & t)) return e;
                                    if (4 & t && 'object' == typeof e && e && e.__esModule) return e;
                                    var i = Object.create(null);
                                    if (
                                        (n.r(i),
                                        Object.defineProperty(i, 'default', { enumerable: !0, value: e }),
                                        2 & t && 'string' != typeof e)
                                    )
                                        for (var o in e)
                                            n.d(
                                                i,
                                                o,
                                                function(t) {
                                                    return e[t];
                                                }.bind(null, o)
                                            );
                                    return i;
                                }),
                                (n.n = function(e) {
                                    var t =
                                        e && e.__esModule
                                            ? function() {
                                                  return e.default;
                                              }
                                            : function() {
                                                  return e;
                                              };
                                    return n.d(t, 'a', t), t;
                                }),
                                (n.o = function(e, t) {
                                    return Object.prototype.hasOwnProperty.call(e, t);
                                }),
                                (n.p = ''),
                                n((n.s = 1))
                            );
                        })([
                            function(e, t) {
                                e.exports = function(e) {
                                    return Array.isArray
                                        ? Array.isArray(e)
                                        : '[object Array]' === Object.prototype.toString.call(e);
                                };
                            },
                            function(e, t, n) {
                                function i(e) {
                                    return (i =
                                        'function' == typeof Symbol && 'symbol' == typeof Symbol.iterator
                                            ? function(e) {
                                                  return typeof e;
                                              }
                                            : function(e) {
                                                  return e &&
                                                      'function' == typeof Symbol &&
                                                      e.constructor === Symbol &&
                                                      e !== Symbol.prototype
                                                      ? 'symbol'
                                                      : typeof e;
                                              })(e);
                                }
                                function o(e, t) {
                                    for (var n = 0; n < t.length; n++) {
                                        var i = t[n];
                                        (i.enumerable = i.enumerable || !1),
                                            (i.configurable = !0),
                                            'value' in i && (i.writable = !0),
                                            Object.defineProperty(e, i.key, i);
                                    }
                                }
                                var r = n(2),
                                    a = n(8),
                                    s = n(0),
                                    c = (function() {
                                        function e(t, n) {
                                            var i = n.location,
                                                o = void 0 === i ? 0 : i,
                                                r = n.distance,
                                                s = void 0 === r ? 100 : r,
                                                c = n.threshold,
                                                l = void 0 === c ? 0.6 : c,
                                                u = n.maxPatternLength,
                                                d = void 0 === u ? 32 : u,
                                                h = n.caseSensitive,
                                                f = void 0 !== h && h,
                                                p = n.tokenSeparator,
                                                m = void 0 === p ? / +/g : p,
                                                v = n.findAllMatches,
                                                g = void 0 !== v && v,
                                                y = n.minMatchCharLength,
                                                b = void 0 === y ? 1 : y,
                                                w = n.id,
                                                E = void 0 === w ? null : w,
                                                _ = n.keys,
                                                C = void 0 === _ ? [] : _,
                                                S = n.shouldSort,
                                                D = void 0 === S || S,
                                                I = n.getFn,
                                                T = void 0 === I ? a : I,
                                                L = n.sortFn,
                                                M =
                                                    void 0 === L
                                                        ? function(e, t) {
                                                              return e.score - t.score;
                                                          }
                                                        : L,
                                                O = n.tokenize,
                                                x = void 0 !== O && O,
                                                A = n.matchAllTokens,
                                                k = void 0 !== A && A,
                                                P = n.includeMatches,
                                                N = void 0 !== P && P,
                                                F = n.includeScore,
                                                j = void 0 !== F && F,
                                                R = n.verbose,
                                                H = void 0 !== R && R;
                                            !(function(e, t) {
                                                if (!(e instanceof t))
                                                    throw new TypeError('Cannot call a class as a function');
                                            })(this, e),
                                                (this.options = {
                                                    location: o,
                                                    distance: s,
                                                    threshold: l,
                                                    maxPatternLength: d,
                                                    isCaseSensitive: f,
                                                    tokenSeparator: m,
                                                    findAllMatches: g,
                                                    minMatchCharLength: b,
                                                    id: E,
                                                    keys: C,
                                                    includeMatches: N,
                                                    includeScore: j,
                                                    shouldSort: D,
                                                    getFn: T,
                                                    sortFn: M,
                                                    verbose: H,
                                                    tokenize: x,
                                                    matchAllTokens: k,
                                                }),
                                                this.setCollection(t);
                                        }
                                        var t, n;
                                        return (
                                            (t = e),
                                            (n = [
                                                {
                                                    key: 'setCollection',
                                                    value: function(e) {
                                                        return (this.list = e), e;
                                                    },
                                                },
                                                {
                                                    key: 'search',
                                                    value: function(e) {
                                                        var t =
                                                            arguments.length > 1 && void 0 !== arguments[1]
                                                                ? arguments[1]
                                                                : { limit: !1 };
                                                        this._log('---------\nSearch pattern: "'.concat(e, '"'));
                                                        var n = this._prepareSearchers(e),
                                                            i = n.tokenSearchers,
                                                            o = n.fullSearcher,
                                                            r = this._search(i, o),
                                                            a = r.weights,
                                                            s = r.results;
                                                        return (
                                                            this._computeScore(a, s),
                                                            this.options.shouldSort && this._sort(s),
                                                            t.limit &&
                                                                'number' == typeof t.limit &&
                                                                (s = s.slice(0, t.limit)),
                                                            this._format(s)
                                                        );
                                                    },
                                                },
                                                {
                                                    key: '_prepareSearchers',
                                                    value: function() {
                                                        var e =
                                                                arguments.length > 0 && void 0 !== arguments[0]
                                                                    ? arguments[0]
                                                                    : '',
                                                            t = [];
                                                        if (this.options.tokenize)
                                                            for (
                                                                var n = e.split(this.options.tokenSeparator),
                                                                    i = 0,
                                                                    o = n.length;
                                                                i < o;
                                                                i += 1
                                                            )
                                                                t.push(new r(n[i], this.options));
                                                        return {
                                                            tokenSearchers: t,
                                                            fullSearcher: new r(e, this.options),
                                                        };
                                                    },
                                                },
                                                {
                                                    key: '_search',
                                                    value: function() {
                                                        var e =
                                                                arguments.length > 0 && void 0 !== arguments[0]
                                                                    ? arguments[0]
                                                                    : [],
                                                            t = arguments.length > 1 ? arguments[1] : void 0,
                                                            n = this.list,
                                                            i = {},
                                                            o = [];
                                                        if ('string' == typeof n[0]) {
                                                            for (var r = 0, a = n.length; r < a; r += 1)
                                                                this._analyze(
                                                                    { key: '', value: n[r], record: r, index: r },
                                                                    {
                                                                        resultMap: i,
                                                                        results: o,
                                                                        tokenSearchers: e,
                                                                        fullSearcher: t,
                                                                    }
                                                                );
                                                            return { weights: null, results: o };
                                                        }
                                                        for (var s = {}, c = 0, l = n.length; c < l; c += 1)
                                                            for (
                                                                var u = n[c], d = 0, h = this.options.keys.length;
                                                                d < h;
                                                                d += 1
                                                            ) {
                                                                var f = this.options.keys[d];
                                                                if ('string' != typeof f) {
                                                                    if (
                                                                        ((s[f.name] = { weight: 1 - f.weight || 1 }),
                                                                        f.weight <= 0 || f.weight > 1)
                                                                    )
                                                                        throw new Error(
                                                                            'Key weight has to be > 0 and <= 1'
                                                                        );
                                                                    f = f.name;
                                                                } else s[f] = { weight: 1 };
                                                                this._analyze(
                                                                    {
                                                                        key: f,
                                                                        value: this.options.getFn(u, f),
                                                                        record: u,
                                                                        index: c,
                                                                    },
                                                                    {
                                                                        resultMap: i,
                                                                        results: o,
                                                                        tokenSearchers: e,
                                                                        fullSearcher: t,
                                                                    }
                                                                );
                                                            }
                                                        return { weights: s, results: o };
                                                    },
                                                },
                                                {
                                                    key: '_analyze',
                                                    value: function(e, t) {
                                                        var n = e.key,
                                                            i = e.arrayIndex,
                                                            o = void 0 === i ? -1 : i,
                                                            r = e.value,
                                                            a = e.record,
                                                            c = e.index,
                                                            l = t.tokenSearchers,
                                                            u = void 0 === l ? [] : l,
                                                            d = t.fullSearcher,
                                                            h = void 0 === d ? [] : d,
                                                            f = t.resultMap,
                                                            p = void 0 === f ? {} : f,
                                                            m = t.results,
                                                            v = void 0 === m ? [] : m;
                                                        if (null != r) {
                                                            var g = !1,
                                                                y = -1,
                                                                b = 0;
                                                            if ('string' == typeof r) {
                                                                this._log('\nKey: '.concat('' === n ? '-' : n));
                                                                var w = h.search(r);
                                                                if (
                                                                    (this._log(
                                                                        'Full text: "'
                                                                            .concat(r, '", score: ')
                                                                            .concat(w.score)
                                                                    ),
                                                                    this.options.tokenize)
                                                                ) {
                                                                    for (
                                                                        var E = r.split(this.options.tokenSeparator),
                                                                            _ = [],
                                                                            C = 0;
                                                                        C < u.length;
                                                                        C += 1
                                                                    ) {
                                                                        var S = u[C];
                                                                        this._log(
                                                                            '\nPattern: "'.concat(S.pattern, '"')
                                                                        );
                                                                        for (var D = !1, I = 0; I < E.length; I += 1) {
                                                                            var T = E[I],
                                                                                L = S.search(T),
                                                                                M = {};
                                                                            L.isMatch
                                                                                ? ((M[T] = L.score),
                                                                                  (g = !0),
                                                                                  (D = !0),
                                                                                  _.push(L.score))
                                                                                : ((M[T] = 1),
                                                                                  this.options.matchAllTokens ||
                                                                                      _.push(1)),
                                                                                this._log(
                                                                                    'Token: "'
                                                                                        .concat(T, '", score: ')
                                                                                        .concat(M[T])
                                                                                );
                                                                        }
                                                                        D && (b += 1);
                                                                    }
                                                                    y = _[0];
                                                                    for (var O = _.length, x = 1; x < O; x += 1)
                                                                        y += _[x];
                                                                    (y /= O), this._log('Token score average:', y);
                                                                }
                                                                var A = w.score;
                                                                y > -1 && (A = (A + y) / 2),
                                                                    this._log('Score average:', A);
                                                                var k =
                                                                    !this.options.tokenize ||
                                                                    !this.options.matchAllTokens ||
                                                                    b >= u.length;
                                                                if (
                                                                    (this._log('\nCheck Matches: '.concat(k)),
                                                                    (g || w.isMatch) && k)
                                                                ) {
                                                                    var P = p[c];
                                                                    P
                                                                        ? P.output.push({
                                                                              key: n,
                                                                              arrayIndex: o,
                                                                              value: r,
                                                                              score: A,
                                                                              matchedIndices: w.matchedIndices,
                                                                          })
                                                                        : ((p[c] = {
                                                                              item: a,
                                                                              output: [
                                                                                  {
                                                                                      key: n,
                                                                                      arrayIndex: o,
                                                                                      value: r,
                                                                                      score: A,
                                                                                      matchedIndices: w.matchedIndices,
                                                                                  },
                                                                              ],
                                                                          }),
                                                                          v.push(p[c]));
                                                                }
                                                            } else if (s(r))
                                                                for (var N = 0, F = r.length; N < F; N += 1)
                                                                    this._analyze(
                                                                        {
                                                                            key: n,
                                                                            arrayIndex: N,
                                                                            value: r[N],
                                                                            record: a,
                                                                            index: c,
                                                                        },
                                                                        {
                                                                            resultMap: p,
                                                                            results: v,
                                                                            tokenSearchers: u,
                                                                            fullSearcher: h,
                                                                        }
                                                                    );
                                                        }
                                                    },
                                                },
                                                {
                                                    key: '_computeScore',
                                                    value: function(e, t) {
                                                        this._log('\n\nComputing score:\n');
                                                        for (var n = 0, i = t.length; n < i; n += 1) {
                                                            for (
                                                                var o = t[n].output, r = o.length, a = 1, s = 1, c = 0;
                                                                c < r;
                                                                c += 1
                                                            ) {
                                                                var l = e ? e[o[c].key].weight : 1,
                                                                    u =
                                                                        (1 === l ? o[c].score : o[c].score || 0.001) *
                                                                        l;
                                                                1 !== l
                                                                    ? (s = Math.min(s, u))
                                                                    : ((o[c].nScore = u), (a *= u));
                                                            }
                                                            (t[n].score = 1 === s ? a : s), this._log(t[n]);
                                                        }
                                                    },
                                                },
                                                {
                                                    key: '_sort',
                                                    value: function(e) {
                                                        this._log('\n\nSorting....'), e.sort(this.options.sortFn);
                                                    },
                                                },
                                                {
                                                    key: '_format',
                                                    value: function(e) {
                                                        var t = [];
                                                        if (this.options.verbose) {
                                                            var n = [];
                                                            this._log(
                                                                '\n\nOutput:\n\n',
                                                                JSON.stringify(e, function(e, t) {
                                                                    if ('object' === i(t) && null !== t) {
                                                                        if (-1 !== n.indexOf(t)) return;
                                                                        n.push(t);
                                                                    }
                                                                    return t;
                                                                })
                                                            ),
                                                                (n = null);
                                                        }
                                                        var o = [];
                                                        this.options.includeMatches &&
                                                            o.push(function(e, t) {
                                                                var n = e.output;
                                                                t.matches = [];
                                                                for (var i = 0, o = n.length; i < o; i += 1) {
                                                                    var r = n[i];
                                                                    if (0 !== r.matchedIndices.length) {
                                                                        var a = {
                                                                            indices: r.matchedIndices,
                                                                            value: r.value,
                                                                        };
                                                                        r.key && (a.key = r.key),
                                                                            r.hasOwnProperty('arrayIndex') &&
                                                                                r.arrayIndex > -1 &&
                                                                                (a.arrayIndex = r.arrayIndex),
                                                                            t.matches.push(a);
                                                                    }
                                                                }
                                                            }),
                                                            this.options.includeScore &&
                                                                o.push(function(e, t) {
                                                                    t.score = e.score;
                                                                });
                                                        for (var r = 0, a = e.length; r < a; r += 1) {
                                                            var s = e[r];
                                                            if (
                                                                (this.options.id &&
                                                                    (s.item = this.options.getFn(
                                                                        s.item,
                                                                        this.options.id
                                                                    )[0]),
                                                                o.length)
                                                            ) {
                                                                for (
                                                                    var c = { item: s.item }, l = 0, u = o.length;
                                                                    l < u;
                                                                    l += 1
                                                                )
                                                                    o[l](s, c);
                                                                t.push(c);
                                                            } else t.push(s.item);
                                                        }
                                                        return t;
                                                    },
                                                },
                                                {
                                                    key: '_log',
                                                    value: function() {
                                                        var e;
                                                        this.options.verbose && (e = console).log.apply(e, arguments);
                                                    },
                                                },
                                            ]) && o(t.prototype, n),
                                            e
                                        );
                                    })();
                                e.exports = c;
                            },
                            function(e, t, n) {
                                function i(e, t) {
                                    for (var n = 0; n < t.length; n++) {
                                        var i = t[n];
                                        (i.enumerable = i.enumerable || !1),
                                            (i.configurable = !0),
                                            'value' in i && (i.writable = !0),
                                            Object.defineProperty(e, i.key, i);
                                    }
                                }
                                var o = n(3),
                                    r = n(4),
                                    a = n(7),
                                    s = (function() {
                                        function e(t, n) {
                                            var i = n.location,
                                                o = void 0 === i ? 0 : i,
                                                r = n.distance,
                                                s = void 0 === r ? 100 : r,
                                                c = n.threshold,
                                                l = void 0 === c ? 0.6 : c,
                                                u = n.maxPatternLength,
                                                d = void 0 === u ? 32 : u,
                                                h = n.isCaseSensitive,
                                                f = void 0 !== h && h,
                                                p = n.tokenSeparator,
                                                m = void 0 === p ? / +/g : p,
                                                v = n.findAllMatches,
                                                g = void 0 !== v && v,
                                                y = n.minMatchCharLength,
                                                b = void 0 === y ? 1 : y;
                                            !(function(e, t) {
                                                if (!(e instanceof t))
                                                    throw new TypeError('Cannot call a class as a function');
                                            })(this, e),
                                                (this.options = {
                                                    location: o,
                                                    distance: s,
                                                    threshold: l,
                                                    maxPatternLength: d,
                                                    isCaseSensitive: f,
                                                    tokenSeparator: m,
                                                    findAllMatches: g,
                                                    minMatchCharLength: b,
                                                }),
                                                (this.pattern = this.options.isCaseSensitive ? t : t.toLowerCase()),
                                                this.pattern.length <= d && (this.patternAlphabet = a(this.pattern));
                                        }
                                        var t, n;
                                        return (
                                            (t = e),
                                            (n = [
                                                {
                                                    key: 'search',
                                                    value: function(e) {
                                                        if (
                                                            (this.options.isCaseSensitive || (e = e.toLowerCase()),
                                                            this.pattern === e)
                                                        )
                                                            return {
                                                                isMatch: !0,
                                                                score: 0,
                                                                matchedIndices: [[0, e.length - 1]],
                                                            };
                                                        var t = this.options,
                                                            n = t.maxPatternLength,
                                                            i = t.tokenSeparator;
                                                        if (this.pattern.length > n) return o(e, this.pattern, i);
                                                        var a = this.options,
                                                            s = a.location,
                                                            c = a.distance,
                                                            l = a.threshold,
                                                            u = a.findAllMatches,
                                                            d = a.minMatchCharLength;
                                                        return r(e, this.pattern, this.patternAlphabet, {
                                                            location: s,
                                                            distance: c,
                                                            threshold: l,
                                                            findAllMatches: u,
                                                            minMatchCharLength: d,
                                                        });
                                                    },
                                                },
                                            ]) && i(t.prototype, n),
                                            e
                                        );
                                    })();
                                e.exports = s;
                            },
                            function(e, t) {
                                var n = /[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g;
                                e.exports = function(e, t) {
                                    var i = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : / +/g,
                                        o = new RegExp(t.replace(n, '\\$&').replace(i, '|')),
                                        r = e.match(o),
                                        a = !!r,
                                        s = [];
                                    if (a)
                                        for (var c = 0, l = r.length; c < l; c += 1) {
                                            var u = r[c];
                                            s.push([e.indexOf(u), u.length - 1]);
                                        }
                                    return { score: a ? 0.5 : 1, isMatch: a, matchedIndices: s };
                                };
                            },
                            function(e, t, n) {
                                var i = n(5),
                                    o = n(6);
                                e.exports = function(e, t, n, r) {
                                    for (
                                        var a = r.location,
                                            s = void 0 === a ? 0 : a,
                                            c = r.distance,
                                            l = void 0 === c ? 100 : c,
                                            u = r.threshold,
                                            d = void 0 === u ? 0.6 : u,
                                            h = r.findAllMatches,
                                            f = void 0 !== h && h,
                                            p = r.minMatchCharLength,
                                            m = void 0 === p ? 1 : p,
                                            v = s,
                                            g = e.length,
                                            y = d,
                                            b = e.indexOf(t, v),
                                            w = t.length,
                                            E = [],
                                            _ = 0;
                                        _ < g;
                                        _ += 1
                                    )
                                        E[_] = 0;
                                    if (-1 !== b) {
                                        var C = i(t, {
                                            errors: 0,
                                            currentLocation: b,
                                            expectedLocation: v,
                                            distance: l,
                                        });
                                        if (((y = Math.min(C, y)), -1 !== (b = e.lastIndexOf(t, v + w)))) {
                                            var S = i(t, {
                                                errors: 0,
                                                currentLocation: b,
                                                expectedLocation: v,
                                                distance: l,
                                            });
                                            y = Math.min(S, y);
                                        }
                                    }
                                    b = -1;
                                    for (var D = [], I = 1, T = w + g, L = 1 << (w - 1), M = 0; M < w; M += 1) {
                                        for (var O = 0, x = T; O < x; )
                                            i(t, {
                                                errors: M,
                                                currentLocation: v + x,
                                                expectedLocation: v,
                                                distance: l,
                                            }) <= y
                                                ? (O = x)
                                                : (T = x),
                                                (x = Math.floor((T - O) / 2 + O));
                                        T = x;
                                        var A = Math.max(1, v - x + 1),
                                            k = f ? g : Math.min(v + x, g) + w,
                                            P = Array(k + 2);
                                        P[k + 1] = (1 << M) - 1;
                                        for (var N = k; N >= A; N -= 1) {
                                            var F = N - 1,
                                                j = n[e.charAt(F)];
                                            if (
                                                (j && (E[F] = 1),
                                                (P[N] = ((P[N + 1] << 1) | 1) & j),
                                                0 !== M && (P[N] |= ((D[N + 1] | D[N]) << 1) | 1 | D[N + 1]),
                                                P[N] & L &&
                                                    (I = i(t, {
                                                        errors: M,
                                                        currentLocation: F,
                                                        expectedLocation: v,
                                                        distance: l,
                                                    })) <= y)
                                            ) {
                                                if (((y = I), (b = F) <= v)) break;
                                                A = Math.max(1, 2 * v - b);
                                            }
                                        }
                                        if (
                                            i(t, {
                                                errors: M + 1,
                                                currentLocation: v,
                                                expectedLocation: v,
                                                distance: l,
                                            }) > y
                                        )
                                            break;
                                        D = P;
                                    }
                                    return { isMatch: b >= 0, score: 0 === I ? 0.001 : I, matchedIndices: o(E, m) };
                                };
                            },
                            function(e, t) {
                                e.exports = function(e, t) {
                                    var n = t.errors,
                                        i = void 0 === n ? 0 : n,
                                        o = t.currentLocation,
                                        r = void 0 === o ? 0 : o,
                                        a = t.expectedLocation,
                                        s = void 0 === a ? 0 : a,
                                        c = t.distance,
                                        l = void 0 === c ? 100 : c,
                                        u = i / e.length,
                                        d = Math.abs(s - r);
                                    return l ? u + d / l : d ? 1 : u;
                                };
                            },
                            function(e, t) {
                                e.exports = function() {
                                    for (
                                        var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : [],
                                            t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : 1,
                                            n = [],
                                            i = -1,
                                            o = -1,
                                            r = 0,
                                            a = e.length;
                                        r < a;
                                        r += 1
                                    ) {
                                        var s = e[r];
                                        s && -1 === i
                                            ? (i = r)
                                            : s || -1 === i || ((o = r - 1) - i + 1 >= t && n.push([i, o]), (i = -1));
                                    }
                                    return e[r - 1] && r - i >= t && n.push([i, r - 1]), n;
                                };
                            },
                            function(e, t) {
                                e.exports = function(e) {
                                    for (var t = {}, n = e.length, i = 0; i < n; i += 1) t[e.charAt(i)] = 0;
                                    for (var o = 0; o < n; o += 1) t[e.charAt(o)] |= 1 << (n - o - 1);
                                    return t;
                                };
                            },
                            function(e, t, n) {
                                var i = n(0);
                                e.exports = function(e, t) {
                                    return (function e(t, n, o) {
                                        if (n) {
                                            var r = n.indexOf('.'),
                                                a = n,
                                                s = null;
                                            -1 !== r && ((a = n.slice(0, r)), (s = n.slice(r + 1)));
                                            var c = t[a];
                                            if (null != c)
                                                if (s || ('string' != typeof c && 'number' != typeof c))
                                                    if (i(c))
                                                        for (var l = 0, u = c.length; l < u; l += 1) e(c[l], s, o);
                                                    else s && e(c, s, o);
                                                else o.push(c.toString());
                                        } else o.push(t);
                                        return o;
                                    })(e, t, []);
                                };
                            },
                        ]);
                    },
                    function(e, t, n) {
                        'use strict';
                        function i(e) {
                            var t,
                                n = e.Symbol;
                            return (
                                'function' == typeof n
                                    ? n.observable
                                        ? (t = n.observable)
                                        : ((t = n('observable')), (n.observable = t))
                                    : (t = '@@observable'),
                                t
                            );
                        }
                        n.d(t, 'a', function() {
                            return i;
                        });
                    },
                    function(e, t, n) {
                        e.exports = n(7);
                    },
                    function(e, t) {
                        var n;
                        n = (function() {
                            return this;
                        })();
                        try {
                            n = n || new Function('return this')();
                        } catch (e) {
                            'object' == typeof window && (n = window);
                        }
                        e.exports = n;
                    },
                    function(e, t) {
                        e.exports = function(e) {
                            if (!e.webpackPolyfill) {
                                var t = Object.create(e);
                                t.children || (t.children = []),
                                    Object.defineProperty(t, 'loaded', {
                                        enumerable: !0,
                                        get: function() {
                                            return t.l;
                                        },
                                    }),
                                    Object.defineProperty(t, 'id', {
                                        enumerable: !0,
                                        get: function() {
                                            return t.i;
                                        },
                                    }),
                                    Object.defineProperty(t, 'exports', { enumerable: !0 }),
                                    (t.webpackPolyfill = 1);
                            }
                            return t;
                        };
                    },
                    function(e, t, n) {
                        'use strict';
                        n.r(t);
                        var i = n(2),
                            o = n.n(i),
                            r = n(0),
                            a = n.n(r),
                            s = n(1),
                            c = function() {
                                return Math.random()
                                    .toString(36)
                                    .substring(7)
                                    .split('')
                                    .join('.');
                            },
                            l = {
                                INIT: '@@redux/INIT' + c(),
                                REPLACE: '@@redux/REPLACE' + c(),
                                PROBE_UNKNOWN_ACTION: function() {
                                    return '@@redux/PROBE_UNKNOWN_ACTION' + c();
                                },
                            };
                        function u(e) {
                            if ('object' != typeof e || null === e) return !1;
                            for (var t = e; null !== Object.getPrototypeOf(t); ) t = Object.getPrototypeOf(t);
                            return Object.getPrototypeOf(e) === t;
                        }
                        function d(e, t, n) {
                            var i;
                            if (
                                ('function' == typeof t && 'function' == typeof n) ||
                                ('function' == typeof n && 'function' == typeof arguments[3])
                            )
                                throw new Error(
                                    'It looks like you are passing several store enhancers to createStore(). This is not supported. Instead, compose them together to a single function.'
                                );
                            if (('function' == typeof t && void 0 === n && ((n = t), (t = void 0)), void 0 !== n)) {
                                if ('function' != typeof n) throw new Error('Expected the enhancer to be a function.');
                                return n(d)(e, t);
                            }
                            if ('function' != typeof e) throw new Error('Expected the reducer to be a function.');
                            var o = e,
                                r = t,
                                a = [],
                                c = a,
                                h = !1;
                            function f() {
                                c === a && (c = a.slice());
                            }
                            function p() {
                                if (h)
                                    throw new Error(
                                        'You may not call store.getState() while the reducer is executing. The reducer has already received the state as an argument. Pass it down from the top reducer instead of reading it from the store.'
                                    );
                                return r;
                            }
                            function m(e) {
                                if ('function' != typeof e) throw new Error('Expected the listener to be a function.');
                                if (h)
                                    throw new Error(
                                        'You may not call store.subscribe() while the reducer is executing. If you would like to be notified after the store has been updated, subscribe from a component and invoke store.getState() in the callback to access the latest state. See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
                                    );
                                var t = !0;
                                return (
                                    f(),
                                    c.push(e),
                                    function() {
                                        if (t) {
                                            if (h)
                                                throw new Error(
                                                    'You may not unsubscribe from a store listener while the reducer is executing. See https://redux.js.org/api-reference/store#subscribe(listener) for more details.'
                                                );
                                            (t = !1), f();
                                            var n = c.indexOf(e);
                                            c.splice(n, 1);
                                        }
                                    }
                                );
                            }
                            function v(e) {
                                if (!u(e))
                                    throw new Error(
                                        'Actions must be plain objects. Use custom middleware for async actions.'
                                    );
                                if (void 0 === e.type)
                                    throw new Error(
                                        'Actions may not have an undefined "type" property. Have you misspelled a constant?'
                                    );
                                if (h) throw new Error('Reducers may not dispatch actions.');
                                try {
                                    (h = !0), (r = o(r, e));
                                } finally {
                                    h = !1;
                                }
                                for (var t = (a = c), n = 0; n < t.length; n++) (0, t[n])();
                                return e;
                            }
                            return (
                                v({ type: l.INIT }),
                                ((i = {
                                    dispatch: v,
                                    subscribe: m,
                                    getState: p,
                                    replaceReducer: function(e) {
                                        if ('function' != typeof e)
                                            throw new Error('Expected the nextReducer to be a function.');
                                        (o = e), v({ type: l.REPLACE });
                                    },
                                })[s.a] = function() {
                                    var e,
                                        t = m;
                                    return (
                                        ((e = {
                                            subscribe: function(e) {
                                                if ('object' != typeof e || null === e)
                                                    throw new TypeError('Expected the observer to be an object.');
                                                function n() {
                                                    e.next && e.next(p());
                                                }
                                                return n(), { unsubscribe: t(n) };
                                            },
                                        })[s.a] = function() {
                                            return this;
                                        }),
                                        e
                                    );
                                }),
                                i
                            );
                        }
                        function h(e, t) {
                            var n = t && t.type;
                            return (
                                'Given ' +
                                ((n && 'action "' + String(n) + '"') || 'an action') +
                                ', reducer "' +
                                e +
                                '" returned undefined. To ignore an action, you must explicitly return the previous state. If you want this reducer to hold no value, you can return null instead of undefined.'
                            );
                        }
                        var f,
                            p = [],
                            m = [],
                            v = [],
                            g = { loading: !1 },
                            y = function(e, t) {
                                switch ((void 0 === e && (e = g), t.type)) {
                                    case 'SET_IS_LOADING':
                                        return { loading: t.isLoading };
                                    default:
                                        return e;
                                }
                            },
                            b = function(e) {
                                return Array.from({ length: e }, function() {
                                    return ((e = 0), (t = 36), Math.floor(Math.random() * (t - e) + e)).toString(36);
                                    var e, t;
                                }).join('');
                            },
                            w = function(e, t) {
                                var n = e.id || (e.name && e.name + '-' + b(2)) || b(4);
                                return (n = t + '-' + (n = n.replace(/(:|\.|\[|\]|,)/g, '')));
                            },
                            E = function(e) {
                                return Object.prototype.toString.call(e).slice(8, -1);
                            },
                            _ = function(e, t) {
                                return null != t && E(t) === e;
                            },
                            C = function(e) {
                                return 'string' != typeof e
                                    ? e
                                    : e
                                          .replace(/&/g, '&amp;')
                                          .replace(/>/g, '&rt;')
                                          .replace(/</g, '&lt;')
                                          .replace(/"/g, '&quot;');
                            },
                            S =
                                ((f = document.createElement('div')),
                                function(e) {
                                    var t = e.trim();
                                    f.innerHTML = t;
                                    for (var n = f.children[0]; f.firstChild; ) f.removeChild(f.firstChild);
                                    return n;
                                }),
                            D = function(e, t) {
                                return e.score - t.score;
                            },
                            I = function(e) {
                                return JSON.parse(JSON.stringify(e));
                            },
                            T = function(e, t) {
                                var n = Object.keys(e).sort(),
                                    i = Object.keys(t).sort();
                                return n.filter(function(e) {
                                    return i.indexOf(e) < 0;
                                });
                            },
                            L = (function(e) {
                                for (var t = Object.keys(e), n = {}, i = 0; i < t.length; i++) {
                                    var o = t[i];
                                    'function' == typeof e[o] && (n[o] = e[o]);
                                }
                                var r,
                                    a = Object.keys(n);
                                try {
                                    !(function(e) {
                                        Object.keys(e).forEach(function(t) {
                                            var n = e[t];
                                            if (void 0 === n(void 0, { type: l.INIT }))
                                                throw new Error(
                                                    'Reducer "' +
                                                        t +
                                                        '" returned undefined during initialization. If the state passed to the reducer is undefined, you must explicitly return the initial state. The initial state may not be undefined. If you don\'t want to set a value for this reducer, you can use null instead of undefined.'
                                                );
                                            if (void 0 === n(void 0, { type: l.PROBE_UNKNOWN_ACTION() }))
                                                throw new Error(
                                                    'Reducer "' +
                                                        t +
                                                        '" returned undefined when probed with a random type. Don\'t try to handle ' +
                                                        l.INIT +
                                                        ' or other actions in "redux/*" namespace. They are considered private. Instead, you must return the current state for any unknown actions, unless it is undefined, in which case you must return the initial state, regardless of the action type. The initial state may not be undefined, but can be null.'
                                                );
                                        });
                                    })(n);
                                } catch (e) {
                                    r = e;
                                }
                                return function(e, t) {
                                    if ((void 0 === e && (e = {}), r)) throw r;
                                    for (var i = !1, o = {}, s = 0; s < a.length; s++) {
                                        var c = a[s],
                                            l = n[c],
                                            u = e[c],
                                            d = l(u, t);
                                        if (void 0 === d) {
                                            var f = h(c, t);
                                            throw new Error(f);
                                        }
                                        (o[c] = d), (i = i || d !== u);
                                    }
                                    return i ? o : e;
                                };
                            })({
                                items: function(e, t) {
                                    switch ((void 0 === e && (e = p), t.type)) {
                                        case 'ADD_ITEM':
                                            return []
                                                .concat(e, [
                                                    {
                                                        id: t.id,
                                                        choiceId: t.choiceId,
                                                        groupId: t.groupId,
                                                        value: t.value,
                                                        label: t.label,
                                                        active: !0,
                                                        highlighted: !1,
                                                        customProperties: t.customProperties,
                                                        placeholder: t.placeholder || !1,
                                                        keyCode: null,
                                                    },
                                                ])
                                                .map(function(e) {
                                                    var t = e;
                                                    return (t.highlighted = !1), t;
                                                });
                                        case 'REMOVE_ITEM':
                                            return e.map(function(e) {
                                                var n = e;
                                                return n.id === t.id && (n.active = !1), n;
                                            });
                                        case 'HIGHLIGHT_ITEM':
                                            return e.map(function(e) {
                                                var n = e;
                                                return n.id === t.id && (n.highlighted = t.highlighted), n;
                                            });
                                        default:
                                            return e;
                                    }
                                },
                                groups: function(e, t) {
                                    switch ((void 0 === e && (e = m), t.type)) {
                                        case 'ADD_GROUP':
                                            return [].concat(e, [
                                                { id: t.id, value: t.value, active: t.active, disabled: t.disabled },
                                            ]);
                                        case 'CLEAR_CHOICES':
                                            return [];
                                        default:
                                            return e;
                                    }
                                },
                                choices: function(e, t) {
                                    switch ((void 0 === e && (e = v), t.type)) {
                                        case 'ADD_CHOICE':
                                            return [].concat(e, [
                                                {
                                                    id: t.id,
                                                    elementId: t.elementId,
                                                    groupId: t.groupId,
                                                    value: t.value,
                                                    label: t.label || t.value,
                                                    disabled: t.disabled || !1,
                                                    selected: !1,
                                                    active: !0,
                                                    score: 9999,
                                                    customProperties: t.customProperties,
                                                    placeholder: t.placeholder || !1,
                                                    keyCode: null,
                                                },
                                            ]);
                                        case 'ADD_ITEM':
                                            return t.activateOptions
                                                ? e.map(function(e) {
                                                      var n = e;
                                                      return (n.active = t.active), n;
                                                  })
                                                : t.choiceId > -1
                                                ? e.map(function(e) {
                                                      var n = e;
                                                      return n.id === parseInt(t.choiceId, 10) && (n.selected = !0), n;
                                                  })
                                                : e;
                                        case 'REMOVE_ITEM':
                                            return t.choiceId > -1
                                                ? e.map(function(e) {
                                                      var n = e;
                                                      return n.id === parseInt(t.choiceId, 10) && (n.selected = !1), n;
                                                  })
                                                : e;
                                        case 'FILTER_CHOICES':
                                            return e.map(function(e) {
                                                var n = e;
                                                return (
                                                    (n.active = t.results.some(function(e) {
                                                        var t = e.item,
                                                            i = e.score;
                                                        return t.id === n.id && ((n.score = i), !0);
                                                    })),
                                                    n
                                                );
                                            });
                                        case 'ACTIVATE_CHOICES':
                                            return e.map(function(e) {
                                                var n = e;
                                                return (n.active = t.active), n;
                                            });
                                        case 'CLEAR_CHOICES':
                                            return v;
                                        default:
                                            return e;
                                    }
                                },
                                general: y,
                            }),
                            M = function(e, t) {
                                var n = e;
                                if ('CLEAR_ALL' === t.type) n = void 0;
                                else if ('RESET_TO' === t.type) return I(t.state);
                                return L(n, t);
                            };
                        function O(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var x = (function() {
                            function e() {
                                this._store = d(
                                    M,
                                    window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
                                );
                            }
                            var t,
                                n,
                                i,
                                o = e.prototype;
                            return (
                                (o.subscribe = function(e) {
                                    this._store.subscribe(e);
                                }),
                                (o.dispatch = function(e) {
                                    this._store.dispatch(e);
                                }),
                                (o.isLoading = function() {
                                    return this.state.general.loading;
                                }),
                                (o.getChoiceById = function(e) {
                                    return this.activeChoices.find(function(t) {
                                        return t.id === parseInt(e, 10);
                                    });
                                }),
                                (o.getGroupById = function(e) {
                                    return this.groups.find(function(t) {
                                        return t.id === e;
                                    });
                                }),
                                (t = e),
                                (n = [
                                    {
                                        key: 'state',
                                        get: function() {
                                            return this._store.getState();
                                        },
                                    },
                                    {
                                        key: 'items',
                                        get: function() {
                                            return this.state.items;
                                        },
                                    },
                                    {
                                        key: 'activeItems',
                                        get: function() {
                                            return this.items.filter(function(e) {
                                                return !0 === e.active;
                                            });
                                        },
                                    },
                                    {
                                        key: 'highlightedActiveItems',
                                        get: function() {
                                            return this.items.filter(function(e) {
                                                return e.active && e.highlighted;
                                            });
                                        },
                                    },
                                    {
                                        key: 'choices',
                                        get: function() {
                                            return this.state.choices;
                                        },
                                    },
                                    {
                                        key: 'activeChoices',
                                        get: function() {
                                            return this.choices.filter(function(e) {
                                                return !0 === e.active;
                                            });
                                        },
                                    },
                                    {
                                        key: 'selectableChoices',
                                        get: function() {
                                            return this.choices.filter(function(e) {
                                                return !0 !== e.disabled;
                                            });
                                        },
                                    },
                                    {
                                        key: 'searchableChoices',
                                        get: function() {
                                            return this.selectableChoices.filter(function(e) {
                                                return !0 !== e.placeholder;
                                            });
                                        },
                                    },
                                    {
                                        key: 'placeholderChoice',
                                        get: function() {
                                            return []
                                                .concat(this.choices)
                                                .reverse()
                                                .find(function(e) {
                                                    return !0 === e.placeholder;
                                                });
                                        },
                                    },
                                    {
                                        key: 'groups',
                                        get: function() {
                                            return this.state.groups;
                                        },
                                    },
                                    {
                                        key: 'activeGroups',
                                        get: function() {
                                            var e = this.groups,
                                                t = this.choices;
                                            return e.filter(function(e) {
                                                var n = !0 === e.active && !1 === e.disabled,
                                                    i = t.some(function(e) {
                                                        return !0 === e.active && !1 === e.disabled;
                                                    });
                                                return n && i;
                                            }, []);
                                        },
                                    },
                                ]) && O(t.prototype, n),
                                i && O(t, i),
                                e
                            );
                        })();
                        function A(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var k = (function() {
                                function e(e) {
                                    var t = e.element,
                                        n = e.type,
                                        i = e.classNames;
                                    (this.element = t), (this.classNames = i), (this.type = n), (this.isActive = !1);
                                }
                                var t,
                                    n,
                                    i,
                                    o = e.prototype;
                                return (
                                    (o.getChild = function(e) {
                                        return this.element.querySelector(e);
                                    }),
                                    (o.show = function() {
                                        return (
                                            this.element.classList.add(this.classNames.activeState),
                                            this.element.setAttribute('aria-expanded', 'true'),
                                            (this.isActive = !0),
                                            this
                                        );
                                    }),
                                    (o.hide = function() {
                                        return (
                                            this.element.classList.remove(this.classNames.activeState),
                                            this.element.setAttribute('aria-expanded', 'false'),
                                            (this.isActive = !1),
                                            this
                                        );
                                    }),
                                    (t = e),
                                    (n = [
                                        {
                                            key: 'distanceFromTopWindow',
                                            get: function() {
                                                return this.element.getBoundingClientRect().bottom;
                                            },
                                        },
                                    ]) && A(t.prototype, n),
                                    i && A(t, i),
                                    e
                                );
                            })(),
                            P = {
                                items: [],
                                choices: [],
                                silent: !1,
                                renderChoiceLimit: -1,
                                maxItemCount: -1,
                                addItems: !0,
                                addItemFilter: null,
                                removeItems: !0,
                                removeItemButton: !1,
                                editItems: !1,
                                duplicateItemsAllowed: !0,
                                delimiter: ',',
                                paste: !0,
                                searchEnabled: !0,
                                searchChoices: !0,
                                searchFloor: 1,
                                searchResultLimit: 4,
                                searchFields: ['label', 'value'],
                                position: 'auto',
                                resetScrollPosition: !0,
                                shouldSort: !0,
                                shouldSortItems: !1,
                                sorter: function(e, t) {
                                    var n = e.value,
                                        i = e.label,
                                        o = void 0 === i ? n : i,
                                        r = t.value,
                                        a = t.label,
                                        s = void 0 === a ? r : a;
                                    return o.localeCompare(s, [], {
                                        sensitivity: 'base',
                                        ignorePunctuation: !0,
                                        numeric: !0,
                                    });
                                },
                                placeholder: !0,
                                placeholderValue: null,
                                searchPlaceholderValue: null,
                                prependValue: null,
                                appendValue: null,
                                renderSelectedChoices: 'auto',
                                loadingText: 'Loading...',
                                noResultsText: 'No results found',
                                noChoicesText: 'No choices to choose from',
                                itemSelectText: 'Press to select',
                                uniqueItemText: 'Only unique values can be added',
                                customAddItemText: 'Only values matching specific conditions can be added',
                                addItemText: function(e) {
                                    return 'Press Enter to add <b>"' + C(e) + '"</b>';
                                },
                                maxItemText: function(e) {
                                    return 'Only ' + e + ' values can be added';
                                },
                                valueComparer: function(e, t) {
                                    return e === t;
                                },
                                fuseOptions: { includeScore: !0 },
                                callbackOnInit: null,
                                callbackOnCreateTemplates: null,
                                classNames: {
                                    containerOuter: 'choices',
                                    containerInner: 'choices__inner',
                                    input: 'choices__input',
                                    inputCloned: 'choices__input--cloned',
                                    list: 'choices__list',
                                    listItems: 'choices__list--multiple',
                                    listSingle: 'choices__list--single',
                                    listDropdown: 'choices__list--dropdown',
                                    item: 'choices__item',
                                    itemSelectable: 'choices__item--selectable',
                                    itemDisabled: 'choices__item--disabled',
                                    itemChoice: 'choices__item--choice',
                                    placeholder: 'choices__placeholder',
                                    group: 'choices__group',
                                    groupHeading: 'choices__heading',
                                    button: 'choices__button',
                                    activeState: 'is-active',
                                    focusState: 'is-focused',
                                    openState: 'is-open',
                                    disabledState: 'is-disabled',
                                    highlightedState: 'is-highlighted',
                                    selectedState: 'is-selected',
                                    flippedState: 'is-flipped',
                                    loadingState: 'is-loading',
                                    noResults: 'has-no-results',
                                    noChoices: 'has-no-choices',
                                },
                            },
                            N = 'showDropdown',
                            F = 'hideDropdown',
                            j = 'change',
                            R = 'choice',
                            H = 'search',
                            B = 'addItem',
                            q = 'removeItem',
                            V = 'highlightItem',
                            Y = 'highlightChoice',
                            K = 'ADD_CHOICE',
                            U = 'FILTER_CHOICES',
                            W = 'ACTIVATE_CHOICES',
                            G = 'CLEAR_CHOICES',
                            z = 'ADD_GROUP',
                            J = 'ADD_ITEM',
                            X = 'REMOVE_ITEM',
                            $ = 'HIGHLIGHT_ITEM',
                            Z = 46,
                            Q = 8,
                            ee = 13,
                            te = 65,
                            ne = 27,
                            ie = 38,
                            oe = 40,
                            re = 33,
                            ae = 34,
                            se = 'text',
                            ce = 'select-one',
                            le = 'select-multiple',
                            ue = (function() {
                                function e(e) {
                                    var t = e.element,
                                        n = e.type,
                                        i = e.classNames,
                                        o = e.position;
                                    (this.element = t),
                                        (this.classNames = i),
                                        (this.type = n),
                                        (this.position = o),
                                        (this.isOpen = !1),
                                        (this.isFlipped = !1),
                                        (this.isFocussed = !1),
                                        (this.isDisabled = !1),
                                        (this.isLoading = !1),
                                        (this._onFocus = this._onFocus.bind(this)),
                                        (this._onBlur = this._onBlur.bind(this));
                                }
                                var t = e.prototype;
                                return (
                                    (t.addEventListeners = function() {
                                        this.element.addEventListener('focus', this._onFocus),
                                            this.element.addEventListener('blur', this._onBlur);
                                    }),
                                    (t.removeEventListeners = function() {
                                        this.element.removeEventListener('focus', this._onFocus),
                                            this.element.removeEventListener('blur', this._onBlur);
                                    }),
                                    (t.shouldFlip = function(e) {
                                        if ('number' != typeof e) return !1;
                                        var t = !1;
                                        return (
                                            'auto' === this.position
                                                ? (t = !window.matchMedia('(min-height: ' + (e + 1) + 'px)').matches)
                                                : 'top' === this.position && (t = !0),
                                            t
                                        );
                                    }),
                                    (t.setActiveDescendant = function(e) {
                                        this.element.setAttribute('aria-activedescendant', e);
                                    }),
                                    (t.removeActiveDescendant = function() {
                                        this.element.removeAttribute('aria-activedescendant');
                                    }),
                                    (t.open = function(e) {
                                        this.element.classList.add(this.classNames.openState),
                                            this.element.setAttribute('aria-expanded', 'true'),
                                            (this.isOpen = !0),
                                            this.shouldFlip(e) &&
                                                (this.element.classList.add(this.classNames.flippedState),
                                                (this.isFlipped = !0));
                                    }),
                                    (t.close = function() {
                                        this.element.classList.remove(this.classNames.openState),
                                            this.element.setAttribute('aria-expanded', 'false'),
                                            this.removeActiveDescendant(),
                                            (this.isOpen = !1),
                                            this.isFlipped &&
                                                (this.element.classList.remove(this.classNames.flippedState),
                                                (this.isFlipped = !1));
                                    }),
                                    (t.focus = function() {
                                        this.isFocussed || this.element.focus();
                                    }),
                                    (t.addFocusState = function() {
                                        this.element.classList.add(this.classNames.focusState);
                                    }),
                                    (t.removeFocusState = function() {
                                        this.element.classList.remove(this.classNames.focusState);
                                    }),
                                    (t.enable = function() {
                                        this.element.classList.remove(this.classNames.disabledState),
                                            this.element.removeAttribute('aria-disabled'),
                                            this.type === ce && this.element.setAttribute('tabindex', '0'),
                                            (this.isDisabled = !1);
                                    }),
                                    (t.disable = function() {
                                        this.element.classList.add(this.classNames.disabledState),
                                            this.element.setAttribute('aria-disabled', 'true'),
                                            this.type === ce && this.element.setAttribute('tabindex', '-1'),
                                            (this.isDisabled = !0);
                                    }),
                                    (t.wrap = function(e) {
                                        !(function(e, t) {
                                            void 0 === t && (t = document.createElement('div')),
                                                e.nextSibling
                                                    ? e.parentNode.insertBefore(t, e.nextSibling)
                                                    : e.parentNode.appendChild(t),
                                                t.appendChild(e);
                                        })(e, this.element);
                                    }),
                                    (t.unwrap = function(e) {
                                        this.element.parentNode.insertBefore(e, this.element),
                                            this.element.parentNode.removeChild(this.element);
                                    }),
                                    (t.addLoadingState = function() {
                                        this.element.classList.add(this.classNames.loadingState),
                                            this.element.setAttribute('aria-busy', 'true'),
                                            (this.isLoading = !0);
                                    }),
                                    (t.removeLoadingState = function() {
                                        this.element.classList.remove(this.classNames.loadingState),
                                            this.element.removeAttribute('aria-busy'),
                                            (this.isLoading = !1);
                                    }),
                                    (t._onFocus = function() {
                                        this.isFocussed = !0;
                                    }),
                                    (t._onBlur = function() {
                                        this.isFocussed = !1;
                                    }),
                                    e
                                );
                            })();
                        function de(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var he = (function() {
                                function e(e) {
                                    var t = e.element,
                                        n = e.type,
                                        i = e.classNames,
                                        o = e.preventPaste;
                                    (this.element = t),
                                        (this.type = n),
                                        (this.classNames = i),
                                        (this.preventPaste = o),
                                        (this.isFocussed = this.element === document.activeElement),
                                        (this.isDisabled = t.disabled),
                                        (this._onPaste = this._onPaste.bind(this)),
                                        (this._onInput = this._onInput.bind(this)),
                                        (this._onFocus = this._onFocus.bind(this)),
                                        (this._onBlur = this._onBlur.bind(this));
                                }
                                var t,
                                    n,
                                    i,
                                    o = e.prototype;
                                return (
                                    (o.addEventListeners = function() {
                                        this.element.addEventListener('paste', this._onPaste),
                                            this.element.addEventListener('input', this._onInput, { passive: !0 }),
                                            this.element.addEventListener('focus', this._onFocus, { passive: !0 }),
                                            this.element.addEventListener('blur', this._onBlur, { passive: !0 });
                                    }),
                                    (o.removeEventListeners = function() {
                                        this.element.removeEventListener('input', this._onInput, { passive: !0 }),
                                            this.element.removeEventListener('paste', this._onPaste),
                                            this.element.removeEventListener('focus', this._onFocus, { passive: !0 }),
                                            this.element.removeEventListener('blur', this._onBlur, { passive: !0 });
                                    }),
                                    (o.enable = function() {
                                        this.element.removeAttribute('disabled'), (this.isDisabled = !1);
                                    }),
                                    (o.disable = function() {
                                        this.element.setAttribute('disabled', ''), (this.isDisabled = !0);
                                    }),
                                    (o.focus = function() {
                                        this.isFocussed || this.element.focus();
                                    }),
                                    (o.blur = function() {
                                        this.isFocussed && this.element.blur();
                                    }),
                                    (o.clear = function(e) {
                                        return (
                                            void 0 === e && (e = !0),
                                            this.element.value && (this.element.value = ''),
                                            e && this.setWidth(),
                                            this
                                        );
                                    }),
                                    (o.setWidth = function() {
                                        var e = this.element,
                                            t = e.style,
                                            n = e.value,
                                            i = e.placeholder;
                                        (t.minWidth = i.length + 1 + 'ch'), (t.width = n.length + 1 + 'ch');
                                    }),
                                    (o.setActiveDescendant = function(e) {
                                        this.element.setAttribute('aria-activedescendant', e);
                                    }),
                                    (o.removeActiveDescendant = function() {
                                        this.element.removeAttribute('aria-activedescendant');
                                    }),
                                    (o._onInput = function() {
                                        this.type !== ce && this.setWidth();
                                    }),
                                    (o._onPaste = function(e) {
                                        this.preventPaste && e.preventDefault();
                                    }),
                                    (o._onFocus = function() {
                                        this.isFocussed = !0;
                                    }),
                                    (o._onBlur = function() {
                                        this.isFocussed = !1;
                                    }),
                                    (t = e),
                                    (n = [
                                        {
                                            key: 'placeholder',
                                            set: function(e) {
                                                this.element.placeholder = e;
                                            },
                                        },
                                        {
                                            key: 'value',
                                            get: function() {
                                                return C(this.element.value);
                                            },
                                            set: function(e) {
                                                this.element.value = e;
                                            },
                                        },
                                    ]) && de(t.prototype, n),
                                    i && de(t, i),
                                    e
                                );
                            })(),
                            fe = (function() {
                                function e(e) {
                                    var t = e.element;
                                    (this.element = t),
                                        (this.scrollPos = this.element.scrollTop),
                                        (this.height = this.element.offsetHeight);
                                }
                                var t = e.prototype;
                                return (
                                    (t.clear = function() {
                                        this.element.innerHTML = '';
                                    }),
                                    (t.append = function(e) {
                                        this.element.appendChild(e);
                                    }),
                                    (t.getChild = function(e) {
                                        return this.element.querySelector(e);
                                    }),
                                    (t.hasChildren = function() {
                                        return this.element.hasChildNodes();
                                    }),
                                    (t.scrollToTop = function() {
                                        this.element.scrollTop = 0;
                                    }),
                                    (t.scrollToChildElement = function(e, t) {
                                        var n = this;
                                        if (e) {
                                            var i = this.element.offsetHeight,
                                                o = this.element.scrollTop + i,
                                                r = e.offsetHeight,
                                                a = e.offsetTop + r,
                                                s = t > 0 ? this.element.scrollTop + a - o : e.offsetTop;
                                            requestAnimationFrame(function() {
                                                n._animateScroll(s, t);
                                            });
                                        }
                                    }),
                                    (t._scrollDown = function(e, t, n) {
                                        var i = (n - e) / t,
                                            o = i > 1 ? i : 1;
                                        this.element.scrollTop = e + o;
                                    }),
                                    (t._scrollUp = function(e, t, n) {
                                        var i = (e - n) / t,
                                            o = i > 1 ? i : 1;
                                        this.element.scrollTop = e - o;
                                    }),
                                    (t._animateScroll = function(e, t) {
                                        var n = this,
                                            i = this.element.scrollTop,
                                            o = !1;
                                        t > 0
                                            ? (this._scrollDown(i, 4, e), i < e && (o = !0))
                                            : (this._scrollUp(i, 4, e), i > e && (o = !0)),
                                            o &&
                                                requestAnimationFrame(function() {
                                                    n._animateScroll(e, t);
                                                });
                                    }),
                                    e
                                );
                            })();
                        function pe(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var me = (function() {
                            function e(e) {
                                var t = e.element,
                                    n = e.classNames;
                                if (
                                    ((this.element = t),
                                    (this.classNames = n),
                                    !(t instanceof HTMLInputElement || t instanceof HTMLSelectElement))
                                )
                                    throw new TypeError('Invalid element passed');
                                this.isDisabled = !1;
                            }
                            var t,
                                n,
                                i,
                                o = e.prototype;
                            return (
                                (o.conceal = function() {
                                    this.element.classList.add(this.classNames.input),
                                        (this.element.hidden = !0),
                                        (this.element.tabIndex = -1);
                                    var e = this.element.getAttribute('style');
                                    e && this.element.setAttribute('data-choice-orig-style', e),
                                        this.element.setAttribute('data-choice', 'active');
                                }),
                                (o.reveal = function() {
                                    this.element.classList.remove(this.classNames.input),
                                        (this.element.hidden = !1),
                                        this.element.removeAttribute('tabindex');
                                    var e = this.element.getAttribute('data-choice-orig-style');
                                    e
                                        ? (this.element.removeAttribute('data-choice-orig-style'),
                                          this.element.setAttribute('style', e))
                                        : this.element.removeAttribute('style'),
                                        this.element.removeAttribute('data-choice'),
                                        (this.element.value = this.element.value);
                                }),
                                (o.enable = function() {
                                    this.element.removeAttribute('disabled'),
                                        (this.element.disabled = !1),
                                        (this.isDisabled = !1);
                                }),
                                (o.disable = function() {
                                    this.element.setAttribute('disabled', ''),
                                        (this.element.disabled = !0),
                                        (this.isDisabled = !0);
                                }),
                                (o.triggerEvent = function(e, t) {
                                    !(function(e, t, n) {
                                        void 0 === n && (n = null);
                                        var i = new CustomEvent(t, { detail: n, bubbles: !0, cancelable: !0 });
                                        e.dispatchEvent(i);
                                    })(this.element, e, t);
                                }),
                                (t = e),
                                (n = [
                                    {
                                        key: 'isActive',
                                        get: function() {
                                            return 'active' === this.element.dataset.choice;
                                        },
                                    },
                                    {
                                        key: 'dir',
                                        get: function() {
                                            return this.element.dir;
                                        },
                                    },
                                    {
                                        key: 'value',
                                        get: function() {
                                            return this.element.value;
                                        },
                                        set: function(e) {
                                            this.element.value = e;
                                        },
                                    },
                                ]) && pe(t.prototype, n),
                                i && pe(t, i),
                                e
                            );
                        })();
                        function ve(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var ge = (function(e) {
                            var t, n, i, o, r;
                            function a(t) {
                                var n,
                                    i = t.element,
                                    o = t.classNames,
                                    r = t.delimiter;
                                return ((n = e.call(this, { element: i, classNames: o }) || this).delimiter = r), n;
                            }
                            return (
                                (n = e),
                                ((t = a).prototype = Object.create(n.prototype)),
                                (t.prototype.constructor = t),
                                (t.__proto__ = n),
                                (i = a),
                                (o = [
                                    {
                                        key: 'value',
                                        get: function() {
                                            return this.element.value;
                                        },
                                        set: function(e) {
                                            var t = e
                                                .map(function(e) {
                                                    return e.value;
                                                })
                                                .join(this.delimiter);
                                            this.element.setAttribute('value', t), (this.element.value = t);
                                        },
                                    },
                                ]) && ve(i.prototype, o),
                                r && ve(i, r),
                                a
                            );
                        })(me);
                        function ye(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var be = (function(e) {
                                var t, n, i, o, r;
                                function a(t) {
                                    var n,
                                        i = t.element,
                                        o = t.classNames,
                                        r = t.template;
                                    return ((n = e.call(this, { element: i, classNames: o }) || this).template = r), n;
                                }
                                return (
                                    (n = e),
                                    ((t = a).prototype = Object.create(n.prototype)),
                                    (t.prototype.constructor = t),
                                    (t.__proto__ = n),
                                    (a.prototype.appendDocFragment = function(e) {
                                        (this.element.innerHTML = ''), this.element.appendChild(e);
                                    }),
                                    (i = a),
                                    (o = [
                                        {
                                            key: 'placeholderOption',
                                            get: function() {
                                                return (
                                                    this.element.querySelector('option[value=""]') ||
                                                    this.element.querySelector('option[placeholder]')
                                                );
                                            },
                                        },
                                        {
                                            key: 'optionGroups',
                                            get: function() {
                                                return Array.from(this.element.getElementsByTagName('OPTGROUP'));
                                            },
                                        },
                                        {
                                            key: 'options',
                                            get: function() {
                                                return Array.from(this.element.options);
                                            },
                                            set: function(e) {
                                                var t = this,
                                                    n = document.createDocumentFragment();
                                                e.forEach(function(e) {
                                                    return (i = e), (o = t.template(i)), void n.appendChild(o);
                                                    var i, o;
                                                }),
                                                    this.appendDocFragment(n);
                                            },
                                        },
                                    ]) && ye(i.prototype, o),
                                    r && ye(i, r),
                                    a
                                );
                            })(me),
                            we = {
                                containerOuter: function(e, t, n, i, o, r) {
                                    var a = e.containerOuter,
                                        s = Object.assign(document.createElement('div'), { className: a });
                                    return (
                                        (s.dataset.type = r),
                                        t && (s.dir = t),
                                        i && (s.tabIndex = 0),
                                        n &&
                                            (s.setAttribute('role', o ? 'combobox' : 'listbox'),
                                            o && s.setAttribute('aria-autocomplete', 'list')),
                                        s.setAttribute('aria-haspopup', 'true'),
                                        s.setAttribute('aria-expanded', 'false'),
                                        s
                                    );
                                },
                                containerInner: function(e) {
                                    var t = e.containerInner;
                                    return Object.assign(document.createElement('div'), { className: t });
                                },
                                itemList: function(e, t) {
                                    var n = e.list,
                                        i = e.listSingle,
                                        o = e.listItems;
                                    return Object.assign(document.createElement('div'), {
                                        className: n + ' ' + (t ? i : o),
                                    });
                                },
                                placeholder: function(e, t) {
                                    var n = e.placeholder;
                                    return Object.assign(document.createElement('div'), { className: n, innerHTML: t });
                                },
                                item: function(e, t, n) {
                                    var i = e.item,
                                        o = e.button,
                                        r = e.highlightedState,
                                        a = e.itemSelectable,
                                        s = e.placeholder,
                                        c = t.id,
                                        l = t.value,
                                        u = t.label,
                                        d = t.customProperties,
                                        h = t.active,
                                        f = t.disabled,
                                        p = t.highlighted,
                                        m = t.placeholder,
                                        v = Object.assign(document.createElement('div'), {
                                            className: i,
                                            innerHTML: u,
                                        });
                                    if (
                                        (Object.assign(v.dataset, { item: '', id: c, value: l, customProperties: d }),
                                        h && v.setAttribute('aria-selected', 'true'),
                                        f && v.setAttribute('aria-disabled', 'true'),
                                        m && v.classList.add(s),
                                        v.classList.add(p ? r : a),
                                        n)
                                    ) {
                                        f && v.classList.remove(a), (v.dataset.deletable = '');
                                        var g = Object.assign(document.createElement('button'), {
                                            type: 'button',
                                            className: o,
                                            innerHTML: 'Remove item',
                                        });
                                        g.setAttribute('aria-label', "Remove item: '" + l + "'"),
                                            (g.dataset.button = ''),
                                            v.appendChild(g);
                                    }
                                    return v;
                                },
                                choiceList: function(e, t) {
                                    var n = e.list,
                                        i = Object.assign(document.createElement('div'), { className: n });
                                    return (
                                        t || i.setAttribute('aria-multiselectable', 'true'),
                                        i.setAttribute('role', 'listbox'),
                                        i
                                    );
                                },
                                choiceGroup: function(e, t) {
                                    var n = e.group,
                                        i = e.groupHeading,
                                        o = e.itemDisabled,
                                        r = t.id,
                                        a = t.value,
                                        s = t.disabled,
                                        c = Object.assign(document.createElement('div'), {
                                            className: n + ' ' + (s ? o : ''),
                                        });
                                    return (
                                        c.setAttribute('role', 'group'),
                                        Object.assign(c.dataset, { group: '', id: r, value: a }),
                                        s && c.setAttribute('aria-disabled', 'true'),
                                        c.appendChild(
                                            Object.assign(document.createElement('div'), { className: i, innerHTML: a })
                                        ),
                                        c
                                    );
                                },
                                choice: function(e, t, n) {
                                    var i = e.item,
                                        o = e.itemChoice,
                                        r = e.itemSelectable,
                                        a = e.selectedState,
                                        s = e.itemDisabled,
                                        c = e.placeholder,
                                        l = t.id,
                                        u = t.value,
                                        d = t.label,
                                        h = t.groupId,
                                        f = t.elementId,
                                        p = t.disabled,
                                        m = t.selected,
                                        v = t.placeholder,
                                        g = Object.assign(document.createElement('div'), {
                                            id: f,
                                            innerHTML: d,
                                            className: i + ' ' + o,
                                        });
                                    return (
                                        m && g.classList.add(a),
                                        v && g.classList.add(c),
                                        g.setAttribute('role', h > 0 ? 'treeitem' : 'option'),
                                        Object.assign(g.dataset, { choice: '', id: l, value: u, selectText: n }),
                                        p
                                            ? (g.classList.add(s),
                                              (g.dataset.choiceDisabled = ''),
                                              g.setAttribute('aria-disabled', 'true'))
                                            : (g.classList.add(r), (g.dataset.choiceSelectable = '')),
                                        g
                                    );
                                },
                                input: function(e, t) {
                                    var n = e.input,
                                        i = e.inputCloned,
                                        o = Object.assign(document.createElement('input'), {
                                            type: 'text',
                                            className: n + ' ' + i,
                                            autocomplete: 'off',
                                            autocapitalize: 'off',
                                            spellcheck: !1,
                                        });
                                    return (
                                        o.setAttribute('role', 'textbox'),
                                        o.setAttribute('aria-autocomplete', 'list'),
                                        o.setAttribute('aria-label', t),
                                        o
                                    );
                                },
                                dropdown: function(e) {
                                    var t = e.list,
                                        n = e.listDropdown,
                                        i = document.createElement('div');
                                    return i.classList.add(t, n), i.setAttribute('aria-expanded', 'false'), i;
                                },
                                notice: function(e, t, n) {
                                    var i = e.item,
                                        o = e.itemChoice,
                                        r = e.noResults,
                                        a = e.noChoices;
                                    void 0 === n && (n = '');
                                    var s = [i, o];
                                    return (
                                        'no-choices' === n ? s.push(a) : 'no-results' === n && s.push(r),
                                        Object.assign(document.createElement('div'), {
                                            innerHTML: t,
                                            className: s.join(' '),
                                        })
                                    );
                                },
                                option: function(e) {
                                    var t = e.label,
                                        n = e.value,
                                        i = e.customProperties,
                                        o = e.active,
                                        r = e.disabled,
                                        a = new Option(t, n, !1, o);
                                    return i && (a.dataset.customProperties = i), (a.disabled = r), a;
                                },
                            },
                            Ee = function(e) {
                                return void 0 === e && (e = !0), { type: W, active: e };
                            },
                            _e = function(e, t) {
                                return { type: $, id: e, highlighted: t };
                            },
                            Ce = function(e) {
                                var t = e.value,
                                    n = e.id,
                                    i = e.active,
                                    o = e.disabled;
                                return { type: z, value: t, id: n, active: i, disabled: o };
                            },
                            Se = function(e) {
                                return { type: 'SET_IS_LOADING', isLoading: e };
                            };
                        function De(e, t) {
                            for (var n = 0; n < t.length; n++) {
                                var i = t[n];
                                (i.enumerable = i.enumerable || !1),
                                    (i.configurable = !0),
                                    'value' in i && (i.writable = !0),
                                    Object.defineProperty(e, i.key, i);
                            }
                        }
                        var Ie =
                                '-ms-scroll-limit' in document.documentElement.style &&
                                '-ms-ime-align' in document.documentElement.style,
                            Te = {},
                            Le = (function() {
                                var e, t, n;
                                function i(e, t) {
                                    var n = this;
                                    void 0 === e && (e = '[data-choice]'),
                                        void 0 === t && (t = {}),
                                        (this.config = a.a.all([P, i.defaults.options, t], {
                                            arrayMerge: function(e, t) {
                                                return [].concat(t);
                                            },
                                        }));
                                    var o = T(this.config, P);
                                    o.length && console.warn('Unknown config option(s) passed', o.join(', '));
                                    var r = 'string' == typeof e ? document.querySelector(e) : e;
                                    if (!(r instanceof HTMLInputElement || r instanceof HTMLSelectElement))
                                        throw TypeError(
                                            'Expected one of the following types text|select-one|select-multiple'
                                        );
                                    if (
                                        ((this._isTextElement = r.type === se),
                                        (this._isSelectOneElement = r.type === ce),
                                        (this._isSelectMultipleElement = r.type === le),
                                        (this._isSelectElement =
                                            this._isSelectOneElement || this._isSelectMultipleElement),
                                        (this.config.searchEnabled =
                                            this._isSelectMultipleElement || this.config.searchEnabled),
                                        ['auto', 'always'].includes(this.config.renderSelectedChoices) ||
                                            (this.config.renderSelectedChoices = 'auto'),
                                        t.addItemFilter && 'function' != typeof t.addItemFilter)
                                    ) {
                                        var s =
                                            t.addItemFilter instanceof RegExp
                                                ? t.addItemFilter
                                                : new RegExp(t.addItemFilter);
                                        this.config.addItemFilter = s.test.bind(s);
                                    }
                                    if (
                                        (this._isTextElement
                                            ? (this.passedElement = new ge({
                                                  element: r,
                                                  classNames: this.config.classNames,
                                                  delimiter: this.config.delimiter,
                                              }))
                                            : (this.passedElement = new be({
                                                  element: r,
                                                  classNames: this.config.classNames,
                                                  template: function(e) {
                                                      return n._templates.option(e);
                                                  },
                                              })),
                                        (this.initialised = !1),
                                        (this._store = new x()),
                                        (this._initialState = {}),
                                        (this._currentState = {}),
                                        (this._prevState = {}),
                                        (this._currentValue = ''),
                                        (this._canSearch = this.config.searchEnabled),
                                        (this._isScrollingOnIe = !1),
                                        (this._highlightPosition = 0),
                                        (this._wasTap = !0),
                                        (this._placeholderValue = this._generatePlaceholderValue()),
                                        (this._baseId = w(this.passedElement.element, 'choices-')),
                                        (this._direction = this.passedElement.dir),
                                        !this._direction)
                                    ) {
                                        var c = window.getComputedStyle(this.passedElement.element).direction;
                                        c !== window.getComputedStyle(document.documentElement).direction &&
                                            (this._direction = c);
                                    }
                                    if (
                                        ((this._idNames = { itemChoice: 'item-choice' }),
                                        (this._presetGroups = this.passedElement.optionGroups),
                                        (this._presetOptions = this.passedElement.options),
                                        (this._presetChoices = this.config.choices),
                                        (this._presetItems = this.config.items),
                                        this.passedElement.value &&
                                            (this._presetItems = this._presetItems.concat(
                                                this.passedElement.value.split(this.config.delimiter)
                                            )),
                                        this.passedElement.options &&
                                            this.passedElement.options.forEach(function(e) {
                                                n._presetChoices.push({
                                                    value: e.value,
                                                    label: e.innerHTML,
                                                    selected: e.selected,
                                                    disabled: e.disabled || e.parentNode.disabled,
                                                    placeholder: '' === e.value || e.hasAttribute('placeholder'),
                                                    customProperties: e.getAttribute('data-custom-properties'),
                                                });
                                            }),
                                        (this._render = this._render.bind(this)),
                                        (this._onFocus = this._onFocus.bind(this)),
                                        (this._onBlur = this._onBlur.bind(this)),
                                        (this._onKeyUp = this._onKeyUp.bind(this)),
                                        (this._onKeyDown = this._onKeyDown.bind(this)),
                                        (this._onClick = this._onClick.bind(this)),
                                        (this._onTouchMove = this._onTouchMove.bind(this)),
                                        (this._onTouchEnd = this._onTouchEnd.bind(this)),
                                        (this._onMouseDown = this._onMouseDown.bind(this)),
                                        (this._onMouseOver = this._onMouseOver.bind(this)),
                                        (this._onFormReset = this._onFormReset.bind(this)),
                                        (this._onAKey = this._onAKey.bind(this)),
                                        (this._onEnterKey = this._onEnterKey.bind(this)),
                                        (this._onEscapeKey = this._onEscapeKey.bind(this)),
                                        (this._onDirectionKey = this._onDirectionKey.bind(this)),
                                        (this._onDeleteKey = this._onDeleteKey.bind(this)),
                                        this.passedElement.isActive)
                                    )
                                        return (
                                            this.config.silent ||
                                                console.warn(
                                                    'Trying to initialise Choices on element already initialised'
                                                ),
                                            void (this.initialised = !0)
                                        );
                                    this.init();
                                }
                                (e = i),
                                    (n = [
                                        {
                                            key: 'defaults',
                                            get: function() {
                                                return Object.preventExtensions({
                                                    get options() {
                                                        return Te;
                                                    },
                                                    get templates() {
                                                        return we;
                                                    },
                                                });
                                            },
                                        },
                                    ]),
                                    (t = null) && De(e.prototype, t),
                                    n && De(e, n);
                                var r = i.prototype;
                                return (
                                    (r.init = function() {
                                        if (!this.initialised) {
                                            this._createTemplates(),
                                                this._createElements(),
                                                this._createStructure(),
                                                (this._initialState = I(this._store.state)),
                                                this._store.subscribe(this._render),
                                                this._render(),
                                                this._addEventListeners(),
                                                (!this.config.addItems ||
                                                    this.passedElement.element.hasAttribute('disabled')) &&
                                                    this.disable(),
                                                (this.initialised = !0);
                                            var e = this.config.callbackOnInit;
                                            e && 'function' == typeof e && e.call(this);
                                        }
                                    }),
                                    (r.destroy = function() {
                                        this.initialised &&
                                            (this._removeEventListeners(),
                                            this.passedElement.reveal(),
                                            this.containerOuter.unwrap(this.passedElement.element),
                                            this.clearStore(),
                                            this._isSelectElement && (this.passedElement.options = this._presetOptions),
                                            (this._templates = null),
                                            (this.initialised = !1));
                                    }),
                                    (r.enable = function() {
                                        return (
                                            this.passedElement.isDisabled && this.passedElement.enable(),
                                            this.containerOuter.isDisabled &&
                                                (this._addEventListeners(),
                                                this.input.enable(),
                                                this.containerOuter.enable()),
                                            this
                                        );
                                    }),
                                    (r.disable = function() {
                                        return (
                                            this.passedElement.isDisabled || this.passedElement.disable(),
                                            this.containerOuter.isDisabled ||
                                                (this._removeEventListeners(),
                                                this.input.disable(),
                                                this.containerOuter.disable()),
                                            this
                                        );
                                    }),
                                    (r.highlightItem = function(e, t) {
                                        if ((void 0 === t && (t = !0), !e)) return this;
                                        var n = e.id,
                                            i = e.groupId,
                                            o = void 0 === i ? -1 : i,
                                            r = e.value,
                                            a = void 0 === r ? '' : r,
                                            s = e.label,
                                            c = void 0 === s ? '' : s,
                                            l = o >= 0 ? this._store.getGroupById(o) : null;
                                        return (
                                            this._store.dispatch(_e(n, !0)),
                                            t &&
                                                this.passedElement.triggerEvent(V, {
                                                    id: n,
                                                    value: a,
                                                    label: c,
                                                    groupValue: l && l.value ? l.value : null,
                                                }),
                                            this
                                        );
                                    }),
                                    (r.unhighlightItem = function(e) {
                                        if (!e) return this;
                                        var t = e.id,
                                            n = e.groupId,
                                            i = void 0 === n ? -1 : n,
                                            o = e.value,
                                            r = void 0 === o ? '' : o,
                                            a = e.label,
                                            s = void 0 === a ? '' : a,
                                            c = i >= 0 ? this._store.getGroupById(i) : null;
                                        return (
                                            this._store.dispatch(_e(t, !1)),
                                            this.passedElement.triggerEvent(V, {
                                                id: t,
                                                value: r,
                                                label: s,
                                                groupValue: c && c.value ? c.value : null,
                                            }),
                                            this
                                        );
                                    }),
                                    (r.highlightAll = function() {
                                        var e = this;
                                        return (
                                            this._store.items.forEach(function(t) {
                                                return e.highlightItem(t);
                                            }),
                                            this
                                        );
                                    }),
                                    (r.unhighlightAll = function() {
                                        var e = this;
                                        return (
                                            this._store.items.forEach(function(t) {
                                                return e.unhighlightItem(t);
                                            }),
                                            this
                                        );
                                    }),
                                    (r.removeActiveItemsByValue = function(e) {
                                        var t = this;
                                        return (
                                            this._store.activeItems
                                                .filter(function(t) {
                                                    return t.value === e;
                                                })
                                                .forEach(function(e) {
                                                    return t._removeItem(e);
                                                }),
                                            this
                                        );
                                    }),
                                    (r.removeActiveItems = function(e) {
                                        var t = this;
                                        return (
                                            this._store.activeItems
                                                .filter(function(t) {
                                                    return t.id !== e;
                                                })
                                                .forEach(function(e) {
                                                    return t._removeItem(e);
                                                }),
                                            this
                                        );
                                    }),
                                    (r.removeHighlightedItems = function(e) {
                                        var t = this;
                                        return (
                                            void 0 === e && (e = !1),
                                            this._store.highlightedActiveItems.forEach(function(n) {
                                                t._removeItem(n), e && t._triggerChange(n.value);
                                            }),
                                            this
                                        );
                                    }),
                                    (r.showDropdown = function(e) {
                                        var t = this;
                                        return this.dropdown.isActive
                                            ? this
                                            : (requestAnimationFrame(function() {
                                                  t.dropdown.show(),
                                                      t.containerOuter.open(t.dropdown.distanceFromTopWindow),
                                                      !e && t._canSearch && t.input.focus(),
                                                      t.passedElement.triggerEvent(N, {});
                                              }),
                                              this);
                                    }),
                                    (r.hideDropdown = function(e) {
                                        var t = this;
                                        return this.dropdown.isActive
                                            ? (requestAnimationFrame(function() {
                                                  t.dropdown.hide(),
                                                      t.containerOuter.close(),
                                                      !e &&
                                                          t._canSearch &&
                                                          (t.input.removeActiveDescendant(), t.input.blur()),
                                                      t.passedElement.triggerEvent(F, {});
                                              }),
                                              this)
                                            : this;
                                    }),
                                    (r.getValue = function(e) {
                                        void 0 === e && (e = !1);
                                        var t = this._store.activeItems.reduce(function(t, n) {
                                            var i = e ? n.value : n;
                                            return t.push(i), t;
                                        }, []);
                                        return this._isSelectOneElement ? t[0] : t;
                                    }),
                                    (r.setValue = function(e) {
                                        var t = this;
                                        return this.initialised
                                            ? (e.forEach(function(e) {
                                                  return t._setChoiceOrItem(e);
                                              }),
                                              this)
                                            : this;
                                    }),
                                    (r.setChoiceByValue = function(e) {
                                        var t = this;
                                        return !this.initialised || this._isTextElement
                                            ? this
                                            : ((Array.isArray(e) ? e : [e]).forEach(function(e) {
                                                  return t._findAndSelectChoiceByValue(e);
                                              }),
                                              this);
                                    }),
                                    (r.setChoices = function(e, t, n, i) {
                                        var o = this;
                                        if (
                                            (void 0 === e && (e = []),
                                            void 0 === t && (t = 'value'),
                                            void 0 === n && (n = 'label'),
                                            void 0 === i && (i = !1),
                                            !this.initialised)
                                        )
                                            throw new ReferenceError(
                                                'setChoices was called on a non-initialized instance of Choices'
                                            );
                                        if (!this._isSelectElement)
                                            throw new TypeError("setChoices can't be used with INPUT based Choices");
                                        if ('string' != typeof t || !t)
                                            throw new TypeError(
                                                "value parameter must be a name of 'value' field in passed objects"
                                            );
                                        if ((i && this.clearChoices(), 'function' == typeof e)) {
                                            var r = e(this);
                                            if ('function' == typeof Promise && r instanceof Promise)
                                                return new Promise(function(e) {
                                                    return requestAnimationFrame(e);
                                                })
                                                    .then(function() {
                                                        return o._handleLoadingState(!0);
                                                    })
                                                    .then(function() {
                                                        return r;
                                                    })
                                                    .then(function(e) {
                                                        return o.setChoices(e, t, n, i);
                                                    })
                                                    .catch(function(e) {
                                                        o.config.silent || console.error(e);
                                                    })
                                                    .then(function() {
                                                        return o._handleLoadingState(!1);
                                                    })
                                                    .then(function() {
                                                        return o;
                                                    });
                                            if (!Array.isArray(r))
                                                throw new TypeError(
                                                    '.setChoices first argument function must return either array of choices or Promise, got: ' +
                                                        typeof r
                                                );
                                            return this.setChoices(r, t, n, !1);
                                        }
                                        if (!Array.isArray(e))
                                            throw new TypeError(
                                                '.setChoices must be called either with array of choices with a function resulting into Promise of array of choices'
                                            );
                                        return (
                                            this.containerOuter.removeLoadingState(),
                                            this._startLoading(),
                                            e.forEach(function(e) {
                                                e.choices
                                                    ? o._addGroup({
                                                          id: parseInt(e.id, 10) || null,
                                                          group: e,
                                                          valueKey: t,
                                                          labelKey: n,
                                                      })
                                                    : o._addChoice({
                                                          value: e[t],
                                                          label: e[n],
                                                          isSelected: e.selected,
                                                          isDisabled: e.disabled,
                                                          customProperties: e.customProperties,
                                                          placeholder: e.placeholder,
                                                      });
                                            }),
                                            this._stopLoading(),
                                            this
                                        );
                                    }),
                                    (r.clearChoices = function() {
                                        return this._store.dispatch({ type: G }), this;
                                    }),
                                    (r.clearStore = function() {
                                        return this._store.dispatch({ type: 'CLEAR_ALL' }), this;
                                    }),
                                    (r.clearInput = function() {
                                        var e = !this._isSelectOneElement;
                                        return (
                                            this.input.clear(e),
                                            !this._isTextElement &&
                                                this._canSearch &&
                                                ((this._isSearching = !1), this._store.dispatch(Ee(!0))),
                                            this
                                        );
                                    }),
                                    (r._render = function() {
                                        if (!this._store.isLoading()) {
                                            this._currentState = this._store.state;
                                            var e =
                                                    this._currentState.choices !== this._prevState.choices ||
                                                    this._currentState.groups !== this._prevState.groups ||
                                                    this._currentState.items !== this._prevState.items,
                                                t = this._isSelectElement,
                                                n = this._currentState.items !== this._prevState.items;
                                            e &&
                                                (t && this._renderChoices(),
                                                n && this._renderItems(),
                                                (this._prevState = this._currentState));
                                        }
                                    }),
                                    (r._renderChoices = function() {
                                        var e = this,
                                            t = this._store,
                                            n = t.activeGroups,
                                            i = t.activeChoices,
                                            o = document.createDocumentFragment();
                                        if (
                                            (this.choiceList.clear(),
                                            this.config.resetScrollPosition &&
                                                requestAnimationFrame(function() {
                                                    return e.choiceList.scrollToTop();
                                                }),
                                            n.length >= 1 && !this._isSearching)
                                        ) {
                                            var r = i.filter(function(e) {
                                                return !0 === e.placeholder && -1 === e.groupId;
                                            });
                                            r.length >= 1 && (o = this._createChoicesFragment(r, o)),
                                                (o = this._createGroupsFragment(n, i, o));
                                        } else i.length >= 1 && (o = this._createChoicesFragment(i, o));
                                        if (o.childNodes && o.childNodes.length > 0) {
                                            var a = this._store.activeItems,
                                                s = this._canAddItem(a, this.input.value);
                                            s.response
                                                ? (this.choiceList.append(o), this._highlightChoice())
                                                : this.choiceList.append(this._getTemplate('notice', s.notice));
                                        } else {
                                            var c, l;
                                            this._isSearching
                                                ? ((l =
                                                      'function' == typeof this.config.noResultsText
                                                          ? this.config.noResultsText()
                                                          : this.config.noResultsText),
                                                  (c = this._getTemplate('notice', l, 'no-results')))
                                                : ((l =
                                                      'function' == typeof this.config.noChoicesText
                                                          ? this.config.noChoicesText()
                                                          : this.config.noChoicesText),
                                                  (c = this._getTemplate('notice', l, 'no-choices'))),
                                                this.choiceList.append(c);
                                        }
                                    }),
                                    (r._renderItems = function() {
                                        var e = this._store.activeItems || [];
                                        this.itemList.clear();
                                        var t = this._createItemsFragment(e);
                                        t.childNodes && this.itemList.append(t);
                                    }),
                                    (r._createGroupsFragment = function(e, t, n) {
                                        var i = this;
                                        return (
                                            void 0 === n && (n = document.createDocumentFragment()),
                                            this.config.shouldSort && e.sort(this.config.sorter),
                                            e.forEach(function(e) {
                                                var o = (function(e) {
                                                    return t.filter(function(t) {
                                                        return i._isSelectOneElement
                                                            ? t.groupId === e.id
                                                            : t.groupId === e.id &&
                                                                  ('always' === i.config.renderSelectedChoices ||
                                                                      !t.selected);
                                                    });
                                                })(e);
                                                if (o.length >= 1) {
                                                    var r = i._getTemplate('choiceGroup', e);
                                                    n.appendChild(r), i._createChoicesFragment(o, n, !0);
                                                }
                                            }),
                                            n
                                        );
                                    }),
                                    (r._createChoicesFragment = function(e, t, n) {
                                        var i = this;
                                        void 0 === t && (t = document.createDocumentFragment()),
                                            void 0 === n && (n = !1);
                                        var o = this.config,
                                            r = o.renderSelectedChoices,
                                            a = o.searchResultLimit,
                                            s = o.renderChoiceLimit,
                                            c = this._isSearching ? D : this.config.sorter,
                                            l = function(e) {
                                                if ('auto' !== r || i._isSelectOneElement || !e.selected) {
                                                    var n = i._getTemplate('choice', e, i.config.itemSelectText);
                                                    t.appendChild(n);
                                                }
                                            },
                                            u = e;
                                        'auto' !== r ||
                                            this._isSelectOneElement ||
                                            (u = e.filter(function(e) {
                                                return !e.selected;
                                            }));
                                        var d = u.reduce(
                                                function(e, t) {
                                                    return (
                                                        t.placeholder
                                                            ? e.placeholderChoices.push(t)
                                                            : e.normalChoices.push(t),
                                                        e
                                                    );
                                                },
                                                { placeholderChoices: [], normalChoices: [] }
                                            ),
                                            h = d.placeholderChoices,
                                            f = d.normalChoices;
                                        (this.config.shouldSort || this._isSearching) && f.sort(c);
                                        var p = u.length,
                                            m = this._isSelectOneElement ? [].concat(h, f) : f;
                                        this._isSearching ? (p = a) : s && s > 0 && !n && (p = s);
                                        for (var v = 0; v < p; v += 1) m[v] && l(m[v]);
                                        return t;
                                    }),
                                    (r._createItemsFragment = function(e, t) {
                                        var n = this;
                                        void 0 === t && (t = document.createDocumentFragment());
                                        var i = this.config,
                                            o = i.shouldSortItems,
                                            r = i.sorter,
                                            a = i.removeItemButton;
                                        return (
                                            o && !this._isSelectOneElement && e.sort(r),
                                            this._isTextElement
                                                ? (this.passedElement.value = e)
                                                : (this.passedElement.options = e),
                                            e.forEach(function(e) {
                                                var i = n._getTemplate('item', e, a);
                                                t.appendChild(i);
                                            }),
                                            t
                                        );
                                    }),
                                    (r._triggerChange = function(e) {
                                        null != e && this.passedElement.triggerEvent(j, { value: e });
                                    }),
                                    (r._selectPlaceholderChoice = function() {
                                        var e = this._store.placeholderChoice;
                                        e &&
                                            (this._addItem({
                                                value: e.value,
                                                label: e.label,
                                                choiceId: e.id,
                                                groupId: e.groupId,
                                                placeholder: e.placeholder,
                                            }),
                                            this._triggerChange(e.value));
                                    }),
                                    (r._handleButtonAction = function(e, t) {
                                        if (e && t && this.config.removeItems && this.config.removeItemButton) {
                                            var n = t.parentNode.getAttribute('data-id'),
                                                i = e.find(function(e) {
                                                    return e.id === parseInt(n, 10);
                                                });
                                            this._removeItem(i),
                                                this._triggerChange(i.value),
                                                this._isSelectOneElement && this._selectPlaceholderChoice();
                                        }
                                    }),
                                    (r._handleItemAction = function(e, t, n) {
                                        var i = this;
                                        if (
                                            (void 0 === n && (n = !1),
                                            e && t && this.config.removeItems && !this._isSelectOneElement)
                                        ) {
                                            var o = t.getAttribute('data-id');
                                            e.forEach(function(e) {
                                                e.id !== parseInt(o, 10) || e.highlighted
                                                    ? !n && e.highlighted && i.unhighlightItem(e)
                                                    : i.highlightItem(e);
                                            }),
                                                this.input.focus();
                                        }
                                    }),
                                    (r._handleChoiceAction = function(e, t) {
                                        if (e && t) {
                                            var n = t.dataset.id,
                                                i = this._store.getChoiceById(n);
                                            if (i) {
                                                var o = e[0] && e[0].keyCode ? e[0].keyCode : null,
                                                    r = this.dropdown.isActive;
                                                (i.keyCode = o),
                                                    this.passedElement.triggerEvent(R, { choice: i }),
                                                    i.selected ||
                                                        i.disabled ||
                                                        (this._canAddItem(e, i.value).response &&
                                                            (this._addItem({
                                                                value: i.value,
                                                                label: i.label,
                                                                choiceId: i.id,
                                                                groupId: i.groupId,
                                                                customProperties: i.customProperties,
                                                                placeholder: i.placeholder,
                                                                keyCode: i.keyCode,
                                                            }),
                                                            this._triggerChange(i.value))),
                                                    this.clearInput(),
                                                    r &&
                                                        this._isSelectOneElement &&
                                                        (this.hideDropdown(!0), this.containerOuter.focus());
                                            }
                                        }
                                    }),
                                    (r._handleBackspace = function(e) {
                                        if (this.config.removeItems && e) {
                                            var t = e[e.length - 1],
                                                n = e.some(function(e) {
                                                    return e.highlighted;
                                                });
                                            this.config.editItems && !n && t
                                                ? ((this.input.value = t.value),
                                                  this.input.setWidth(),
                                                  this._removeItem(t),
                                                  this._triggerChange(t.value))
                                                : (n || this.highlightItem(t, !1), this.removeHighlightedItems(!0));
                                        }
                                    }),
                                    (r._startLoading = function() {
                                        this._store.dispatch(Se(!0));
                                    }),
                                    (r._stopLoading = function() {
                                        this._store.dispatch(Se(!1));
                                    }),
                                    (r._handleLoadingState = function(e) {
                                        void 0 === e && (e = !0);
                                        var t = this.itemList.getChild('.' + this.config.classNames.placeholder);
                                        e
                                            ? (this.disable(),
                                              this.containerOuter.addLoadingState(),
                                              this._isSelectOneElement
                                                  ? t
                                                      ? (t.innerHTML = this.config.loadingText)
                                                      : ((t = this._getTemplate(
                                                            'placeholder',
                                                            this.config.loadingText
                                                        )),
                                                        this.itemList.append(t))
                                                  : (this.input.placeholder = this.config.loadingText))
                                            : (this.enable(),
                                              this.containerOuter.removeLoadingState(),
                                              this._isSelectOneElement
                                                  ? (t.innerHTML = this._placeholderValue || '')
                                                  : (this.input.placeholder = this._placeholderValue || ''));
                                    }),
                                    (r._handleSearch = function(e) {
                                        if (e && this.input.isFocussed) {
                                            var t = this._store.choices,
                                                n = this.config,
                                                i = n.searchFloor,
                                                o = n.searchChoices,
                                                r = t.some(function(e) {
                                                    return !e.active;
                                                });
                                            if (e && e.length >= i) {
                                                var a = o ? this._searchChoices(e) : 0;
                                                this.passedElement.triggerEvent(H, { value: e, resultCount: a });
                                            } else r && ((this._isSearching = !1), this._store.dispatch(Ee(!0)));
                                        }
                                    }),
                                    (r._canAddItem = function(e, t) {
                                        var n = !0,
                                            i =
                                                'function' == typeof this.config.addItemText
                                                    ? this.config.addItemText(t)
                                                    : this.config.addItemText;
                                        if (!this._isSelectOneElement) {
                                            var o = (function(e, t, n) {
                                                return (
                                                    void 0 === n && (n = 'value'),
                                                    e.some(function(e) {
                                                        return 'string' == typeof t ? e[n] === t.trim() : e[n] === t;
                                                    })
                                                );
                                            })(e, t);
                                            this.config.maxItemCount > 0 &&
                                                this.config.maxItemCount <= e.length &&
                                                ((n = !1),
                                                (i =
                                                    'function' == typeof this.config.maxItemText
                                                        ? this.config.maxItemText(this.config.maxItemCount)
                                                        : this.config.maxItemText)),
                                                !this.config.duplicateItemsAllowed &&
                                                    o &&
                                                    n &&
                                                    ((n = !1),
                                                    (i =
                                                        'function' == typeof this.config.uniqueItemText
                                                            ? this.config.uniqueItemText(t)
                                                            : this.config.uniqueItemText)),
                                                this._isTextElement &&
                                                    this.config.addItems &&
                                                    n &&
                                                    'function' == typeof this.config.addItemFilter &&
                                                    !this.config.addItemFilter(t) &&
                                                    ((n = !1),
                                                    (i =
                                                        'function' == typeof this.config.customAddItemText
                                                            ? this.config.customAddItemText(t)
                                                            : this.config.customAddItemText));
                                        }
                                        return { response: n, notice: i };
                                    }),
                                    (r._searchChoices = function(e) {
                                        var t = 'string' == typeof e ? e.trim() : e,
                                            n =
                                                'string' == typeof this._currentValue
                                                    ? this._currentValue.trim()
                                                    : this._currentValue;
                                        if (t.length < 1 && t === n + ' ') return 0;
                                        var i = this._store.searchableChoices,
                                            r = t,
                                            a = [].concat(this.config.searchFields),
                                            s = Object.assign(this.config.fuseOptions, { keys: a }),
                                            c = new o.a(i, s).search(r);
                                        return (
                                            (this._currentValue = t),
                                            (this._highlightPosition = 0),
                                            (this._isSearching = !0),
                                            this._store.dispatch(
                                                (function(e) {
                                                    return { type: U, results: e };
                                                })(c)
                                            ),
                                            c.length
                                        );
                                    }),
                                    (r._addEventListeners = function() {
                                        var e = document.documentElement;
                                        e.addEventListener('touchend', this._onTouchEnd, !0),
                                            this.containerOuter.element.addEventListener(
                                                'keydown',
                                                this._onKeyDown,
                                                !0
                                            ),
                                            this.containerOuter.element.addEventListener(
                                                'mousedown',
                                                this._onMouseDown,
                                                !0
                                            ),
                                            e.addEventListener('click', this._onClick, { passive: !0 }),
                                            e.addEventListener('touchmove', this._onTouchMove, { passive: !0 }),
                                            this.dropdown.element.addEventListener('mouseover', this._onMouseOver, {
                                                passive: !0,
                                            }),
                                            this._isSelectOneElement &&
                                                (this.containerOuter.element.addEventListener('focus', this._onFocus, {
                                                    passive: !0,
                                                }),
                                                this.containerOuter.element.addEventListener('blur', this._onBlur, {
                                                    passive: !0,
                                                })),
                                            this.input.element.addEventListener('keyup', this._onKeyUp, {
                                                passive: !0,
                                            }),
                                            this.input.element.addEventListener('focus', this._onFocus, {
                                                passive: !0,
                                            }),
                                            this.input.element.addEventListener('blur', this._onBlur, { passive: !0 }),
                                            this.input.element.form &&
                                                this.input.element.form.addEventListener('reset', this._onFormReset, {
                                                    passive: !0,
                                                }),
                                            this.input.addEventListeners();
                                    }),
                                    (r._removeEventListeners = function() {
                                        var e = document.documentElement;
                                        e.removeEventListener('touchend', this._onTouchEnd, !0),
                                            this.containerOuter.element.removeEventListener(
                                                'keydown',
                                                this._onKeyDown,
                                                !0
                                            ),
                                            this.containerOuter.element.removeEventListener(
                                                'mousedown',
                                                this._onMouseDown,
                                                !0
                                            ),
                                            e.removeEventListener('click', this._onClick),
                                            e.removeEventListener('touchmove', this._onTouchMove),
                                            this.dropdown.element.removeEventListener('mouseover', this._onMouseOver),
                                            this._isSelectOneElement &&
                                                (this.containerOuter.element.removeEventListener(
                                                    'focus',
                                                    this._onFocus
                                                ),
                                                this.containerOuter.element.removeEventListener('blur', this._onBlur)),
                                            this.input.element.removeEventListener('keyup', this._onKeyUp),
                                            this.input.element.removeEventListener('focus', this._onFocus),
                                            this.input.element.removeEventListener('blur', this._onBlur),
                                            this.input.element.form &&
                                                this.input.element.form.removeEventListener('reset', this._onFormReset),
                                            this.input.removeEventListeners();
                                    }),
                                    (r._onKeyDown = function(e) {
                                        var t,
                                            n = e.target,
                                            i = e.keyCode,
                                            o = e.ctrlKey,
                                            r = e.metaKey,
                                            a = this._store.activeItems,
                                            s = this.input.isFocussed,
                                            c = this.dropdown.isActive,
                                            l = this.itemList.hasChildren(),
                                            u = String.fromCharCode(i),
                                            d = Z,
                                            h = Q,
                                            f = ee,
                                            p = te,
                                            m = ne,
                                            v = ie,
                                            g = oe,
                                            y = re,
                                            b = ae,
                                            w = o || r;
                                        !this._isTextElement && /[a-zA-Z0-9-_ ]/.test(u) && this.showDropdown();
                                        var E =
                                            (((t = {})[p] = this._onAKey),
                                            (t[f] = this._onEnterKey),
                                            (t[m] = this._onEscapeKey),
                                            (t[v] = this._onDirectionKey),
                                            (t[y] = this._onDirectionKey),
                                            (t[g] = this._onDirectionKey),
                                            (t[b] = this._onDirectionKey),
                                            (t[h] = this._onDeleteKey),
                                            (t[d] = this._onDeleteKey),
                                            t);
                                        E[i] &&
                                            E[i]({
                                                event: e,
                                                target: n,
                                                keyCode: i,
                                                metaKey: r,
                                                activeItems: a,
                                                hasFocusedInput: s,
                                                hasActiveDropdown: c,
                                                hasItems: l,
                                                hasCtrlDownKeyPressed: w,
                                            });
                                    }),
                                    (r._onKeyUp = function(e) {
                                        var t = e.target,
                                            n = e.keyCode,
                                            i = this.input.value,
                                            o = this._store.activeItems,
                                            r = this._canAddItem(o, i),
                                            a = Z,
                                            s = Q;
                                        if (this._isTextElement)
                                            if (r.notice && i) {
                                                var c = this._getTemplate('notice', r.notice);
                                                (this.dropdown.element.innerHTML = c.outerHTML), this.showDropdown(!0);
                                            } else this.hideDropdown(!0);
                                        else {
                                            var l = (n === a || n === s) && !t.value,
                                                u = !this._isTextElement && this._isSearching,
                                                d = this._canSearch && r.response;
                                            l && u
                                                ? ((this._isSearching = !1), this._store.dispatch(Ee(!0)))
                                                : d && this._handleSearch(this.input.value);
                                        }
                                        this._canSearch = this.config.searchEnabled;
                                    }),
                                    (r._onAKey = function(e) {
                                        var t = e.hasItems;
                                        e.hasCtrlDownKeyPressed &&
                                            t &&
                                            ((this._canSearch = !1),
                                            this.config.removeItems &&
                                                !this.input.value &&
                                                this.input.element === document.activeElement &&
                                                this.highlightAll());
                                    }),
                                    (r._onEnterKey = function(e) {
                                        var t = e.event,
                                            n = e.target,
                                            i = e.activeItems,
                                            o = e.hasActiveDropdown,
                                            r = ee,
                                            a = n.hasAttribute('data-button');
                                        if (this._isTextElement && n.value) {
                                            var s = this.input.value;
                                            this._canAddItem(i, s).response &&
                                                (this.hideDropdown(!0),
                                                this._addItem({ value: s }),
                                                this._triggerChange(s),
                                                this.clearInput());
                                        }
                                        if ((a && (this._handleButtonAction(i, n), t.preventDefault()), o)) {
                                            var c = this.dropdown.getChild(
                                                '.' + this.config.classNames.highlightedState
                                            );
                                            c && (i[0] && (i[0].keyCode = r), this._handleChoiceAction(i, c)),
                                                t.preventDefault();
                                        } else this._isSelectOneElement && (this.showDropdown(), t.preventDefault());
                                    }),
                                    (r._onEscapeKey = function(e) {
                                        e.hasActiveDropdown && (this.hideDropdown(!0), this.containerOuter.focus());
                                    }),
                                    (r._onDirectionKey = function(e) {
                                        var t,
                                            n,
                                            i,
                                            o = e.event,
                                            r = e.hasActiveDropdown,
                                            a = e.keyCode,
                                            s = e.metaKey,
                                            c = oe,
                                            l = re,
                                            u = ae;
                                        if (r || this._isSelectOneElement) {
                                            this.showDropdown(), (this._canSearch = !1);
                                            var d,
                                                h = a === c || a === u ? 1 : -1;
                                            if (s || a === u || a === l)
                                                d =
                                                    h > 0
                                                        ? this.dropdown.element.querySelector(
                                                              '[data-choice-selectable]:last-of-type'
                                                          )
                                                        : this.dropdown.element.querySelector(
                                                              '[data-choice-selectable]'
                                                          );
                                            else {
                                                var f = this.dropdown.element.querySelector(
                                                    '.' + this.config.classNames.highlightedState
                                                );
                                                d = f
                                                    ? (function(e, t, n) {
                                                          if (
                                                              (void 0 === n && (n = 1),
                                                              e instanceof Element && 'string' == typeof t)
                                                          ) {
                                                              for (
                                                                  var i =
                                                                          (n > 0 ? 'next' : 'previous') +
                                                                          'ElementSibling',
                                                                      o = e[i];
                                                                  o;

                                                              ) {
                                                                  if (o.matches(t)) return o;
                                                                  o = o[i];
                                                              }
                                                              return o;
                                                          }
                                                      })(f, '[data-choice-selectable]', h)
                                                    : this.dropdown.element.querySelector('[data-choice-selectable]');
                                            }
                                            d &&
                                                ((t = d),
                                                (n = this.choiceList.element),
                                                void 0 === (i = h) && (i = 1),
                                                (t &&
                                                    (i > 0
                                                        ? n.scrollTop + n.offsetHeight >= t.offsetTop + t.offsetHeight
                                                        : t.offsetTop >= n.scrollTop)) ||
                                                    this.choiceList.scrollToChildElement(d, h),
                                                this._highlightChoice(d)),
                                                o.preventDefault();
                                        }
                                    }),
                                    (r._onDeleteKey = function(e) {
                                        var t = e.event,
                                            n = e.target,
                                            i = e.hasFocusedInput,
                                            o = e.activeItems;
                                        !i ||
                                            n.value ||
                                            this._isSelectOneElement ||
                                            (this._handleBackspace(o), t.preventDefault());
                                    }),
                                    (r._onTouchMove = function() {
                                        this._wasTap && (this._wasTap = !1);
                                    }),
                                    (r._onTouchEnd = function(e) {
                                        var t = (e || e.touches[0]).target;
                                        this._wasTap &&
                                            this.containerOuter.element.contains(t) &&
                                            ((t === this.containerOuter.element || t === this.containerInner.element) &&
                                                (this._isTextElement
                                                    ? this.input.focus()
                                                    : this._isSelectMultipleElement && this.showDropdown()),
                                            e.stopPropagation()),
                                            (this._wasTap = !0);
                                    }),
                                    (r._onMouseDown = function(e) {
                                        var t = e.target;
                                        if (t instanceof HTMLElement) {
                                            if (Ie && this.choiceList.element.contains(t)) {
                                                var n = this.choiceList.element.firstElementChild,
                                                    i =
                                                        'ltr' === this._direction
                                                            ? e.offsetX >= n.offsetWidth
                                                            : e.offsetX < n.offsetLeft;
                                                this._isScrollingOnIe = i;
                                            }
                                            if (t !== this.input.element) {
                                                var o = t.closest('[data-button],[data-item],[data-choice]');
                                                if (o instanceof HTMLElement) {
                                                    var r = e.shiftKey,
                                                        a = this._store.activeItems,
                                                        s = o.dataset;
                                                    'button' in s
                                                        ? this._handleButtonAction(a, o)
                                                        : 'item' in s
                                                        ? this._handleItemAction(a, o, r)
                                                        : 'choice' in s && this._handleChoiceAction(a, o);
                                                }
                                                e.preventDefault();
                                            }
                                        }
                                    }),
                                    (r._onMouseOver = function(e) {
                                        var t = e.target;
                                        t instanceof HTMLElement && 'choice' in t.dataset && this._highlightChoice(t);
                                    }),
                                    (r._onClick = function(e) {
                                        var t = e.target;
                                        this.containerOuter.element.contains(t)
                                            ? this.dropdown.isActive || this.containerOuter.isDisabled
                                                ? this._isSelectOneElement &&
                                                  t !== this.input.element &&
                                                  !this.dropdown.element.contains(t) &&
                                                  this.hideDropdown()
                                                : this._isTextElement
                                                ? document.activeElement !== this.input.element && this.input.focus()
                                                : (this.showDropdown(), this.containerOuter.focus())
                                            : (this._store.highlightedActiveItems.length > 0 && this.unhighlightAll(),
                                              this.containerOuter.removeFocusState(),
                                              this.hideDropdown(!0));
                                    }),
                                    (r._onFocus = function(e) {
                                        var t,
                                            n = this,
                                            i = e.target;
                                        this.containerOuter.element.contains(i) &&
                                            (((t = {})[se] = function() {
                                                i === n.input.element && n.containerOuter.addFocusState();
                                            }),
                                            (t[ce] = function() {
                                                n.containerOuter.addFocusState(),
                                                    i === n.input.element && n.showDropdown(!0);
                                            }),
                                            (t[le] = function() {
                                                i === n.input.element &&
                                                    (n.showDropdown(!0), n.containerOuter.addFocusState());
                                            }),
                                            t)[this.passedElement.element.type]();
                                    }),
                                    (r._onBlur = function(e) {
                                        var t = this,
                                            n = e.target;
                                        if (this.containerOuter.element.contains(n) && !this._isScrollingOnIe) {
                                            var i,
                                                o = this._store.activeItems.some(function(e) {
                                                    return e.highlighted;
                                                });
                                            (((i = {})[se] = function() {
                                                n === t.input.element &&
                                                    (t.containerOuter.removeFocusState(),
                                                    o && t.unhighlightAll(),
                                                    t.hideDropdown(!0));
                                            }),
                                            (i[ce] = function() {
                                                t.containerOuter.removeFocusState(),
                                                    (n === t.input.element ||
                                                        (n === t.containerOuter.element && !t._canSearch)) &&
                                                        t.hideDropdown(!0);
                                            }),
                                            (i[le] = function() {
                                                n === t.input.element &&
                                                    (t.containerOuter.removeFocusState(),
                                                    t.hideDropdown(!0),
                                                    o && t.unhighlightAll());
                                            }),
                                            i)[this.passedElement.element.type]();
                                        } else (this._isScrollingOnIe = !1), this.input.element.focus();
                                    }),
                                    (r._onFormReset = function() {
                                        this._store.dispatch({ type: 'RESET_TO', state: this._initialState });
                                    }),
                                    (r._highlightChoice = function(e) {
                                        var t = this;
                                        void 0 === e && (e = null);
                                        var n = Array.from(
                                            this.dropdown.element.querySelectorAll('[data-choice-selectable]')
                                        );
                                        if (n.length) {
                                            var i = e;
                                            Array.from(
                                                this.dropdown.element.querySelectorAll(
                                                    '.' + this.config.classNames.highlightedState
                                                )
                                            ).forEach(function(e) {
                                                e.classList.remove(t.config.classNames.highlightedState),
                                                    e.setAttribute('aria-selected', 'false');
                                            }),
                                                i
                                                    ? (this._highlightPosition = n.indexOf(i))
                                                    : (i =
                                                          n.length > this._highlightPosition
                                                              ? n[this._highlightPosition]
                                                              : n[n.length - 1]) || (i = n[0]),
                                                i.classList.add(this.config.classNames.highlightedState),
                                                i.setAttribute('aria-selected', 'true'),
                                                this.passedElement.triggerEvent(Y, { el: i }),
                                                this.dropdown.isActive &&
                                                    (this.input.setActiveDescendant(i.id),
                                                    this.containerOuter.setActiveDescendant(i.id));
                                        }
                                    }),
                                    (r._addItem = function(e) {
                                        var t = e.value,
                                            n = e.label,
                                            i = void 0 === n ? null : n,
                                            o = e.choiceId,
                                            r = void 0 === o ? -1 : o,
                                            a = e.groupId,
                                            s = void 0 === a ? -1 : a,
                                            c = e.customProperties,
                                            l = void 0 === c ? null : c,
                                            u = e.placeholder,
                                            d = void 0 !== u && u,
                                            h = e.keyCode,
                                            f = void 0 === h ? null : h,
                                            p = 'string' == typeof t ? t.trim() : t,
                                            m = f,
                                            v = l,
                                            g = this._store.items,
                                            y = i || p,
                                            b = r || -1,
                                            w = s >= 0 ? this._store.getGroupById(s) : null,
                                            E = g ? g.length + 1 : 1;
                                        return (
                                            this.config.prependValue && (p = this.config.prependValue + p.toString()),
                                            this.config.appendValue && (p += this.config.appendValue.toString()),
                                            this._store.dispatch(
                                                (function(e) {
                                                    var t = e.value,
                                                        n = e.label,
                                                        i = e.id,
                                                        o = e.choiceId,
                                                        r = e.groupId,
                                                        a = e.customProperties,
                                                        s = e.placeholder,
                                                        c = e.keyCode;
                                                    return {
                                                        type: J,
                                                        value: t,
                                                        label: n,
                                                        id: i,
                                                        choiceId: o,
                                                        groupId: r,
                                                        customProperties: a,
                                                        placeholder: s,
                                                        keyCode: c,
                                                    };
                                                })({
                                                    value: p,
                                                    label: y,
                                                    id: E,
                                                    choiceId: b,
                                                    groupId: s,
                                                    customProperties: l,
                                                    placeholder: d,
                                                    keyCode: m,
                                                })
                                            ),
                                            this._isSelectOneElement && this.removeActiveItems(E),
                                            this.passedElement.triggerEvent(B, {
                                                id: E,
                                                value: p,
                                                label: y,
                                                customProperties: v,
                                                groupValue: w && w.value ? w.value : void 0,
                                                keyCode: m,
                                            }),
                                            this
                                        );
                                    }),
                                    (r._removeItem = function(e) {
                                        if (!e || !_('Object', e)) return this;
                                        var t = e.id,
                                            n = e.value,
                                            i = e.label,
                                            o = e.choiceId,
                                            r = e.groupId,
                                            a = r >= 0 ? this._store.getGroupById(r) : null;
                                        return (
                                            this._store.dispatch(
                                                (function(e, t) {
                                                    return { type: X, id: e, choiceId: t };
                                                })(t, o)
                                            ),
                                            a && a.value
                                                ? this.passedElement.triggerEvent(q, {
                                                      id: t,
                                                      value: n,
                                                      label: i,
                                                      groupValue: a.value,
                                                  })
                                                : this.passedElement.triggerEvent(q, { id: t, value: n, label: i }),
                                            this
                                        );
                                    }),
                                    (r._addChoice = function(e) {
                                        var t = e.value,
                                            n = e.label,
                                            i = void 0 === n ? null : n,
                                            o = e.isSelected,
                                            r = void 0 !== o && o,
                                            a = e.isDisabled,
                                            s = void 0 !== a && a,
                                            c = e.groupId,
                                            l = void 0 === c ? -1 : c,
                                            u = e.customProperties,
                                            d = void 0 === u ? null : u,
                                            h = e.placeholder,
                                            f = void 0 !== h && h,
                                            p = e.keyCode,
                                            m = void 0 === p ? null : p;
                                        if (null != t) {
                                            var v = this._store.choices,
                                                g = i || t,
                                                y = v ? v.length + 1 : 1,
                                                b = this._baseId + '-' + this._idNames.itemChoice + '-' + y;
                                            this._store.dispatch(
                                                (function(e) {
                                                    var t = e.value,
                                                        n = e.label,
                                                        i = e.id,
                                                        o = e.groupId,
                                                        r = e.disabled,
                                                        a = e.elementId,
                                                        s = e.customProperties,
                                                        c = e.placeholder,
                                                        l = e.keyCode;
                                                    return {
                                                        type: K,
                                                        value: t,
                                                        label: n,
                                                        id: i,
                                                        groupId: o,
                                                        disabled: r,
                                                        elementId: a,
                                                        customProperties: s,
                                                        placeholder: c,
                                                        keyCode: l,
                                                    };
                                                })({
                                                    id: y,
                                                    groupId: l,
                                                    elementId: b,
                                                    value: t,
                                                    label: g,
                                                    disabled: s,
                                                    customProperties: d,
                                                    placeholder: f,
                                                    keyCode: m,
                                                })
                                            ),
                                                r &&
                                                    this._addItem({
                                                        value: t,
                                                        label: g,
                                                        choiceId: y,
                                                        customProperties: d,
                                                        placeholder: f,
                                                        keyCode: m,
                                                    });
                                        }
                                    }),
                                    (r._addGroup = function(e) {
                                        var t = this,
                                            n = e.group,
                                            i = e.id,
                                            o = e.valueKey,
                                            r = void 0 === o ? 'value' : o,
                                            a = e.labelKey,
                                            s = void 0 === a ? 'label' : a,
                                            c = _('Object', n)
                                                ? n.choices
                                                : Array.from(n.getElementsByTagName('OPTION')),
                                            l = i || Math.floor(new Date().valueOf() * Math.random()),
                                            u = !!n.disabled && n.disabled;
                                        c
                                            ? (this._store.dispatch(
                                                  Ce({ value: n.label, id: l, active: !0, disabled: u })
                                              ),
                                              c.forEach(function(e) {
                                                  var n = e.disabled || (e.parentNode && e.parentNode.disabled);
                                                  t._addChoice({
                                                      value: e[r],
                                                      label: _('Object', e) ? e[s] : e.innerHTML,
                                                      isSelected: e.selected,
                                                      isDisabled: n,
                                                      groupId: l,
                                                      customProperties: e.customProperties,
                                                      placeholder: e.placeholder,
                                                  });
                                              }))
                                            : this._store.dispatch(
                                                  Ce({ value: n.label, id: n.id, active: !1, disabled: n.disabled })
                                              );
                                    }),
                                    (r._getTemplate = function(e) {
                                        var t;
                                        if (!e) return null;
                                        for (
                                            var n = this.config.classNames,
                                                i = arguments.length,
                                                o = new Array(i > 1 ? i - 1 : 0),
                                                r = 1;
                                            r < i;
                                            r++
                                        )
                                            o[r - 1] = arguments[r];
                                        return (t = this._templates[e]).call.apply(t, [this, n].concat(o));
                                    }),
                                    (r._createTemplates = function() {
                                        var e = this.config.callbackOnCreateTemplates,
                                            t = {};
                                        e && 'function' == typeof e && (t = e.call(this, S)),
                                            (this._templates = a()(we, t));
                                    }),
                                    (r._createElements = function() {
                                        (this.containerOuter = new ue({
                                            element: this._getTemplate(
                                                'containerOuter',
                                                this._direction,
                                                this._isSelectElement,
                                                this._isSelectOneElement,
                                                this.config.searchEnabled,
                                                this.passedElement.element.type
                                            ),
                                            classNames: this.config.classNames,
                                            type: this.passedElement.element.type,
                                            position: this.config.position,
                                        })),
                                            (this.containerInner = new ue({
                                                element: this._getTemplate('containerInner'),
                                                classNames: this.config.classNames,
                                                type: this.passedElement.element.type,
                                                position: this.config.position,
                                            })),
                                            (this.input = new he({
                                                element: this._getTemplate('input', this._placeholderValue),
                                                classNames: this.config.classNames,
                                                type: this.passedElement.element.type,
                                                preventPaste: !this.config.paste,
                                            })),
                                            (this.choiceList = new fe({
                                                element: this._getTemplate('choiceList', this._isSelectOneElement),
                                            })),
                                            (this.itemList = new fe({
                                                element: this._getTemplate('itemList', this._isSelectOneElement),
                                            })),
                                            (this.dropdown = new k({
                                                element: this._getTemplate('dropdown'),
                                                classNames: this.config.classNames,
                                                type: this.passedElement.element.type,
                                            }));
                                    }),
                                    (r._createStructure = function() {
                                        this.passedElement.conceal(),
                                            this.containerInner.wrap(this.passedElement.element),
                                            this.containerOuter.wrap(this.containerInner.element),
                                            this._isSelectOneElement
                                                ? (this.input.placeholder = this.config.searchPlaceholderValue || '')
                                                : this._placeholderValue &&
                                                  ((this.input.placeholder = this._placeholderValue),
                                                  this.input.setWidth()),
                                            this.containerOuter.element.appendChild(this.containerInner.element),
                                            this.containerOuter.element.appendChild(this.dropdown.element),
                                            this.containerInner.element.appendChild(this.itemList.element),
                                            this._isTextElement ||
                                                this.dropdown.element.appendChild(this.choiceList.element),
                                            this._isSelectOneElement
                                                ? this.config.searchEnabled &&
                                                  this.dropdown.element.insertBefore(
                                                      this.input.element,
                                                      this.dropdown.element.firstChild
                                                  )
                                                : this.containerInner.element.appendChild(this.input.element),
                                            this._isSelectElement &&
                                                ((this._highlightPosition = 0),
                                                (this._isSearching = !1),
                                                this._startLoading(),
                                                this._presetGroups.length
                                                    ? this._addPredefinedGroups(this._presetGroups)
                                                    : this._addPredefinedChoices(this._presetChoices),
                                                this._stopLoading()),
                                            this._isTextElement && this._addPredefinedItems(this._presetItems);
                                    }),
                                    (r._addPredefinedGroups = function(e) {
                                        var t = this,
                                            n = this.passedElement.placeholderOption;
                                        n &&
                                            'SELECT' === n.parentNode.tagName &&
                                            this._addChoice({
                                                value: n.value,
                                                label: n.innerHTML,
                                                isSelected: n.selected,
                                                isDisabled: n.disabled,
                                                placeholder: !0,
                                            }),
                                            e.forEach(function(e) {
                                                return t._addGroup({ group: e, id: e.id || null });
                                            });
                                    }),
                                    (r._addPredefinedChoices = function(e) {
                                        var t = this;
                                        this.config.shouldSort && e.sort(this.config.sorter);
                                        var n = e.some(function(e) {
                                                return e.selected;
                                            }),
                                            i = e.findIndex(function(e) {
                                                return void 0 === e.disabled || !e.disabled;
                                            });
                                        e.forEach(function(e, o) {
                                            var r = e.value,
                                                a = e.label,
                                                s = e.customProperties,
                                                c = e.placeholder;
                                            if (t._isSelectElement)
                                                if (e.choices) t._addGroup({ group: e, id: e.id || null });
                                                else {
                                                    var l = !(!t._isSelectOneElement || n || o !== i) || e.selected,
                                                        u = e.disabled;
                                                    t._addChoice({
                                                        value: r,
                                                        label: a,
                                                        isSelected: l,
                                                        isDisabled: u,
                                                        customProperties: s,
                                                        placeholder: c,
                                                    });
                                                }
                                            else
                                                t._addChoice({
                                                    value: r,
                                                    label: a,
                                                    isSelected: e.selected,
                                                    isDisabled: e.disabled,
                                                    customProperties: s,
                                                    placeholder: c,
                                                });
                                        });
                                    }),
                                    (r._addPredefinedItems = function(e) {
                                        var t = this;
                                        e.forEach(function(e) {
                                            'object' == typeof e &&
                                                e.value &&
                                                t._addItem({
                                                    value: e.value,
                                                    label: e.label,
                                                    choiceId: e.id,
                                                    customProperties: e.customProperties,
                                                    placeholder: e.placeholder,
                                                }),
                                                'string' == typeof e && t._addItem({ value: e });
                                        });
                                    }),
                                    (r._setChoiceOrItem = function(e) {
                                        var t = this;
                                        ({
                                            object: function() {
                                                e.value &&
                                                    (t._isTextElement
                                                        ? t._addItem({
                                                              value: e.value,
                                                              label: e.label,
                                                              choiceId: e.id,
                                                              customProperties: e.customProperties,
                                                              placeholder: e.placeholder,
                                                          })
                                                        : t._addChoice({
                                                              value: e.value,
                                                              label: e.label,
                                                              isSelected: !0,
                                                              isDisabled: !1,
                                                              customProperties: e.customProperties,
                                                              placeholder: e.placeholder,
                                                          }));
                                            },
                                            string: function() {
                                                t._isTextElement
                                                    ? t._addItem({ value: e })
                                                    : t._addChoice({
                                                          value: e,
                                                          label: e,
                                                          isSelected: !0,
                                                          isDisabled: !1,
                                                      });
                                            },
                                        }[E(e).toLowerCase()]());
                                    }),
                                    (r._findAndSelectChoiceByValue = function(e) {
                                        var t = this,
                                            n = this._store.choices.find(function(n) {
                                                return t.config.valueComparer(n.value, e);
                                            });
                                        n &&
                                            !n.selected &&
                                            this._addItem({
                                                value: n.value,
                                                label: n.label,
                                                choiceId: n.id,
                                                groupId: n.groupId,
                                                customProperties: n.customProperties,
                                                placeholder: n.placeholder,
                                                keyCode: n.keyCode,
                                            });
                                    }),
                                    (r._generatePlaceholderValue = function() {
                                        if (this._isSelectElement) {
                                            var e = this.passedElement.placeholderOption;
                                            return !!e && e.text;
                                        }
                                        var t = this.config,
                                            n = t.placeholder,
                                            i = t.placeholderValue,
                                            o = this.passedElement.element.dataset;
                                        if (n) {
                                            if (i) return i;
                                            if (o.placeholder) return o.placeholder;
                                        }
                                        return !1;
                                    }),
                                    i
                                );
                            })();
                        t.default = Le;
                    },
                ]).default;
            }),
            (e.exports = i());
    },
    function(e, t, n) {
        n(6), (e.exports = n(18));
    },
    function(e, t, n) {
        n(7), n(8), n(9), n(10), n(11), n(12), n(13), n(14), n(1), n(17), n(15), n(16);
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i,
            o,
            r = n(2),
            a = n.n(r),
            s = n(0);
        a.a.start(),
            Object(s.f)('click', '[data-turbolinks-preserve-scroll]', function() {
                i = window.scrollY;
            }),
            Object(s.f)(
                'input',
                '[data-turbolinks-search]',
                Object(s.c)(function(e) {
                    var t = e.target,
                        n = t.value
                            ? t.dataset.turbolinksSearchUrl.replace('%search%', t.value)
                            : t.dataset.turbolinksSearchClearUrl;
                    (i = window.scrollY),
                        (o = document.activeElement.matches('[data-turbolinks-search]') ? n : null),
                        a.a.visit(n, { action: 'replace' });
                }, 400)
            ),
            document.addEventListener('turbolinks:render', function() {
                o === window.location.href && Object(s.a)('[data-turbolinks-search]').focus(),
                    i && (window.scrollTo(0, i), (i = void 0));
            });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0);
        function o(e) {
            var t = (function(e) {
                if (e.matches('[type="checkbox"]')) return String(e.checked);
                if (e.matches('[type="radio"]')) {
                    var t = Object(i.b)('[name="'.concat(e.name, '"]')).find(function(e) {
                        return e.checked;
                    });
                    return t ? t.value : 'null';
                }
                return e.value;
            })(e);
            Object(i.b)('[data-conditional-'.concat(e.dataset.conditional, ']')).forEach(function(n) {
                var i = n.matches('[data-conditional-'.concat(e.dataset.conditional, '="').concat(t, '"]'));
                n.classList.toggle('hidden', !i);
            }),
                Object(i.b)('[data-conditional-unless-'.concat(e.dataset.conditional, ']')).forEach(function(n) {
                    var i = !n.matches('[data-conditional-unless-'.concat(e.dataset.conditional, '="').concat(t, '"]'));
                    n.classList.toggle('hidden', !i);
                });
        }
        window.addEventListener('turbolinks:load', function() {
            Object(i.b)('[data-conditional]').forEach(o);
        }),
            Object(i.f)('change', '[data-conditional]', function(e) {
                o(e.target);
            });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0),
            o = n(1);
        Object(i.f)('submit', '[data-confirm]', function(e) {
            var t = e.event,
                n = e.target;
            t.preventDefault(),
                Object(o.showModal)('confirm', {
                    onConfirm: function() {
                        n.submit();
                    },
                });
        });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(3),
            o = n.n(i);
        document.addEventListener('turbolinks:load', function() {
            document.querySelectorAll('[data-datepicker]').forEach(function(e) {
                o()(e, { dateFormat: 'Y-m-d', minDate: 'today', position: 'above' });
            });
        });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(2),
            o = n.n(i),
            r = n(0),
            a = n(1);
        Object(r.f)('input', '[data-dirty-check]', function(e) {
            e.target.dirty = !0;
        }),
            Object(r.f)('click', '[data-dirty-warn]', function() {
                Object(r.a)('[data-dirty-check]') &&
                    Object(r.a)('[data-dirty-check]').dirty &&
                    document.addEventListener(
                        'turbolinks:before-visit',
                        function(e) {
                            e.preventDefault(),
                                Object(a.showModal)('dirty-warning', {
                                    onConfirm: function() {
                                        o.a.visit(e.data.url);
                                    },
                                });
                        },
                        { once: !0 }
                    );
            });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0);
        Object(i.f)('click', '[data-dismiss]', function(e) {
            e.target.remove();
        }),
            document.addEventListener('turbolinks:load', function() {
                Object(i.b)('[data-dismiss]').forEach(function(e) {
                    var t = Math.min(Math.max(60 * e.textContent.trim().length, 5e3), 15e3);
                    setTimeout(function() {
                        Object(i.e)(e, 'fade').then(function() {
                            e.remove();
                        });
                    }, t);
                });
            });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0);
        Object(i.f)('click', '[data-dropdown-trigger]', function(e) {
            var t = e.target,
                n = Object(i.a)('[data-dropdown-list]', t.closest('[data-dropdown]'));
            function o(e) {
                n.contains(e.target) ||
                    (Object(i.e)(n, 'fade'),
                    t.classList.remove('dropdown-trigger-open'),
                    window.removeEventListener('click', o));
            }
            n.classList.contains('hidden') &&
                (Object(i.d)(n, 'fade'),
                t.classList.add('dropdown-trigger-open'),
                setTimeout(function() {
                    window.addEventListener('click', o);
                }));
        });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0);
        function o() {
            var e = Object(i.a)('[data-html-preview-source]'),
                t = Object(i.a)('[data-html-preview-target]');
            e && t && (t.src = 'data:text/html;base64,'.concat(btoa(unescape(encodeURIComponent(e.value)))));
        }
        Object(i.f)('input', '[data-html-preview-source]', o), document.addEventListener('turbolinks:load', o);
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i = n(0);
        document.addEventListener('turbolinks:load', function() {
            var e = Object(i.a)('[data-segments]');
            if (e) {
                var t = JSON.parse(e.dataset.segments),
                    n = Object(i.a)('[data-segments-email-list]', e),
                    o = Object(i.a)('[name="segment"][value="entire_list"]', e),
                    r = Object(i.a)('[name="segment"][value="segment"]', e),
                    a = Object(i.a)('[data-segments-create]', e),
                    s = Object(i.a)('a', a),
                    c = Object(i.a)('[data-segments-choose]', e),
                    l = Object(i.a)('select', c);
                u({ selectedSegmentId: e.dataset.segmentsSelected }),
                    n.addEventListener('input', function() {
                        u({ reset: !0 });
                    }),
                    o.addEventListener('input', function() {
                        return u();
                    }),
                    r.addEventListener('input', function() {
                        return u();
                    });
            }
            function u() {
                var e = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : {},
                    i = e.reset,
                    u = void 0 !== i && i,
                    d = e.selectedSegmentId,
                    h = void 0 === d ? null : d;
                u && (o.checked = !0);
                var f = t.find(function(e) {
                        return e.id == n.value;
                    }),
                    p = f.segments.length > 0;
                (r.disabled = !p),
                    a.classList.toggle('hidden', p),
                    (s.href = f.createSegmentUrl),
                    c.classList.toggle('hidden', !p),
                    l.parentNode.classList.toggle('hidden', o.checked),
                    (l.innerHTML = f.segments
                        .map(function(e, t) {
                            var n = h ? e.id == h : 0 === t;
                            return '\n                    <option value="'
                                .concat(e.id, '" ')
                                .concat(n ? 'selected' : '', '>\n                        ')
                                .concat(e.name, '\n                    </option>\n                ');
                        })
                        .join(''));
            }
        });
    },
    function(e, t, n) {
        'use strict';
        n.r(t),
            n.d(t, 'createTagsInput', function() {
                return a;
            });
        var i = n(4),
            o = n.n(i),
            r = n(0);
        function a(e, t) {
            var n = t.tags,
                i = t.selectedTags,
                a = t.canCreateNewTags;
            var s = n.map(function(e) {
                    return {
                        value: e,
                        label: e,
                        selected: i.includes(e),
                        customProperties: { isCurrentSearch: !1, exists: !0 },
                    };
                }),
                c = new o.a(e, {
                    removeItemButton: !0,
                    noResultsText: a ? 'Type to add tags' : 'No tags found',
                    noChoicesText: a ? 'Type to add tags' : 'No tags to choose from',
                    itemSelectText: a ? 'Press to add' : ' Press to select',
                    shouldSortItems: !1,
                    choices: s,
                });
            function l(e) {
                var t = Boolean(
                    c._currentState.choices.find(function(e) {
                        return e.customProperties.isCurrentSearch;
                    })
                );
                (t || e) &&
                    (t
                        ? e
                            ? (function(e) {
                                  d(e)
                                      ? u()
                                      : c._currentState.choices.forEach(function(t) {
                                            t.customProperties.isCurrentSearch && ((t.value = e), (t.label = e));
                                        });
                              })(e)
                            : u()
                        : (function(e) {
                              d(e) ||
                                  c.setChoices([
                                      { value: e, label: e, customProperties: { isCurrentSearch: !0, exists: !1 } },
                                  ]);
                          })(e));
            }
            function u() {
                var e = c._currentState.choices.findIndex(function(e) {
                    return e.customProperties.isCurrentSearch;
                });
                -1 !== e && c._currentState.choices.splice(e, 1);
            }
            function d(e) {
                return (
                    -1 !==
                    c._currentState.choices.findIndex(function(t) {
                        return !t.customProperties.isCurrentSearch && t.value.toLowerCase() === e.toLowerCase();
                    })
                );
            }
            return (
                e.addEventListener('addItem', function() {
                    c._currentState.choices.forEach(function(e) {
                        delete e.customProperties.isCurrentSearch;
                    });
                }),
                a &&
                    Object(r.a)('input.choices__input', e.parentNode).addEventListener('input', function(e) {
                        l(e.target.value);
                    }),
                c
            );
        }
        document.addEventListener('turbolinks:load', function() {
            Object(r.b)('[data-tags]').forEach(function(e) {
                window.tagsInput = a(e, {
                    tags: JSON.parse(e.dataset.tags),
                    selectedTags: JSON.parse(e.dataset.tagsSelected),
                    canCreateNewTags: 'tagsAllowCreate' in e.dataset,
                });
            });
        });
    },
    function(e, t, n) {
        'use strict';
        n.r(t);
        var i,
            o = 11;
        var r = 'http://www.w3.org/1999/xhtml',
            a = 'undefined' == typeof document ? void 0 : document,
            s = !!a && 'content' in a.createElement('template'),
            c = !!a && a.createRange && 'createContextualFragment' in a.createRange();
        function l(e) {
            return (
                (e = e.trim()),
                s
                    ? (function(e) {
                          var t = a.createElement('template');
                          return (t.innerHTML = e), t.content.childNodes[0];
                      })(e)
                    : c
                    ? (function(e) {
                          return (
                              i || (i = a.createRange()).selectNode(a.body), i.createContextualFragment(e).childNodes[0]
                          );
                      })(e)
                    : (function(e) {
                          var t = a.createElement('body');
                          return (t.innerHTML = e), t.childNodes[0];
                      })(e)
            );
        }
        function u(e, t) {
            var n = e.nodeName,
                i = t.nodeName;
            return (
                n === i || (!!(t.actualize && n.charCodeAt(0) < 91 && i.charCodeAt(0) > 90) && n === i.toUpperCase())
            );
        }
        function d(e, t, n) {
            e[n] !== t[n] && ((e[n] = t[n]), e[n] ? e.setAttribute(n, '') : e.removeAttribute(n));
        }
        var h = {
                OPTION: function(e, t) {
                    var n = e.parentNode;
                    if (n) {
                        var i = n.nodeName.toUpperCase();
                        'OPTGROUP' === i && (i = (n = n.parentNode) && n.nodeName.toUpperCase()),
                            'SELECT' !== i ||
                                n.hasAttribute('multiple') ||
                                (e.hasAttribute('selected') &&
                                    !t.selected &&
                                    (e.setAttribute('selected', 'selected'), e.removeAttribute('selected')),
                                (n.selectedIndex = -1));
                    }
                    d(e, t, 'selected');
                },
                INPUT: function(e, t) {
                    d(e, t, 'checked'),
                        d(e, t, 'disabled'),
                        e.value !== t.value && (e.value = t.value),
                        t.hasAttribute('value') || e.removeAttribute('value');
                },
                TEXTAREA: function(e, t) {
                    var n = t.value;
                    e.value !== n && (e.value = n);
                    var i = e.firstChild;
                    if (i) {
                        var o = i.nodeValue;
                        if (o == n || (!n && o == e.placeholder)) return;
                        i.nodeValue = n;
                    }
                },
                SELECT: function(e, t) {
                    if (!t.hasAttribute('multiple')) {
                        for (var n, i, o = -1, r = 0, a = e.firstChild; a; )
                            if ('OPTGROUP' === (i = a.nodeName && a.nodeName.toUpperCase())) a = (n = a).firstChild;
                            else {
                                if ('OPTION' === i) {
                                    if (a.hasAttribute('selected')) {
                                        o = r;
                                        break;
                                    }
                                    r++;
                                }
                                !(a = a.nextSibling) && n && ((a = n.nextSibling), (n = null));
                            }
                        e.selectedIndex = o;
                    }
                },
            },
            f = 1,
            p = 11,
            m = 3,
            v = 8;
        function g() {}
        function y(e) {
            return e.id;
        }
        var b,
            w = (function(e) {
                return function(t, n, i) {
                    if ((i || (i = {}), 'string' == typeof n))
                        if ('#document' === t.nodeName || 'HTML' === t.nodeName) {
                            var o = n;
                            (n = a.createElement('html')).innerHTML = o;
                        } else n = l(n);
                    var s = i.getNodeKey || y,
                        c = i.onBeforeNodeAdded || g,
                        d = i.onNodeAdded || g,
                        b = i.onBeforeElUpdated || g,
                        w = i.onElUpdated || g,
                        E = i.onBeforeNodeDiscarded || g,
                        _ = i.onNodeDiscarded || g,
                        C = i.onBeforeElChildrenUpdated || g,
                        S = !0 === i.childrenOnly,
                        D = Object.create(null),
                        I = [];
                    function T(e) {
                        I.push(e);
                    }
                    function L(e, t, n) {
                        !1 !== E(e) &&
                            (t && t.removeChild(e),
                            _(e),
                            (function e(t, n) {
                                if (t.nodeType === f)
                                    for (var i = t.firstChild; i; ) {
                                        var o = void 0;
                                        n && (o = s(i)) ? T(o) : (_(i), i.firstChild && e(i, n)), (i = i.nextSibling);
                                    }
                            })(e, n));
                    }
                    function M(e) {
                        d(e);
                        for (var t = e.firstChild; t; ) {
                            var n = t.nextSibling,
                                i = s(t);
                            if (i) {
                                var o = D[i];
                                o && u(t, o) && (t.parentNode.replaceChild(o, t), O(o, t));
                            }
                            M(t), (t = n);
                        }
                    }
                    function O(t, n, i) {
                        var o = s(n);
                        if ((o && delete D[o], !i)) {
                            if (!1 === b(t, n)) return;
                            if ((e(t, n), w(t), !1 === C(t, n))) return;
                        }
                        'TEXTAREA' !== t.nodeName
                            ? (function(e, t) {
                                  var n,
                                      i,
                                      o,
                                      r,
                                      l,
                                      d = t.firstChild,
                                      p = e.firstChild;
                                  e: for (; d; ) {
                                      for (r = d.nextSibling, n = s(d); p; ) {
                                          if (((o = p.nextSibling), d.isSameNode && d.isSameNode(p))) {
                                              (d = r), (p = o);
                                              continue e;
                                          }
                                          i = s(p);
                                          var g = p.nodeType,
                                              y = void 0;
                                          if (
                                              (g === d.nodeType &&
                                                  (g === f
                                                      ? (n
                                                            ? n !== i &&
                                                              ((l = D[n])
                                                                  ? o === l
                                                                      ? (y = !1)
                                                                      : (e.insertBefore(l, p),
                                                                        i ? T(i) : L(p, e, !0),
                                                                        (p = l))
                                                                  : (y = !1))
                                                            : i && (y = !1),
                                                        (y = !1 !== y && u(p, d)) && O(p, d))
                                                      : (g !== m && g != v) ||
                                                        ((y = !0),
                                                        p.nodeValue !== d.nodeValue && (p.nodeValue = d.nodeValue))),
                                              y)
                                          ) {
                                              (d = r), (p = o);
                                              continue e;
                                          }
                                          i ? T(i) : L(p, e, !0), (p = o);
                                      }
                                      if (n && (l = D[n]) && u(l, d)) e.appendChild(l), O(l, d);
                                      else {
                                          var b = c(d);
                                          !1 !== b &&
                                              (b && (d = b),
                                              d.actualize && (d = d.actualize(e.ownerDocument || a)),
                                              e.appendChild(d),
                                              M(d));
                                      }
                                      (d = r), (p = o);
                                  }
                                  !(function(e, t, n) {
                                      for (; t; ) {
                                          var i = t.nextSibling;
                                          (n = s(t)) ? T(n) : L(t, e, !0), (t = i);
                                      }
                                  })(e, p, i);
                                  var w = h[e.nodeName];
                                  w && w(e, t);
                              })(t, n)
                            : h.TEXTAREA(t, n);
                    }
                    !(function e(t) {
                        if (t.nodeType === f || t.nodeType === p)
                            for (var n = t.firstChild; n; ) {
                                var i = s(n);
                                i && (D[i] = n), e(n), (n = n.nextSibling);
                            }
                    })(t);
                    var x,
                        A,
                        k = t,
                        P = k.nodeType,
                        N = n.nodeType;
                    if (!S)
                        if (P === f)
                            N === f
                                ? u(t, n) ||
                                  (_(t),
                                  (k = (function(e, t) {
                                      for (var n = e.firstChild; n; ) {
                                          var i = n.nextSibling;
                                          t.appendChild(n), (n = i);
                                      }
                                      return t;
                                  })(
                                      t,
                                      ((x = n.nodeName),
                                      (A = n.namespaceURI) && A !== r ? a.createElementNS(A, x) : a.createElement(x))
                                  )))
                                : (k = n);
                        else if (P === m || P === v) {
                            if (N === P) return k.nodeValue !== n.nodeValue && (k.nodeValue = n.nodeValue), k;
                            k = n;
                        }
                    if (k === n) _(t);
                    else {
                        if (n.isSameNode && n.isSameNode(k)) return;
                        if ((O(k, n, S), I))
                            for (var F = 0, j = I.length; F < j; F++) {
                                var R = D[I[F]];
                                R && L(R, R.parentNode, !1);
                            }
                    }
                    return (
                        !S &&
                            k !== t &&
                            t.parentNode &&
                            (k.actualize && (k = k.actualize(t.ownerDocument || a)), t.parentNode.replaceChild(k, t)),
                        k
                    );
                };
            })(function(e, t) {
                var n,
                    i,
                    r,
                    a,
                    s = t.attributes;
                if (t.nodeType !== o && e.nodeType !== o) {
                    for (var c = 0; c < s.length; c++)
                        (i = (n = s[c]).name),
                            (r = n.namespaceURI),
                            (a = n.value),
                            r
                                ? ((i = n.localName || i),
                                  e.getAttributeNS(r, i) !== a &&
                                      ('xmlns' === n.prefix && (i = n.name), e.setAttributeNS(r, i, a)))
                                : e.getAttribute(i) !== a && e.setAttribute(i, a);
                    for (var l = e.attributes, u = 0; u < l.length; u++)
                        (i = (n = l[u]).name),
                            (r = n.namespaceURI)
                                ? ((i = n.localName || i), t.hasAttributeNS(r, i) || e.removeAttributeNS(r, i))
                                : t.hasAttribute(i) || e.removeAttribute(i);
                }
            }),
            E = n(0);
        document.addEventListener('turbolinks:load', function() {
            clearInterval(b),
                (b = setInterval(function() {
                    var e,
                        t = Object(E.b)('[data-poll]');
                    t.length &&
                        (t.forEach(function(e) {
                            if (!e.id) throw new Error("You can't poll an element that doesn't have an ID");
                        }),
                        (e = t),
                        fetch('')
                            .then(function(e) {
                                return e.text();
                            })
                            .then(function(t) {
                                var n = new DOMParser().parseFromString(t, 'text/html');
                                e.forEach(function(e) {
                                    var t = n.getElementById(e.id);
                                    document.body.contains(e) && t && w(e, t);
                                });
                            }));
                }, 5e3));
        });
    },
    function(e, t) {},
]);
