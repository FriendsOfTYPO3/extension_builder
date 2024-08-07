/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  YAHOO.env._id_counter = YAHOO.env._id_counter || 0;
  var E = YAHOO.util,
    L = YAHOO.lang,
    m = YAHOO.env.ua,
    A = YAHOO.lang.trim,
    d = {},
    h = {},
    N = /^t(?:able|d|h)$/i,
    X = /color$/i,
    K = window.document,
    W = K.documentElement,
    e = "ownerDocument",
    n = "defaultView",
    v = "documentElement",
    t = "compatMode",
    b = "offsetLeft",
    P = "offsetTop",
    u = "offsetParent",
    Z = "parentNode",
    l = "nodeType",
    C = "tagName",
    O = "scrollLeft",
    i = "scrollTop",
    Q = "getBoundingClientRect",
    w = "getComputedStyle",
    a = "currentStyle",
    M = "CSS1Compat",
    c = "BackCompat",
    g = "class",
    F = "className",
    J = "",
    B = " ",
    s = "(?:^|\\s)",
    k = "(?= |$)",
    U = "g",
    p = "position",
    f = "fixed",
    V = "relative",
    j = "left",
    o = "top",
    r = "medium",
    q = "borderLeftWidth",
    R = "borderTopWidth",
    D = m.opera,
    I = m.webkit,
    H = m.gecko,
    T = m.ie;
  E.Dom = {
    CUSTOM_ATTRIBUTES: !W.hasAttribute
      ? { for: "htmlFor", class: F }
      : { htmlFor: "for", className: g },
    get: function (y) {
      var AA, Y, z, x, G;
      if (y) {
        if (y[l] || y.item) {
          return y;
        }
        if (typeof y === "string") {
          AA = y;
          y = K.getElementById(y);
          if (y && y.id === AA) {
            return y;
          } else {
            if (y && K.all) {
              y = null;
              Y = K.all[AA];
              for (x = 0, G = Y.length; x < G; ++x) {
                if (Y[x].id === AA) {
                  return Y[x];
                }
              }
            }
          }
          return y;
        }
        if (y.DOM_EVENTS) {
          y = y.get("element");
        }
        if ("length" in y) {
          z = [];
          for (x = 0, G = y.length; x < G; ++x) {
            z[z.length] = E.Dom.get(y[x]);
          }
          return z;
        }
        return y;
      }
      return null;
    },
    getComputedStyle: function (G, Y) {
      if (window[w]) {
        return G[e][n][w](G, null)[Y];
      } else {
        if (G[a]) {
          return E.Dom.IE_ComputedStyle.get(G, Y);
        }
      }
    },
    getStyle: function (G, Y) {
      return E.Dom.batch(G, E.Dom._getStyle, Y);
    },
    _getStyle: (function () {
      if (window[w]) {
        return function (G, y) {
          y = y === "float" ? (y = "cssFloat") : E.Dom._toCamel(y);
          var x = G.style[y],
            Y;
          if (!x) {
            Y = G[e][n][w](G, null);
            if (Y) {
              x = Y[y];
            }
          }
          return x;
        };
      } else {
        if (W[a]) {
          return function (G, y) {
            var x;
            switch (y) {
              case "opacity":
                x = 100;
                try {
                  x = G.filters["DXImageTransform.Microsoft.Alpha"].opacity;
                } catch (z) {
                  try {
                    x = G.filters("alpha").opacity;
                  } catch (Y) {}
                }
                return x / 100;
              case "float":
                y = "styleFloat";
              default:
                y = E.Dom._toCamel(y);
                x = G[a] ? G[a][y] : null;
                return G.style[y] || x;
            }
          };
        }
      }
    })(),
    setStyle: function (G, Y, x) {
      E.Dom.batch(G, E.Dom._setStyle, { prop: Y, val: x });
    },
    _setStyle: (function () {
      if (T) {
        return function (Y, G) {
          var x = E.Dom._toCamel(G.prop),
            y = G.val;
          if (Y) {
            switch (x) {
              case "opacity":
                if (L.isString(Y.style.filter)) {
                  Y.style.filter = "alpha(opacity=" + y * 100 + ")";
                  if (!Y[a] || !Y[a].hasLayout) {
                    Y.style.zoom = 1;
                  }
                }
                break;
              case "float":
                x = "styleFloat";
              default:
                Y.style[x] = y;
            }
          } else {
          }
        };
      } else {
        return function (Y, G) {
          var x = E.Dom._toCamel(G.prop),
            y = G.val;
          if (Y) {
            if (x == "float") {
              x = "cssFloat";
            }
            Y.style[x] = y;
          } else {
          }
        };
      }
    })(),
    getXY: function (G) {
      return E.Dom.batch(G, E.Dom._getXY);
    },
    _canPosition: function (G) {
      return E.Dom._getStyle(G, "display") !== "none" && E.Dom._inDoc(G);
    },
    _getXY: (function () {
      if (K[v][Q]) {
        return function (y) {
          var z,
            Y,
            AA,
            AF,
            AE,
            AD,
            AC,
            G,
            x,
            AB = Math.floor,
            AG = false;
          if (E.Dom._canPosition(y)) {
            AA = y[Q]();
            AF = y[e];
            z = E.Dom.getDocumentScrollLeft(AF);
            Y = E.Dom.getDocumentScrollTop(AF);
            AG = [AB(AA[j]), AB(AA[o])];
            if (T && m.ie < 8) {
              AE = 2;
              AD = 2;
              AC = AF[t];
              G = S(AF[v], q);
              x = S(AF[v], R);
              if (m.ie === 6) {
                if (AC !== c) {
                  AE = 0;
                  AD = 0;
                }
              }
              if (AC == c) {
                if (G !== r) {
                  AE = parseInt(G, 10);
                }
                if (x !== r) {
                  AD = parseInt(x, 10);
                }
              }
              AG[0] -= AE;
              AG[1] -= AD;
            }
            if (Y || z) {
              AG[0] += z;
              AG[1] += Y;
            }
            AG[0] = AB(AG[0]);
            AG[1] = AB(AG[1]);
          } else {
          }
          return AG;
        };
      } else {
        return function (y) {
          var x,
            Y,
            AA,
            AB,
            AC,
            z = false,
            G = y;
          if (E.Dom._canPosition(y)) {
            z = [y[b], y[P]];
            x = E.Dom.getDocumentScrollLeft(y[e]);
            Y = E.Dom.getDocumentScrollTop(y[e]);
            AC = H || m.webkit > 519 ? true : false;
            while ((G = G[u])) {
              z[0] += G[b];
              z[1] += G[P];
              if (AC) {
                z = E.Dom._calcBorders(G, z);
              }
            }
            if (E.Dom._getStyle(y, p) !== f) {
              G = y;
              while ((G = G[Z]) && G[C]) {
                AA = G[i];
                AB = G[O];
                if (H && E.Dom._getStyle(G, "overflow") !== "visible") {
                  z = E.Dom._calcBorders(G, z);
                }
                if (AA || AB) {
                  z[0] -= AB;
                  z[1] -= AA;
                }
              }
              z[0] += x;
              z[1] += Y;
            } else {
              if (D) {
                z[0] -= x;
                z[1] -= Y;
              } else {
                if (I || H) {
                  z[0] += x;
                  z[1] += Y;
                }
              }
            }
            z[0] = Math.floor(z[0]);
            z[1] = Math.floor(z[1]);
          } else {
          }
          return z;
        };
      }
    })(),
    getX: function (G) {
      var Y = function (x) {
        return E.Dom.getXY(x)[0];
      };
      return E.Dom.batch(G, Y, E.Dom, true);
    },
    getY: function (G) {
      var Y = function (x) {
        return E.Dom.getXY(x)[1];
      };
      return E.Dom.batch(G, Y, E.Dom, true);
    },
    setXY: function (G, x, Y) {
      E.Dom.batch(G, E.Dom._setXY, { pos: x, noRetry: Y });
    },
    _setXY: function (G, z) {
      var AA = E.Dom._getStyle(G, p),
        y = E.Dom.setStyle,
        AD = z.pos,
        Y = z.noRetry,
        AB = [
          parseInt(E.Dom.getComputedStyle(G, j), 10),
          parseInt(E.Dom.getComputedStyle(G, o), 10),
        ],
        AC,
        x;
      if (AA == "static") {
        AA = V;
        y(G, p, AA);
      }
      AC = E.Dom._getXY(G);
      if (!AD || AC === false) {
        return false;
      }
      if (isNaN(AB[0])) {
        AB[0] = AA == V ? 0 : G[b];
      }
      if (isNaN(AB[1])) {
        AB[1] = AA == V ? 0 : G[P];
      }
      if (AD[0] !== null) {
        y(G, j, AD[0] - AC[0] + AB[0] + "px");
      }
      if (AD[1] !== null) {
        y(G, o, AD[1] - AC[1] + AB[1] + "px");
      }
      if (!Y) {
        x = E.Dom._getXY(G);
        if (
          (AD[0] !== null && x[0] != AD[0]) ||
          (AD[1] !== null && x[1] != AD[1])
        ) {
          E.Dom._setXY(G, { pos: AD, noRetry: true });
        }
      }
    },
    setX: function (Y, G) {
      E.Dom.setXY(Y, [G, null]);
    },
    setY: function (G, Y) {
      E.Dom.setXY(G, [null, Y]);
    },
    getRegion: function (G) {
      var Y = function (x) {
        var y = false;
        if (E.Dom._canPosition(x)) {
          y = E.Region.getRegion(x);
        } else {
        }
        return y;
      };
      return E.Dom.batch(G, Y, E.Dom, true);
    },
    getClientWidth: function () {
      return E.Dom.getViewportWidth();
    },
    getClientHeight: function () {
      return E.Dom.getViewportHeight();
    },
    getElementsByClassName: function (AB, AF, AC, AE, x, AD) {
      AB = L.trim(AB);
      AF = AF || "*";
      AC = AC ? E.Dom.get(AC) : null || K;
      if (!AC) {
        return [];
      }
      var Y = [],
        G = AC.getElementsByTagName(AF),
        z = E.Dom.hasClass;
      for (var y = 0, AA = G.length; y < AA; ++y) {
        if (z(G[y], AB)) {
          Y[Y.length] = G[y];
        }
      }
      if (AE) {
        E.Dom.batch(Y, AE, x, AD);
      }
      return Y;
    },
    hasClass: function (Y, G) {
      return E.Dom.batch(Y, E.Dom._hasClass, G);
    },
    _hasClass: function (x, Y) {
      var G = false,
        y;
      if (x && Y) {
        y = E.Dom.getAttribute(x, F) || J;
        if (Y.exec) {
          G = Y.test(y);
        } else {
          G = Y && (B + y + B).indexOf(B + Y + B) > -1;
        }
      } else {
      }
      return G;
    },
    addClass: function (Y, G) {
      return E.Dom.batch(Y, E.Dom._addClass, G);
    },
    _addClass: function (x, Y) {
      var G = false,
        y;
      if (x && Y) {
        y = E.Dom.getAttribute(x, F) || J;
        if (!E.Dom._hasClass(x, Y)) {
          E.Dom.setAttribute(x, F, A(y + B + Y));
          G = true;
        }
      } else {
      }
      return G;
    },
    removeClass: function (Y, G) {
      return E.Dom.batch(Y, E.Dom._removeClass, G);
    },
    _removeClass: function (y, x) {
      var Y = false,
        AA,
        z,
        G;
      if (y && x) {
        AA = E.Dom.getAttribute(y, F) || J;
        E.Dom.setAttribute(y, F, AA.replace(E.Dom._getClassRegex(x), J));
        z = E.Dom.getAttribute(y, F);
        if (AA !== z) {
          E.Dom.setAttribute(y, F, A(z));
          Y = true;
          if (E.Dom.getAttribute(y, F) === "") {
            G = y.hasAttribute && y.hasAttribute(g) ? g : F;
            y.removeAttribute(G);
          }
        }
      } else {
      }
      return Y;
    },
    replaceClass: function (x, Y, G) {
      return E.Dom.batch(x, E.Dom._replaceClass, { from: Y, to: G });
    },
    _replaceClass: function (y, x) {
      var Y,
        AB,
        AA,
        G = false,
        z;
      if (y && x) {
        AB = x.from;
        AA = x.to;
        if (!AA) {
          G = false;
        } else {
          if (!AB) {
            G = E.Dom._addClass(y, x.to);
          } else {
            if (AB !== AA) {
              z = E.Dom.getAttribute(y, F) || J;
              Y = (B + z.replace(E.Dom._getClassRegex(AB), B + AA)).split(
                E.Dom._getClassRegex(AA),
              );
              Y.splice(1, 0, B + AA);
              E.Dom.setAttribute(y, F, A(Y.join(J)));
              G = true;
            }
          }
        }
      } else {
      }
      return G;
    },
    generateId: function (G, x) {
      x = x || "yui-gen";
      var Y = function (y) {
        if (y && y.id) {
          return y.id;
        }
        var z = x + YAHOO.env._id_counter++;
        if (y) {
          if (y[e].getElementById(z)) {
            return E.Dom.generateId(y, z + x);
          }
          y.id = z;
        }
        return z;
      };
      return E.Dom.batch(G, Y, E.Dom, true) || Y.apply(E.Dom, arguments);
    },
    isAncestor: function (Y, x) {
      Y = E.Dom.get(Y);
      x = E.Dom.get(x);
      var G = false;
      if (Y && x && Y[l] && x[l]) {
        if (Y.contains && Y !== x) {
          G = Y.contains(x);
        } else {
          if (Y.compareDocumentPosition) {
            G = !!(Y.compareDocumentPosition(x) & 16);
          }
        }
      } else {
      }
      return G;
    },
    inDocument: function (G, Y) {
      return E.Dom._inDoc(E.Dom.get(G), Y);
    },
    _inDoc: function (Y, x) {
      var G = false;
      if (Y && Y[C]) {
        x = x || Y[e];
        G = E.Dom.isAncestor(x[v], Y);
      } else {
      }
      return G;
    },
    getElementsBy: function (Y, AF, AB, AD, y, AC, AE) {
      AF = AF || "*";
      AB = AB ? E.Dom.get(AB) : null || K;
      if (!AB) {
        return [];
      }
      var x = [],
        G = AB.getElementsByTagName(AF);
      for (var z = 0, AA = G.length; z < AA; ++z) {
        if (Y(G[z])) {
          if (AE) {
            x = G[z];
            break;
          } else {
            x[x.length] = G[z];
          }
        }
      }
      if (AD) {
        E.Dom.batch(x, AD, y, AC);
      }
      return x;
    },
    getElementBy: function (x, G, Y) {
      return E.Dom.getElementsBy(x, G, Y, null, null, null, true);
    },
    batch: function (x, AB, AA, z) {
      var y = [],
        Y = z ? AA : window;
      x = x && (x[C] || x.item) ? x : E.Dom.get(x);
      if (x && AB) {
        if (x[C] || x.length === undefined) {
          return AB.call(Y, x, AA);
        }
        for (var G = 0; G < x.length; ++G) {
          y[y.length] = AB.call(Y, x[G], AA);
        }
      } else {
        return false;
      }
      return y;
    },
    getDocumentHeight: function () {
      var Y = K[t] != M || I ? K.body.scrollHeight : W.scrollHeight,
        G = Math.max(Y, E.Dom.getViewportHeight());
      return G;
    },
    getDocumentWidth: function () {
      var Y = K[t] != M || I ? K.body.scrollWidth : W.scrollWidth,
        G = Math.max(Y, E.Dom.getViewportWidth());
      return G;
    },
    getViewportHeight: function () {
      var G = self.innerHeight,
        Y = K[t];
      if ((Y || T) && !D) {
        G = Y == M ? W.clientHeight : K.body.clientHeight;
      }
      return G;
    },
    getViewportWidth: function () {
      var G = self.innerWidth,
        Y = K[t];
      if (Y || T) {
        G = Y == M ? W.clientWidth : K.body.clientWidth;
      }
      return G;
    },
    getAncestorBy: function (G, Y) {
      while ((G = G[Z])) {
        if (E.Dom._testElement(G, Y)) {
          return G;
        }
      }
      return null;
    },
    getAncestorByClassName: function (Y, G) {
      Y = E.Dom.get(Y);
      if (!Y) {
        return null;
      }
      var x = function (y) {
        return E.Dom.hasClass(y, G);
      };
      return E.Dom.getAncestorBy(Y, x);
    },
    getAncestorByTagName: function (Y, G) {
      Y = E.Dom.get(Y);
      if (!Y) {
        return null;
      }
      var x = function (y) {
        return y[C] && y[C].toUpperCase() == G.toUpperCase();
      };
      return E.Dom.getAncestorBy(Y, x);
    },
    getPreviousSiblingBy: function (G, Y) {
      while (G) {
        G = G.previousSibling;
        if (E.Dom._testElement(G, Y)) {
          return G;
        }
      }
      return null;
    },
    getPreviousSibling: function (G) {
      G = E.Dom.get(G);
      if (!G) {
        return null;
      }
      return E.Dom.getPreviousSiblingBy(G);
    },
    getNextSiblingBy: function (G, Y) {
      while (G) {
        G = G.nextSibling;
        if (E.Dom._testElement(G, Y)) {
          return G;
        }
      }
      return null;
    },
    getNextSibling: function (G) {
      G = E.Dom.get(G);
      if (!G) {
        return null;
      }
      return E.Dom.getNextSiblingBy(G);
    },
    getFirstChildBy: function (G, x) {
      var Y = E.Dom._testElement(G.firstChild, x) ? G.firstChild : null;
      return Y || E.Dom.getNextSiblingBy(G.firstChild, x);
    },
    getFirstChild: function (G, Y) {
      G = E.Dom.get(G);
      if (!G) {
        return null;
      }
      return E.Dom.getFirstChildBy(G);
    },
    getLastChildBy: function (G, x) {
      if (!G) {
        return null;
      }
      var Y = E.Dom._testElement(G.lastChild, x) ? G.lastChild : null;
      return Y || E.Dom.getPreviousSiblingBy(G.lastChild, x);
    },
    getLastChild: function (G) {
      G = E.Dom.get(G);
      return E.Dom.getLastChildBy(G);
    },
    getChildrenBy: function (Y, y) {
      var x = E.Dom.getFirstChildBy(Y, y),
        G = x ? [x] : [];
      E.Dom.getNextSiblingBy(x, function (z) {
        if (!y || y(z)) {
          G[G.length] = z;
        }
        return false;
      });
      return G;
    },
    getChildren: function (G) {
      G = E.Dom.get(G);
      if (!G) {
      }
      return E.Dom.getChildrenBy(G);
    },
    getDocumentScrollLeft: function (G) {
      G = G || K;
      return Math.max(G[v].scrollLeft, G.body.scrollLeft);
    },
    getDocumentScrollTop: function (G) {
      G = G || K;
      return Math.max(G[v].scrollTop, G.body.scrollTop);
    },
    insertBefore: function (Y, G) {
      Y = E.Dom.get(Y);
      G = E.Dom.get(G);
      if (!Y || !G || !G[Z]) {
        return null;
      }
      return G[Z].insertBefore(Y, G);
    },
    insertAfter: function (Y, G) {
      Y = E.Dom.get(Y);
      G = E.Dom.get(G);
      if (!Y || !G || !G[Z]) {
        return null;
      }
      if (G.nextSibling) {
        return G[Z].insertBefore(Y, G.nextSibling);
      } else {
        return G[Z].appendChild(Y);
      }
    },
    getClientRegion: function () {
      var x = E.Dom.getDocumentScrollTop(),
        Y = E.Dom.getDocumentScrollLeft(),
        y = E.Dom.getViewportWidth() + Y,
        G = E.Dom.getViewportHeight() + x;
      return new E.Region(x, y, G, Y);
    },
    setAttribute: function (Y, G, x) {
      G = E.Dom.CUSTOM_ATTRIBUTES[G] || G;
      Y.setAttribute(G, x);
    },
    getAttribute: function (Y, G) {
      G = E.Dom.CUSTOM_ATTRIBUTES[G] || G;
      return Y.getAttribute(G);
    },
    _toCamel: function (Y) {
      var x = d;

      function G(y, z) {
        return z.toUpperCase();
      }

      return (
        x[Y] || (x[Y] = Y.indexOf("-") === -1 ? Y : Y.replace(/-([a-z])/gi, G))
      );
    },
    _getClassRegex: function (Y) {
      var G;
      if (Y !== undefined) {
        if (Y.exec) {
          G = Y;
        } else {
          G = h[Y];
          if (!G) {
            Y = Y.replace(E.Dom._patterns.CLASS_RE_TOKENS, "\\$1");
            G = h[Y] = new RegExp(s + Y + k, U);
          }
        }
      }
      return G;
    },
    _patterns: {
      ROOT_TAG: /^body|html$/i,
      CLASS_RE_TOKENS: /([\.\(\)\^\$\*\+\?\|\[\]\{\}])/g,
    },
    _testElement: function (G, Y) {
      return G && G[l] == 1 && (!Y || Y(G));
    },
    _calcBorders: function (x, y) {
      var Y = parseInt(E.Dom[w](x, R), 10) || 0,
        G = parseInt(E.Dom[w](x, q), 10) || 0;
      if (H) {
        if (N.test(x[C])) {
          Y = 0;
          G = 0;
        }
      }
      y[0] += G;
      y[1] += Y;
      return y;
    },
  };
  var S = E.Dom[w];
  if (m.opera) {
    E.Dom[w] = function (Y, G) {
      var x = S(Y, G);
      if (X.test(G)) {
        x = E.Dom.Color.toRGB(x);
      }
      return x;
    };
  }
  if (m.webkit) {
    E.Dom[w] = function (Y, G) {
      var x = S(Y, G);
      if (x === "rgba(0, 0, 0, 0)") {
        x = "transparent";
      }
      return x;
    };
  }
})();
YAHOO.util.Region = function (C, D, A, B) {
  this.top = C;
  this.y = C;
  this[1] = C;
  this.right = D;
  this.bottom = A;
  this.left = B;
  this.x = B;
  this[0] = B;
  this.width = this.right - this.left;
  this.height = this.bottom - this.top;
};
YAHOO.util.Region.prototype.contains = function (A) {
  return (
    A.left >= this.left &&
    A.right <= this.right &&
    A.top >= this.top &&
    A.bottom <= this.bottom
  );
};
YAHOO.util.Region.prototype.getArea = function () {
  return (this.bottom - this.top) * (this.right - this.left);
};
YAHOO.util.Region.prototype.intersect = function (E) {
  var C = Math.max(this.top, E.top),
    D = Math.min(this.right, E.right),
    A = Math.min(this.bottom, E.bottom),
    B = Math.max(this.left, E.left);
  if (A >= C && D >= B) {
    return new YAHOO.util.Region(C, D, A, B);
  } else {
    return null;
  }
};
YAHOO.util.Region.prototype.union = function (E) {
  var C = Math.min(this.top, E.top),
    D = Math.max(this.right, E.right),
    A = Math.max(this.bottom, E.bottom),
    B = Math.min(this.left, E.left);
  return new YAHOO.util.Region(C, D, A, B);
};
YAHOO.util.Region.prototype.toString = function () {
  return (
    "Region {" +
    "top: " +
    this.top +
    ", right: " +
    this.right +
    ", bottom: " +
    this.bottom +
    ", left: " +
    this.left +
    ", height: " +
    this.height +
    ", width: " +
    this.width +
    "}"
  );
};
YAHOO.util.Region.getRegion = function (D) {
  var F = YAHOO.util.Dom.getXY(D),
    C = F[1],
    E = F[0] + D.offsetWidth,
    A = F[1] + D.offsetHeight,
    B = F[0];
  return new YAHOO.util.Region(C, E, A, B);
};
YAHOO.util.Point = function (A, B) {
  if (YAHOO.lang.isArray(A)) {
    B = A[1];
    A = A[0];
  }
  YAHOO.util.Point.superclass.constructor.call(this, B, A, B, A);
};
YAHOO.extend(YAHOO.util.Point, YAHOO.util.Region);
(function () {
  var B = YAHOO.util,
    A = "clientTop",
    F = "clientLeft",
    J = "parentNode",
    K = "right",
    W = "hasLayout",
    I = "px",
    U = "opacity",
    L = "auto",
    D = "borderLeftWidth",
    G = "borderTopWidth",
    P = "borderRightWidth",
    V = "borderBottomWidth",
    S = "visible",
    Q = "transparent",
    N = "height",
    E = "width",
    H = "style",
    T = "currentStyle",
    R = /^width|height$/,
    O =
      /^(\d[.\d]*)+(em|ex|px|gd|rem|vw|vh|vm|ch|mm|cm|in|pt|pc|deg|rad|ms|s|hz|khz|%){1}?/i,
    M = {
      get: function (X, Z) {
        var Y = "",
          a = X[T][Z];
        if (Z === U) {
          Y = B.Dom.getStyle(X, U);
        } else {
          if (!a || (a.indexOf && a.indexOf(I) > -1)) {
            Y = a;
          } else {
            if (B.Dom.IE_COMPUTED[Z]) {
              Y = B.Dom.IE_COMPUTED[Z](X, Z);
            } else {
              if (O.test(a)) {
                Y = B.Dom.IE.ComputedStyle.getPixel(X, Z);
              } else {
                Y = a;
              }
            }
          }
        }
        return Y;
      },
      getOffset: function (Z, e) {
        var b = Z[T][e],
          X = e.charAt(0).toUpperCase() + e.substr(1),
          c = "offset" + X,
          Y = "pixel" + X,
          a = "",
          d;
        if (b == L) {
          d = Z[c];
          if (d === undefined) {
            a = 0;
          }
          a = d;
          if (R.test(e)) {
            Z[H][e] = d;
            if (Z[c] > d) {
              a = d - (Z[c] - d);
            }
            Z[H][e] = L;
          }
        } else {
          if (!Z[H][Y] && !Z[H][e]) {
            Z[H][e] = b;
          }
          a = Z[H][Y];
        }
        return a + I;
      },
      getBorderWidth: function (X, Z) {
        var Y = null;
        if (!X[T][W]) {
          X[H].zoom = 1;
        }
        switch (Z) {
          case G:
            Y = X[A];
            break;
          case V:
            Y = X.offsetHeight - X.clientHeight - X[A];
            break;
          case D:
            Y = X[F];
            break;
          case P:
            Y = X.offsetWidth - X.clientWidth - X[F];
            break;
        }
        return Y + I;
      },
      getPixel: function (Y, X) {
        var a = null,
          b = Y[T][K],
          Z = Y[T][X];
        Y[H][K] = Z;
        a = Y[H].pixelRight;
        Y[H][K] = b;
        return a + I;
      },
      getMargin: function (Y, X) {
        var Z;
        if (Y[T][X] == L) {
          Z = 0 + I;
        } else {
          Z = B.Dom.IE.ComputedStyle.getPixel(Y, X);
        }
        return Z;
      },
      getVisibility: function (Y, X) {
        var Z;
        while ((Z = Y[T]) && Z[X] == "inherit") {
          Y = Y[J];
        }
        return Z ? Z[X] : S;
      },
      getColor: function (Y, X) {
        return B.Dom.Color.toRGB(Y[T][X]) || Q;
      },
      getBorderColor: function (Y, X) {
        var Z = Y[T],
          a = Z[X] || Z.color;
        return B.Dom.Color.toRGB(B.Dom.Color.toHex(a));
      },
    },
    C = {};
  C.top = C.right = C.bottom = C.left = C[E] = C[N] = M.getOffset;
  C.color = M.getColor;
  C[G] = C[P] = C[V] = C[D] = M.getBorderWidth;
  C.marginTop = C.marginRight = C.marginBottom = C.marginLeft = M.getMargin;
  C.visibility = M.getVisibility;
  C.borderColor =
    C.borderTopColor =
    C.borderRightColor =
    C.borderBottomColor =
    C.borderLeftColor =
      M.getBorderColor;
  B.Dom.IE_COMPUTED = C;
  B.Dom.IE_ComputedStyle = M;
})();
(function () {
  var C = "toString",
    A = parseInt,
    B = RegExp,
    D = YAHOO.util;
  D.Dom.Color = {
    KEYWORDS: {
      black: "000",
      silver: "c0c0c0",
      gray: "808080",
      white: "fff",
      maroon: "800000",
      red: "f00",
      purple: "800080",
      fuchsia: "f0f",
      green: "008000",
      lime: "0f0",
      olive: "808000",
      yellow: "ff0",
      navy: "000080",
      blue: "00f",
      teal: "008080",
      aqua: "0ff",
    },
    re_RGB: /^rgb\(([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\)$/i,
    re_hex: /^#?([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})$/i,
    re_hex3: /([0-9A-F])/gi,
    toRGB: function (E) {
      if (!D.Dom.Color.re_RGB.test(E)) {
        E = D.Dom.Color.toHex(E);
      }
      if (D.Dom.Color.re_hex.exec(E)) {
        E = "rgb(" + [A(B.$1, 16), A(B.$2, 16), A(B.$3, 16)].join(", ") + ")";
      }
      return E;
    },
    toHex: function (H) {
      H = D.Dom.Color.KEYWORDS[H] || H;
      if (D.Dom.Color.re_RGB.exec(H)) {
        var G = B.$1.length === 1 ? "0" + B.$1 : Number(B.$1),
          F = B.$2.length === 1 ? "0" + B.$2 : Number(B.$2),
          E = B.$3.length === 1 ? "0" + B.$3 : Number(B.$3);
        H = [G[C](16), F[C](16), E[C](16)].join("");
      }
      if (H.length < 6) {
        H = H.replace(D.Dom.Color.re_hex3, "$1$1");
      }
      if (H !== "transparent" && H.indexOf("#") < 0) {
        H = "#" + H;
      }
      return H.toLowerCase();
    },
  };
})();
YAHOO.register("dom", YAHOO.util.Dom, { version: "2.7.0", build: "1799" });
