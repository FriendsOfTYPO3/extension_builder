/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  YAHOO.util.Config = function (D) {
    if (D) {
      this.init(D);
    }
  };
  var B = YAHOO.lang,
    C = YAHOO.util.CustomEvent,
    A = YAHOO.util.Config;
  A.CONFIG_CHANGED_EVENT = "configChanged";
  A.BOOLEAN_TYPE = "boolean";
  A.prototype = {
    owner: null,
    queueInProgress: false,
    config: null,
    initialConfig: null,
    eventQueue: null,
    configChangedEvent: null,
    init: function (D) {
      this.owner = D;
      this.configChangedEvent = this.createEvent(A.CONFIG_CHANGED_EVENT);
      this.configChangedEvent.signature = C.LIST;
      this.queueInProgress = false;
      this.config = {};
      this.initialConfig = {};
      this.eventQueue = [];
    },
    checkBoolean: function (D) {
      return typeof D == A.BOOLEAN_TYPE;
    },
    checkNumber: function (D) {
      return !isNaN(D);
    },
    fireEvent: function (D, F) {
      var E = this.config[D];
      if (E && E.event) {
        E.event.fire(F);
      }
    },
    addProperty: function (E, D) {
      E = E.toLowerCase();
      this.config[E] = D;
      D.event = this.createEvent(E, { scope: this.owner });
      D.event.signature = C.LIST;
      D.key = E;
      if (D.handler) {
        D.event.subscribe(D.handler, this.owner);
      }
      this.setProperty(E, D.value, true);
      if (!D.suppressEvent) {
        this.queueProperty(E, D.value);
      }
    },
    getConfig: function () {
      var D = {},
        F = this.config,
        G,
        E;
      for (G in F) {
        if (B.hasOwnProperty(F, G)) {
          E = F[G];
          if (E && E.event) {
            D[G] = E.value;
          }
        }
      }
      return D;
    },
    getProperty: function (D) {
      var E = this.config[D.toLowerCase()];
      if (E && E.event) {
        return E.value;
      } else {
        return undefined;
      }
    },
    resetProperty: function (D) {
      D = D.toLowerCase();
      var E = this.config[D];
      if (E && E.event) {
        if (this.initialConfig[D] && !B.isUndefined(this.initialConfig[D])) {
          this.setProperty(D, this.initialConfig[D]);
          return true;
        }
      } else {
        return false;
      }
    },
    setProperty: function (E, G, D) {
      var F;
      E = E.toLowerCase();
      if (this.queueInProgress && !D) {
        this.queueProperty(E, G);
        return true;
      } else {
        F = this.config[E];
        if (F && F.event) {
          if (F.validator && !F.validator(G)) {
            return false;
          } else {
            F.value = G;
            if (!D) {
              this.fireEvent(E, G);
              this.configChangedEvent.fire([E, G]);
            }
            return true;
          }
        } else {
          return false;
        }
      }
    },
    queueProperty: function (S, P) {
      S = S.toLowerCase();
      var R = this.config[S],
        K = false,
        J,
        G,
        H,
        I,
        O,
        Q,
        F,
        M,
        N,
        D,
        L,
        T,
        E;
      if (R && R.event) {
        if (!B.isUndefined(P) && R.validator && !R.validator(P)) {
          return false;
        } else {
          if (!B.isUndefined(P)) {
            R.value = P;
          } else {
            P = R.value;
          }
          K = false;
          J = this.eventQueue.length;
          for (L = 0; L < J; L++) {
            G = this.eventQueue[L];
            if (G) {
              H = G[0];
              I = G[1];
              if (H == S) {
                this.eventQueue[L] = null;
                this.eventQueue.push([S, !B.isUndefined(P) ? P : I]);
                K = true;
                break;
              }
            }
          }
          if (!K && !B.isUndefined(P)) {
            this.eventQueue.push([S, P]);
          }
        }
        if (R.supercedes) {
          O = R.supercedes.length;
          for (T = 0; T < O; T++) {
            Q = R.supercedes[T];
            F = this.eventQueue.length;
            for (E = 0; E < F; E++) {
              M = this.eventQueue[E];
              if (M) {
                N = M[0];
                D = M[1];
                if (N == Q.toLowerCase()) {
                  this.eventQueue.push([N, D]);
                  this.eventQueue[E] = null;
                  break;
                }
              }
            }
          }
        }
        return true;
      } else {
        return false;
      }
    },
    refireEvent: function (D) {
      D = D.toLowerCase();
      var E = this.config[D];
      if (E && E.event && !B.isUndefined(E.value)) {
        if (this.queueInProgress) {
          this.queueProperty(D);
        } else {
          this.fireEvent(D, E.value);
        }
      }
    },
    applyConfig: function (D, G) {
      var F, E;
      if (G) {
        E = {};
        for (F in D) {
          if (B.hasOwnProperty(D, F)) {
            E[F.toLowerCase()] = D[F];
          }
        }
        this.initialConfig = E;
      }
      for (F in D) {
        if (B.hasOwnProperty(D, F)) {
          this.queueProperty(F, D[F]);
        }
      }
    },
    refresh: function () {
      var D;
      for (D in this.config) {
        if (B.hasOwnProperty(this.config, D)) {
          this.refireEvent(D);
        }
      }
    },
    fireQueue: function () {
      var E, H, D, G, F;
      this.queueInProgress = true;
      for (E = 0; E < this.eventQueue.length; E++) {
        H = this.eventQueue[E];
        if (H) {
          D = H[0];
          G = H[1];
          F = this.config[D];
          F.value = G;
          this.eventQueue[E] = null;
          this.fireEvent(D, G);
        }
      }
      this.queueInProgress = false;
      this.eventQueue = [];
    },
    subscribeToConfigEvent: function (E, F, H, D) {
      var G = this.config[E.toLowerCase()];
      if (G && G.event) {
        if (!A.alreadySubscribed(G.event, F, H)) {
          G.event.subscribe(F, H, D);
        }
        return true;
      } else {
        return false;
      }
    },
    unsubscribeFromConfigEvent: function (D, E, G) {
      var F = this.config[D.toLowerCase()];
      if (F && F.event) {
        return F.event.unsubscribe(E, G);
      } else {
        return false;
      }
    },
    toString: function () {
      var D = "Config";
      if (this.owner) {
        D += " [" + this.owner.toString() + "]";
      }
      return D;
    },
    outputEventQueue: function () {
      var D = "",
        G,
        E,
        F = this.eventQueue.length;
      for (E = 0; E < F; E++) {
        G = this.eventQueue[E];
        if (G) {
          D += G[0] + "=" + G[1] + ", ";
        }
      }
      return D;
    },
    destroy: function () {
      var E = this.config,
        D,
        F;
      for (D in E) {
        if (B.hasOwnProperty(E, D)) {
          F = E[D];
          F.event.unsubscribeAll();
          F.event = null;
        }
      }
      this.configChangedEvent.unsubscribeAll();
      this.configChangedEvent = null;
      this.owner = null;
      this.config = null;
      this.initialConfig = null;
      this.eventQueue = null;
    },
  };
  A.alreadySubscribed = function (E, H, I) {
    var F = E.subscribers.length,
      D,
      G;
    if (F > 0) {
      G = F - 1;
      do {
        D = E.subscribers[G];
        if (D && D.obj == I && D.fn == H) {
          return true;
        }
      } while (G--);
    }
    return false;
  };
  YAHOO.lang.augmentProto(A, YAHOO.util.EventProvider);
})();
(function () {
  YAHOO.widget.Module = function (R, Q) {
    if (R) {
      this.init(R, Q);
    } else {
    }
  };
  var F = YAHOO.util.Dom,
    D = YAHOO.util.Config,
    N = YAHOO.util.Event,
    M = YAHOO.util.CustomEvent,
    G = YAHOO.widget.Module,
    I = YAHOO.env.ua,
    H,
    P,
    O,
    E,
    A = {
      BEFORE_INIT: "beforeInit",
      INIT: "init",
      APPEND: "append",
      BEFORE_RENDER: "beforeRender",
      RENDER: "render",
      CHANGE_HEADER: "changeHeader",
      CHANGE_BODY: "changeBody",
      CHANGE_FOOTER: "changeFooter",
      CHANGE_CONTENT: "changeContent",
      DESTORY: "destroy",
      BEFORE_SHOW: "beforeShow",
      SHOW: "show",
      BEFORE_HIDE: "beforeHide",
      HIDE: "hide",
    },
    J = {
      VISIBLE: { key: "visible", value: true, validator: YAHOO.lang.isBoolean },
      EFFECT: { key: "effect", suppressEvent: true, supercedes: ["visible"] },
      MONITOR_RESIZE: { key: "monitorresize", value: true },
      APPEND_TO_DOCUMENT_BODY: { key: "appendtodocumentbody", value: false },
    };
  G.IMG_ROOT = null;
  G.IMG_ROOT_SSL = null;
  G.CSS_MODULE = "yui-module";
  G.CSS_HEADER = "hd";
  G.CSS_BODY = "bd";
  G.CSS_FOOTER = "ft";
  G.RESIZE_MONITOR_SECURE_URL = "javascript:false;";
  G.RESIZE_MONITOR_BUFFER = 1;
  G.textResizeEvent = new M("textResize");
  G.forceDocumentRedraw = function () {
    var Q = document.documentElement;
    if (Q) {
      Q.className += " ";
      Q.className = YAHOO.lang.trim(Q.className);
    }
  };

  function L() {
    if (!H) {
      H = document.createElement("div");
      H.innerHTML =
        '<div class="' +
        G.CSS_HEADER +
        '"></div>' +
        '<div class="' +
        G.CSS_BODY +
        '"></div><div class="' +
        G.CSS_FOOTER +
        '"></div>';
      P = H.firstChild;
      O = P.nextSibling;
      E = O.nextSibling;
    }
    return H;
  }

  function K() {
    if (!P) {
      L();
    }
    return P.cloneNode(false);
  }

  function B() {
    if (!O) {
      L();
    }
    return O.cloneNode(false);
  }

  function C() {
    if (!E) {
      L();
    }
    return E.cloneNode(false);
  }

  G.prototype = {
    constructor: G,
    element: null,
    header: null,
    body: null,
    footer: null,
    id: null,
    imageRoot: G.IMG_ROOT,
    initEvents: function () {
      var Q = M.LIST;
      this.beforeInitEvent = this.createEvent(A.BEFORE_INIT);
      this.beforeInitEvent.signature = Q;
      this.initEvent = this.createEvent(A.INIT);
      this.initEvent.signature = Q;
      this.appendEvent = this.createEvent(A.APPEND);
      this.appendEvent.signature = Q;
      this.beforeRenderEvent = this.createEvent(A.BEFORE_RENDER);
      this.beforeRenderEvent.signature = Q;
      this.renderEvent = this.createEvent(A.RENDER);
      this.renderEvent.signature = Q;
      this.changeHeaderEvent = this.createEvent(A.CHANGE_HEADER);
      this.changeHeaderEvent.signature = Q;
      this.changeBodyEvent = this.createEvent(A.CHANGE_BODY);
      this.changeBodyEvent.signature = Q;
      this.changeFooterEvent = this.createEvent(A.CHANGE_FOOTER);
      this.changeFooterEvent.signature = Q;
      this.changeContentEvent = this.createEvent(A.CHANGE_CONTENT);
      this.changeContentEvent.signature = Q;
      this.destroyEvent = this.createEvent(A.DESTORY);
      this.destroyEvent.signature = Q;
      this.beforeShowEvent = this.createEvent(A.BEFORE_SHOW);
      this.beforeShowEvent.signature = Q;
      this.showEvent = this.createEvent(A.SHOW);
      this.showEvent.signature = Q;
      this.beforeHideEvent = this.createEvent(A.BEFORE_HIDE);
      this.beforeHideEvent.signature = Q;
      this.hideEvent = this.createEvent(A.HIDE);
      this.hideEvent.signature = Q;
    },
    platform: (function () {
      var Q = navigator.userAgent.toLowerCase();
      if (Q.indexOf("windows") != -1 || Q.indexOf("win32") != -1) {
        return "windows";
      } else {
        if (Q.indexOf("macintosh") != -1) {
          return "mac";
        } else {
          return false;
        }
      }
    })(),
    browser: (function () {
      var Q = navigator.userAgent.toLowerCase();
      if (Q.indexOf("opera") != -1) {
        return "opera";
      } else {
        if (Q.indexOf("msie 7") != -1) {
          return "ie7";
        } else {
          if (Q.indexOf("msie") != -1) {
            return "ie";
          } else {
            if (Q.indexOf("safari") != -1) {
              return "safari";
            } else {
              if (Q.indexOf("gecko") != -1) {
                return "gecko";
              } else {
                return false;
              }
            }
          }
        }
      }
    })(),
    isSecure: (function () {
      if (window.location.href.toLowerCase().indexOf("https") === 0) {
        return true;
      } else {
        return false;
      }
    })(),
    initDefaultConfig: function () {
      this.cfg.addProperty(J.VISIBLE.key, {
        handler: this.configVisible,
        value: J.VISIBLE.value,
        validator: J.VISIBLE.validator,
      });
      this.cfg.addProperty(J.EFFECT.key, {
        suppressEvent: J.EFFECT.suppressEvent,
        supercedes: J.EFFECT.supercedes,
      });
      this.cfg.addProperty(J.MONITOR_RESIZE.key, {
        handler: this.configMonitorResize,
        value: J.MONITOR_RESIZE.value,
      });
      this.cfg.addProperty(J.APPEND_TO_DOCUMENT_BODY.key, {
        value: J.APPEND_TO_DOCUMENT_BODY.value,
      });
    },
    init: function (V, U) {
      var S, W;
      this.initEvents();
      this.beforeInitEvent.fire(G);
      this.cfg = new D(this);
      if (this.isSecure) {
        this.imageRoot = G.IMG_ROOT_SSL;
      }
      if (typeof V == "string") {
        S = V;
        V = document.getElementById(V);
        if (!V) {
          V = L().cloneNode(false);
          V.id = S;
        }
      }
      this.id = F.generateId(V);
      this.element = V;
      W = this.element.firstChild;
      if (W) {
        var R = false,
          Q = false,
          T = false;
        do {
          if (1 == W.nodeType) {
            if (!R && F.hasClass(W, G.CSS_HEADER)) {
              this.header = W;
              R = true;
            } else {
              if (!Q && F.hasClass(W, G.CSS_BODY)) {
                this.body = W;
                Q = true;
              } else {
                if (!T && F.hasClass(W, G.CSS_FOOTER)) {
                  this.footer = W;
                  T = true;
                }
              }
            }
          }
        } while ((W = W.nextSibling));
      }
      this.initDefaultConfig();
      F.addClass(this.element, G.CSS_MODULE);
      if (U) {
        this.cfg.applyConfig(U, true);
      }
      if (
        !D.alreadySubscribed(this.renderEvent, this.cfg.fireQueue, this.cfg)
      ) {
        this.renderEvent.subscribe(this.cfg.fireQueue, this.cfg, true);
      }
      this.initEvent.fire(G);
    },
    initResizeMonitor: function () {
      var R = I.gecko && this.platform == "windows";
      if (R) {
        var Q = this;
        setTimeout(function () {
          Q._initResizeMonitor();
        }, 0);
      } else {
        this._initResizeMonitor();
      }
    },
    _initResizeMonitor: function () {
      var Q, S, U;

      function W() {
        G.textResizeEvent.fire();
      }

      if (!I.opera) {
        S = F.get("_yuiResizeMonitor");
        var V = this._supportsCWResize();
        if (!S) {
          S = document.createElement("iframe");
          if (this.isSecure && G.RESIZE_MONITOR_SECURE_URL && I.ie) {
            S.src = G.RESIZE_MONITOR_SECURE_URL;
          }
          if (!V) {
            U = [
              "<html><head><script ",
              'type="text/javascript">',
              "window.onresize=function(){window.parent.",
              "YAHOO.widget.Module.textResizeEvent.",
              "fire();};<",
              "/script></head>",
              "<body></body></html>",
            ].join("");
            S.src = "data:text/html;charset=utf-8," + encodeURIComponent(U);
          }
          S.id = "_yuiResizeMonitor";
          S.title = "Text Resize Monitor";
          S.style.position = "absolute";
          S.style.visibility = "hidden";
          var R = document.body,
            T = R.firstChild;
          if (T) {
            R.insertBefore(S, T);
          } else {
            R.appendChild(S);
          }
          S.style.width = "2em";
          S.style.height = "2em";
          S.style.top = -1 * (S.offsetHeight + G.RESIZE_MONITOR_BUFFER) + "px";
          S.style.left = "0";
          S.style.borderWidth = "0";
          S.style.visibility = "visible";
          if (I.webkit) {
            Q = S.contentWindow.document;
            Q.open();
            Q.close();
          }
        }
        if (S && S.contentWindow) {
          G.textResizeEvent.subscribe(this.onDomResize, this, true);
          if (!G.textResizeInitialized) {
            if (V) {
              if (!N.on(S.contentWindow, "resize", W)) {
                N.on(S, "resize", W);
              }
            }
            G.textResizeInitialized = true;
          }
          this.resizeMonitor = S;
        }
      }
    },
    _supportsCWResize: function () {
      var Q = true;
      if (I.gecko && I.gecko <= 1.8) {
        Q = false;
      }
      return Q;
    },
    onDomResize: function (S, R) {
      var Q = -1 * (this.resizeMonitor.offsetHeight + G.RESIZE_MONITOR_BUFFER);
      this.resizeMonitor.style.top = Q + "px";
      this.resizeMonitor.style.left = "0";
    },
    setHeader: function (R) {
      var Q = this.header || (this.header = K());
      if (R.nodeName) {
        Q.innerHTML = "";
        Q.appendChild(R);
      } else {
        Q.innerHTML = R;
      }
      this.changeHeaderEvent.fire(R);
      this.changeContentEvent.fire();
    },
    appendToHeader: function (R) {
      var Q = this.header || (this.header = K());
      Q.appendChild(R);
      this.changeHeaderEvent.fire(R);
      this.changeContentEvent.fire();
    },
    setBody: function (R) {
      var Q = this.body || (this.body = B());
      if (R.nodeName) {
        Q.innerHTML = "";
        Q.appendChild(R);
      } else {
        Q.innerHTML = R;
      }
      this.changeBodyEvent.fire(R);
      this.changeContentEvent.fire();
    },
    appendToBody: function (R) {
      var Q = this.body || (this.body = B());
      Q.appendChild(R);
      this.changeBodyEvent.fire(R);
      this.changeContentEvent.fire();
    },
    setFooter: function (R) {
      var Q = this.footer || (this.footer = C());
      if (R.nodeName) {
        Q.innerHTML = "";
        Q.appendChild(R);
      } else {
        Q.innerHTML = R;
      }
      this.changeFooterEvent.fire(R);
      this.changeContentEvent.fire();
    },
    appendToFooter: function (R) {
      var Q = this.footer || (this.footer = C());
      Q.appendChild(R);
      this.changeFooterEvent.fire(R);
      this.changeContentEvent.fire();
    },
    render: function (S, Q) {
      var T = this,
        U;

      function R(V) {
        if (typeof V == "string") {
          V = document.getElementById(V);
        }
        if (V) {
          T._addToParent(V, T.element);
          T.appendEvent.fire();
        }
      }

      this.beforeRenderEvent.fire();
      if (!Q) {
        Q = this.element;
      }
      if (S) {
        R(S);
      } else {
        if (!F.inDocument(this.element)) {
          return false;
        }
      }
      if (this.header && !F.inDocument(this.header)) {
        U = Q.firstChild;
        if (U) {
          Q.insertBefore(this.header, U);
        } else {
          Q.appendChild(this.header);
        }
      }
      if (this.body && !F.inDocument(this.body)) {
        if (this.footer && F.isAncestor(this.moduleElement, this.footer)) {
          Q.insertBefore(this.body, this.footer);
        } else {
          Q.appendChild(this.body);
        }
      }
      if (this.footer && !F.inDocument(this.footer)) {
        Q.appendChild(this.footer);
      }
      this.renderEvent.fire();
      return true;
    },
    destroy: function () {
      var Q;
      if (this.element) {
        N.purgeElement(this.element, true);
        Q = this.element.parentNode;
      }
      if (Q) {
        Q.removeChild(this.element);
      }
      this.element = null;
      this.header = null;
      this.body = null;
      this.footer = null;
      G.textResizeEvent.unsubscribe(this.onDomResize, this);
      this.cfg.destroy();
      this.cfg = null;
      this.destroyEvent.fire();
    },
    show: function () {
      this.cfg.setProperty("visible", true);
    },
    hide: function () {
      this.cfg.setProperty("visible", false);
    },
    configVisible: function (R, Q, S) {
      var T = Q[0];
      if (T) {
        this.beforeShowEvent.fire();
        F.setStyle(this.element, "display", "block");
        this.showEvent.fire();
      } else {
        this.beforeHideEvent.fire();
        F.setStyle(this.element, "display", "none");
        this.hideEvent.fire();
      }
    },
    configMonitorResize: function (S, R, T) {
      var Q = R[0];
      if (Q) {
        this.initResizeMonitor();
      } else {
        G.textResizeEvent.unsubscribe(this.onDomResize, this, true);
        this.resizeMonitor = null;
      }
    },
    _addToParent: function (Q, R) {
      if (
        !this.cfg.getProperty("appendtodocumentbody") &&
        Q === document.body &&
        Q.firstChild
      ) {
        Q.insertBefore(R, Q.firstChild);
      } else {
        Q.appendChild(R);
      }
    },
    toString: function () {
      return "Module " + this.id;
    },
  };
  YAHOO.lang.augmentProto(G, YAHOO.util.EventProvider);
})();
(function () {
  YAHOO.widget.Overlay = function (P, O) {
    YAHOO.widget.Overlay.superclass.constructor.call(this, P, O);
  };
  var I = YAHOO.lang,
    M = YAHOO.util.CustomEvent,
    G = YAHOO.widget.Module,
    N = YAHOO.util.Event,
    F = YAHOO.util.Dom,
    D = YAHOO.util.Config,
    K = YAHOO.env.ua,
    B = YAHOO.widget.Overlay,
    H = "subscribe",
    E = "unsubscribe",
    C = "contained",
    J,
    A = { BEFORE_MOVE: "beforeMove", MOVE: "move" },
    L = {
      X: {
        key: "x",
        validator: I.isNumber,
        suppressEvent: true,
        supercedes: ["iframe"],
      },
      Y: {
        key: "y",
        validator: I.isNumber,
        suppressEvent: true,
        supercedes: ["iframe"],
      },
      XY: { key: "xy", suppressEvent: true, supercedes: ["iframe"] },
      CONTEXT: { key: "context", suppressEvent: true, supercedes: ["iframe"] },
      FIXED_CENTER: {
        key: "fixedcenter",
        value: false,
        supercedes: ["iframe", "visible"],
      },
      WIDTH: {
        key: "width",
        suppressEvent: true,
        supercedes: ["context", "fixedcenter", "iframe"],
      },
      HEIGHT: {
        key: "height",
        suppressEvent: true,
        supercedes: ["context", "fixedcenter", "iframe"],
      },
      AUTO_FILL_HEIGHT: {
        key: "autofillheight",
        supercedes: ["height"],
        value: "body",
      },
      ZINDEX: { key: "zindex", value: null },
      CONSTRAIN_TO_VIEWPORT: {
        key: "constraintoviewport",
        value: false,
        validator: I.isBoolean,
        supercedes: ["iframe", "x", "y", "xy"],
      },
      IFRAME: {
        key: "iframe",
        value: K.ie == 6 ? true : false,
        validator: I.isBoolean,
        supercedes: ["zindex"],
      },
      PREVENT_CONTEXT_OVERLAP: {
        key: "preventcontextoverlap",
        value: false,
        validator: I.isBoolean,
        supercedes: ["constraintoviewport"],
      },
    };
  B.IFRAME_SRC = "javascript:false;";
  B.IFRAME_OFFSET = 3;
  B.VIEWPORT_OFFSET = 10;
  B.TOP_LEFT = "tl";
  B.TOP_RIGHT = "tr";
  B.BOTTOM_LEFT = "bl";
  B.BOTTOM_RIGHT = "br";
  B.CSS_OVERLAY = "yui-overlay";
  B.STD_MOD_RE = /^\s*?(body|footer|header)\s*?$/i;
  B.windowScrollEvent = new M("windowScroll");
  B.windowResizeEvent = new M("windowResize");
  B.windowScrollHandler = function (P) {
    var O = N.getTarget(P);
    if (!O || O === window || O === window.document) {
      if (K.ie) {
        if (!window.scrollEnd) {
          window.scrollEnd = -1;
        }
        clearTimeout(window.scrollEnd);
        window.scrollEnd = setTimeout(function () {
          B.windowScrollEvent.fire();
        }, 1);
      } else {
        B.windowScrollEvent.fire();
      }
    }
  };
  B.windowResizeHandler = function (O) {
    if (K.ie) {
      if (!window.resizeEnd) {
        window.resizeEnd = -1;
      }
      clearTimeout(window.resizeEnd);
      window.resizeEnd = setTimeout(function () {
        B.windowResizeEvent.fire();
      }, 100);
    } else {
      B.windowResizeEvent.fire();
    }
  };
  B._initialized = null;
  if (B._initialized === null) {
    N.on(window, "scroll", B.windowScrollHandler);
    N.on(window, "resize", B.windowResizeHandler);
    B._initialized = true;
  }
  B._TRIGGER_MAP = {
    windowScroll: B.windowScrollEvent,
    windowResize: B.windowResizeEvent,
    textResize: G.textResizeEvent,
  };
  YAHOO.extend(B, G, {
    CONTEXT_TRIGGERS: [],
    init: function (P, O) {
      B.superclass.init.call(this, P);
      this.beforeInitEvent.fire(B);
      F.addClass(this.element, B.CSS_OVERLAY);
      if (O) {
        this.cfg.applyConfig(O, true);
      }
      if (this.platform == "mac" && K.gecko) {
        if (
          !D.alreadySubscribed(
            this.showEvent,
            this.showMacGeckoScrollbars,
            this,
          )
        ) {
          this.showEvent.subscribe(this.showMacGeckoScrollbars, this, true);
        }
        if (
          !D.alreadySubscribed(
            this.hideEvent,
            this.hideMacGeckoScrollbars,
            this,
          )
        ) {
          this.hideEvent.subscribe(this.hideMacGeckoScrollbars, this, true);
        }
      }
      this.initEvent.fire(B);
    },
    initEvents: function () {
      B.superclass.initEvents.call(this);
      var O = M.LIST;
      this.beforeMoveEvent = this.createEvent(A.BEFORE_MOVE);
      this.beforeMoveEvent.signature = O;
      this.moveEvent = this.createEvent(A.MOVE);
      this.moveEvent.signature = O;
    },
    initDefaultConfig: function () {
      B.superclass.initDefaultConfig.call(this);
      var O = this.cfg;
      O.addProperty(L.X.key, {
        handler: this.configX,
        validator: L.X.validator,
        suppressEvent: L.X.suppressEvent,
        supercedes: L.X.supercedes,
      });
      O.addProperty(L.Y.key, {
        handler: this.configY,
        validator: L.Y.validator,
        suppressEvent: L.Y.suppressEvent,
        supercedes: L.Y.supercedes,
      });
      O.addProperty(L.XY.key, {
        handler: this.configXY,
        suppressEvent: L.XY.suppressEvent,
        supercedes: L.XY.supercedes,
      });
      O.addProperty(L.CONTEXT.key, {
        handler: this.configContext,
        suppressEvent: L.CONTEXT.suppressEvent,
        supercedes: L.CONTEXT.supercedes,
      });
      O.addProperty(L.FIXED_CENTER.key, {
        handler: this.configFixedCenter,
        value: L.FIXED_CENTER.value,
        validator: L.FIXED_CENTER.validator,
        supercedes: L.FIXED_CENTER.supercedes,
      });
      O.addProperty(L.WIDTH.key, {
        handler: this.configWidth,
        suppressEvent: L.WIDTH.suppressEvent,
        supercedes: L.WIDTH.supercedes,
      });
      O.addProperty(L.HEIGHT.key, {
        handler: this.configHeight,
        suppressEvent: L.HEIGHT.suppressEvent,
        supercedes: L.HEIGHT.supercedes,
      });
      O.addProperty(L.AUTO_FILL_HEIGHT.key, {
        handler: this.configAutoFillHeight,
        value: L.AUTO_FILL_HEIGHT.value,
        validator: this._validateAutoFill,
        supercedes: L.AUTO_FILL_HEIGHT.supercedes,
      });
      O.addProperty(L.ZINDEX.key, {
        handler: this.configzIndex,
        value: L.ZINDEX.value,
      });
      O.addProperty(L.CONSTRAIN_TO_VIEWPORT.key, {
        handler: this.configConstrainToViewport,
        value: L.CONSTRAIN_TO_VIEWPORT.value,
        validator: L.CONSTRAIN_TO_VIEWPORT.validator,
        supercedes: L.CONSTRAIN_TO_VIEWPORT.supercedes,
      });
      O.addProperty(L.IFRAME.key, {
        handler: this.configIframe,
        value: L.IFRAME.value,
        validator: L.IFRAME.validator,
        supercedes: L.IFRAME.supercedes,
      });
      O.addProperty(L.PREVENT_CONTEXT_OVERLAP.key, {
        value: L.PREVENT_CONTEXT_OVERLAP.value,
        validator: L.PREVENT_CONTEXT_OVERLAP.validator,
        supercedes: L.PREVENT_CONTEXT_OVERLAP.supercedes,
      });
    },
    moveTo: function (O, P) {
      this.cfg.setProperty("xy", [O, P]);
    },
    hideMacGeckoScrollbars: function () {
      F.replaceClass(this.element, "show-scrollbars", "hide-scrollbars");
    },
    showMacGeckoScrollbars: function () {
      F.replaceClass(this.element, "hide-scrollbars", "show-scrollbars");
    },
    _setDomVisibility: function (O) {
      F.setStyle(this.element, "visibility", O ? "visible" : "hidden");
      if (O) {
        F.removeClass(this.element, "yui-overlay-hidden");
      } else {
        F.addClass(this.element, "yui-overlay-hidden");
      }
    },
    configVisible: function (R, O, X) {
      var Q = O[0],
        S = F.getStyle(this.element, "visibility"),
        Y = this.cfg.getProperty("effect"),
        V = [],
        U = this.platform == "mac" && K.gecko,
        g = D.alreadySubscribed,
        W,
        P,
        f,
        c,
        b,
        a,
        d,
        Z,
        T;
      if (S == "inherit") {
        f = this.element.parentNode;
        while (f.nodeType != 9 && f.nodeType != 11) {
          S = F.getStyle(f, "visibility");
          if (S != "inherit") {
            break;
          }
          f = f.parentNode;
        }
        if (S == "inherit") {
          S = "visible";
        }
      }
      if (Y) {
        if (Y instanceof Array) {
          Z = Y.length;
          for (c = 0; c < Z; c++) {
            W = Y[c];
            V[V.length] = W.effect(this, W.duration);
          }
        } else {
          V[V.length] = Y.effect(this, Y.duration);
        }
      }
      if (Q) {
        if (U) {
          this.showMacGeckoScrollbars();
        }
        if (Y) {
          if (Q) {
            if (S != "visible" || S === "") {
              this.beforeShowEvent.fire();
              T = V.length;
              for (b = 0; b < T; b++) {
                P = V[b];
                if (
                  b === 0 &&
                  !g(
                    P.animateInCompleteEvent,
                    this.showEvent.fire,
                    this.showEvent,
                  )
                ) {
                  P.animateInCompleteEvent.subscribe(
                    this.showEvent.fire,
                    this.showEvent,
                    true,
                  );
                }
                P.animateIn();
              }
            }
          }
        } else {
          if (S != "visible" || S === "") {
            this.beforeShowEvent.fire();
            this._setDomVisibility(true);
            this.cfg.refireEvent("iframe");
            this.showEvent.fire();
          } else {
            this._setDomVisibility(true);
          }
        }
      } else {
        if (U) {
          this.hideMacGeckoScrollbars();
        }
        if (Y) {
          if (S == "visible") {
            this.beforeHideEvent.fire();
            T = V.length;
            for (a = 0; a < T; a++) {
              d = V[a];
              if (
                a === 0 &&
                !g(
                  d.animateOutCompleteEvent,
                  this.hideEvent.fire,
                  this.hideEvent,
                )
              ) {
                d.animateOutCompleteEvent.subscribe(
                  this.hideEvent.fire,
                  this.hideEvent,
                  true,
                );
              }
              d.animateOut();
            }
          } else {
            if (S === "") {
              this._setDomVisibility(false);
            }
          }
        } else {
          if (S == "visible" || S === "") {
            this.beforeHideEvent.fire();
            this._setDomVisibility(false);
            this.hideEvent.fire();
          } else {
            this._setDomVisibility(false);
          }
        }
      }
    },
    doCenterOnDOMEvent: function () {
      var O = this.cfg,
        P = O.getProperty("fixedcenter");
      if (O.getProperty("visible")) {
        if (P && (P !== C || this.fitsInViewport())) {
          this.center();
        }
      }
    },
    fitsInViewport: function () {
      var S = B.VIEWPORT_OFFSET,
        Q = this.element,
        T = Q.offsetWidth,
        R = Q.offsetHeight,
        O = F.getViewportWidth(),
        P = F.getViewportHeight();
      return T + S < O && R + S < P;
    },
    configFixedCenter: function (S, Q, T) {
      var U = Q[0],
        P = D.alreadySubscribed,
        R = B.windowResizeEvent,
        O = B.windowScrollEvent;
      if (U) {
        this.center();
        if (!P(this.beforeShowEvent, this.center)) {
          this.beforeShowEvent.subscribe(this.center);
        }
        if (!P(R, this.doCenterOnDOMEvent, this)) {
          R.subscribe(this.doCenterOnDOMEvent, this, true);
        }
        if (!P(O, this.doCenterOnDOMEvent, this)) {
          O.subscribe(this.doCenterOnDOMEvent, this, true);
        }
      } else {
        this.beforeShowEvent.unsubscribe(this.center);
        R.unsubscribe(this.doCenterOnDOMEvent, this);
        O.unsubscribe(this.doCenterOnDOMEvent, this);
      }
    },
    configHeight: function (R, P, S) {
      var O = P[0],
        Q = this.element;
      F.setStyle(Q, "height", O);
      this.cfg.refireEvent("iframe");
    },
    configAutoFillHeight: function (T, S, P) {
      var V = S[0],
        Q = this.cfg,
        U = "autofillheight",
        W = "height",
        R = Q.getProperty(U),
        O = this._autoFillOnHeightChange;
      Q.unsubscribeFromConfigEvent(W, O);
      G.textResizeEvent.unsubscribe(O);
      this.changeContentEvent.unsubscribe(O);
      if (R && V !== R && this[R]) {
        F.setStyle(this[R], W, "");
      }
      if (V) {
        V = I.trim(V.toLowerCase());
        Q.subscribeToConfigEvent(W, O, this[V], this);
        G.textResizeEvent.subscribe(O, this[V], this);
        this.changeContentEvent.subscribe(O, this[V], this);
        Q.setProperty(U, V, true);
      }
    },
    configWidth: function (R, O, S) {
      var Q = O[0],
        P = this.element;
      F.setStyle(P, "width", Q);
      this.cfg.refireEvent("iframe");
    },
    configzIndex: function (Q, O, R) {
      var S = O[0],
        P = this.element;
      if (!S) {
        S = F.getStyle(P, "zIndex");
        if (!S || isNaN(S)) {
          S = 0;
        }
      }
      if (this.iframe || this.cfg.getProperty("iframe") === true) {
        if (S <= 0) {
          S = 1;
        }
      }
      F.setStyle(P, "zIndex", S);
      this.cfg.setProperty("zIndex", S, true);
      if (this.iframe) {
        this.stackIframe();
      }
    },
    configXY: function (Q, P, R) {
      var T = P[0],
        O = T[0],
        S = T[1];
      this.cfg.setProperty("x", O);
      this.cfg.setProperty("y", S);
      this.beforeMoveEvent.fire([O, S]);
      O = this.cfg.getProperty("x");
      S = this.cfg.getProperty("y");
      this.cfg.refireEvent("iframe");
      this.moveEvent.fire([O, S]);
    },
    configX: function (Q, P, R) {
      var O = P[0],
        S = this.cfg.getProperty("y");
      this.cfg.setProperty("x", O, true);
      this.cfg.setProperty("y", S, true);
      this.beforeMoveEvent.fire([O, S]);
      O = this.cfg.getProperty("x");
      S = this.cfg.getProperty("y");
      F.setX(this.element, O, true);
      this.cfg.setProperty("xy", [O, S], true);
      this.cfg.refireEvent("iframe");
      this.moveEvent.fire([O, S]);
    },
    configY: function (Q, P, R) {
      var O = this.cfg.getProperty("x"),
        S = P[0];
      this.cfg.setProperty("x", O, true);
      this.cfg.setProperty("y", S, true);
      this.beforeMoveEvent.fire([O, S]);
      O = this.cfg.getProperty("x");
      S = this.cfg.getProperty("y");
      F.setY(this.element, S, true);
      this.cfg.setProperty("xy", [O, S], true);
      this.cfg.refireEvent("iframe");
      this.moveEvent.fire([O, S]);
    },
    showIframe: function () {
      var P = this.iframe,
        O;
      if (P) {
        O = this.element.parentNode;
        if (O != P.parentNode) {
          this._addToParent(O, P);
        }
        P.style.display = "block";
      }
    },
    hideIframe: function () {
      if (this.iframe) {
        this.iframe.style.display = "none";
      }
    },
    syncIframe: function () {
      var O = this.iframe,
        Q = this.element,
        S = B.IFRAME_OFFSET,
        P = S * 2,
        R;
      if (O) {
        O.style.width = Q.offsetWidth + P + "px";
        O.style.height = Q.offsetHeight + P + "px";
        R = this.cfg.getProperty("xy");
        if (!I.isArray(R) || isNaN(R[0]) || isNaN(R[1])) {
          this.syncPosition();
          R = this.cfg.getProperty("xy");
        }
        F.setXY(O, [R[0] - S, R[1] - S]);
      }
    },
    stackIframe: function () {
      if (this.iframe) {
        var O = F.getStyle(this.element, "zIndex");
        if (!YAHOO.lang.isUndefined(O) && !isNaN(O)) {
          F.setStyle(this.iframe, "zIndex", O - 1);
        }
      }
    },
    configIframe: function (R, Q, S) {
      var O = Q[0];

      function T() {
        var V = this.iframe,
          W = this.element,
          X;
        if (!V) {
          if (!J) {
            J = document.createElement("iframe");
            if (this.isSecure) {
              J.src = B.IFRAME_SRC;
            }
            if (K.ie) {
              J.style.filter = "alpha(opacity=0)";
              J.frameBorder = 0;
            } else {
              J.style.opacity = "0";
            }
            J.style.position = "absolute";
            J.style.border = "none";
            J.style.margin = "0";
            J.style.padding = "0";
            J.style.display = "none";
            J.tabIndex = -1;
          }
          V = J.cloneNode(false);
          X = W.parentNode;
          var U = X || document.body;
          this._addToParent(U, V);
          this.iframe = V;
        }
        this.showIframe();
        this.syncIframe();
        this.stackIframe();
        if (!this._hasIframeEventListeners) {
          this.showEvent.subscribe(this.showIframe);
          this.hideEvent.subscribe(this.hideIframe);
          this.changeContentEvent.subscribe(this.syncIframe);
          this._hasIframeEventListeners = true;
        }
      }

      function P() {
        T.call(this);
        this.beforeShowEvent.unsubscribe(P);
        this._iframeDeferred = false;
      }

      if (O) {
        if (this.cfg.getProperty("visible")) {
          T.call(this);
        } else {
          if (!this._iframeDeferred) {
            this.beforeShowEvent.subscribe(P);
            this._iframeDeferred = true;
          }
        }
      } else {
        this.hideIframe();
        if (this._hasIframeEventListeners) {
          this.showEvent.unsubscribe(this.showIframe);
          this.hideEvent.unsubscribe(this.hideIframe);
          this.changeContentEvent.unsubscribe(this.syncIframe);
          this._hasIframeEventListeners = false;
        }
      }
    },
    _primeXYFromDOM: function () {
      if (YAHOO.lang.isUndefined(this.cfg.getProperty("xy"))) {
        this.syncPosition();
        this.cfg.refireEvent("xy");
        this.beforeShowEvent.unsubscribe(this._primeXYFromDOM);
      }
    },
    configConstrainToViewport: function (P, O, Q) {
      var R = O[0];
      if (R) {
        if (
          !D.alreadySubscribed(
            this.beforeMoveEvent,
            this.enforceConstraints,
            this,
          )
        ) {
          this.beforeMoveEvent.subscribe(this.enforceConstraints, this, true);
        }
        if (!D.alreadySubscribed(this.beforeShowEvent, this._primeXYFromDOM)) {
          this.beforeShowEvent.subscribe(this._primeXYFromDOM);
        }
      } else {
        this.beforeShowEvent.unsubscribe(this._primeXYFromDOM);
        this.beforeMoveEvent.unsubscribe(this.enforceConstraints, this);
      }
    },
    configContext: function (T, S, P) {
      var W = S[0],
        Q,
        O,
        U,
        R,
        V = this.CONTEXT_TRIGGERS;
      if (W) {
        Q = W[0];
        O = W[1];
        U = W[2];
        R = W[3];
        if (V && V.length > 0) {
          R = (R || []).concat(V);
        }
        if (Q) {
          if (typeof Q == "string") {
            this.cfg.setProperty(
              "context",
              [document.getElementById(Q), O, U, R],
              true,
            );
          }
          if (O && U) {
            this.align(O, U);
          }
          if (this._contextTriggers) {
            this._processTriggers(
              this._contextTriggers,
              E,
              this._alignOnTrigger,
            );
          }
          if (R) {
            this._processTriggers(R, H, this._alignOnTrigger);
            this._contextTriggers = R;
          }
        }
      }
    },
    _alignOnTrigger: function (P, O) {
      this.align();
    },
    _findTriggerCE: function (O) {
      var P = null;
      if (O instanceof M) {
        P = O;
      } else {
        if (B._TRIGGER_MAP[O]) {
          P = B._TRIGGER_MAP[O];
        }
      }
      return P;
    },
    _processTriggers: function (S, U, R) {
      var Q, T;
      for (var P = 0, O = S.length; P < O; ++P) {
        Q = S[P];
        T = this._findTriggerCE(Q);
        if (T) {
          T[U](R, this, true);
        } else {
          this[U](Q, R);
        }
      }
    },
    align: function (P, O) {
      var U = this.cfg.getProperty("context"),
        T = this,
        S,
        R,
        V;

      function Q(W, X) {
        switch (P) {
          case B.TOP_LEFT:
            T.moveTo(X, W);
            break;
          case B.TOP_RIGHT:
            T.moveTo(X - R.offsetWidth, W);
            break;
          case B.BOTTOM_LEFT:
            T.moveTo(X, W - R.offsetHeight);
            break;
          case B.BOTTOM_RIGHT:
            T.moveTo(X - R.offsetWidth, W - R.offsetHeight);
            break;
        }
      }

      if (U) {
        S = U[0];
        R = this.element;
        T = this;
        if (!P) {
          P = U[1];
        }
        if (!O) {
          O = U[2];
        }
        if (R && S) {
          V = F.getRegion(S);
          switch (O) {
            case B.TOP_LEFT:
              Q(V.top, V.left);
              break;
            case B.TOP_RIGHT:
              Q(V.top, V.right);
              break;
            case B.BOTTOM_LEFT:
              Q(V.bottom, V.left);
              break;
            case B.BOTTOM_RIGHT:
              Q(V.bottom, V.right);
              break;
          }
        }
      }
    },
    enforceConstraints: function (P, O, Q) {
      var S = O[0];
      var R = this.getConstrainedXY(S[0], S[1]);
      this.cfg.setProperty("x", R[0], true);
      this.cfg.setProperty("y", R[1], true);
      this.cfg.setProperty("xy", R, true);
    },
    getConstrainedX: function (V) {
      var S = this,
        O = S.element,
        e = O.offsetWidth,
        c = B.VIEWPORT_OFFSET,
        h = F.getViewportWidth(),
        d = F.getDocumentScrollLeft(),
        Y = e + c < h,
        b = this.cfg.getProperty("context"),
        Q,
        X,
        j,
        T = false,
        f,
        W,
        g = d + c,
        P = d + h - e - c,
        i = V,
        U = { tltr: true, blbr: true, brbl: true, trtl: true };
      var Z = function () {
        var k;
        if (S.cfg.getProperty("x") - d > X) {
          k = X - e;
        } else {
          k = X + j;
        }
        S.cfg.setProperty("x", k + d, true);
        return k;
      };
      var R = function () {
        if (S.cfg.getProperty("x") - d > X) {
          return W - c;
        } else {
          return f - c;
        }
      };
      var a = function () {
        var k = R(),
          l;
        if (e > k) {
          if (T) {
            Z();
          } else {
            Z();
            T = true;
            l = a();
          }
        }
        return l;
      };
      if (V < g || V > P) {
        if (Y) {
          if (
            this.cfg.getProperty("preventcontextoverlap") &&
            b &&
            U[b[1] + b[2]]
          ) {
            Q = b[0];
            X = F.getX(Q) - d;
            j = Q.offsetWidth;
            f = X;
            W = h - (X + j);
            a();
            i = this.cfg.getProperty("x");
          } else {
            if (V < g) {
              i = g;
            } else {
              if (V > P) {
                i = P;
              }
            }
          }
        } else {
          i = c + d;
        }
      }
      return i;
    },
    getConstrainedY: function (Z) {
      var W = this,
        P = W.element,
        i = P.offsetHeight,
        h = B.VIEWPORT_OFFSET,
        d = F.getViewportHeight(),
        g = F.getDocumentScrollTop(),
        e = i + h < d,
        f = this.cfg.getProperty("context"),
        U,
        a,
        b,
        X = false,
        V,
        Q,
        c = g + h,
        S = g + d - i - h,
        O = Z,
        Y = { trbr: true, tlbl: true, bltl: true, brtr: true };
      var T = function () {
        var k;
        if (W.cfg.getProperty("y") - g > a) {
          k = a - i;
        } else {
          k = a + b;
        }
        W.cfg.setProperty("y", k + g, true);
        return k;
      };
      var R = function () {
        if (W.cfg.getProperty("y") - g > a) {
          return Q - h;
        } else {
          return V - h;
        }
      };
      var j = function () {
        var l = R(),
          k;
        if (i > l) {
          if (X) {
            T();
          } else {
            T();
            X = true;
            k = j();
          }
        }
        return k;
      };
      if (Z < c || Z > S) {
        if (e) {
          if (
            this.cfg.getProperty("preventcontextoverlap") &&
            f &&
            Y[f[1] + f[2]]
          ) {
            U = f[0];
            b = U.offsetHeight;
            a = F.getY(U) - g;
            V = a;
            Q = d - (a + b);
            j();
            O = W.cfg.getProperty("y");
          } else {
            if (Z < c) {
              O = c;
            } else {
              if (Z > S) {
                O = S;
              }
            }
          }
        } else {
          O = h + g;
        }
      }
      return O;
    },
    getConstrainedXY: function (O, P) {
      return [this.getConstrainedX(O), this.getConstrainedY(P)];
    },
    center: function () {
      var R = B.VIEWPORT_OFFSET,
        S = this.element.offsetWidth,
        Q = this.element.offsetHeight,
        P = F.getViewportWidth(),
        T = F.getViewportHeight(),
        O,
        U;
      if (S < P) {
        O = P / 2 - S / 2 + F.getDocumentScrollLeft();
      } else {
        O = R + F.getDocumentScrollLeft();
      }
      if (Q < T) {
        U = T / 2 - Q / 2 + F.getDocumentScrollTop();
      } else {
        U = R + F.getDocumentScrollTop();
      }
      this.cfg.setProperty("xy", [parseInt(O, 10), parseInt(U, 10)]);
      this.cfg.refireEvent("iframe");
      if (K.webkit) {
        this.forceContainerRedraw();
      }
    },
    syncPosition: function () {
      var O = F.getXY(this.element);
      this.cfg.setProperty("x", O[0], true);
      this.cfg.setProperty("y", O[1], true);
      this.cfg.setProperty("xy", O, true);
    },
    onDomResize: function (Q, P) {
      var O = this;
      B.superclass.onDomResize.call(this, Q, P);
      setTimeout(function () {
        O.syncPosition();
        O.cfg.refireEvent("iframe");
        O.cfg.refireEvent("context");
      }, 0);
    },
    _getComputedHeight: (function () {
      if (document.defaultView && document.defaultView.getComputedStyle) {
        return function (P) {
          var O = null;
          if (P.ownerDocument && P.ownerDocument.defaultView) {
            var Q = P.ownerDocument.defaultView.getComputedStyle(P, "");
            if (Q) {
              O = parseInt(Q.height, 10);
            }
          }
          return I.isNumber(O) ? O : null;
        };
      } else {
        return function (P) {
          var O = null;
          if (P.style.pixelHeight) {
            O = P.style.pixelHeight;
          }
          return I.isNumber(O) ? O : null;
        };
      }
    })(),
    _validateAutoFillHeight: function (O) {
      return !O || (I.isString(O) && B.STD_MOD_RE.test(O));
    },
    _autoFillOnHeightChange: function (R, P, Q) {
      var O = this.cfg.getProperty("height");
      if ((O && O !== "auto") || O === 0) {
        this.fillHeight(Q);
      }
    },
    _getPreciseHeight: function (P) {
      var O = P.offsetHeight;
      if (P.getBoundingClientRect) {
        var Q = P.getBoundingClientRect();
        O = Q.bottom - Q.top;
      }
      return O;
    },
    fillHeight: function (R) {
      if (R) {
        var P = this.innerElement || this.element,
          O = [this.header, this.body, this.footer],
          V,
          W = 0,
          X = 0,
          T = 0,
          Q = false;
        for (var U = 0, S = O.length; U < S; U++) {
          V = O[U];
          if (V) {
            if (R !== V) {
              X += this._getPreciseHeight(V);
            } else {
              Q = true;
            }
          }
        }
        if (Q) {
          if (K.ie || K.opera) {
            F.setStyle(R, "height", 0 + "px");
          }
          W = this._getComputedHeight(P);
          if (W === null) {
            F.addClass(P, "yui-override-padding");
            W = P.clientHeight;
            F.removeClass(P, "yui-override-padding");
          }
          T = Math.max(W - X, 0);
          F.setStyle(R, "height", T + "px");
          if (R.offsetHeight != T) {
            T = Math.max(T - (R.offsetHeight - T), 0);
          }
          F.setStyle(R, "height", T + "px");
        }
      }
    },
    bringToTop: function () {
      var S = [],
        R = this.element;

      function V(Z, Y) {
        var b = F.getStyle(Z, "zIndex"),
          a = F.getStyle(Y, "zIndex"),
          X = !b || isNaN(b) ? 0 : parseInt(b, 10),
          W = !a || isNaN(a) ? 0 : parseInt(a, 10);
        if (X > W) {
          return -1;
        } else {
          if (X < W) {
            return 1;
          } else {
            return 0;
          }
        }
      }

      function Q(Y) {
        var X = F.hasClass(Y, B.CSS_OVERLAY),
          W = YAHOO.widget.Panel;
        if (X && !F.isAncestor(R, Y)) {
          if (W && F.hasClass(Y, W.CSS_PANEL)) {
            S[S.length] = Y.parentNode;
          } else {
            S[S.length] = Y;
          }
        }
      }

      F.getElementsBy(Q, "DIV", document.body);
      S.sort(V);
      var O = S[0],
        U;
      if (O) {
        U = F.getStyle(O, "zIndex");
        if (!isNaN(U)) {
          var T = false;
          if (O != R) {
            T = true;
          } else {
            if (S.length > 1) {
              var P = F.getStyle(S[1], "zIndex");
              if (!isNaN(P) && U == P) {
                T = true;
              }
            }
          }
          if (T) {
            this.cfg.setProperty("zindex", parseInt(U, 10) + 2);
          }
        }
      }
    },
    destroy: function () {
      if (this.iframe) {
        this.iframe.parentNode.removeChild(this.iframe);
      }
      this.iframe = null;
      B.windowResizeEvent.unsubscribe(this.doCenterOnDOMEvent, this);
      B.windowScrollEvent.unsubscribe(this.doCenterOnDOMEvent, this);
      G.textResizeEvent.unsubscribe(this._autoFillOnHeightChange);
      B.superclass.destroy.call(this);
    },
    forceContainerRedraw: function () {
      var O = this;
      F.addClass(O.element, "yui-force-redraw");
      setTimeout(function () {
        F.removeClass(O.element, "yui-force-redraw");
      }, 0);
    },
    toString: function () {
      return "Overlay " + this.id;
    },
  });
})();
(function () {
  YAHOO.widget.OverlayManager = function (G) {
    this.init(G);
  };
  var D = YAHOO.widget.Overlay,
    C = YAHOO.util.Event,
    E = YAHOO.util.Dom,
    B = YAHOO.util.Config,
    F = YAHOO.util.CustomEvent,
    A = YAHOO.widget.OverlayManager;
  A.CSS_FOCUSED = "focused";
  A.prototype = {
    constructor: A,
    overlays: null,
    initDefaultConfig: function () {
      this.cfg.addProperty("overlays", { suppressEvent: true });
      this.cfg.addProperty("focusevent", { value: "mousedown" });
    },
    init: function (I) {
      this.cfg = new B(this);
      this.initDefaultConfig();
      if (I) {
        this.cfg.applyConfig(I, true);
      }
      this.cfg.fireQueue();
      var H = null;
      this.getActive = function () {
        return H;
      };
      this.focus = function (J) {
        var K = this.find(J);
        if (K) {
          K.focus();
        }
      };
      this.remove = function (K) {
        var M = this.find(K),
          J;
        if (M) {
          if (H == M) {
            H = null;
          }
          var L = M.element === null && M.cfg === null ? true : false;
          if (!L) {
            J = E.getStyle(M.element, "zIndex");
            M.cfg.setProperty("zIndex", -1000, true);
          }
          this.overlays.sort(this.compareZIndexDesc);
          this.overlays = this.overlays.slice(0, this.overlays.length - 1);
          M.hideEvent.unsubscribe(M.blur);
          M.destroyEvent.unsubscribe(this._onOverlayDestroy, M);
          M.focusEvent.unsubscribe(this._onOverlayFocusHandler, M);
          M.blurEvent.unsubscribe(this._onOverlayBlurHandler, M);
          if (!L) {
            C.removeListener(
              M.element,
              this.cfg.getProperty("focusevent"),
              this._onOverlayElementFocus,
            );
            M.cfg.setProperty("zIndex", J, true);
            M.cfg.setProperty("manager", null);
          }
          if (M.focusEvent._managed) {
            M.focusEvent = null;
          }
          if (M.blurEvent._managed) {
            M.blurEvent = null;
          }
          if (M.focus._managed) {
            M.focus = null;
          }
          if (M.blur._managed) {
            M.blur = null;
          }
        }
      };
      this.blurAll = function () {
        var K = this.overlays.length,
          J;
        if (K > 0) {
          J = K - 1;
          do {
            this.overlays[J].blur();
          } while (J--);
        }
      };
      this._manageBlur = function (J) {
        var K = false;
        if (H == J) {
          E.removeClass(H.element, A.CSS_FOCUSED);
          H = null;
          K = true;
        }
        return K;
      };
      this._manageFocus = function (J) {
        var K = false;
        if (H != J) {
          if (H) {
            H.blur();
          }
          H = J;
          this.bringToTop(H);
          E.addClass(H.element, A.CSS_FOCUSED);
          K = true;
        }
        return K;
      };
      var G = this.cfg.getProperty("overlays");
      if (!this.overlays) {
        this.overlays = [];
      }
      if (G) {
        this.register(G);
        this.overlays.sort(this.compareZIndexDesc);
      }
    },
    _onOverlayElementFocus: function (I) {
      var G = C.getTarget(I),
        H = this.close;
      if (H && (G == H || E.isAncestor(H, G))) {
        this.blur();
      } else {
        this.focus();
      }
    },
    _onOverlayDestroy: function (H, G, I) {
      this.remove(I);
    },
    _onOverlayFocusHandler: function (H, G, I) {
      this._manageFocus(I);
    },
    _onOverlayBlurHandler: function (H, G, I) {
      this._manageBlur(I);
    },
    _bindFocus: function (G) {
      var H = this;
      if (!G.focusEvent) {
        G.focusEvent = G.createEvent("focus");
        G.focusEvent.signature = F.LIST;
        G.focusEvent._managed = true;
      } else {
        G.focusEvent.subscribe(H._onOverlayFocusHandler, G, H);
      }
      if (!G.focus) {
        C.on(
          G.element,
          H.cfg.getProperty("focusevent"),
          H._onOverlayElementFocus,
          null,
          G,
        );
        G.focus = function () {
          if (H._manageFocus(this)) {
            if (this.cfg.getProperty("visible") && this.focusFirst) {
              this.focusFirst();
            }
            this.focusEvent.fire();
          }
        };
        G.focus._managed = true;
      }
    },
    _bindBlur: function (G) {
      var H = this;
      if (!G.blurEvent) {
        G.blurEvent = G.createEvent("blur");
        G.blurEvent.signature = F.LIST;
        G.focusEvent._managed = true;
      } else {
        G.blurEvent.subscribe(H._onOverlayBlurHandler, G, H);
      }
      if (!G.blur) {
        G.blur = function () {
          if (H._manageBlur(this)) {
            this.blurEvent.fire();
          }
        };
        G.blur._managed = true;
      }
      G.hideEvent.subscribe(G.blur);
    },
    _bindDestroy: function (G) {
      var H = this;
      G.destroyEvent.subscribe(H._onOverlayDestroy, G, H);
    },
    _syncZIndex: function (G) {
      var H = E.getStyle(G.element, "zIndex");
      if (!isNaN(H)) {
        G.cfg.setProperty("zIndex", parseInt(H, 10));
      } else {
        G.cfg.setProperty("zIndex", 0);
      }
    },
    register: function (G) {
      var J = false,
        H,
        I;
      if (G instanceof D) {
        G.cfg.addProperty("manager", { value: this });
        this._bindFocus(G);
        this._bindBlur(G);
        this._bindDestroy(G);
        this._syncZIndex(G);
        this.overlays.push(G);
        this.bringToTop(G);
        J = true;
      } else {
        if (G instanceof Array) {
          for (H = 0, I = G.length; H < I; H++) {
            J = this.register(G[H]) || J;
          }
        }
      }
      return J;
    },
    bringToTop: function (M) {
      var I = this.find(M),
        L,
        G,
        J;
      if (I) {
        J = this.overlays;
        J.sort(this.compareZIndexDesc);
        G = J[0];
        if (G) {
          L = E.getStyle(G.element, "zIndex");
          if (!isNaN(L)) {
            var K = false;
            if (G !== I) {
              K = true;
            } else {
              if (J.length > 1) {
                var H = E.getStyle(J[1].element, "zIndex");
                if (!isNaN(H) && L == H) {
                  K = true;
                }
              }
            }
            if (K) {
              I.cfg.setProperty("zindex", parseInt(L, 10) + 2);
            }
          }
          J.sort(this.compareZIndexDesc);
        }
      }
    },
    find: function (G) {
      var K = G instanceof D,
        I = this.overlays,
        M = I.length,
        J = null,
        L,
        H;
      if (K || typeof G == "string") {
        for (H = M - 1; H >= 0; H--) {
          L = I[H];
          if ((K && L === G) || L.id == G) {
            J = L;
            break;
          }
        }
      }
      return J;
    },
    compareZIndexDesc: function (J, I) {
      var H = J.cfg ? J.cfg.getProperty("zIndex") : null,
        G = I.cfg ? I.cfg.getProperty("zIndex") : null;
      if (H === null && G === null) {
        return 0;
      } else {
        if (H === null) {
          return 1;
        } else {
          if (G === null) {
            return -1;
          } else {
            if (H > G) {
              return -1;
            } else {
              if (H < G) {
                return 1;
              } else {
                return 0;
              }
            }
          }
        }
      }
    },
    showAll: function () {
      var H = this.overlays,
        I = H.length,
        G;
      for (G = I - 1; G >= 0; G--) {
        H[G].show();
      }
    },
    hideAll: function () {
      var H = this.overlays,
        I = H.length,
        G;
      for (G = I - 1; G >= 0; G--) {
        H[G].hide();
      }
    },
    toString: function () {
      return "OverlayManager";
    },
  };
})();
(function () {
  YAHOO.widget.ContainerEffect = function (E, H, G, D, F) {
    if (!F) {
      F = YAHOO.util.Anim;
    }
    this.overlay = E;
    this.attrIn = H;
    this.attrOut = G;
    this.targetElement = D || E.element;
    this.animClass = F;
  };
  var B = YAHOO.util.Dom,
    C = YAHOO.util.CustomEvent,
    A = YAHOO.widget.ContainerEffect;
  A.FADE = function (D, F) {
    var G = YAHOO.util.Easing,
      I = {
        attributes: { opacity: { from: 0, to: 1 } },
        duration: F,
        method: G.easeIn,
      },
      E = {
        attributes: { opacity: { to: 0 } },
        duration: F,
        method: G.easeOut,
      },
      H = new A(D, I, E, D.element);
    H.handleUnderlayStart = function () {
      var K = this.overlay.underlay;
      if (K && YAHOO.env.ua.ie) {
        var J = K.filters && K.filters.length > 0;
        if (J) {
          B.addClass(D.element, "yui-effect-fade");
        }
      }
    };
    H.handleUnderlayComplete = function () {
      var J = this.overlay.underlay;
      if (J && YAHOO.env.ua.ie) {
        B.removeClass(D.element, "yui-effect-fade");
      }
    };
    H.handleStartAnimateIn = function (K, J, L) {
      B.addClass(L.overlay.element, "hide-select");
      if (!L.overlay.underlay) {
        L.overlay.cfg.refireEvent("underlay");
      }
      L.handleUnderlayStart();
      L.overlay._setDomVisibility(true);
      B.setStyle(L.overlay.element, "opacity", 0);
    };
    H.handleCompleteAnimateIn = function (K, J, L) {
      B.removeClass(L.overlay.element, "hide-select");
      if (L.overlay.element.style.filter) {
        L.overlay.element.style.filter = null;
      }
      L.handleUnderlayComplete();
      L.overlay.cfg.refireEvent("iframe");
      L.animateInCompleteEvent.fire();
    };
    H.handleStartAnimateOut = function (K, J, L) {
      B.addClass(L.overlay.element, "hide-select");
      L.handleUnderlayStart();
    };
    H.handleCompleteAnimateOut = function (K, J, L) {
      B.removeClass(L.overlay.element, "hide-select");
      if (L.overlay.element.style.filter) {
        L.overlay.element.style.filter = null;
      }
      L.overlay._setDomVisibility(false);
      B.setStyle(L.overlay.element, "opacity", 1);
      L.handleUnderlayComplete();
      L.overlay.cfg.refireEvent("iframe");
      L.animateOutCompleteEvent.fire();
    };
    H.init();
    return H;
  };
  A.SLIDE = function (F, D) {
    var I = YAHOO.util.Easing,
      L = F.cfg.getProperty("x") || B.getX(F.element),
      K = F.cfg.getProperty("y") || B.getY(F.element),
      M = B.getClientWidth(),
      H = F.element.offsetWidth,
      J = {
        attributes: { points: { to: [L, K] } },
        duration: D,
        method: I.easeIn,
      },
      E = {
        attributes: { points: { to: [M + 25, K] } },
        duration: D,
        method: I.easeOut,
      },
      G = new A(F, J, E, F.element, YAHOO.util.Motion);
    G.handleStartAnimateIn = function (O, N, P) {
      P.overlay.element.style.left = -25 - H + "px";
      P.overlay.element.style.top = K + "px";
    };
    G.handleTweenAnimateIn = function (Q, P, R) {
      var S = B.getXY(R.overlay.element),
        O = S[0],
        N = S[1];
      if (B.getStyle(R.overlay.element, "visibility") == "hidden" && O < L) {
        R.overlay._setDomVisibility(true);
      }
      R.overlay.cfg.setProperty("xy", [O, N], true);
      R.overlay.cfg.refireEvent("iframe");
    };
    G.handleCompleteAnimateIn = function (O, N, P) {
      P.overlay.cfg.setProperty("xy", [L, K], true);
      P.startX = L;
      P.startY = K;
      P.overlay.cfg.refireEvent("iframe");
      P.animateInCompleteEvent.fire();
    };
    G.handleStartAnimateOut = function (O, N, R) {
      var P = B.getViewportWidth(),
        S = B.getXY(R.overlay.element),
        Q = S[1];
      R.animOut.attributes.points.to = [P + 25, Q];
    };
    G.handleTweenAnimateOut = function (P, O, Q) {
      var S = B.getXY(Q.overlay.element),
        N = S[0],
        R = S[1];
      Q.overlay.cfg.setProperty("xy", [N, R], true);
      Q.overlay.cfg.refireEvent("iframe");
    };
    G.handleCompleteAnimateOut = function (O, N, P) {
      P.overlay._setDomVisibility(false);
      P.overlay.cfg.setProperty("xy", [L, K]);
      P.animateOutCompleteEvent.fire();
    };
    G.init();
    return G;
  };
  A.prototype = {
    init: function () {
      this.beforeAnimateInEvent = this.createEvent("beforeAnimateIn");
      this.beforeAnimateInEvent.signature = C.LIST;
      this.beforeAnimateOutEvent = this.createEvent("beforeAnimateOut");
      this.beforeAnimateOutEvent.signature = C.LIST;
      this.animateInCompleteEvent = this.createEvent("animateInComplete");
      this.animateInCompleteEvent.signature = C.LIST;
      this.animateOutCompleteEvent = this.createEvent("animateOutComplete");
      this.animateOutCompleteEvent.signature = C.LIST;
      this.animIn = new this.animClass(
        this.targetElement,
        this.attrIn.attributes,
        this.attrIn.duration,
        this.attrIn.method,
      );
      this.animIn.onStart.subscribe(this.handleStartAnimateIn, this);
      this.animIn.onTween.subscribe(this.handleTweenAnimateIn, this);
      this.animIn.onComplete.subscribe(this.handleCompleteAnimateIn, this);
      this.animOut = new this.animClass(
        this.targetElement,
        this.attrOut.attributes,
        this.attrOut.duration,
        this.attrOut.method,
      );
      this.animOut.onStart.subscribe(this.handleStartAnimateOut, this);
      this.animOut.onTween.subscribe(this.handleTweenAnimateOut, this);
      this.animOut.onComplete.subscribe(this.handleCompleteAnimateOut, this);
    },
    animateIn: function () {
      this.beforeAnimateInEvent.fire();
      this.animIn.animate();
    },
    animateOut: function () {
      this.beforeAnimateOutEvent.fire();
      this.animOut.animate();
    },
    handleStartAnimateIn: function (E, D, F) {},
    handleTweenAnimateIn: function (E, D, F) {},
    handleCompleteAnimateIn: function (E, D, F) {},
    handleStartAnimateOut: function (E, D, F) {},
    handleTweenAnimateOut: function (E, D, F) {},
    handleCompleteAnimateOut: function (E, D, F) {},
    toString: function () {
      var D = "ContainerEffect";
      if (this.overlay) {
        D += " [" + this.overlay.toString() + "]";
      }
      return D;
    },
  };
  YAHOO.lang.augmentProto(A, YAHOO.util.EventProvider);
})();
YAHOO.register("containercore", YAHOO.widget.Module, {
  version: "2.7.0",
  build: "1799",
});
