/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
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
