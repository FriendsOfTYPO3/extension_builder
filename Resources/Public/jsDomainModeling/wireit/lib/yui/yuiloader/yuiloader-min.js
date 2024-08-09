/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
if (typeof YAHOO == "undefined" || !YAHOO) {
  var YAHOO = {};
}
YAHOO.namespace = function () {
  var A = arguments,
    E = null,
    C,
    B,
    D;
  for (C = 0; C < A.length; C = C + 1) {
    D = ("" + A[C]).split(".");
    E = YAHOO;
    for (B = D[0] == "YAHOO" ? 1 : 0; B < D.length; B = B + 1) {
      E[D[B]] = E[D[B]] || {};
      E = E[D[B]];
    }
  }
  return E;
};
YAHOO.log = function (D, A, C) {
  var B = YAHOO.widget.Logger;
  if (B && B.log) {
    return B.log(D, A, C);
  } else {
    return false;
  }
};
YAHOO.register = function (A, E, D) {
  var I = YAHOO.env.modules,
    B,
    H,
    G,
    F,
    C;
  if (!I[A]) {
    I[A] = { versions: [], builds: [] };
  }
  B = I[A];
  H = D.version;
  G = D.build;
  F = YAHOO.env.listeners;
  B.name = A;
  B.version = H;
  B.build = G;
  B.versions.push(H);
  B.builds.push(G);
  B.mainClass = E;
  for (C = 0; C < F.length; C = C + 1) {
    F[C](B);
  }
  if (E) {
    E.VERSION = H;
    E.BUILD = G;
  } else {
    YAHOO.log("mainClass is undefined for module " + A, "warn");
  }
};
YAHOO.env = YAHOO.env || { modules: [], listeners: [] };
YAHOO.env.getVersion = function (A) {
  return YAHOO.env.modules[A] || null;
};
YAHOO.env.ua = (function () {
  var C = {
      ie: 0,
      opera: 0,
      gecko: 0,
      webkit: 0,
      mobile: null,
      air: 0,
      caja: 0,
    },
    B = navigator.userAgent,
    A;
  if (/KHTML/.test(B)) {
    C.webkit = 1;
  }
  A = B.match(/AppleWebKit\/([^\s]*)/);
  if (A && A[1]) {
    C.webkit = parseFloat(A[1]);
    if (/ Mobile\//.test(B)) {
      C.mobile = "Apple";
    } else {
      A = B.match(/NokiaN[^\/]*/);
      if (A) {
        C.mobile = A[0];
      }
    }
    A = B.match(/AdobeAIR\/([^\s]*)/);
    if (A) {
      C.air = A[0];
    }
  }
  if (!C.webkit) {
    A = B.match(/Opera[\s\/]([^\s]*)/);
    if (A && A[1]) {
      C.opera = parseFloat(A[1]);
      A = B.match(/Opera Mini[^;]*/);
      if (A) {
        C.mobile = A[0];
      }
    } else {
      A = B.match(/MSIE\s([^;]*)/);
      if (A && A[1]) {
        C.ie = parseFloat(A[1]);
      } else {
        A = B.match(/Gecko\/([^\s]*)/);
        if (A) {
          C.gecko = 1;
          A = B.match(/rv:([^\s\)]*)/);
          if (A && A[1]) {
            C.gecko = parseFloat(A[1]);
          }
        }
      }
    }
  }
  A = B.match(/Caja\/([^\s]*)/);
  if (A && A[1]) {
    C.caja = parseFloat(A[1]);
  }
  return C;
})();
(function () {
  YAHOO.namespace("util", "widget", "example");
  if ("undefined" !== typeof YAHOO_config) {
    var B = YAHOO_config.listener,
      A = YAHOO.env.listeners,
      D = true,
      C;
    if (B) {
      for (C = 0; C < A.length; C = C + 1) {
        if (A[C] == B) {
          D = false;
          break;
        }
      }
      if (D) {
        A.push(B);
      }
    }
  }
})();
YAHOO.lang = YAHOO.lang || {};
(function () {
  var B = YAHOO.lang,
    F = "[object Array]",
    C = "[object Function]",
    A = Object.prototype,
    E = ["toString", "valueOf"],
    D = {
      isArray: function (G) {
        return A.toString.apply(G) === F;
      },
      isBoolean: function (G) {
        return typeof G === "boolean";
      },
      isFunction: function (G) {
        return A.toString.apply(G) === C;
      },
      isNull: function (G) {
        return G === null;
      },
      isNumber: function (G) {
        return typeof G === "number" && isFinite(G);
      },
      isObject: function (G) {
        return (G && (typeof G === "object" || B.isFunction(G))) || false;
      },
      isString: function (G) {
        return typeof G === "string";
      },
      isUndefined: function (G) {
        return typeof G === "undefined";
      },
      _IEEnumFix: YAHOO.env.ua.ie
        ? function (I, H) {
            var G, K, J;
            for (G = 0; G < E.length; G = G + 1) {
              K = E[G];
              J = H[K];
              if (B.isFunction(J) && J != A[K]) {
                I[K] = J;
              }
            }
          }
        : function () {},
      extend: function (J, K, I) {
        if (!K || !J) {
          throw new Error(
            "extend failed, please check that " +
              "all dependencies are included.",
          );
        }
        var H = function () {},
          G;
        H.prototype = K.prototype;
        J.prototype = new H();
        J.prototype.constructor = J;
        J.superclass = K.prototype;
        if (K.prototype.constructor == A.constructor) {
          K.prototype.constructor = K;
        }
        if (I) {
          for (G in I) {
            if (B.hasOwnProperty(I, G)) {
              J.prototype[G] = I[G];
            }
          }
          B._IEEnumFix(J.prototype, I);
        }
      },
      augmentObject: function (K, J) {
        if (!J || !K) {
          throw new Error("Absorb failed, verify dependencies.");
        }
        var G = arguments,
          I,
          L,
          H = G[2];
        if (H && H !== true) {
          for (I = 2; I < G.length; I = I + 1) {
            K[G[I]] = J[G[I]];
          }
        } else {
          for (L in J) {
            if (H || !(L in K)) {
              K[L] = J[L];
            }
          }
          B._IEEnumFix(K, J);
        }
      },
      augmentProto: function (J, I) {
        if (!I || !J) {
          throw new Error("Augment failed, verify dependencies.");
        }
        var G = [J.prototype, I.prototype],
          H;
        for (H = 2; H < arguments.length; H = H + 1) {
          G.push(arguments[H]);
        }
        B.augmentObject.apply(this, G);
      },
      dump: function (G, L) {
        var I,
          K,
          N = [],
          O = "{...}",
          H = "f(){...}",
          M = ", ",
          J = " => ";
        if (!B.isObject(G)) {
          return G + "";
        } else {
          if (G instanceof Date || ("nodeType" in G && "tagName" in G)) {
            return G;
          } else {
            if (B.isFunction(G)) {
              return H;
            }
          }
        }
        L = B.isNumber(L) ? L : 3;
        if (B.isArray(G)) {
          N.push("[");
          for (I = 0, K = G.length; I < K; I = I + 1) {
            if (B.isObject(G[I])) {
              N.push(L > 0 ? B.dump(G[I], L - 1) : O);
            } else {
              N.push(G[I]);
            }
            N.push(M);
          }
          if (N.length > 1) {
            N.pop();
          }
          N.push("]");
        } else {
          N.push("{");
          for (I in G) {
            if (B.hasOwnProperty(G, I)) {
              N.push(I + J);
              if (B.isObject(G[I])) {
                N.push(L > 0 ? B.dump(G[I], L - 1) : O);
              } else {
                N.push(G[I]);
              }
              N.push(M);
            }
          }
          if (N.length > 1) {
            N.pop();
          }
          N.push("}");
        }
        return N.join("");
      },
      substitute: function (V, H, O) {
        var L,
          K,
          J,
          R,
          S,
          U,
          Q = [],
          I,
          M = "dump",
          P = " ",
          G = "{",
          T = "}",
          N;
        for (;;) {
          L = V.lastIndexOf(G);
          if (L < 0) {
            break;
          }
          K = V.indexOf(T, L);
          if (L + 1 >= K) {
            break;
          }
          I = V.substring(L + 1, K);
          R = I;
          U = null;
          J = R.indexOf(P);
          if (J > -1) {
            U = R.substring(J + 1);
            R = R.substring(0, J);
          }
          S = H[R];
          if (O) {
            S = O(R, S, U);
          }
          if (B.isObject(S)) {
            if (B.isArray(S)) {
              S = B.dump(S, parseInt(U, 10));
            } else {
              U = U || "";
              N = U.indexOf(M);
              if (N > -1) {
                U = U.substring(4);
              }
              if (S.toString === A.toString || N > -1) {
                S = B.dump(S, parseInt(U, 10));
              } else {
                S = S.toString();
              }
            }
          } else {
            if (!B.isString(S) && !B.isNumber(S)) {
              S = "~-" + Q.length + "-~";
              Q[Q.length] = I;
            }
          }
          V = V.substring(0, L) + S + V.substring(K + 1);
        }
        for (L = Q.length - 1; L >= 0; L = L - 1) {
          V = V.replace(new RegExp("~-" + L + "-~"), "{" + Q[L] + "}", "g");
        }
        return V;
      },
      trim: function (G) {
        try {
          return G.replace(/^\s+|\s+$/g, "");
        } catch (H) {
          return G;
        }
      },
      merge: function () {
        var J = {},
          H = arguments,
          G = H.length,
          I;
        for (I = 0; I < G; I = I + 1) {
          B.augmentObject(J, H[I], true);
        }
        return J;
      },
      later: function (N, H, O, J, K) {
        N = N || 0;
        H = H || {};
        var I = O,
          M = J,
          L,
          G;
        if (B.isString(O)) {
          I = H[O];
        }
        if (!I) {
          throw new TypeError("method undefined");
        }
        if (!B.isArray(M)) {
          M = [J];
        }
        L = function () {
          I.apply(H, M);
        };
        G = K ? setInterval(L, N) : setTimeout(L, N);
        return {
          interval: K,
          cancel: function () {
            if (this.interval) {
              clearInterval(G);
            } else {
              clearTimeout(G);
            }
          },
        };
      },
      isValue: function (G) {
        return (
          B.isObject(G) || B.isString(G) || B.isNumber(G) || B.isBoolean(G)
        );
      },
    };
  B.hasOwnProperty = A.hasOwnProperty
    ? function (G, H) {
        return G && G.hasOwnProperty(H);
      }
    : function (G, H) {
        return !B.isUndefined(G[H]) && G.constructor.prototype[H] !== G[H];
      };
  D.augmentObject(B, D, true);
  YAHOO.util.Lang = B;
  B.augment = B.augmentProto;
  YAHOO.augment = B.augmentProto;
  YAHOO.extend = B.extend;
})();
YAHOO.register("yahoo", YAHOO, { version: "2.7.0", build: "1799" });
YAHOO.util.Get = (function () {
  var M = {},
    L = 0,
    R = 0,
    E = false,
    N = YAHOO.env.ua,
    S = YAHOO.lang;
  var J = function (W, T, X) {
    var U = X || window,
      Y = U.document,
      Z = Y.createElement(W);
    for (var V in T) {
      if (T[V] && YAHOO.lang.hasOwnProperty(T, V)) {
        Z.setAttribute(V, T[V]);
      }
    }
    return Z;
  };
  var I = function (T, U, W) {
    var V = W || "utf-8";
    return J(
      "link",
      {
        id: "yui__dyn_" + R++,
        type: "text/css",
        charset: V,
        rel: "stylesheet",
        href: T,
      },
      U,
    );
  };
  var P = function (T, U, W) {
    var V = W || "utf-8";
    return J(
      "script",
      { id: "yui__dyn_" + R++, type: "text/javascript", charset: V, src: T },
      U,
    );
  };
  var A = function (T, U) {
    return {
      tId: T.tId,
      win: T.win,
      data: T.data,
      nodes: T.nodes,
      msg: U,
      purge: function () {
        D(this.tId);
      },
    };
  };
  var B = function (T, W) {
    var U = M[W],
      V = S.isString(T) ? U.win.document.getElementById(T) : T;
    if (!V) {
      Q(W, "target node not found: " + T);
    }
    return V;
  };
  var Q = function (W, V) {
    var T = M[W];
    if (T.onFailure) {
      var U = T.scope || T.win;
      T.onFailure.call(U, A(T, V));
    }
  };
  var C = function (W) {
    var T = M[W];
    T.finished = true;
    if (T.aborted) {
      var V = "transaction " + W + " was aborted";
      Q(W, V);
      return;
    }
    if (T.onSuccess) {
      var U = T.scope || T.win;
      T.onSuccess.call(U, A(T));
    }
  };
  var O = function (V) {
    var T = M[V];
    if (T.onTimeout) {
      var U = T.scope || T;
      T.onTimeout.call(U, A(T));
    }
  };
  var G = function (V, Z) {
    var U = M[V];
    if (U.timer) {
      U.timer.cancel();
    }
    if (U.aborted) {
      var X = "transaction " + V + " was aborted";
      Q(V, X);
      return;
    }
    if (Z) {
      U.url.shift();
      if (U.varName) {
        U.varName.shift();
      }
    } else {
      U.url = S.isString(U.url) ? [U.url] : U.url;
      if (U.varName) {
        U.varName = S.isString(U.varName) ? [U.varName] : U.varName;
      }
    }
    var c = U.win,
      b = c.document,
      a = b.getElementsByTagName("head")[0],
      W;
    if (U.url.length === 0) {
      if (
        U.type === "script" &&
        N.webkit &&
        N.webkit < 420 &&
        !U.finalpass &&
        !U.varName
      ) {
        var Y = P(null, U.win, U.charset);
        Y.innerHTML = 'YAHOO.util.Get._finalize("' + V + '");';
        U.nodes.push(Y);
        a.appendChild(Y);
      } else {
        C(V);
      }
      return;
    }
    var T = U.url[0];
    if (!T) {
      U.url.shift();
      return G(V);
    }
    if (U.timeout) {
      U.timer = S.later(U.timeout, U, O, V);
    }
    if (U.type === "script") {
      W = P(T, c, U.charset);
    } else {
      W = I(T, c, U.charset);
    }
    F(U.type, W, V, T, c, U.url.length);
    U.nodes.push(W);
    if (U.insertBefore) {
      var e = B(U.insertBefore, V);
      if (e) {
        e.parentNode.insertBefore(W, e);
      }
    } else {
      a.appendChild(W);
    }
    if ((N.webkit || N.gecko) && U.type === "css") {
      G(V, T);
    }
  };
  var K = function () {
    if (E) {
      return;
    }
    E = true;
    for (var T in M) {
      var U = M[T];
      if (U.autopurge && U.finished) {
        D(U.tId);
        delete M[T];
      }
    }
    E = false;
  };
  var D = function (a) {
    var X = M[a];
    if (X) {
      var Z = X.nodes,
        T = Z.length,
        Y = X.win.document,
        W = Y.getElementsByTagName("head")[0];
      if (X.insertBefore) {
        var V = B(X.insertBefore, a);
        if (V) {
          W = V.parentNode;
        }
      }
      for (var U = 0; U < T; U = U + 1) {
        W.removeChild(Z[U]);
      }
      X.nodes = [];
    }
  };
  var H = function (U, T, V) {
    var X = "q" + L++;
    V = V || {};
    if (L % YAHOO.util.Get.PURGE_THRESH === 0) {
      K();
    }
    M[X] = S.merge(V, {
      tId: X,
      type: U,
      url: T,
      finished: false,
      aborted: false,
      nodes: [],
    });
    var W = M[X];
    W.win = W.win || window;
    W.scope = W.scope || W.win;
    W.autopurge =
      "autopurge" in W ? W.autopurge : U === "script" ? true : false;
    S.later(0, W, G, X);
    return { tId: X };
  };
  var F = function (c, X, W, U, Y, Z, b) {
    var a = b || G;
    if (N.ie) {
      X.onreadystatechange = function () {
        var d = this.readyState;
        if ("loaded" === d || "complete" === d) {
          X.onreadystatechange = null;
          a(W, U);
        }
      };
    } else {
      if (N.webkit) {
        if (c === "script") {
          if (N.webkit >= 420) {
            X.addEventListener("load", function () {
              a(W, U);
            });
          } else {
            var T = M[W];
            if (T.varName) {
              var V = YAHOO.util.Get.POLL_FREQ;
              T.maxattempts = YAHOO.util.Get.TIMEOUT / V;
              T.attempts = 0;
              T._cache = T.varName[0].split(".");
              T.timer = S.later(
                V,
                T,
                function (j) {
                  var f = this._cache,
                    e = f.length,
                    d = this.win,
                    g;
                  for (g = 0; g < e; g = g + 1) {
                    d = d[f[g]];
                    if (!d) {
                      this.attempts++;
                      if (this.attempts++ > this.maxattempts) {
                        var h = "Over retry limit, giving up";
                        T.timer.cancel();
                        Q(W, h);
                      } else {
                      }
                      return;
                    }
                  }
                  T.timer.cancel();
                  a(W, U);
                },
                null,
                true,
              );
            } else {
              S.later(YAHOO.util.Get.POLL_FREQ, null, a, [W, U]);
            }
          }
        }
      } else {
        X.onload = function () {
          a(W, U);
        };
      }
    }
  };
  return {
    POLL_FREQ: 10,
    PURGE_THRESH: 20,
    TIMEOUT: 2000,
    _finalize: function (T) {
      S.later(0, null, C, T);
    },
    abort: function (U) {
      var V = S.isString(U) ? U : U.tId;
      var T = M[V];
      if (T) {
        T.aborted = true;
      }
    },
    script: function (T, U) {
      return H("script", T, U);
    },
    css: function (T, U) {
      return H("css", T, U);
    },
  };
})();
YAHOO.register("get", YAHOO.util.Get, { version: "2.7.0", build: "1799" });
(function () {
  var Y = YAHOO,
    util = Y.util,
    lang = Y.lang,
    env = Y.env,
    PROV = "_provides",
    SUPER = "_supersedes",
    REQ = "expanded",
    AFTER = "_after";
  var YUI = {
    dupsAllowed: { yahoo: true, get: true },
    info: {
      root: "2.7.0/build/",
      base: "http://yui.yahooapis.com/2.7.0/build/",
      comboBase: "http://yui.yahooapis.com/combo?",
      skin: {
        defaultSkin: "sam",
        base: "assets/skins/",
        path: "skin.css",
        after: ["reset", "fonts", "grids", "base"],
        rollup: 3,
      },
      dupsAllowed: ["yahoo", "get"],
      moduleInfo: {
        animation: {
          type: "js",
          path: "animation/animation-min.js",
          requires: ["dom", "event"],
        },
        autocomplete: {
          type: "js",
          path: "autocomplete/autocomplete-min.js",
          requires: ["dom", "event", "datasource"],
          optional: ["connection", "animation"],
          skinnable: true,
        },
        base: {
          type: "css",
          path: "base/base-min.css",
          after: ["reset", "fonts", "grids"],
        },
        button: {
          type: "js",
          path: "button/button-min.js",
          requires: ["element"],
          optional: ["menu"],
          skinnable: true,
        },
        calendar: {
          type: "js",
          path: "calendar/calendar-min.js",
          requires: ["event", "dom"],
          skinnable: true,
        },
        carousel: {
          type: "js",
          path: "carousel/carousel-min.js",
          requires: ["element"],
          optional: ["animation"],
          skinnable: true,
        },
        charts: {
          type: "js",
          path: "charts/charts-min.js",
          requires: ["element", "json", "datasource"],
        },
        colorpicker: {
          type: "js",
          path: "colorpicker/colorpicker-min.js",
          requires: ["slider", "element"],
          optional: ["animation"],
          skinnable: true,
        },
        connection: {
          type: "js",
          path: "connection/connection-min.js",
          requires: ["event"],
        },
        container: {
          type: "js",
          path: "container/container-min.js",
          requires: ["dom", "event"],
          optional: ["dragdrop", "animation", "connection"],
          supersedes: ["containercore"],
          skinnable: true,
        },
        containercore: {
          type: "js",
          path: "container/container_core-min.js",
          requires: ["dom", "event"],
          pkg: "container",
        },
        cookie: {
          type: "js",
          path: "cookie/cookie-min.js",
          requires: ["yahoo"],
        },
        datasource: {
          type: "js",
          path: "datasource/datasource-min.js",
          requires: ["event"],
          optional: ["connection"],
        },
        datatable: {
          type: "js",
          path: "datatable/datatable-min.js",
          requires: ["element", "datasource"],
          optional: ["calendar", "dragdrop", "paginator"],
          skinnable: true,
        },
        dom: { type: "js", path: "dom/dom-min.js", requires: ["yahoo"] },
        dragdrop: {
          type: "js",
          path: "dragdrop/dragdrop-min.js",
          requires: ["dom", "event"],
        },
        editor: {
          type: "js",
          path: "editor/editor-min.js",
          requires: ["menu", "element", "button"],
          optional: ["animation", "dragdrop"],
          supersedes: ["simpleeditor"],
          skinnable: true,
        },
        element: {
          type: "js",
          path: "element/element-min.js",
          requires: ["dom", "event"],
        },
        event: { type: "js", path: "event/event-min.js", requires: ["yahoo"] },
        fonts: { type: "css", path: "fonts/fonts-min.css" },
        get: { type: "js", path: "get/get-min.js", requires: ["yahoo"] },
        grids: {
          type: "css",
          path: "grids/grids-min.css",
          requires: ["fonts"],
          optional: ["reset"],
        },
        history: {
          type: "js",
          path: "history/history-min.js",
          requires: ["event"],
        },
        imagecropper: {
          type: "js",
          path: "imagecropper/imagecropper-min.js",
          requires: ["dom", "event", "dragdrop", "element", "resize"],
          skinnable: true,
        },
        imageloader: {
          type: "js",
          path: "imageloader/imageloader-min.js",
          requires: ["event", "dom"],
        },
        json: { type: "js", path: "json/json-min.js", requires: ["yahoo"] },
        layout: {
          type: "js",
          path: "layout/layout-min.js",
          requires: ["dom", "event", "element"],
          optional: ["animation", "dragdrop", "resize", "selector"],
          skinnable: true,
        },
        logger: {
          type: "js",
          path: "logger/logger-min.js",
          requires: ["event", "dom"],
          optional: ["dragdrop"],
          skinnable: true,
        },
        menu: {
          type: "js",
          path: "menu/menu-min.js",
          requires: ["containercore"],
          skinnable: true,
        },
        paginator: {
          type: "js",
          path: "paginator/paginator-min.js",
          requires: ["element"],
          skinnable: true,
        },
        profiler: {
          type: "js",
          path: "profiler/profiler-min.js",
          requires: ["yahoo"],
        },
        profilerviewer: {
          type: "js",
          path: "profilerviewer/profilerviewer-min.js",
          requires: ["profiler", "yuiloader", "element"],
          skinnable: true,
        },
        reset: { type: "css", path: "reset/reset-min.css" },
        "reset-fonts-grids": {
          type: "css",
          path: "reset-fonts-grids/reset-fonts-grids.css",
          supersedes: ["reset", "fonts", "grids", "reset-fonts"],
          rollup: 4,
        },
        "reset-fonts": {
          type: "css",
          path: "reset-fonts/reset-fonts.css",
          supersedes: ["reset", "fonts"],
          rollup: 2,
        },
        resize: {
          type: "js",
          path: "resize/resize-min.js",
          requires: ["dom", "event", "dragdrop", "element"],
          optional: ["animation"],
          skinnable: true,
        },
        selector: {
          type: "js",
          path: "selector/selector-min.js",
          requires: ["yahoo", "dom"],
        },
        simpleeditor: {
          type: "js",
          path: "editor/simpleeditor-min.js",
          requires: ["element"],
          optional: [
            "containercore",
            "menu",
            "button",
            "animation",
            "dragdrop",
          ],
          skinnable: true,
          pkg: "editor",
        },
        slider: {
          type: "js",
          path: "slider/slider-min.js",
          requires: ["dragdrop"],
          optional: ["animation"],
          skinnable: true,
        },
        stylesheet: {
          type: "js",
          path: "stylesheet/stylesheet-min.js",
          requires: ["yahoo"],
        },
        tabview: {
          type: "js",
          path: "tabview/tabview-min.js",
          requires: ["element"],
          optional: ["connection"],
          skinnable: true,
        },
        treeview: {
          type: "js",
          path: "treeview/treeview-min.js",
          requires: ["event", "dom"],
          optional: ["json"],
          skinnable: true,
        },
        uploader: {
          type: "js",
          path: "uploader/uploader.js",
          requires: ["element"],
        },
        utilities: {
          type: "js",
          path: "utilities/utilities.js",
          supersedes: [
            "yahoo",
            "event",
            "dragdrop",
            "animation",
            "dom",
            "connection",
            "element",
            "yahoo-dom-event",
            "get",
            "yuiloader",
            "yuiloader-dom-event",
          ],
          rollup: 8,
        },
        yahoo: { type: "js", path: "yahoo/yahoo-min.js" },
        "yahoo-dom-event": {
          type: "js",
          path: "yahoo-dom-event/yahoo-dom-event.js",
          supersedes: ["yahoo", "event", "dom"],
          rollup: 3,
        },
        yuiloader: {
          type: "js",
          path: "yuiloader/yuiloader-min.js",
          supersedes: ["yahoo", "get"],
        },
        "yuiloader-dom-event": {
          type: "js",
          path: "yuiloader-dom-event/yuiloader-dom-event.js",
          supersedes: [
            "yahoo",
            "dom",
            "event",
            "get",
            "yuiloader",
            "yahoo-dom-event",
          ],
          rollup: 5,
        },
        yuitest: {
          type: "js",
          path: "yuitest/yuitest-min.js",
          requires: ["logger"],
          skinnable: true,
        },
      },
    },
    ObjectUtil: {
      appendArray: function (o, a) {
        if (a) {
          for (var i = 0; i < a.length; i = i + 1) {
            o[a[i]] = true;
          }
        }
      },
      keys: function (o, ordered) {
        var a = [],
          i;
        for (i in o) {
          if (lang.hasOwnProperty(o, i)) {
            a.push(i);
          }
        }
        return a;
      },
    },
    ArrayUtil: {
      appendArray: function (a1, a2) {
        Array.prototype.push.apply(a1, a2);
      },
      indexOf: function (a, val) {
        for (var i = 0; i < a.length; i = i + 1) {
          if (a[i] === val) {
            return i;
          }
        }
        return -1;
      },
      toObject: function (a) {
        var o = {};
        for (var i = 0; i < a.length; i = i + 1) {
          o[a[i]] = true;
        }
        return o;
      },
      uniq: function (a) {
        return YUI.ObjectUtil.keys(YUI.ArrayUtil.toObject(a));
      },
    },
  };
  YAHOO.util.YUILoader = function (o) {
    this._internalCallback = null;
    this._useYahooListener = false;
    this.onSuccess = null;
    this.onFailure = Y.log;
    this.onProgress = null;
    this.onTimeout = null;
    this.scope = this;
    this.data = null;
    this.insertBefore = null;
    this.charset = null;
    this.varName = null;
    this.base = YUI.info.base;
    this.comboBase = YUI.info.comboBase;
    this.combine = false;
    this.root = YUI.info.root;
    this.timeout = 0;
    this.ignore = null;
    this.force = null;
    this.allowRollup = true;
    this.filter = null;
    this.required = {};
    this.moduleInfo = lang.merge(YUI.info.moduleInfo);
    this.rollups = null;
    this.loadOptional = false;
    this.sorted = [];
    this.loaded = {};
    this.dirty = true;
    this.inserted = {};
    var self = this;
    env.listeners.push(function (m) {
      if (self._useYahooListener) {
        self.loadNext(m.name);
      }
    });
    this.skin = lang.merge(YUI.info.skin);
    this._config(o);
  };
  Y.util.YUILoader.prototype = {
    FILTERS: {
      RAW: { searchExp: "-min\\.js", replaceStr: ".js" },
      DEBUG: { searchExp: "-min\\.js", replaceStr: "-debug.js" },
    },
    SKIN_PREFIX: "skin-",
    _config: function (o) {
      if (o) {
        for (var i in o) {
          if (lang.hasOwnProperty(o, i)) {
            if (i == "require") {
              this.require(o[i]);
            } else {
              this[i] = o[i];
            }
          }
        }
      }
      var f = this.filter;
      if (lang.isString(f)) {
        f = f.toUpperCase();
        if (f === "DEBUG") {
          this.require("logger");
        }
        if (!Y.widget.LogWriter) {
          Y.widget.LogWriter = function () {
            return Y;
          };
        }
        this.filter = this.FILTERS[f];
      }
    },
    addModule: function (o) {
      if (!o || !o.name || !o.type || (!o.path && !o.fullpath)) {
        return false;
      }
      o.ext = "ext" in o ? o.ext : true;
      o.requires = o.requires || [];
      this.moduleInfo[o.name] = o;
      this.dirty = true;
      return true;
    },
    require: function (what) {
      var a = typeof what === "string" ? arguments : what;
      this.dirty = true;
      YUI.ObjectUtil.appendArray(this.required, a);
    },
    _addSkin: function (skin, mod) {
      var name = this.formatSkin(skin),
        info = this.moduleInfo,
        sinf = this.skin,
        ext = info[mod] && info[mod].ext;
      if (!info[name]) {
        this.addModule({
          name: name,
          type: "css",
          path: sinf.base + skin + "/" + sinf.path,
          after: sinf.after,
          rollup: sinf.rollup,
          ext: ext,
        });
      }
      if (mod) {
        name = this.formatSkin(skin, mod);
        if (!info[name]) {
          var mdef = info[mod],
            pkg = mdef.pkg || mod;
          this.addModule({
            name: name,
            type: "css",
            after: sinf.after,
            path: pkg + "/" + sinf.base + skin + "/" + mod + ".css",
            ext: ext,
          });
        }
      }
      return name;
    },
    getRequires: function (mod) {
      if (!mod) {
        return [];
      }
      if (!this.dirty && mod.expanded) {
        return mod.expanded;
      }
      mod.requires = mod.requires || [];
      var i,
        d = [],
        r = mod.requires,
        o = mod.optional,
        info = this.moduleInfo,
        m;
      for (i = 0; i < r.length; i = i + 1) {
        d.push(r[i]);
        m = info[r[i]];
        YUI.ArrayUtil.appendArray(d, this.getRequires(m));
      }
      if (o && this.loadOptional) {
        for (i = 0; i < o.length; i = i + 1) {
          d.push(o[i]);
          YUI.ArrayUtil.appendArray(d, this.getRequires(info[o[i]]));
        }
      }
      mod.expanded = YUI.ArrayUtil.uniq(d);
      return mod.expanded;
    },
    getProvides: function (name, notMe) {
      var addMe = !notMe,
        ckey = addMe ? PROV : SUPER,
        m = this.moduleInfo[name],
        o = {};
      if (!m) {
        return o;
      }
      if (m[ckey]) {
        return m[ckey];
      }
      var s = m.supersedes,
        done = {},
        me = this;
      var add = function (mm) {
        if (!done[mm]) {
          done[mm] = true;
          lang.augmentObject(o, me.getProvides(mm));
        }
      };
      if (s) {
        for (var i = 0; i < s.length; i = i + 1) {
          add(s[i]);
        }
      }
      m[SUPER] = o;
      m[PROV] = lang.merge(o);
      m[PROV][name] = true;
      return m[ckey];
    },
    calculate: function (o) {
      if (o || this.dirty) {
        this._config(o);
        this._setup();
        this._explode();
        if (this.allowRollup) {
          this._rollup();
        }
        this._reduce();
        this._sort();
        this.dirty = false;
      }
    },
    _setup: function () {
      var info = this.moduleInfo,
        name,
        i,
        j;
      for (name in info) {
        if (lang.hasOwnProperty(info, name)) {
          var m = info[name];
          if (m && m.skinnable) {
            var o = this.skin.overrides,
              smod;
            if (o && o[name]) {
              for (i = 0; i < o[name].length; i = i + 1) {
                smod = this._addSkin(o[name][i], name);
              }
            } else {
              smod = this._addSkin(this.skin.defaultSkin, name);
            }
            m.requires.push(smod);
          }
        }
      }
      var l = lang.merge(this.inserted);
      if (!this._sandbox) {
        l = lang.merge(l, env.modules);
      }
      if (this.ignore) {
        YUI.ObjectUtil.appendArray(l, this.ignore);
      }
      if (this.force) {
        for (i = 0; i < this.force.length; i = i + 1) {
          if (this.force[i] in l) {
            delete l[this.force[i]];
          }
        }
      }
      for (j in l) {
        if (lang.hasOwnProperty(l, j)) {
          lang.augmentObject(l, this.getProvides(j));
        }
      }
      this.loaded = l;
    },
    _explode: function () {
      var r = this.required,
        i,
        mod;
      for (i in r) {
        if (lang.hasOwnProperty(r, i)) {
          mod = this.moduleInfo[i];
          if (mod) {
            var req = this.getRequires(mod);
            if (req) {
              YUI.ObjectUtil.appendArray(r, req);
            }
          }
        }
      }
    },
    _skin: function () {},
    formatSkin: function (skin, mod) {
      var s = this.SKIN_PREFIX + skin;
      if (mod) {
        s = s + "-" + mod;
      }
      return s;
    },
    parseSkin: function (mod) {
      if (mod.indexOf(this.SKIN_PREFIX) === 0) {
        var a = mod.split("-");
        return { skin: a[1], module: a[2] };
      }
      return null;
    },
    _rollup: function () {
      var i,
        j,
        m,
        s,
        rollups = {},
        r = this.required,
        roll,
        info = this.moduleInfo;
      if (this.dirty || !this.rollups) {
        for (i in info) {
          if (lang.hasOwnProperty(info, i)) {
            m = info[i];
            if (m && m.rollup) {
              rollups[i] = m;
            }
          }
        }
        this.rollups = rollups;
      }
      for (;;) {
        var rolled = false;
        for (i in rollups) {
          if (!r[i] && !this.loaded[i]) {
            m = info[i];
            s = m.supersedes;
            roll = false;
            if (!m.rollup) {
              continue;
            }
            var skin = m.ext ? false : this.parseSkin(i),
              c = 0;
            if (skin) {
              for (j in r) {
                if (lang.hasOwnProperty(r, j)) {
                  if (i !== j && this.parseSkin(j)) {
                    c++;
                    roll = c >= m.rollup;
                    if (roll) {
                      break;
                    }
                  }
                }
              }
            } else {
              for (j = 0; j < s.length; j = j + 1) {
                if (this.loaded[s[j]] && !YUI.dupsAllowed[s[j]]) {
                  roll = false;
                  break;
                } else {
                  if (r[s[j]]) {
                    c++;
                    roll = c >= m.rollup;
                    if (roll) {
                      break;
                    }
                  }
                }
              }
            }
            if (roll) {
              r[i] = true;
              rolled = true;
              this.getRequires(m);
            }
          }
        }
        if (!rolled) {
          break;
        }
      }
    },
    _reduce: function () {
      var i,
        j,
        s,
        m,
        r = this.required;
      for (i in r) {
        if (i in this.loaded) {
          delete r[i];
        } else {
          var skinDef = this.parseSkin(i);
          if (skinDef) {
            if (!skinDef.module) {
              var skin_pre = this.SKIN_PREFIX + skinDef.skin;
              for (j in r) {
                if (lang.hasOwnProperty(r, j)) {
                  m = this.moduleInfo[j];
                  var ext = m && m.ext;
                  if (!ext && j !== i && j.indexOf(skin_pre) > -1) {
                    delete r[j];
                  }
                }
              }
            }
          } else {
            m = this.moduleInfo[i];
            s = m && m.supersedes;
            if (s) {
              for (j = 0; j < s.length; j = j + 1) {
                if (s[j] in r) {
                  delete r[s[j]];
                }
              }
            }
          }
        }
      }
    },
    _onFailure: function (msg) {
      YAHOO.log("Failure", "info", "loader");
      var f = this.onFailure;
      if (f) {
        f.call(this.scope, {
          msg: "failure: " + msg,
          data: this.data,
          success: false,
        });
      }
    },
    _onTimeout: function () {
      YAHOO.log("Timeout", "info", "loader");
      var f = this.onTimeout;
      if (f) {
        f.call(this.scope, { msg: "timeout", data: this.data, success: false });
      }
    },
    _sort: function () {
      var s = [],
        info = this.moduleInfo,
        loaded = this.loaded,
        checkOptional = !this.loadOptional,
        me = this;
      var requires = function (aa, bb) {
        var mm = info[aa];
        if (loaded[bb] || !mm) {
          return false;
        }
        var ii,
          rr = mm.expanded,
          after = mm.after,
          other = info[bb],
          optional = mm.optional;
        if (rr && YUI.ArrayUtil.indexOf(rr, bb) > -1) {
          return true;
        }
        if (after && YUI.ArrayUtil.indexOf(after, bb) > -1) {
          return true;
        }
        if (
          checkOptional &&
          optional &&
          YUI.ArrayUtil.indexOf(optional, bb) > -1
        ) {
          return true;
        }
        var ss = info[bb] && info[bb].supersedes;
        if (ss) {
          for (ii = 0; ii < ss.length; ii = ii + 1) {
            if (requires(aa, ss[ii])) {
              return true;
            }
          }
        }
        if (mm.ext && mm.type == "css" && !other.ext && other.type == "css") {
          return true;
        }
        return false;
      };
      for (var i in this.required) {
        if (lang.hasOwnProperty(this.required, i)) {
          s.push(i);
        }
      }
      var p = 0;
      for (;;) {
        var l = s.length,
          a,
          b,
          j,
          k,
          moved = false;
        for (j = p; j < l; j = j + 1) {
          a = s[j];
          for (k = j + 1; k < l; k = k + 1) {
            if (requires(a, s[k])) {
              b = s.splice(k, 1);
              s.splice(j, 0, b[0]);
              moved = true;
              break;
            }
          }
          if (moved) {
            break;
          } else {
            p = p + 1;
          }
        }
        if (!moved) {
          break;
        }
      }
      this.sorted = s;
    },
    toString: function () {
      var o = {
        type: "YUILoader",
        base: this.base,
        filter: this.filter,
        required: this.required,
        loaded: this.loaded,
        inserted: this.inserted,
      };
      lang.dump(o, 1);
    },
    _combine: function () {
      this._combining = [];
      var self = this,
        s = this.sorted,
        len = s.length,
        js = this.comboBase,
        css = this.comboBase,
        target,
        startLen = js.length,
        i,
        m,
        type = this.loadType;
      YAHOO.log("type " + type);
      for (i = 0; i < len; i = i + 1) {
        m = this.moduleInfo[s[i]];
        if (m && !m.ext && (!type || type === m.type)) {
          target = this.root + m.path;
          target += "&";
          if (m.type == "js") {
            js += target;
          } else {
            css += target;
          }
          this._combining.push(s[i]);
        }
      }
      if (this._combining.length) {
        YAHOO.log(
          "Attempting to combine: " + this._combining,
          "info",
          "loader",
        );
        var callback = function (o) {
            var c = this._combining,
              len = c.length,
              i,
              m;
            for (i = 0; i < len; i = i + 1) {
              this.inserted[c[i]] = true;
            }
            this.loadNext(o.data);
          },
          loadScript = function () {
            if (js.length > startLen) {
              YAHOO.util.Get.script(self._filter(js), {
                data: self._loading,
                onSuccess: callback,
                onFailure: self._onFailure,
                onTimeout: self._onTimeout,
                insertBefore: self.insertBefore,
                charset: self.charset,
                timeout: self.timeout,
                scope: self,
              });
            }
          };
        if (css.length > startLen) {
          YAHOO.util.Get.css(this._filter(css), {
            data: this._loading,
            onSuccess: loadScript,
            onFailure: this._onFailure,
            onTimeout: this._onTimeout,
            insertBefore: this.insertBefore,
            charset: this.charset,
            timeout: this.timeout,
            scope: self,
          });
        } else {
          loadScript();
        }
      } else {
        this.loadNext(this._loading);
      }
    },
    insert: function (o, type) {
      this.calculate(o);
      this._loading = true;
      this.loadType = type;
      if (this.combine) {
        return this._combine();
      }
      if (!type) {
        var self = this;
        this._internalCallback = function () {
          self._internalCallback = null;
          self.insert(null, "js");
        };
        this.insert(null, "css");
        return;
      }
      this.loadNext();
    },
    sandbox: function (o, type) {
      this._config(o);
      if (!this.onSuccess) {
        throw new Error(
          "You must supply an onSuccess handler for your sandbox",
        );
      }
      this._sandbox = true;
      var self = this;
      if (!type || type !== "js") {
        this._internalCallback = function () {
          self._internalCallback = null;
          self.sandbox(null, "js");
        };
        this.insert(null, "css");
        return;
      }
      if (!util.Connect) {
        var ld = new YAHOO.util.YUILoader();
        ld.insert(
          {
            base: this.base,
            filter: this.filter,
            require: "connection",
            insertBefore: this.insertBefore,
            charset: this.charset,
            onSuccess: function () {
              this.sandbox(null, "js");
            },
            scope: this,
          },
          "js",
        );
        return;
      }
      this._scriptText = [];
      this._loadCount = 0;
      this._stopCount = this.sorted.length;
      this._xhr = [];
      this.calculate();
      var s = this.sorted,
        l = s.length,
        i,
        m,
        url;
      for (i = 0; i < l; i = i + 1) {
        m = this.moduleInfo[s[i]];
        if (!m) {
          this._onFailure("undefined module " + m);
          for (var j = 0; j < this._xhr.length; j = j + 1) {
            this._xhr[j].abort();
          }
          return;
        }
        if (m.type !== "js") {
          this._loadCount++;
          continue;
        }
        url = m.fullpath;
        url = url ? this._filter(url) : this._url(m.path);
        var xhrData = {
          success: function (o) {
            var idx = o.argument[0],
              name = o.argument[2];
            this._scriptText[idx] = o.responseText;
            if (this.onProgress) {
              this.onProgress.call(this.scope, {
                name: name,
                scriptText: o.responseText,
                xhrResponse: o,
                data: this.data,
              });
            }
            this._loadCount++;
            if (this._loadCount >= this._stopCount) {
              var v = this.varName || "YAHOO";
              var t = "(function() {\n";
              var b = "\nreturn " + v + ";\n})();";
              var ref = eval(t + this._scriptText.join("\n") + b);
              this._pushEvents(ref);
              if (ref) {
                this.onSuccess.call(this.scope, {
                  reference: ref,
                  data: this.data,
                });
              } else {
                this._onFailure.call(this.varName + " reference failure");
              }
            }
          },
          failure: function (o) {
            this.onFailure.call(this.scope, {
              msg: "XHR failure",
              xhrResponse: o,
              data: this.data,
            });
          },
          scope: this,
          argument: [i, url, s[i]],
        };
        this._xhr.push(util.Connect.asyncRequest("GET", url, xhrData));
      }
    },
    loadNext: function (mname) {
      if (!this._loading) {
        return;
      }
      if (mname) {
        if (mname !== this._loading) {
          return;
        }
        this.inserted[mname] = true;
        if (this.onProgress) {
          this.onProgress.call(this.scope, { name: mname, data: this.data });
        }
      }
      var s = this.sorted,
        len = s.length,
        i,
        m;
      for (i = 0; i < len; i = i + 1) {
        if (s[i] in this.inserted) {
          continue;
        }
        if (s[i] === this._loading) {
          return;
        }
        m = this.moduleInfo[s[i]];
        if (!m) {
          this.onFailure.call(this.scope, {
            msg: "undefined module " + m,
            data: this.data,
          });
          return;
        }
        if (!this.loadType || this.loadType === m.type) {
          this._loading = s[i];
          var fn = m.type === "css" ? util.Get.css : util.Get.script,
            url = m.fullpath,
            self = this,
            c = function (o) {
              self.loadNext(o.data);
            };
          url = url ? this._filter(url) : this._url(m.path);
          if (
            env.ua.webkit &&
            env.ua.webkit < 420 &&
            m.type === "js" &&
            !m.varName
          ) {
            c = null;
            this._useYahooListener = true;
          }
          fn(url, {
            data: s[i],
            onSuccess: c,
            onFailure: this._onFailure,
            onTimeout: this._onTimeout,
            insertBefore: this.insertBefore,
            charset: this.charset,
            timeout: this.timeout,
            varName: m.varName,
            scope: self,
          });
          return;
        }
      }
      this._loading = null;
      if (this._internalCallback) {
        var f = this._internalCallback;
        this._internalCallback = null;
        f.call(this);
      } else {
        if (this.onSuccess) {
          this._pushEvents();
          this.onSuccess.call(this.scope, { data: this.data });
        }
      }
    },
    _pushEvents: function (ref) {
      var r = ref || YAHOO;
      if (r.util && r.util.Event) {
        r.util.Event._load();
      }
    },
    _filter: function (str) {
      var f = this.filter;
      return f ? str.replace(new RegExp(f.searchExp, "g"), f.replaceStr) : str;
    },
    _url: function (path) {
      return this._filter((this.base || "") + path);
    },
  };
})();
YAHOO.register("yuiloader", YAHOO.util.YUILoader, {
  version: "2.7.0",
  build: "1799",
});
