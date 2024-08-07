/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
YAHOO.util.History = (function () {
  var C = null;
  var K = null;
  var F = false;
  var D = [];
  var B = [];

  function I() {
    var M, L;
    L = top.location.href;
    M = L.indexOf("#");
    return M >= 0 ? L.substr(M + 1) : null;
  }

  function A() {
    var M,
      N,
      O = [],
      L = [];
    for (M in D) {
      if (YAHOO.lang.hasOwnProperty(D, M)) {
        N = D[M];
        O.push(M + "=" + N.initialState);
        L.push(M + "=" + N.currentState);
      }
    }
    K.value = O.join("&") + "|" + L.join("&");
    if (YAHOO.env.ua.webkit) {
      K.value += "|" + B.join(",");
    }
  }

  function H(L) {
    var Q, R, M, O, P, T, S, N;
    if (!L) {
      for (M in D) {
        if (YAHOO.lang.hasOwnProperty(D, M)) {
          O = D[M];
          O.currentState = O.initialState;
          O.onStateChange(unescape(O.currentState));
        }
      }
      return;
    }
    P = [];
    T = L.split("&");
    for (Q = 0, R = T.length; Q < R; Q++) {
      S = T[Q].split("=");
      if (S.length === 2) {
        M = S[0];
        N = S[1];
        P[M] = N;
      }
    }
    for (M in D) {
      if (YAHOO.lang.hasOwnProperty(D, M)) {
        O = D[M];
        N = P[M];
        if (!N || O.currentState !== N) {
          O.currentState = N || O.initialState;
          O.onStateChange(unescape(O.currentState));
        }
      }
    }
  }

  function J(O) {
    var L, N;
    L = '<html><body><div id="state">' + O + "</div></body></html>";
    try {
      N = C.contentWindow.document;
      N.open();
      N.write(L);
      N.close();
      return true;
    } catch (M) {
      return false;
    }
  }

  function G() {
    var O, L, N, M;
    if (!C.contentWindow || !C.contentWindow.document) {
      setTimeout(G, 10);
      return;
    }
    O = C.contentWindow.document;
    L = O.getElementById("state");
    N = L ? L.innerText : null;
    M = I();
    setInterval(function () {
      var U, Q, R, S, T, P;
      O = C.contentWindow.document;
      L = O.getElementById("state");
      U = L ? L.innerText : null;
      T = I();
      if (U !== N) {
        N = U;
        H(N);
        if (!N) {
          Q = [];
          for (R in D) {
            if (YAHOO.lang.hasOwnProperty(D, R)) {
              S = D[R];
              Q.push(R + "=" + S.initialState);
            }
          }
          T = Q.join("&");
        } else {
          T = N;
        }
        top.location.hash = T;
        M = T;
        A();
      } else {
        if (T !== M) {
          M = T;
          J(T);
        }
      }
    }, 50);
    F = true;
    YAHOO.util.History.onLoadEvent.fire();
  }

  function E() {
    var S, U, Q, W, M, O, V, P, T, N, L, R;
    Q = K.value.split("|");
    if (Q.length > 1) {
      V = Q[0].split("&");
      for (S = 0, U = V.length; S < U; S++) {
        W = V[S].split("=");
        if (W.length === 2) {
          M = W[0];
          P = W[1];
          O = D[M];
          if (O) {
            O.initialState = P;
          }
        }
      }
      T = Q[1].split("&");
      for (S = 0, U = T.length; S < U; S++) {
        W = T[S].split("=");
        if (W.length >= 2) {
          M = W[0];
          N = W[1];
          O = D[M];
          if (O) {
            O.currentState = N;
          }
        }
      }
    }
    if (Q.length > 2) {
      B = Q[2].split(",");
    }
    if (YAHOO.env.ua.ie) {
      if (
        typeof document.documentMode === "undefined" ||
        document.documentMode < 8
      ) {
        G();
      } else {
        YAHOO.util.Event.on(top, "hashchange", function () {
          var X = I();
          H(X);
          A();
        });
        F = true;
        YAHOO.util.History.onLoadEvent.fire();
      }
    } else {
      L = history.length;
      R = I();
      setInterval(function () {
        var Z, X, Y;
        X = I();
        Y = history.length;
        if (X !== R) {
          R = X;
          L = Y;
          H(R);
          A();
        } else {
          if (Y !== L && YAHOO.env.ua.webkit) {
            R = X;
            L = Y;
            Z = B[L - 1];
            H(Z);
            A();
          }
        }
      }, 50);
      F = true;
      YAHOO.util.History.onLoadEvent.fire();
    }
  }

  return {
    onLoadEvent: new YAHOO.util.CustomEvent("onLoad"),
    onReady: function (M, N, L) {
      if (F) {
        setTimeout(function () {
          var O = window;
          if (L) {
            if (L === true) {
              O = N;
            } else {
              O = L;
            }
          }
          M.call(O, "onLoad", [], N);
        }, 0);
      } else {
        YAHOO.util.History.onLoadEvent.subscribe(M, N, L);
      }
    },
    register: function (O, L, Q, R, N) {
      var P, M;
      if (
        typeof O !== "string" ||
        YAHOO.lang.trim(O) === "" ||
        typeof L !== "string" ||
        typeof Q !== "function"
      ) {
        throw new Error("Missing or invalid argument");
      }
      if (D[O]) {
        return;
      }
      if (F) {
        throw new Error(
          "All modules must be registered before calling YAHOO.util.History.initialize",
        );
      }
      O = escape(O);
      L = escape(L);
      P = null;
      if (N === true) {
        P = R;
      } else {
        P = N;
      }
      M = function (S) {
        return Q.call(P, S, R);
      };
      D[O] = { name: O, initialState: L, currentState: L, onStateChange: M };
    },
    initialize: function (L, M) {
      if (F) {
        return;
      }
      if (YAHOO.env.ua.opera && typeof history.navigationMode !== "undefined") {
        history.navigationMode = "compatible";
      }
      if (typeof L === "string") {
        L = document.getElementById(L);
      }
      if (
        !L ||
        (L.tagName.toUpperCase() !== "TEXTAREA" &&
          (L.tagName.toUpperCase() !== "INPUT" ||
            (L.type !== "hidden" && L.type !== "text")))
      ) {
        throw new Error("Missing or invalid argument");
      }
      K = L;
      if (
        YAHOO.env.ua.ie &&
        (typeof document.documentMode === "undefined" ||
          document.documentMode < 8)
      ) {
        if (typeof M === "string") {
          M = document.getElementById(M);
        }
        if (!M || M.tagName.toUpperCase() !== "IFRAME") {
          throw new Error("Missing or invalid argument");
        }
        C = M;
      }
      YAHOO.util.Event.onDOMReady(E);
    },
    navigate: function (M, N) {
      var L;
      if (typeof M !== "string" || typeof N !== "string") {
        throw new Error("Missing or invalid argument");
      }
      L = {};
      L[M] = N;
      return YAHOO.util.History.multiNavigate(L);
    },
    multiNavigate: function (M) {
      var L, N, P, O, Q;
      if (typeof M !== "object") {
        throw new Error("Missing or invalid argument");
      }
      if (!F) {
        throw new Error("The Browser History Manager is not initialized");
      }
      for (N in M) {
        if (!D[N]) {
          throw new Error("The following module has not been registered: " + N);
        }
      }
      L = [];
      for (N in D) {
        if (YAHOO.lang.hasOwnProperty(D, N)) {
          P = D[N];
          if (YAHOO.lang.hasOwnProperty(M, N)) {
            O = M[unescape(N)];
          } else {
            O = unescape(P.currentState);
          }
          N = escape(N);
          O = escape(O);
          L.push(N + "=" + O);
        }
      }
      Q = L.join("&");
      if (
        YAHOO.env.ua.ie &&
        (typeof document.documentMode === "undefined" ||
          document.documentMode < 8)
      ) {
        return J(Q);
      } else {
        top.location.hash = Q;
        if (YAHOO.env.ua.webkit) {
          B[history.length] = Q;
          A();
        }
        return true;
      }
    },
    getCurrentState: function (L) {
      var M;
      if (typeof L !== "string") {
        throw new Error("Missing or invalid argument");
      }
      if (!F) {
        throw new Error("The Browser History Manager is not initialized");
      }
      M = D[L];
      if (!M) {
        throw new Error("No such registered module: " + L);
      }
      return unescape(M.currentState);
    },
    getBookmarkedState: function (Q) {
      var P, M, L, S, N, R, O;
      if (typeof Q !== "string") {
        throw new Error("Missing or invalid argument");
      }
      L = top.location.href.indexOf("#");
      if (L >= 0) {
        S = top.location.href.substr(L + 1);
        N = S.split("&");
        for (P = 0, M = N.length; P < M; P++) {
          R = N[P].split("=");
          if (R.length === 2) {
            O = R[0];
            if (O === Q) {
              return unescape(R[1]);
            }
          }
        }
      }
      return null;
    },
    getQueryStringParameter: function (Q, N) {
      var O, M, L, S, R, P;
      N = N || top.location.href;
      L = N.indexOf("?");
      S = L >= 0 ? N.substr(L + 1) : N;
      L = S.lastIndexOf("#");
      S = L >= 0 ? S.substr(0, L) : S;
      R = S.split("&");
      for (O = 0, M = R.length; O < M; O++) {
        P = R[O].split("=");
        if (P.length >= 2) {
          if (P[0] === Q) {
            return unescape(P[1]);
          }
        }
      }
      return null;
    },
  };
})();
YAHOO.register("history", YAHOO.util.History, {
  version: "2.7.0",
  build: "1799",
});
