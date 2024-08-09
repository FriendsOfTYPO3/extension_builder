/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
YAHOO.util.CustomEvent = function (D, C, B, A) {
  this.type = D;
  this.scope = C || window;
  this.silent = B;
  this.signature = A || YAHOO.util.CustomEvent.LIST;
  this.subscribers = [];
  if (!this.silent) {
  }
  var E = "_YUICEOnSubscribe";
  if (D !== E) {
    this.subscribeEvent = new YAHOO.util.CustomEvent(E, this, true);
  }
  this.lastError = null;
};
YAHOO.util.CustomEvent.LIST = 0;
YAHOO.util.CustomEvent.FLAT = 1;
YAHOO.util.CustomEvent.prototype = {
  subscribe: function (A, B, C) {
    if (!A) {
      throw new Error("Invalid callback for subscriber to '" + this.type + "'");
    }
    if (this.subscribeEvent) {
      this.subscribeEvent.fire(A, B, C);
    }
    this.subscribers.push(new YAHOO.util.Subscriber(A, B, C));
  },
  unsubscribe: function (D, F) {
    if (!D) {
      return this.unsubscribeAll();
    }
    var E = false;
    for (var B = 0, A = this.subscribers.length; B < A; ++B) {
      var C = this.subscribers[B];
      if (C && C.contains(D, F)) {
        this._delete(B);
        E = true;
      }
    }
    return E;
  },
  fire: function () {
    this.lastError = null;
    var K = [],
      E = this.subscribers.length;
    if (!E && this.silent) {
      return true;
    }
    var I = [].slice.call(arguments, 0),
      G = true,
      D,
      J = false;
    if (!this.silent) {
    }
    var C = this.subscribers.slice(),
      A = YAHOO.util.Event.throwErrors;
    for (D = 0; D < E; ++D) {
      var M = C[D];
      if (!M) {
        J = true;
      } else {
        if (!this.silent) {
        }
        var L = M.getScope(this.scope);
        if (this.signature == YAHOO.util.CustomEvent.FLAT) {
          var B = null;
          if (I.length > 0) {
            B = I[0];
          }
          try {
            G = M.fn.call(L, B, M.obj);
          } catch (F) {
            this.lastError = F;
            if (A) {
              throw F;
            }
          }
        } else {
          try {
            G = M.fn.call(L, this.type, I, M.obj);
          } catch (H) {
            this.lastError = H;
            if (A) {
              throw H;
            }
          }
        }
        if (false === G) {
          if (!this.silent) {
          }
          break;
        }
      }
    }
    return G !== false;
  },
  unsubscribeAll: function () {
    var A = this.subscribers.length,
      B;
    for (B = A - 1; B > -1; B--) {
      this._delete(B);
    }
    this.subscribers = [];
    return A;
  },
  _delete: function (A) {
    var B = this.subscribers[A];
    if (B) {
      delete B.fn;
      delete B.obj;
    }
    this.subscribers.splice(A, 1);
  },
  toString: function () {
    return "CustomEvent: " + "'" + this.type + "', " + "context: " + this.scope;
  },
};
YAHOO.util.Subscriber = function (A, B, C) {
  this.fn = A;
  this.obj = YAHOO.lang.isUndefined(B) ? null : B;
  this.overrideContext = C;
};
YAHOO.util.Subscriber.prototype.getScope = function (A) {
  if (this.overrideContext) {
    if (this.overrideContext === true) {
      return this.obj;
    } else {
      return this.overrideContext;
    }
  }
  return A;
};
YAHOO.util.Subscriber.prototype.contains = function (A, B) {
  if (B) {
    return this.fn == A && this.obj == B;
  } else {
    return this.fn == A;
  }
};
YAHOO.util.Subscriber.prototype.toString = function () {
  return (
    "Subscriber { obj: " +
    this.obj +
    ", overrideContext: " +
    (this.overrideContext || "no") +
    " }"
  );
};
if (!YAHOO.util.Event) {
  YAHOO.util.Event = (function () {
    var H = false;
    var I = [];
    var J = [];
    var G = [];
    var E = [];
    var C = 0;
    var F = [];
    var B = [];
    var A = 0;
    var D = {
      63232: 38,
      63233: 40,
      63234: 37,
      63235: 39,
      63276: 33,
      63277: 34,
      25: 9,
    };
    var K = YAHOO.env.ua.ie ? "focusin" : "focus";
    var L = YAHOO.env.ua.ie ? "focusout" : "blur";
    return {
      POLL_RETRYS: 2000,
      POLL_INTERVAL: 20,
      EL: 0,
      TYPE: 1,
      FN: 2,
      WFN: 3,
      UNLOAD_OBJ: 3,
      ADJ_SCOPE: 4,
      OBJ: 5,
      OVERRIDE: 6,
      lastError: null,
      isSafari: YAHOO.env.ua.webkit,
      webkit: YAHOO.env.ua.webkit,
      isIE: YAHOO.env.ua.ie,
      _interval: null,
      _dri: null,
      DOMReady: false,
      throwErrors: false,
      startInterval: function () {
        if (!this._interval) {
          var M = this;
          var N = function () {
            M._tryPreloadAttach();
          };
          this._interval = setInterval(N, this.POLL_INTERVAL);
        }
      },
      onAvailable: function (S, O, Q, R, P) {
        var M = YAHOO.lang.isString(S) ? [S] : S;
        for (var N = 0; N < M.length; N = N + 1) {
          F.push({
            id: M[N],
            fn: O,
            obj: Q,
            overrideContext: R,
            checkReady: P,
          });
        }
        C = this.POLL_RETRYS;
        this.startInterval();
      },
      onContentReady: function (P, M, N, O) {
        this.onAvailable(P, M, N, O, true);
      },
      onDOMReady: function (M, N, O) {
        if (this.DOMReady) {
          setTimeout(function () {
            var P = window;
            if (O) {
              if (O === true) {
                P = N;
              } else {
                P = O;
              }
            }
            M.call(P, "DOMReady", [], N);
          }, 0);
        } else {
          this.DOMReadyEvent.subscribe(M, N, O);
        }
      },
      _addListener: function (O, M, Y, S, W, b) {
        if (!Y || !Y.call) {
          return false;
        }
        if (this._isValidCollection(O)) {
          var Z = true;
          for (var T = 0, V = O.length; T < V; ++T) {
            Z = this.on(O[T], M, Y, S, W) && Z;
          }
          return Z;
        } else {
          if (YAHOO.lang.isString(O)) {
            var R = this.getEl(O);
            if (R) {
              O = R;
            } else {
              this.onAvailable(O, function () {
                YAHOO.util.Event.on(O, M, Y, S, W);
              });
              return true;
            }
          }
        }
        if (!O) {
          return false;
        }
        if ("unload" == M && S !== this) {
          J[J.length] = [O, M, Y, S, W];
          return true;
        }
        var N = O;
        if (W) {
          if (W === true) {
            N = S;
          } else {
            N = W;
          }
        }
        var P = function (c) {
          return Y.call(N, YAHOO.util.Event.getEvent(c, O), S);
        };
        var a = [O, M, Y, P, N, S, W];
        var U = I.length;
        I[U] = a;
        if (this.useLegacyEvent(O, M)) {
          var Q = this.getLegacyIndex(O, M);
          if (Q == -1 || O != G[Q][0]) {
            Q = G.length;
            B[O.id + M] = Q;
            G[Q] = [O, M, O["on" + M]];
            E[Q] = [];
            O["on" + M] = function (c) {
              YAHOO.util.Event.fireLegacyEvent(YAHOO.util.Event.getEvent(c), Q);
            };
          }
          E[Q].push(a);
        } else {
          try {
            this._simpleAdd(O, M, P, b);
          } catch (X) {
            this.lastError = X;
            this.removeListener(O, M, Y);
            return false;
          }
        }
        return true;
      },
      addListener: function (N, Q, M, O, P) {
        return this._addListener(N, Q, M, O, P, false);
      },
      addFocusListener: function (N, M, O, P) {
        return this._addListener(N, K, M, O, P, true);
      },
      removeFocusListener: function (N, M) {
        return this.removeListener(N, K, M);
      },
      addBlurListener: function (N, M, O, P) {
        return this._addListener(N, L, M, O, P, true);
      },
      removeBlurListener: function (N, M) {
        return this.removeListener(N, L, M);
      },
      fireLegacyEvent: function (R, P) {
        var T = true,
          M,
          V,
          U,
          N,
          S;
        V = E[P].slice();
        for (var O = 0, Q = V.length; O < Q; ++O) {
          U = V[O];
          if (U && U[this.WFN]) {
            N = U[this.ADJ_SCOPE];
            S = U[this.WFN].call(N, R);
            T = T && S;
          }
        }
        M = G[P];
        if (M && M[2]) {
          M[2](R);
        }
        return T;
      },
      getLegacyIndex: function (N, O) {
        var M = this.generateId(N) + O;
        if (typeof B[M] == "undefined") {
          return -1;
        } else {
          return B[M];
        }
      },
      useLegacyEvent: function (M, N) {
        return (
          this.webkit && this.webkit < 419 && ("click" == N || "dblclick" == N)
        );
      },
      removeListener: function (N, M, V) {
        var Q, T, X;
        if (typeof N == "string") {
          N = this.getEl(N);
        } else {
          if (this._isValidCollection(N)) {
            var W = true;
            for (Q = N.length - 1; Q > -1; Q--) {
              W = this.removeListener(N[Q], M, V) && W;
            }
            return W;
          }
        }
        if (!V || !V.call) {
          return this.purgeElement(N, false, M);
        }
        if ("unload" == M) {
          for (Q = J.length - 1; Q > -1; Q--) {
            X = J[Q];
            if (X && X[0] == N && X[1] == M && X[2] == V) {
              J.splice(Q, 1);
              return true;
            }
          }
          return false;
        }
        var R = null;
        var S = arguments[3];
        if ("undefined" === typeof S) {
          S = this._getCacheIndex(N, M, V);
        }
        if (S >= 0) {
          R = I[S];
        }
        if (!N || !R) {
          return false;
        }
        if (this.useLegacyEvent(N, M)) {
          var P = this.getLegacyIndex(N, M);
          var O = E[P];
          if (O) {
            for (Q = 0, T = O.length; Q < T; ++Q) {
              X = O[Q];
              if (
                X &&
                X[this.EL] == N &&
                X[this.TYPE] == M &&
                X[this.FN] == V
              ) {
                O.splice(Q, 1);
                break;
              }
            }
          }
        } else {
          try {
            this._simpleRemove(N, M, R[this.WFN], false);
          } catch (U) {
            this.lastError = U;
            return false;
          }
        }
        delete I[S][this.WFN];
        delete I[S][this.FN];
        I.splice(S, 1);
        return true;
      },
      getTarget: function (O, N) {
        var M = O.target || O.srcElement;
        return this.resolveTextNode(M);
      },
      resolveTextNode: function (N) {
        try {
          if (N && 3 == N.nodeType) {
            return N.parentNode;
          }
        } catch (M) {}
        return N;
      },
      getPageX: function (N) {
        var M = N.pageX;
        if (!M && 0 !== M) {
          M = N.clientX || 0;
          if (this.isIE) {
            M += this._getScrollLeft();
          }
        }
        return M;
      },
      getPageY: function (M) {
        var N = M.pageY;
        if (!N && 0 !== N) {
          N = M.clientY || 0;
          if (this.isIE) {
            N += this._getScrollTop();
          }
        }
        return N;
      },
      getXY: function (M) {
        return [this.getPageX(M), this.getPageY(M)];
      },
      getRelatedTarget: function (N) {
        var M = N.relatedTarget;
        if (!M) {
          if (N.type == "mouseout") {
            M = N.toElement;
          } else {
            if (N.type == "mouseover") {
              M = N.fromElement;
            }
          }
        }
        return this.resolveTextNode(M);
      },
      getTime: function (O) {
        if (!O.time) {
          var N = new Date().getTime();
          try {
            O.time = N;
          } catch (M) {
            this.lastError = M;
            return N;
          }
        }
        return O.time;
      },
      stopEvent: function (M) {
        this.stopPropagation(M);
        this.preventDefault(M);
      },
      stopPropagation: function (M) {
        if (M.stopPropagation) {
          M.stopPropagation();
        } else {
          M.cancelBubble = true;
        }
      },
      preventDefault: function (M) {
        if (M.preventDefault) {
          M.preventDefault();
        } else {
          M.returnValue = false;
        }
      },
      getEvent: function (O, M) {
        var N = O || window.event;
        if (!N) {
          var P = this.getEvent.caller;
          while (P) {
            N = P.arguments[0];
            if (N && Event == N.constructor) {
              break;
            }
            P = P.caller;
          }
        }
        return N;
      },
      getCharCode: function (N) {
        var M = N.keyCode || N.charCode || 0;
        if (YAHOO.env.ua.webkit && M in D) {
          M = D[M];
        }
        return M;
      },
      _getCacheIndex: function (Q, R, P) {
        for (var O = 0, N = I.length; O < N; O = O + 1) {
          var M = I[O];
          if (M && M[this.FN] == P && M[this.EL] == Q && M[this.TYPE] == R) {
            return O;
          }
        }
        return -1;
      },
      generateId: function (M) {
        var N = M.id;
        if (!N) {
          N = "yuievtautoid-" + A;
          ++A;
          M.id = N;
        }
        return N;
      },
      _isValidCollection: function (N) {
        try {
          return (
            N &&
            typeof N !== "string" &&
            N.length &&
            !N.tagName &&
            !N.alert &&
            typeof N[0] !== "undefined"
          );
        } catch (M) {
          return false;
        }
      },
      elCache: {},
      getEl: function (M) {
        return typeof M === "string" ? document.getElementById(M) : M;
      },
      clearCache: function () {},
      DOMReadyEvent: new YAHOO.util.CustomEvent("DOMReady", this),
      _load: function (N) {
        if (!H) {
          H = true;
          var M = YAHOO.util.Event;
          M._ready();
          M._tryPreloadAttach();
        }
      },
      _ready: function (N) {
        var M = YAHOO.util.Event;
        if (!M.DOMReady) {
          M.DOMReady = true;
          M.DOMReadyEvent.fire();
          M._simpleRemove(document, "DOMContentLoaded", M._ready);
        }
      },
      _tryPreloadAttach: function () {
        if (F.length === 0) {
          C = 0;
          if (this._interval) {
            clearInterval(this._interval);
            this._interval = null;
          }
          return;
        }
        if (this.locked) {
          return;
        }
        if (this.isIE) {
          if (!this.DOMReady) {
            this.startInterval();
            return;
          }
        }
        this.locked = true;
        var S = !H;
        if (!S) {
          S = C > 0 && F.length > 0;
        }
        var R = [];
        var T = function (V, W) {
          var U = V;
          if (W.overrideContext) {
            if (W.overrideContext === true) {
              U = W.obj;
            } else {
              U = W.overrideContext;
            }
          }
          W.fn.call(U, W.obj);
        };
        var N,
          M,
          Q,
          P,
          O = [];
        for (N = 0, M = F.length; N < M; N = N + 1) {
          Q = F[N];
          if (Q) {
            P = this.getEl(Q.id);
            if (P) {
              if (Q.checkReady) {
                if (H || P.nextSibling || !S) {
                  O.push(Q);
                  F[N] = null;
                }
              } else {
                T(P, Q);
                F[N] = null;
              }
            } else {
              R.push(Q);
            }
          }
        }
        for (N = 0, M = O.length; N < M; N = N + 1) {
          Q = O[N];
          T(this.getEl(Q.id), Q);
        }
        C--;
        if (S) {
          for (N = F.length - 1; N > -1; N--) {
            Q = F[N];
            if (!Q || !Q.id) {
              F.splice(N, 1);
            }
          }
          this.startInterval();
        } else {
          if (this._interval) {
            clearInterval(this._interval);
            this._interval = null;
          }
        }
        this.locked = false;
      },
      purgeElement: function (Q, R, T) {
        var O = YAHOO.lang.isString(Q) ? this.getEl(Q) : Q;
        var S = this.getListeners(O, T),
          P,
          M;
        if (S) {
          for (P = S.length - 1; P > -1; P--) {
            var N = S[P];
            this.removeListener(O, N.type, N.fn);
          }
        }
        if (R && O && O.childNodes) {
          for (P = 0, M = O.childNodes.length; P < M; ++P) {
            this.purgeElement(O.childNodes[P], R, T);
          }
        }
      },
      getListeners: function (O, M) {
        var R = [],
          N;
        if (!M) {
          N = [I, J];
        } else {
          if (M === "unload") {
            N = [J];
          } else {
            N = [I];
          }
        }
        var T = YAHOO.lang.isString(O) ? this.getEl(O) : O;
        for (var Q = 0; Q < N.length; Q = Q + 1) {
          var V = N[Q];
          if (V) {
            for (var S = 0, U = V.length; S < U; ++S) {
              var P = V[S];
              if (P && P[this.EL] === T && (!M || M === P[this.TYPE])) {
                R.push({
                  type: P[this.TYPE],
                  fn: P[this.FN],
                  obj: P[this.OBJ],
                  adjust: P[this.OVERRIDE],
                  scope: P[this.ADJ_SCOPE],
                  index: S,
                });
              }
            }
          }
        }
        return R.length ? R : null;
      },
      _unload: function (T) {
        var N = YAHOO.util.Event,
          Q,
          P,
          O,
          S,
          R,
          U = J.slice(),
          M;
        for (Q = 0, S = J.length; Q < S; ++Q) {
          O = U[Q];
          if (O) {
            M = window;
            if (O[N.ADJ_SCOPE]) {
              if (O[N.ADJ_SCOPE] === true) {
                M = O[N.UNLOAD_OBJ];
              } else {
                M = O[N.ADJ_SCOPE];
              }
            }
            O[N.FN].call(M, N.getEvent(T, O[N.EL]), O[N.UNLOAD_OBJ]);
            U[Q] = null;
          }
        }
        O = null;
        M = null;
        J = null;
        if (I) {
          for (P = I.length - 1; P > -1; P--) {
            O = I[P];
            if (O) {
              N.removeListener(O[N.EL], O[N.TYPE], O[N.FN], P);
            }
          }
          O = null;
        }
        G = null;
        N._simpleRemove(window, "unload", N._unload);
      },
      _getScrollLeft: function () {
        return this._getScroll()[1];
      },
      _getScrollTop: function () {
        return this._getScroll()[0];
      },
      _getScroll: function () {
        var M = document.documentElement,
          N = document.body;
        if (M && (M.scrollTop || M.scrollLeft)) {
          return [M.scrollTop, M.scrollLeft];
        } else {
          if (N) {
            return [N.scrollTop, N.scrollLeft];
          } else {
            return [0, 0];
          }
        }
      },
      regCE: function () {},
      _simpleAdd: (function () {
        if (window.addEventListener) {
          return function (O, P, N, M) {
            O.addEventListener(P, N, M);
          };
        } else {
          if (window.attachEvent) {
            return function (O, P, N, M) {
              O.attachEvent("on" + P, N);
            };
          } else {
            return function () {};
          }
        }
      })(),
      _simpleRemove: (function () {
        if (window.removeEventListener) {
          return function (O, P, N, M) {
            O.removeEventListener(P, N, M);
          };
        } else {
          if (window.detachEvent) {
            return function (N, O, M) {
              N.detachEvent("on" + O, M);
            };
          } else {
            return function () {};
          }
        }
      })(),
    };
  })();
  (function () {
    var EU = YAHOO.util.Event;
    EU.on = EU.addListener;
    EU.onFocus = EU.addFocusListener;
    EU.onBlur = EU.addBlurListener;
    /* DOMReady: based on work by: Dean Edwards/John Resig/Matthias Miller */
    if (EU.isIE) {
      YAHOO.util.Event.onDOMReady(
        YAHOO.util.Event._tryPreloadAttach,
        YAHOO.util.Event,
        true,
      );
      var n = document.createElement("p");
      EU._dri = setInterval(function () {
        try {
          n.doScroll("left");
          clearInterval(EU._dri);
          EU._dri = null;
          EU._ready();
          n = null;
        } catch (ex) {}
      }, EU.POLL_INTERVAL);
    } else {
      if (EU.webkit && EU.webkit < 525) {
        EU._dri = setInterval(function () {
          var rs = document.readyState;
          if ("loaded" == rs || "complete" == rs) {
            clearInterval(EU._dri);
            EU._dri = null;
            EU._ready();
          }
        }, EU.POLL_INTERVAL);
      } else {
        EU._simpleAdd(document, "DOMContentLoaded", EU._ready);
      }
    }
    EU._simpleAdd(window, "load", EU._load);
    EU._simpleAdd(window, "unload", EU._unload);
    EU._tryPreloadAttach();
  })();
}
YAHOO.util.EventProvider = function () {};
YAHOO.util.EventProvider.prototype = {
  __yui_events: null,
  __yui_subscribers: null,
  subscribe: function (A, C, F, E) {
    this.__yui_events = this.__yui_events || {};
    var D = this.__yui_events[A];
    if (D) {
      D.subscribe(C, F, E);
    } else {
      this.__yui_subscribers = this.__yui_subscribers || {};
      var B = this.__yui_subscribers;
      if (!B[A]) {
        B[A] = [];
      }
      B[A].push({ fn: C, obj: F, overrideContext: E });
    }
  },
  unsubscribe: function (C, E, G) {
    this.__yui_events = this.__yui_events || {};
    var A = this.__yui_events;
    if (C) {
      var F = A[C];
      if (F) {
        return F.unsubscribe(E, G);
      }
    } else {
      var B = true;
      for (var D in A) {
        if (YAHOO.lang.hasOwnProperty(A, D)) {
          B = B && A[D].unsubscribe(E, G);
        }
      }
      return B;
    }
    return false;
  },
  unsubscribeAll: function (A) {
    return this.unsubscribe(A);
  },
  createEvent: function (G, D) {
    this.__yui_events = this.__yui_events || {};
    var A = D || {};
    var I = this.__yui_events;
    if (I[G]) {
    } else {
      var H = A.scope || this;
      var E = A.silent;
      var B = new YAHOO.util.CustomEvent(G, H, E, YAHOO.util.CustomEvent.FLAT);
      I[G] = B;
      if (A.onSubscribeCallback) {
        B.subscribeEvent.subscribe(A.onSubscribeCallback);
      }
      this.__yui_subscribers = this.__yui_subscribers || {};
      var F = this.__yui_subscribers[G];
      if (F) {
        for (var C = 0; C < F.length; ++C) {
          B.subscribe(F[C].fn, F[C].obj, F[C].overrideContext);
        }
      }
    }
    return I[G];
  },
  fireEvent: function (E, D, A, C) {
    this.__yui_events = this.__yui_events || {};
    var G = this.__yui_events[E];
    if (!G) {
      return null;
    }
    var B = [];
    for (var F = 1; F < arguments.length; ++F) {
      B.push(arguments[F]);
    }
    return G.fire.apply(G, B);
  },
  hasEvent: function (A) {
    if (this.__yui_events) {
      if (this.__yui_events[A]) {
        return true;
      }
    }
    return false;
  },
};
(function () {
  var A = YAHOO.util.Event,
    C = YAHOO.lang;
  YAHOO.util.KeyListener = function (D, I, E, F) {
    if (!D) {
    } else {
      if (!I) {
      } else {
        if (!E) {
        }
      }
    }
    if (!F) {
      F = YAHOO.util.KeyListener.KEYDOWN;
    }
    var G = new YAHOO.util.CustomEvent("keyPressed");
    this.enabledEvent = new YAHOO.util.CustomEvent("enabled");
    this.disabledEvent = new YAHOO.util.CustomEvent("disabled");
    if (C.isString(D)) {
      D = document.getElementById(D);
    }
    if (C.isFunction(E)) {
      G.subscribe(E);
    } else {
      G.subscribe(E.fn, E.scope, E.correctScope);
    }

    function H(O, N) {
      if (!I.shift) {
        I.shift = false;
      }
      if (!I.alt) {
        I.alt = false;
      }
      if (!I.ctrl) {
        I.ctrl = false;
      }
      if (O.shiftKey == I.shift && O.altKey == I.alt && O.ctrlKey == I.ctrl) {
        var J,
          M = I.keys,
          L;
        if (YAHOO.lang.isArray(M)) {
          for (var K = 0; K < M.length; K++) {
            J = M[K];
            L = A.getCharCode(O);
            if (J == L) {
              G.fire(L, O);
              break;
            }
          }
        } else {
          L = A.getCharCode(O);
          if (M == L) {
            G.fire(L, O);
          }
        }
      }
    }

    this.enable = function () {
      if (!this.enabled) {
        A.on(D, F, H);
        this.enabledEvent.fire(I);
      }
      this.enabled = true;
    };
    this.disable = function () {
      if (this.enabled) {
        A.removeListener(D, F, H);
        this.disabledEvent.fire(I);
      }
      this.enabled = false;
    };
    this.toString = function () {
      return (
        "KeyListener [" +
        I.keys +
        "] " +
        D.tagName +
        (D.id ? "[" + D.id + "]" : "")
      );
    };
  };
  var B = YAHOO.util.KeyListener;
  B.KEYDOWN = "keydown";
  B.KEYUP = "keyup";
  B.KEY = {
    ALT: 18,
    BACK_SPACE: 8,
    CAPS_LOCK: 20,
    CONTROL: 17,
    DELETE: 46,
    DOWN: 40,
    END: 35,
    ENTER: 13,
    ESCAPE: 27,
    HOME: 36,
    LEFT: 37,
    META: 224,
    NUM_LOCK: 144,
    PAGE_DOWN: 34,
    PAGE_UP: 33,
    PAUSE: 19,
    PRINTSCREEN: 44,
    RIGHT: 39,
    SCROLL_LOCK: 145,
    SHIFT: 16,
    SPACE: 32,
    TAB: 9,
    UP: 38,
  };
})();
YAHOO.register("event", YAHOO.util.Event, { version: "2.7.0", build: "1799" });
