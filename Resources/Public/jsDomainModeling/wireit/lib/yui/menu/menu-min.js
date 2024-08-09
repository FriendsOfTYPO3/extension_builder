/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  var S = "DIV",
    O = "hd",
    K = "bd",
    N = "ft",
    X = "LI",
    A = "disabled",
    D = "mouseover",
    F = "mouseout",
    U = "mousedown",
    G = "mouseup",
    R = YAHOO.env.ua.ie ? "focusin" : "focus",
    V = "click",
    B = "keydown",
    M = "keyup",
    I = "keypress",
    L = "clicktohide",
    T = "position",
    P = "dynamic",
    Y = "showdelay",
    J = "selected",
    E = "visible",
    W = "UL",
    Q = "MenuManager",
    C = YAHOO.util.Dom,
    Z = YAHOO.util.Event,
    H = YAHOO.lang;
  YAHOO.widget.MenuManager = (function () {
    var a = false,
      c = {},
      r = {},
      d = {},
      n = {
        click: "clickEvent",
        mousedown: "mouseDownEvent",
        mouseup: "mouseUpEvent",
        mouseover: "mouseOverEvent",
        mouseout: "mouseOutEvent",
        keydown: "keyDownEvent",
        keyup: "keyUpEvent",
        keypress: "keyPressEvent",
        focus: "focusEvent",
        focusin: "focusEvent",
        blur: "blurEvent",
        focusout: "blurEvent",
      },
      m = null,
      k = null;

    function o(u) {
      var s, t;
      if (u && u.tagName) {
        switch (u.tagName.toUpperCase()) {
          case S:
            s = u.parentNode;
            if (
              (C.hasClass(u, O) || C.hasClass(u, K) || C.hasClass(u, N)) &&
              s &&
              s.tagName &&
              s.tagName.toUpperCase() == S
            ) {
              t = s;
            } else {
              t = u;
            }
            break;
          case X:
            t = u;
            break;
          default:
            s = u.parentNode;
            if (s) {
              t = o(s);
            }
            break;
        }
      }
      return t;
    }

    function q(w) {
      var s = Z.getTarget(w),
        t = o(s),
        y,
        u,
        v,
        AA,
        z;
      if (t) {
        u = t.tagName.toUpperCase();
        if (u == X) {
          v = t.id;
          if (v && d[v]) {
            AA = d[v];
            z = AA.parent;
          }
        } else {
          if (u == S) {
            if (t.id) {
              z = c[t.id];
            }
          }
        }
      }
      if (z) {
        y = n[w.type];
        if (AA && !AA.cfg.getProperty(A)) {
          AA[y].fire(w);
        }
        z[y].fire(w, AA);
      } else {
        if (w.type == U) {
          for (var x in r) {
            if (H.hasOwnProperty(r, x)) {
              z = r[x];
              if (
                z.cfg.getProperty(L) &&
                !(z instanceof YAHOO.widget.MenuBar) &&
                z.cfg.getProperty(T) == P
              ) {
                z.hide();
              } else {
                if (z.cfg.getProperty(Y) > 0) {
                  z._cancelShowDelay();
                }
                if (z.activeItem) {
                  z.activeItem.blur();
                  z.activeItem.cfg.setProperty(J, false);
                  z.activeItem = null;
                }
              }
            }
          }
        } else {
          if (w.type == R) {
            m = s;
          }
        }
      }
    }

    function f(t, s, u) {
      if (c[u.id]) {
        this.removeMenu(u);
      }
    }

    function j(t, s) {
      var u = s[1];
      if (u) {
        k = u;
      }
    }

    function i(t, s) {
      k = null;
    }

    function b(t, s, v) {
      if (v && v.focus) {
        try {
          v.focus();
        } catch (u) {}
      }
      this.hideEvent.unsubscribe(b, v);
    }

    function l(t, s) {
      if (this === this.getRoot() && this.cfg.getProperty(T) === P) {
        this.hideEvent.subscribe(b, m);
        this.focus();
      }
    }

    function g(u, t) {
      var s = t[0],
        v = this.id;
      if (s) {
        r[v] = this;
      } else {
        if (r[v]) {
          delete r[v];
        }
      }
    }

    function h(t, s) {
      p(this);
    }

    function p(t) {
      var s = t.id;
      if (s && d[s]) {
        if (k == t) {
          k = null;
        }
        delete d[s];
        t.destroyEvent.unsubscribe(h);
      }
    }

    function e(t, s) {
      var v = s[0],
        u;
      if (v instanceof YAHOO.widget.MenuItem) {
        u = v.id;
        if (!d[u]) {
          d[u] = v;
          v.destroyEvent.subscribe(h);
        }
      }
    }

    return {
      addMenu: function (t) {
        var s;
        if (t instanceof YAHOO.widget.Menu && t.id && !c[t.id]) {
          c[t.id] = t;
          if (!a) {
            s = document;
            Z.on(s, D, q, this, true);
            Z.on(s, F, q, this, true);
            Z.on(s, U, q, this, true);
            Z.on(s, G, q, this, true);
            Z.on(s, V, q, this, true);
            Z.on(s, B, q, this, true);
            Z.on(s, M, q, this, true);
            Z.on(s, I, q, this, true);
            Z.onFocus(s, q, this, true);
            Z.onBlur(s, q, this, true);
            a = true;
          }
          t.cfg.subscribeToConfigEvent(E, g);
          t.destroyEvent.subscribe(f, t, this);
          t.itemAddedEvent.subscribe(e);
          t.focusEvent.subscribe(j);
          t.blurEvent.subscribe(i);
          t.showEvent.subscribe(l);
        }
      },
      removeMenu: function (v) {
        var t, s, u;
        if (v) {
          t = v.id;
          if (t in c && c[t] == v) {
            s = v.getItems();
            if (s && s.length > 0) {
              u = s.length - 1;
              do {
                p(s[u]);
              } while (u--);
            }
            delete c[t];
            if (t in r && r[t] == v) {
              delete r[t];
            }
            if (v.cfg) {
              v.cfg.unsubscribeFromConfigEvent(E, g);
            }
            v.destroyEvent.unsubscribe(f, v);
            v.itemAddedEvent.unsubscribe(e);
            v.focusEvent.unsubscribe(j);
            v.blurEvent.unsubscribe(i);
          }
        }
      },
      hideVisible: function () {
        var s;
        for (var t in r) {
          if (H.hasOwnProperty(r, t)) {
            s = r[t];
            if (
              !(s instanceof YAHOO.widget.MenuBar) &&
              s.cfg.getProperty(T) == P
            ) {
              s.hide();
            }
          }
        }
      },
      getVisible: function () {
        return r;
      },
      getMenus: function () {
        return c;
      },
      getMenu: function (t) {
        var s;
        if (t in c) {
          s = c[t];
        }
        return s;
      },
      getMenuItem: function (t) {
        var s;
        if (t in d) {
          s = d[t];
        }
        return s;
      },
      getMenuItemGroup: function (w) {
        var t = C.get(w),
          s,
          y,
          x,
          u,
          v;
        if (t && t.tagName && t.tagName.toUpperCase() == W) {
          y = t.firstChild;
          if (y) {
            s = [];
            do {
              u = y.id;
              if (u) {
                x = this.getMenuItem(u);
                if (x) {
                  s[s.length] = x;
                }
              }
            } while ((y = y.nextSibling));
            if (s.length > 0) {
              v = s;
            }
          }
        }
        return v;
      },
      getFocusedMenuItem: function () {
        return k;
      },
      getFocusedMenu: function () {
        var s;
        if (k) {
          s = k.parent.getRoot();
        }
        return s;
      },
      toString: function () {
        return Q;
      },
    };
  })();
})();
(function () {
  var AN = YAHOO.lang,
    Ao = "Menu",
    H = "DIV",
    K = "div",
    Ak = "id",
    AI = "SELECT",
    f = "xy",
    R = "y",
    Av = "UL",
    L = "ul",
    AK = "first-of-type",
    l = "LI",
    i = "OPTGROUP",
    Ax = "OPTION",
    Af = "disabled",
    AY = "none",
    z = "selected",
    Ar = "groupindex",
    j = "index",
    O = "submenu",
    As = "visible",
    AX = "hidedelay",
    Ab = "position",
    AE = "dynamic",
    C = "static",
    Al = AE + "," + C,
    Y = "windows",
    Q = "url",
    M = "#",
    V = "target",
    AU = "maxheight",
    T = "topscrollbar",
    y = "bottomscrollbar",
    e = "_",
    P = T + e + Af,
    E = y + e + Af,
    c = "mousemove",
    At = "showdelay",
    d = "submenuhidedelay",
    AG = "iframe",
    x = "constraintoviewport",
    A2 = "preventcontextoverlap",
    AP = "submenualignment",
    a = "autosubmenudisplay",
    AD = "clicktohide",
    h = "container",
    k = "scrollincrement",
    Ah = "minscrollheight",
    A0 = "classname",
    Ae = "shadow",
    Ap = "keepopen",
    Ay = "hd",
    D = "hastitle",
    q = "context",
    v = "",
    Ai = "mousedown",
    Ac = "keydown",
    Am = "height",
    U = "width",
    AR = "px",
    Aw = "effect",
    AF = "monitorresize",
    AW = "display",
    AV = "block",
    J = "visibility",
    AA = "absolute",
    AT = "zindex",
    m = "yui-menu-body-scrolled",
    AL = "&#32;",
    Az = " ",
    Ag = "mouseover",
    G = "mouseout",
    AS = "itemAdded",
    o = "itemRemoved",
    AM = "hidden",
    t = "yui-menu-shadow",
    AH = t + "-visible",
    n = t + Az + AH;
  YAHOO.widget.Menu = function (A4, A3) {
    if (A3) {
      this.parent = A3.parent;
      this.lazyLoad = A3.lazyLoad || A3.lazyload;
      this.itemData = A3.itemData || A3.itemdata;
    }
    YAHOO.widget.Menu.superclass.constructor.call(this, A4, A3);
  };

  function B(A4) {
    var A3 = false;
    if (AN.isString(A4)) {
      A3 = Al.indexOf(A4.toLowerCase()) != -1;
    }
    return A3;
  }

  var g = YAHOO.util.Dom,
    AB = YAHOO.util.Event,
    Au = YAHOO.widget.Module,
    AC = YAHOO.widget.Overlay,
    s = YAHOO.widget.Menu,
    A1 = YAHOO.widget.MenuManager,
    F = YAHOO.util.CustomEvent,
    Aq = YAHOO.env.ua,
    An,
    Aa = [
      ["mouseOverEvent", Ag],
      ["mouseOutEvent", G],
      ["mouseDownEvent", Ai],
      ["mouseUpEvent", "mouseup"],
      ["clickEvent", "click"],
      ["keyPressEvent", "keypress"],
      ["keyDownEvent", Ac],
      ["keyUpEvent", "keyup"],
      ["focusEvent", "focus"],
      ["blurEvent", "blur"],
      ["itemAddedEvent", AS],
      ["itemRemovedEvent", o],
    ],
    AZ = { key: As, value: false, validator: AN.isBoolean },
    AQ = {
      key: x,
      value: true,
      validator: AN.isBoolean,
      supercedes: [AG, "x", R, f],
    },
    AJ = { key: A2, value: true, validator: AN.isBoolean, supercedes: [x] },
    S = { key: Ab, value: AE, validator: B, supercedes: [As, AG] },
    A = { key: AP, value: ["tl", "tr"] },
    u = { key: a, value: true, validator: AN.isBoolean, suppressEvent: true },
    Z = { key: At, value: 250, validator: AN.isNumber, suppressEvent: true },
    r = { key: AX, value: 0, validator: AN.isNumber, suppressEvent: true },
    w = { key: d, value: 250, validator: AN.isNumber, suppressEvent: true },
    p = { key: AD, value: true, validator: AN.isBoolean, suppressEvent: true },
    AO = { key: h, suppressEvent: true },
    Ad = {
      key: k,
      value: 1,
      validator: AN.isNumber,
      supercedes: [AU],
      suppressEvent: true,
    },
    N = {
      key: Ah,
      value: 90,
      validator: AN.isNumber,
      supercedes: [AU],
      suppressEvent: true,
    },
    X = {
      key: AU,
      value: 0,
      validator: AN.isNumber,
      supercedes: [AG],
      suppressEvent: true,
    },
    W = { key: A0, value: null, validator: AN.isString, suppressEvent: true },
    b = { key: Af, value: false, validator: AN.isBoolean, suppressEvent: true },
    I = {
      key: Ae,
      value: true,
      validator: AN.isBoolean,
      suppressEvent: true,
      supercedes: [As],
    },
    Aj = { key: Ap, value: false, validator: AN.isBoolean };
  YAHOO.lang.extend(s, AC, {
    CSS_CLASS_NAME: "yuimenu",
    ITEM_TYPE: null,
    GROUP_TITLE_TAG_NAME: "h6",
    OFF_SCREEN_POSITION: "-999em",
    _useHideDelay: false,
    _bHandledMouseOverEvent: false,
    _bHandledMouseOutEvent: false,
    _aGroupTitleElements: null,
    _aItemGroups: null,
    _aListElements: null,
    _nCurrentMouseX: 0,
    _bStopMouseEventHandlers: false,
    _sClassName: null,
    lazyLoad: false,
    itemData: null,
    activeItem: null,
    parent: null,
    srcElement: null,
    init: function (A5, A4) {
      this._aItemGroups = [];
      this._aListElements = [];
      this._aGroupTitleElements = [];
      if (!this.ITEM_TYPE) {
        this.ITEM_TYPE = YAHOO.widget.MenuItem;
      }
      var A3;
      if (AN.isString(A5)) {
        A3 = g.get(A5);
      } else {
        if (A5.tagName) {
          A3 = A5;
        }
      }
      if (A3 && A3.tagName) {
        switch (A3.tagName.toUpperCase()) {
          case H:
            this.srcElement = A3;
            if (!A3.id) {
              A3.setAttribute(Ak, g.generateId());
            }
            s.superclass.init.call(this, A3);
            this.beforeInitEvent.fire(s);
            break;
          case AI:
            this.srcElement = A3;
            s.superclass.init.call(this, g.generateId());
            this.beforeInitEvent.fire(s);
            break;
        }
      } else {
        s.superclass.init.call(this, A5);
        this.beforeInitEvent.fire(s);
      }
      if (this.element) {
        g.addClass(this.element, this.CSS_CLASS_NAME);
        this.initEvent.subscribe(this._onInit);
        this.beforeRenderEvent.subscribe(this._onBeforeRender);
        this.renderEvent.subscribe(this._onRender);
        this.beforeShowEvent.subscribe(this._onBeforeShow);
        this.hideEvent.subscribe(this._onHide);
        this.showEvent.subscribe(this._onShow);
        this.beforeHideEvent.subscribe(this._onBeforeHide);
        this.mouseOverEvent.subscribe(this._onMouseOver);
        this.mouseOutEvent.subscribe(this._onMouseOut);
        this.clickEvent.subscribe(this._onClick);
        this.keyDownEvent.subscribe(this._onKeyDown);
        this.keyPressEvent.subscribe(this._onKeyPress);
        this.blurEvent.subscribe(this._onBlur);
        if ((Aq.gecko && Aq.gecko < 1.9) || Aq.webkit) {
          this.cfg.subscribeToConfigEvent(R, this._onYChange);
        }
        if (A4) {
          this.cfg.applyConfig(A4, true);
        }
        A1.addMenu(this);
        this.initEvent.fire(s);
      }
    },
    _initSubTree: function () {
      var A4 = this.srcElement,
        A3,
        A6,
        A9,
        BA,
        A8,
        A7,
        A5;
      if (A4) {
        A3 = A4.tagName && A4.tagName.toUpperCase();
        if (A3 == H) {
          BA = this.body.firstChild;
          if (BA) {
            A6 = 0;
            A9 = this.GROUP_TITLE_TAG_NAME.toUpperCase();
            do {
              if (BA && BA.tagName) {
                switch (BA.tagName.toUpperCase()) {
                  case A9:
                    this._aGroupTitleElements[A6] = BA;
                    break;
                  case Av:
                    this._aListElements[A6] = BA;
                    this._aItemGroups[A6] = [];
                    A6++;
                    break;
                }
              }
            } while ((BA = BA.nextSibling));
            if (this._aListElements[0]) {
              g.addClass(this._aListElements[0], AK);
            }
          }
        }
        BA = null;
        if (A3) {
          switch (A3) {
            case H:
              A8 = this._aListElements;
              A7 = A8.length;
              if (A7 > 0) {
                A5 = A7 - 1;
                do {
                  BA = A8[A5].firstChild;
                  if (BA) {
                    do {
                      if (BA && BA.tagName && BA.tagName.toUpperCase() == l) {
                        this.addItem(
                          new this.ITEM_TYPE(BA, { parent: this }),
                          A5,
                        );
                      }
                    } while ((BA = BA.nextSibling));
                  }
                } while (A5--);
              }
              break;
            case AI:
              BA = A4.firstChild;
              do {
                if (BA && BA.tagName) {
                  switch (BA.tagName.toUpperCase()) {
                    case i:
                    case Ax:
                      this.addItem(new this.ITEM_TYPE(BA, { parent: this }));
                      break;
                  }
                }
              } while ((BA = BA.nextSibling));
              break;
          }
        }
      }
    },
    _getFirstEnabledItem: function () {
      var A3 = this.getItems(),
        A7 = A3.length,
        A6,
        A5;
      for (var A4 = 0; A4 < A7; A4++) {
        A6 = A3[A4];
        if (A6 && !A6.cfg.getProperty(Af) && A6.element.style.display != AY) {
          A5 = A6;
          break;
        }
      }
      return A5;
    },
    _addItemToGroup: function (A8, A9, BD) {
      var BB, BE, A6, BC, A7, A4, A5, BA;

      function A3(BF, BG) {
        return BF[BG] || A3(BF, BG + 1);
      }

      if (A9 instanceof this.ITEM_TYPE) {
        BB = A9;
        BB.parent = this;
      } else {
        if (AN.isString(A9)) {
          BB = new this.ITEM_TYPE(A9, { parent: this });
        } else {
          if (AN.isObject(A9)) {
            A9.parent = this;
            BB = new this.ITEM_TYPE(A9.text, A9);
          }
        }
      }
      if (BB) {
        if (BB.cfg.getProperty(z)) {
          this.activeItem = BB;
        }
        BE = AN.isNumber(A8) ? A8 : 0;
        A6 = this._getItemGroup(BE);
        if (!A6) {
          A6 = this._createItemGroup(BE);
        }
        if (AN.isNumber(BD)) {
          A7 = BD >= A6.length;
          if (A6[BD]) {
            A6.splice(BD, 0, BB);
          } else {
            A6[BD] = BB;
          }
          BC = A6[BD];
          if (BC) {
            if (
              A7 &&
              (!BC.element.parentNode || BC.element.parentNode.nodeType == 11)
            ) {
              this._aListElements[BE].appendChild(BC.element);
            } else {
              A4 = A3(A6, BD + 1);
              if (
                A4 &&
                (!BC.element.parentNode || BC.element.parentNode.nodeType == 11)
              ) {
                this._aListElements[BE].insertBefore(BC.element, A4.element);
              }
            }
            BC.parent = this;
            this._subscribeToItemEvents(BC);
            this._configureSubmenu(BC);
            this._updateItemProperties(BE);
            this.itemAddedEvent.fire(BC);
            this.changeContentEvent.fire();
            BA = BC;
          }
        } else {
          A5 = A6.length;
          A6[A5] = BB;
          BC = A6[A5];
          if (BC) {
            if (!g.isAncestor(this._aListElements[BE], BC.element)) {
              this._aListElements[BE].appendChild(BC.element);
            }
            BC.element.setAttribute(Ar, BE);
            BC.element.setAttribute(j, A5);
            BC.parent = this;
            BC.index = A5;
            BC.groupIndex = BE;
            this._subscribeToItemEvents(BC);
            this._configureSubmenu(BC);
            if (A5 === 0) {
              g.addClass(BC.element, AK);
            }
            this.itemAddedEvent.fire(BC);
            this.changeContentEvent.fire();
            BA = BC;
          }
        }
      }
      return BA;
    },
    _removeItemFromGroupByIndex: function (A6, A4) {
      var A5 = AN.isNumber(A6) ? A6 : 0,
        A7 = this._getItemGroup(A5),
        A9,
        A8,
        A3;
      if (A7) {
        A9 = A7.splice(A4, 1);
        A8 = A9[0];
        if (A8) {
          this._updateItemProperties(A5);
          if (A7.length === 0) {
            A3 = this._aListElements[A5];
            if (this.body && A3) {
              this.body.removeChild(A3);
            }
            this._aItemGroups.splice(A5, 1);
            this._aListElements.splice(A5, 1);
            A3 = this._aListElements[0];
            if (A3) {
              g.addClass(A3, AK);
            }
          }
          this.itemRemovedEvent.fire(A8);
          this.changeContentEvent.fire();
        }
      }
      return A8;
    },
    _removeItemFromGroupByValue: function (A6, A3) {
      var A8 = this._getItemGroup(A6),
        A9,
        A7,
        A5,
        A4;
      if (A8) {
        A9 = A8.length;
        A7 = -1;
        if (A9 > 0) {
          A4 = A9 - 1;
          do {
            if (A8[A4] == A3) {
              A7 = A4;
              break;
            }
          } while (A4--);
          if (A7 > -1) {
            A5 = this._removeItemFromGroupByIndex(A6, A7);
          }
        }
      }
      return A5;
    },
    _updateItemProperties: function (A4) {
      var A5 = this._getItemGroup(A4),
        A8 = A5.length,
        A7,
        A6,
        A3;
      if (A8 > 0) {
        A3 = A8 - 1;
        do {
          A7 = A5[A3];
          if (A7) {
            A6 = A7.element;
            A7.index = A3;
            A7.groupIndex = A4;
            A6.setAttribute(Ar, A4);
            A6.setAttribute(j, A3);
            g.removeClass(A6, AK);
          }
        } while (A3--);
        if (A6) {
          g.addClass(A6, AK);
        }
      }
    },
    _createItemGroup: function (A5) {
      var A3, A4;
      if (!this._aItemGroups[A5]) {
        this._aItemGroups[A5] = [];
        A3 = document.createElement(L);
        this._aListElements[A5] = A3;
        A4 = this._aItemGroups[A5];
      }
      return A4;
    },
    _getItemGroup: function (A5) {
      var A3 = AN.isNumber(A5) ? A5 : 0,
        A6 = this._aItemGroups,
        A4;
      if (A3 in A6) {
        A4 = A6[A3];
      }
      return A4;
    },
    _configureSubmenu: function (A3) {
      var A4 = A3.cfg.getProperty(O);
      if (A4) {
        this.cfg.configChangedEvent.subscribe(
          this._onParentMenuConfigChange,
          A4,
          true,
        );
        this.renderEvent.subscribe(this._onParentMenuRender, A4, true);
      }
    },
    _subscribeToItemEvents: function (A3) {
      A3.destroyEvent.subscribe(this._onMenuItemDestroy, A3, this);
      A3.cfg.configChangedEvent.subscribe(
        this._onMenuItemConfigChange,
        A3,
        this,
      );
    },
    _onVisibleChange: function (A5, A4) {
      var A3 = A4[0];
      if (A3) {
        g.addClass(this.element, As);
      } else {
        g.removeClass(this.element, As);
      }
    },
    _cancelHideDelay: function () {
      var A3 = this.getRoot()._hideDelayTimer;
      if (A3) {
        A3.cancel();
      }
    },
    _execHideDelay: function () {
      this._cancelHideDelay();
      var A3 = this.getRoot();
      A3._hideDelayTimer = AN.later(A3.cfg.getProperty(AX), this, function () {
        if (A3.activeItem) {
          if (A3.hasFocus()) {
            A3.activeItem.focus();
          }
          A3.clearActiveItem();
        }
        if (
          A3 == this &&
          !(this instanceof YAHOO.widget.MenuBar) &&
          this.cfg.getProperty(Ab) == AE
        ) {
          this.hide();
        }
      });
    },
    _cancelShowDelay: function () {
      var A3 = this.getRoot()._showDelayTimer;
      if (A3) {
        A3.cancel();
      }
    },
    _execSubmenuHideDelay: function (A5, A4, A3) {
      A5._submenuHideDelayTimer = AN.later(50, this, function () {
        if (this._nCurrentMouseX > A4 + 10) {
          A5._submenuHideDelayTimer = AN.later(A3, A5, function () {
            this.hide();
          });
        } else {
          A5.hide();
        }
      });
    },
    _disableScrollHeader: function () {
      if (!this._bHeaderDisabled) {
        g.addClass(this.header, P);
        this._bHeaderDisabled = true;
      }
    },
    _disableScrollFooter: function () {
      if (!this._bFooterDisabled) {
        g.addClass(this.footer, E);
        this._bFooterDisabled = true;
      }
    },
    _enableScrollHeader: function () {
      if (this._bHeaderDisabled) {
        g.removeClass(this.header, P);
        this._bHeaderDisabled = false;
      }
    },
    _enableScrollFooter: function () {
      if (this._bFooterDisabled) {
        g.removeClass(this.footer, E);
        this._bFooterDisabled = false;
      }
    },
    _onMouseOver: function (BF, A8) {
      var BG = A8[0],
        BC = A8[1],
        A3 = AB.getTarget(BG),
        A7 = this.getRoot(),
        BE = this._submenuHideDelayTimer,
        A4,
        A6,
        BB,
        A5,
        BA,
        A9;
      var BD = function () {
        if (this.parent.cfg.getProperty(z)) {
          this.show();
        }
      };
      if (!this._bStopMouseEventHandlers) {
        if (
          !this._bHandledMouseOverEvent &&
          (A3 == this.element || g.isAncestor(this.element, A3))
        ) {
          if (this._useHideDelay) {
            this._cancelHideDelay();
          }
          this._nCurrentMouseX = 0;
          AB.on(this.element, c, this._onMouseMove, this, true);
          if (!(BC && g.isAncestor(BC.element, AB.getRelatedTarget(BG)))) {
            this.clearActiveItem();
          }
          if (this.parent && BE) {
            BE.cancel();
            this.parent.cfg.setProperty(z, true);
            A4 = this.parent.parent;
            A4._bHandledMouseOutEvent = true;
            A4._bHandledMouseOverEvent = false;
          }
          this._bHandledMouseOverEvent = true;
          this._bHandledMouseOutEvent = false;
        }
        if (
          BC &&
          !BC.handledMouseOverEvent &&
          !BC.cfg.getProperty(Af) &&
          (A3 == BC.element || g.isAncestor(BC.element, A3))
        ) {
          A6 = this.cfg.getProperty(At);
          BB = A6 > 0;
          if (BB) {
            this._cancelShowDelay();
          }
          A5 = this.activeItem;
          if (A5) {
            A5.cfg.setProperty(z, false);
          }
          BA = BC.cfg;
          BA.setProperty(z, true);
          if (this.hasFocus() || A7._hasFocus) {
            BC.focus();
            A7._hasFocus = false;
          }
          if (this.cfg.getProperty(a)) {
            A9 = BA.getProperty(O);
            if (A9) {
              if (BB) {
                A7._showDelayTimer = AN.later(A7.cfg.getProperty(At), A9, BD);
              } else {
                A9.show();
              }
            }
          }
          BC.handledMouseOverEvent = true;
          BC.handledMouseOutEvent = false;
        }
      }
    },
    _onMouseOut: function (BB, A5) {
      var BC = A5[0],
        A9 = A5[1],
        A6 = AB.getRelatedTarget(BC),
        BA = false,
        A8,
        A7,
        A3,
        A4;
      if (!this._bStopMouseEventHandlers) {
        if (A9 && !A9.cfg.getProperty(Af)) {
          A8 = A9.cfg;
          A7 = A8.getProperty(O);
          if (A7 && (A6 == A7.element || g.isAncestor(A7.element, A6))) {
            BA = true;
          }
          if (
            !A9.handledMouseOutEvent &&
            ((A6 != A9.element && !g.isAncestor(A9.element, A6)) || BA)
          ) {
            if (!BA) {
              A9.cfg.setProperty(z, false);
              if (A7) {
                A3 = this.cfg.getProperty(d);
                A4 = this.cfg.getProperty(At);
                if (
                  !(this instanceof YAHOO.widget.MenuBar) &&
                  A3 > 0 &&
                  A4 >= A3
                ) {
                  this._execSubmenuHideDelay(A7, AB.getPageX(BC), A3);
                } else {
                  A7.hide();
                }
              }
            }
            A9.handledMouseOutEvent = true;
            A9.handledMouseOverEvent = false;
          }
        }
        if (
          !this._bHandledMouseOutEvent &&
          ((A6 != this.element && !g.isAncestor(this.element, A6)) || BA)
        ) {
          if (this._useHideDelay) {
            this._execHideDelay();
          }
          AB.removeListener(this.element, c, this._onMouseMove);
          this._nCurrentMouseX = AB.getPageX(BC);
          this._bHandledMouseOutEvent = true;
          this._bHandledMouseOverEvent = false;
        }
      }
    },
    _onMouseMove: function (A4, A3) {
      if (!this._bStopMouseEventHandlers) {
        this._nCurrentMouseX = AB.getPageX(A4);
      }
    },
    _onClick: function (BE, A5) {
      var BF = A5[0],
        A9 = A5[1],
        BB = false,
        A7,
        BC,
        A4,
        A3,
        A8,
        BA,
        BD;
      var A6 = function () {
        if (!(Aq.gecko && this.platform == Y && BF.button > 0)) {
          A4 = this.getRoot();
          if (
            A4 instanceof YAHOO.widget.MenuBar ||
            A4.cfg.getProperty(Ab) == C
          ) {
            A4.clearActiveItem();
          } else {
            A4.hide();
          }
        }
      };
      if (A9) {
        if (A9.cfg.getProperty(Af)) {
          AB.preventDefault(BF);
          A6.call(this);
        } else {
          A7 = A9.cfg.getProperty(O);
          A8 = A9.cfg.getProperty(Q);
          if (A8) {
            BA = A8.indexOf(M);
            BD = A8.length;
            if (BA != -1) {
              A8 = A8.substr(BA, BD);
              BD = A8.length;
              if (BD > 1) {
                A3 = A8.substr(1, BD);
                BC = YAHOO.widget.MenuManager.getMenu(A3);
                if (BC) {
                  BB = this.getRoot() === BC.getRoot();
                }
              } else {
                if (BD === 1) {
                  BB = true;
                }
              }
            }
          }
          if (BB && !A9.cfg.getProperty(V)) {
            AB.preventDefault(BF);
            if (Aq.webkit) {
              A9.focus();
            } else {
              A9.focusEvent.fire();
            }
          }
          if (!A7 && !this.cfg.getProperty(Ap)) {
            A6.call(this);
          }
        }
      }
    },
    _onKeyDown: function (BH, BB) {
      var BE = BB[0],
        BD = BB[1],
        BA,
        BF,
        A4,
        A8,
        BI,
        A3,
        BK,
        A7,
        BG,
        A6,
        BC,
        BJ,
        A9;
      if (this._useHideDelay) {
        this._cancelHideDelay();
      }

      function A5() {
        this._bStopMouseEventHandlers = true;
        AN.later(10, this, function () {
          this._bStopMouseEventHandlers = false;
        });
      }

      if (BD && !BD.cfg.getProperty(Af)) {
        BF = BD.cfg;
        A4 = this.parent;
        switch (BE.keyCode) {
          case 38:
          case 40:
            BI =
              BE.keyCode == 38
                ? BD.getPreviousEnabledSibling()
                : BD.getNextEnabledSibling();
            if (BI) {
              this.clearActiveItem();
              BI.cfg.setProperty(z, true);
              BI.focus();
              if (this.cfg.getProperty(AU) > 0) {
                A3 = this.body;
                BK = A3.scrollTop;
                A7 = A3.offsetHeight;
                BG = this.getItems();
                A6 = BG.length - 1;
                BC = BI.element.offsetTop;
                if (BE.keyCode == 40) {
                  if (BC >= A7 + BK) {
                    A3.scrollTop = BC - A7;
                  } else {
                    if (BC <= BK) {
                      A3.scrollTop = 0;
                    }
                  }
                  if (BI == BG[A6]) {
                    A3.scrollTop = BI.element.offsetTop;
                  }
                } else {
                  if (BC <= BK) {
                    A3.scrollTop = BC - BI.element.offsetHeight;
                  } else {
                    if (BC >= BK + A7) {
                      A3.scrollTop = BC;
                    }
                  }
                  if (BI == BG[0]) {
                    A3.scrollTop = 0;
                  }
                }
                BK = A3.scrollTop;
                BJ = A3.scrollHeight - A3.offsetHeight;
                if (BK === 0) {
                  this._disableScrollHeader();
                  this._enableScrollFooter();
                } else {
                  if (BK == BJ) {
                    this._enableScrollHeader();
                    this._disableScrollFooter();
                  } else {
                    this._enableScrollHeader();
                    this._enableScrollFooter();
                  }
                }
              }
            }
            AB.preventDefault(BE);
            A5();
            break;
          case 39:
            BA = BF.getProperty(O);
            if (BA) {
              if (!BF.getProperty(z)) {
                BF.setProperty(z, true);
              }
              BA.show();
              BA.setInitialFocus();
              BA.setInitialSelection();
            } else {
              A8 = this.getRoot();
              if (A8 instanceof YAHOO.widget.MenuBar) {
                BI = A8.activeItem.getNextEnabledSibling();
                if (BI) {
                  A8.clearActiveItem();
                  BI.cfg.setProperty(z, true);
                  BA = BI.cfg.getProperty(O);
                  if (BA) {
                    BA.show();
                    BA.setInitialFocus();
                  } else {
                    BI.focus();
                  }
                }
              }
            }
            AB.preventDefault(BE);
            A5();
            break;
          case 37:
            if (A4) {
              A9 = A4.parent;
              if (A9 instanceof YAHOO.widget.MenuBar) {
                BI = A9.activeItem.getPreviousEnabledSibling();
                if (BI) {
                  A9.clearActiveItem();
                  BI.cfg.setProperty(z, true);
                  BA = BI.cfg.getProperty(O);
                  if (BA) {
                    BA.show();
                    BA.setInitialFocus();
                  } else {
                    BI.focus();
                  }
                }
              } else {
                this.hide();
                A4.focus();
              }
            }
            AB.preventDefault(BE);
            A5();
            break;
        }
      }
      if (BE.keyCode == 27) {
        if (this.cfg.getProperty(Ab) == AE) {
          this.hide();
          if (this.parent) {
            this.parent.focus();
          }
        } else {
          if (this.activeItem) {
            BA = this.activeItem.cfg.getProperty(O);
            if (BA && BA.cfg.getProperty(As)) {
              BA.hide();
              this.activeItem.focus();
            } else {
              this.activeItem.blur();
              this.activeItem.cfg.setProperty(z, false);
            }
          }
        }
        AB.preventDefault(BE);
      }
    },
    _onKeyPress: function (A5, A4) {
      var A3 = A4[0];
      if (A3.keyCode == 40 || A3.keyCode == 38) {
        AB.preventDefault(A3);
      }
    },
    _onBlur: function (A4, A3) {
      if (this._hasFocus) {
        this._hasFocus = false;
      }
    },
    _onYChange: function (A4, A3) {
      var A6 = this.parent,
        A8,
        A5,
        A7;
      if (A6) {
        A8 = A6.parent.body.scrollTop;
        if (A8 > 0) {
          A7 = this.cfg.getProperty(R) - A8;
          g.setY(this.element, A7);
          A5 = this.iframe;
          if (A5) {
            g.setY(A5, A7);
          }
          this.cfg.setProperty(R, A7, true);
        }
      }
    },
    _onScrollTargetMouseOver: function (A9, BC) {
      var BB = this._bodyScrollTimer;
      if (BB) {
        BB.cancel();
      }
      this._cancelHideDelay();
      var A5 = AB.getTarget(A9),
        A7 = this.body,
        A6 = this.cfg.getProperty(k),
        A3,
        A4;

      function BA() {
        var BD = A7.scrollTop;
        if (BD < A3) {
          A7.scrollTop = BD + A6;
          this._enableScrollHeader();
        } else {
          A7.scrollTop = A3;
          this._bodyScrollTimer.cancel();
          this._disableScrollFooter();
        }
      }

      function A8() {
        var BD = A7.scrollTop;
        if (BD > 0) {
          A7.scrollTop = BD - A6;
          this._enableScrollFooter();
        } else {
          A7.scrollTop = 0;
          this._bodyScrollTimer.cancel();
          this._disableScrollHeader();
        }
      }

      if (g.hasClass(A5, Ay)) {
        A4 = A8;
      } else {
        A3 = A7.scrollHeight - A7.offsetHeight;
        A4 = BA;
      }
      this._bodyScrollTimer = AN.later(10, this, A4, null, true);
    },
    _onScrollTargetMouseOut: function (A5, A3) {
      var A4 = this._bodyScrollTimer;
      if (A4) {
        A4.cancel();
      }
      this._cancelHideDelay();
    },
    _onInit: function (A4, A3) {
      this.cfg.subscribeToConfigEvent(As, this._onVisibleChange);
      var A5 = !this.parent,
        A6 = this.lazyLoad;
      if (
        ((A5 && !A6) ||
          (A5 && (this.cfg.getProperty(As) || this.cfg.getProperty(Ab) == C)) ||
          (!A5 && !A6)) &&
        this.getItemGroups().length === 0
      ) {
        if (this.srcElement) {
          this._initSubTree();
        }
        if (this.itemData) {
          this.addItems(this.itemData);
        }
      } else {
        if (A6) {
          this.cfg.fireQueue();
        }
      }
    },
    _onBeforeRender: function (A6, A5) {
      var A7 = this.element,
        BA = this._aListElements.length,
        A4 = true,
        A9 = 0,
        A3,
        A8;
      if (BA > 0) {
        do {
          A3 = this._aListElements[A9];
          if (A3) {
            if (A4) {
              g.addClass(A3, AK);
              A4 = false;
            }
            if (!g.isAncestor(A7, A3)) {
              this.appendToBody(A3);
            }
            A8 = this._aGroupTitleElements[A9];
            if (A8) {
              if (!g.isAncestor(A7, A8)) {
                A3.parentNode.insertBefore(A8, A3);
              }
              g.addClass(A3, D);
            }
          }
          A9++;
        } while (A9 < BA);
      }
    },
    _onRender: function (A4, A3) {
      if (this.cfg.getProperty(Ab) == AE) {
        if (!this.cfg.getProperty(As)) {
          this.positionOffScreen();
        }
      }
    },
    _onBeforeShow: function (A5, A4) {
      var A7,
        BA,
        A6,
        A8 = this.cfg.getProperty(h);
      if (this.lazyLoad && this.getItemGroups().length === 0) {
        if (this.srcElement) {
          this._initSubTree();
        }
        if (this.itemData) {
          if (
            this.parent &&
            this.parent.parent &&
            this.parent.parent.srcElement &&
            this.parent.parent.srcElement.tagName.toUpperCase() == AI
          ) {
            A7 = this.itemData.length;
            for (BA = 0; BA < A7; BA++) {
              if (this.itemData[BA].tagName) {
                this.addItem(new this.ITEM_TYPE(this.itemData[BA]));
              }
            }
          } else {
            this.addItems(this.itemData);
          }
        }
        A6 = this.srcElement;
        if (A6) {
          if (A6.tagName.toUpperCase() == AI) {
            if (g.inDocument(A6)) {
              this.render(A6.parentNode);
            } else {
              this.render(A8);
            }
          } else {
            this.render();
          }
        } else {
          if (this.parent) {
            this.render(this.parent.element);
          } else {
            this.render(A8);
          }
        }
      }
      var A9 = this.parent,
        A3;
      if (!A9 && this.cfg.getProperty(Ab) == AE) {
        this.cfg.refireEvent(f);
      }
      if (A9) {
        A3 = A9.parent.cfg.getProperty(AP);
        this.cfg.setProperty(q, [A9.element, A3[0], A3[1]]);
        this.align();
      }
    },
    getConstrainedY: function (BF) {
      var BQ = this,
        BM = BQ.cfg.getProperty(q),
        BT = BQ.cfg.getProperty(AU),
        BP,
        BE = { trbr: true, tlbl: true, bltl: true, brtr: true },
        A8 = BM && BE[BM[1] + BM[2]],
        BA = BQ.element,
        BU = BA.offsetHeight,
        BO = AC.VIEWPORT_OFFSET,
        BJ = g.getViewportHeight(),
        BN = g.getDocumentScrollTop(),
        BK = BQ.cfg.getProperty(Ah) + BO < BJ,
        BS,
        BB,
        BH,
        BI,
        BD = false,
        BC,
        A5,
        BG = BN + BO,
        A7 = BN + BJ - BU - BO,
        A3 = BF;
      var A9 = function () {
        var BV;
        if (BQ.cfg.getProperty(R) - BN > BH) {
          BV = BH - BU;
        } else {
          BV = BH + BI;
        }
        BQ.cfg.setProperty(R, BV + BN, true);
        return BV;
      };
      var A6 = function () {
        if (BQ.cfg.getProperty(R) - BN > BH) {
          return A5 - BO;
        } else {
          return BC - BO;
        }
      };
      var BL = function () {
        var BV;
        if (BQ.cfg.getProperty(R) - BN > BH) {
          BV = BH + BI;
        } else {
          BV = BH - BA.offsetHeight;
        }
        BQ.cfg.setProperty(R, BV + BN, true);
      };
      var A4 = function () {
        BQ._setScrollHeight(this.cfg.getProperty(AU));
        BQ.hideEvent.unsubscribe(A4);
      };
      var BR = function () {
        var BY = A6(),
          BV = BQ.getItems().length > 0,
          BX,
          BW;
        if (BU > BY) {
          BX = BV ? BQ.cfg.getProperty(Ah) : BU;
          if (BY > BX && BV) {
            BP = BY;
          } else {
            BP = BT;
          }
          BQ._setScrollHeight(BP);
          BQ.hideEvent.subscribe(A4);
          BL();
          if (BY < BX) {
            if (BD) {
              A9();
            } else {
              A9();
              BD = true;
              BW = BR();
            }
          }
        } else {
          if (BP && BP !== BT) {
            BQ._setScrollHeight(BT);
            BQ.hideEvent.subscribe(A4);
            BL();
          }
        }
        return BW;
      };
      if (BF < BG || BF > A7) {
        if (BK) {
          if (BQ.cfg.getProperty(A2) && A8) {
            BB = BM[0];
            BI = BB.offsetHeight;
            BH = g.getY(BB) - BN;
            BC = BH;
            A5 = BJ - (BH + BI);
            BR();
            A3 = BQ.cfg.getProperty(R);
          } else {
            if (!(BQ instanceof YAHOO.widget.MenuBar) && BU >= BJ) {
              BS = BJ - BO * 2;
              if (BS > BQ.cfg.getProperty(Ah)) {
                BQ._setScrollHeight(BS);
                BQ.hideEvent.subscribe(A4);
                BL();
                A3 = BQ.cfg.getProperty(R);
              }
            } else {
              if (BF < BG) {
                A3 = BG;
              } else {
                if (BF > A7) {
                  A3 = A7;
                }
              }
            }
          }
        } else {
          A3 = BO + BN;
        }
      }
      return A3;
    },
    _onHide: function (A4, A3) {
      if (this.cfg.getProperty(Ab) === AE) {
        this.positionOffScreen();
      }
    },
    _onShow: function (BB, A9) {
      var A3 = this.parent,
        A5,
        A6,
        A8,
        A4;

      function A7(BD) {
        var BC;
        if (BD.type == Ai || (BD.type == Ac && BD.keyCode == 27)) {
          BC = AB.getTarget(BD);
          if (BC != A5.element || !g.isAncestor(A5.element, BC)) {
            A5.cfg.setProperty(a, false);
            AB.removeListener(document, Ai, A7);
            AB.removeListener(document, Ac, A7);
          }
        }
      }

      function BA(BD, BC, BE) {
        this.cfg.setProperty(U, v);
        this.hideEvent.unsubscribe(BA, BE);
      }

      if (A3) {
        A5 = A3.parent;
        if (
          !A5.cfg.getProperty(a) &&
          (A5 instanceof YAHOO.widget.MenuBar || A5.cfg.getProperty(Ab) == C)
        ) {
          A5.cfg.setProperty(a, true);
          AB.on(document, Ai, A7);
          AB.on(document, Ac, A7);
        }
        if (
          this.cfg.getProperty("x") < A5.cfg.getProperty("x") &&
          Aq.gecko &&
          Aq.gecko < 1.9 &&
          !this.cfg.getProperty(U)
        ) {
          A6 = this.element;
          A8 = A6.offsetWidth;
          A6.style.width = A8 + AR;
          A4 = A8 - (A6.offsetWidth - A8) + AR;
          this.cfg.setProperty(U, A4);
          this.hideEvent.subscribe(BA, A4);
        }
      }
    },
    _onBeforeHide: function (A5, A4) {
      var A3 = this.activeItem,
        A7 = this.getRoot(),
        A8,
        A6;
      if (A3) {
        A8 = A3.cfg;
        A8.setProperty(z, false);
        A6 = A8.getProperty(O);
        if (A6) {
          A6.hide();
        }
      }
      if (Aq.ie && this.cfg.getProperty(Ab) === AE && this.parent) {
        A7._hasFocus = this.hasFocus();
      }
      if (A7 == this) {
        A7.blur();
      }
    },
    _onParentMenuConfigChange: function (A4, A3, A7) {
      var A5 = A3[0][0],
        A6 = A3[0][1];
      switch (A5) {
        case AG:
        case x:
        case AX:
        case At:
        case d:
        case AD:
        case Aw:
        case A0:
        case k:
        case AU:
        case Ah:
        case AF:
        case Ae:
        case A2:
          A7.cfg.setProperty(A5, A6);
          break;
        case AP:
          if (!(this.parent.parent instanceof YAHOO.widget.MenuBar)) {
            A7.cfg.setProperty(A5, A6);
          }
          break;
      }
    },
    _onParentMenuRender: function (A4, A3, A9) {
      var A6 = A9.parent.parent,
        A5 = A6.cfg,
        A7 = {
          constraintoviewport: A5.getProperty(x),
          xy: [0, 0],
          clicktohide: A5.getProperty(AD),
          effect: A5.getProperty(Aw),
          showdelay: A5.getProperty(At),
          hidedelay: A5.getProperty(AX),
          submenuhidedelay: A5.getProperty(d),
          classname: A5.getProperty(A0),
          scrollincrement: A5.getProperty(k),
          maxheight: A5.getProperty(AU),
          minscrollheight: A5.getProperty(Ah),
          iframe: A5.getProperty(AG),
          shadow: A5.getProperty(Ae),
          preventcontextoverlap: A5.getProperty(A2),
          monitorresize: A5.getProperty(AF),
        },
        A8;
      if (!(A6 instanceof YAHOO.widget.MenuBar)) {
        A7[AP] = A5.getProperty(AP);
      }
      A9.cfg.applyConfig(A7);
      if (!this.lazyLoad) {
        A8 = this.parent.element;
        if (this.element.parentNode == A8) {
          this.render();
        } else {
          this.render(A8);
        }
      }
    },
    _onMenuItemDestroy: function (A5, A4, A3) {
      this._removeItemFromGroupByValue(A3.groupIndex, A3);
    },
    _onMenuItemConfigChange: function (A5, A4, A3) {
      var A7 = A4[0][0],
        A8 = A4[0][1],
        A6;
      switch (A7) {
        case z:
          if (A8 === true) {
            this.activeItem = A3;
          }
          break;
        case O:
          A6 = A4[0][1];
          if (A6) {
            this._configureSubmenu(A3);
          }
          break;
      }
    },
    configVisible: function (A5, A4, A6) {
      var A3, A7;
      if (this.cfg.getProperty(Ab) == AE) {
        s.superclass.configVisible.call(this, A5, A4, A6);
      } else {
        A3 = A4[0];
        A7 = g.getStyle(this.element, AW);
        g.setStyle(this.element, J, As);
        if (A3) {
          if (A7 != AV) {
            this.beforeShowEvent.fire();
            g.setStyle(this.element, AW, AV);
            this.showEvent.fire();
          }
        } else {
          if (A7 == AV) {
            this.beforeHideEvent.fire();
            g.setStyle(this.element, AW, AY);
            this.hideEvent.fire();
          }
        }
      }
    },
    configPosition: function (A5, A4, A8) {
      var A7 = this.element,
        A6 = A4[0] == C ? C : AA,
        A9 = this.cfg,
        A3;
      g.setStyle(A7, Ab, A6);
      if (A6 == C) {
        g.setStyle(A7, AW, AV);
        A9.setProperty(As, true);
      } else {
        g.setStyle(A7, J, AM);
      }
      if (A6 == AA) {
        A3 = A9.getProperty(AT);
        if (!A3 || A3 === 0) {
          A9.setProperty(AT, 1);
        }
      }
    },
    configIframe: function (A4, A3, A5) {
      if (this.cfg.getProperty(Ab) == AE) {
        s.superclass.configIframe.call(this, A4, A3, A5);
      }
    },
    configHideDelay: function (A4, A3, A5) {
      var A6 = A3[0];
      this._useHideDelay = A6 > 0;
    },
    configContainer: function (A4, A3, A6) {
      var A5 = A3[0];
      if (AN.isString(A5)) {
        this.cfg.setProperty(h, g.get(A5), true);
      }
    },
    _clearSetWidthFlag: function () {
      this._widthSetForScroll = false;
      this.cfg.unsubscribeFromConfigEvent(U, this._clearSetWidthFlag);
    },
    _setScrollHeight: function (BE) {
      var BA = BE,
        A9 = false,
        BF = false,
        A6,
        A7,
        BD,
        A4,
        BC,
        BG,
        A3,
        BB,
        A8,
        A5;
      if (this.getItems().length > 0) {
        A6 = this.element;
        A7 = this.body;
        BD = this.header;
        A4 = this.footer;
        BC = this._onScrollTargetMouseOver;
        BG = this._onScrollTargetMouseOut;
        A3 = this.cfg.getProperty(Ah);
        if (BA > 0 && BA < A3) {
          BA = A3;
        }
        g.setStyle(A7, Am, v);
        g.removeClass(A7, m);
        A7.scrollTop = 0;
        BF = (Aq.gecko && Aq.gecko < 1.9) || Aq.ie;
        if (BA > 0 && BF && !this.cfg.getProperty(U)) {
          A8 = A6.offsetWidth;
          A6.style.width = A8 + AR;
          A5 = A8 - (A6.offsetWidth - A8) + AR;
          this.cfg.unsubscribeFromConfigEvent(U, this._clearSetWidthFlag);
          this.cfg.setProperty(U, A5);
          this._widthSetForScroll = true;
          this.cfg.subscribeToConfigEvent(U, this._clearSetWidthFlag);
        }
        if (BA > 0 && !BD && !A4) {
          this.setHeader(AL);
          this.setFooter(AL);
          BD = this.header;
          A4 = this.footer;
          g.addClass(BD, T);
          g.addClass(A4, y);
          A6.insertBefore(BD, A7);
          A6.appendChild(A4);
        }
        BB = BA;
        if (BD && A4) {
          BB = BB - (BD.offsetHeight + A4.offsetHeight);
        }
        if (BB > 0 && A7.offsetHeight > BA) {
          g.addClass(A7, m);
          g.setStyle(A7, Am, BB + AR);
          if (!this._hasScrollEventHandlers) {
            AB.on(BD, Ag, BC, this, true);
            AB.on(BD, G, BG, this, true);
            AB.on(A4, Ag, BC, this, true);
            AB.on(A4, G, BG, this, true);
            this._hasScrollEventHandlers = true;
          }
          this._disableScrollHeader();
          this._enableScrollFooter();
          A9 = true;
        } else {
          if (BD && A4) {
            if (this._widthSetForScroll) {
              this._widthSetForScroll = false;
              this.cfg.unsubscribeFromConfigEvent(U, this._clearSetWidthFlag);
              this.cfg.setProperty(U, v);
            }
            this._enableScrollHeader();
            this._enableScrollFooter();
            if (this._hasScrollEventHandlers) {
              AB.removeListener(BD, Ag, BC);
              AB.removeListener(BD, G, BG);
              AB.removeListener(A4, Ag, BC);
              AB.removeListener(A4, G, BG);
              this._hasScrollEventHandlers = false;
            }
            A6.removeChild(BD);
            A6.removeChild(A4);
            this.header = null;
            this.footer = null;
            A9 = true;
          }
        }
        if (A9) {
          this.cfg.refireEvent(AG);
          this.cfg.refireEvent(Ae);
        }
      }
    },
    _setMaxHeight: function (A4, A3, A5) {
      this._setScrollHeight(A5);
      this.renderEvent.unsubscribe(this._setMaxHeight);
    },
    configMaxHeight: function (A4, A3, A5) {
      var A6 = A3[0];
      if (this.lazyLoad && !this.body && A6 > 0) {
        this.renderEvent.subscribe(this._setMaxHeight, A6, this);
      } else {
        this._setScrollHeight(A6);
      }
    },
    configClassName: function (A5, A4, A6) {
      var A3 = A4[0];
      if (this._sClassName) {
        g.removeClass(this.element, this._sClassName);
      }
      g.addClass(this.element, A3);
      this._sClassName = A3;
    },
    _onItemAdded: function (A4, A3) {
      var A5 = A3[0];
      if (A5) {
        A5.cfg.setProperty(Af, true);
      }
    },
    configDisabled: function (A5, A4, A8) {
      var A7 = A4[0],
        A3 = this.getItems(),
        A9,
        A6;
      if (AN.isArray(A3)) {
        A9 = A3.length;
        if (A9 > 0) {
          A6 = A9 - 1;
          do {
            A3[A6].cfg.setProperty(Af, A7);
          } while (A6--);
        }
        if (A7) {
          this.clearActiveItem(true);
          g.addClass(this.element, Af);
          this.itemAddedEvent.subscribe(this._onItemAdded);
        } else {
          g.removeClass(this.element, Af);
          this.itemAddedEvent.unsubscribe(this._onItemAdded);
        }
      }
    },
    configShadow: function (BB, A5, BA) {
      var A9 = function () {
        var BE = this.element,
          BD = this._shadow;
        if (BD && BE) {
          if (BD.style.width && BD.style.height) {
            BD.style.width = v;
            BD.style.height = v;
          }
          BD.style.width = BE.offsetWidth + 6 + AR;
          BD.style.height = BE.offsetHeight + 1 + AR;
        }
      };
      var BC = function () {
        this.element.appendChild(this._shadow);
      };
      var A7 = function () {
        g.addClass(this._shadow, AH);
      };
      var A8 = function () {
        g.removeClass(this._shadow, AH);
      };
      var A4 = function () {
        var BE = this._shadow,
          BD;
        if (!BE) {
          BD = this.element;
          if (!An) {
            An = document.createElement(K);
            An.className = n;
          }
          BE = An.cloneNode(false);
          BD.appendChild(BE);
          this._shadow = BE;
          this.beforeShowEvent.subscribe(A7);
          this.beforeHideEvent.subscribe(A8);
          if (Aq.ie) {
            AN.later(0, this, function () {
              A9.call(this);
              this.syncIframe();
            });
            this.cfg.subscribeToConfigEvent(U, A9);
            this.cfg.subscribeToConfigEvent(Am, A9);
            this.cfg.subscribeToConfigEvent(AU, A9);
            this.changeContentEvent.subscribe(A9);
            Au.textResizeEvent.subscribe(A9, this, true);
            this.destroyEvent.subscribe(function () {
              Au.textResizeEvent.unsubscribe(A9, this);
            });
          }
          this.cfg.subscribeToConfigEvent(AU, BC);
        }
      };
      var A6 = function () {
        if (this._shadow) {
          BC.call(this);
          if (Aq.ie) {
            A9.call(this);
          }
        } else {
          A4.call(this);
        }
        this.beforeShowEvent.unsubscribe(A6);
      };
      var A3 = A5[0];
      if (A3 && this.cfg.getProperty(Ab) == AE) {
        if (this.cfg.getProperty(As)) {
          if (this._shadow) {
            BC.call(this);
            if (Aq.ie) {
              A9.call(this);
            }
          } else {
            A4.call(this);
          }
        } else {
          this.beforeShowEvent.subscribe(A6);
        }
      }
    },
    initEvents: function () {
      s.superclass.initEvents.call(this);
      var A4 = Aa.length - 1,
        A5,
        A3;
      do {
        A5 = Aa[A4];
        A3 = this.createEvent(A5[1]);
        A3.signature = F.LIST;
        this[A5[0]] = A3;
      } while (A4--);
    },
    positionOffScreen: function () {
      var A4 = this.iframe,
        A5 = this.element,
        A3 = this.OFF_SCREEN_POSITION;
      A5.style.top = v;
      A5.style.left = v;
      if (A4) {
        A4.style.top = A3;
        A4.style.left = A3;
      }
    },
    getRoot: function () {
      var A5 = this.parent,
        A4,
        A3;
      if (A5) {
        A4 = A5.parent;
        A3 = A4 ? A4.getRoot() : this;
      } else {
        A3 = this;
      }
      return A3;
    },
    toString: function () {
      var A4 = Ao,
        A3 = this.id;
      if (A3) {
        A4 += Az + A3;
      }
      return A4;
    },
    setItemGroupTitle: function (A8, A7) {
      var A6, A5, A4, A3;
      if (AN.isString(A8) && A8.length > 0) {
        A6 = AN.isNumber(A7) ? A7 : 0;
        A5 = this._aGroupTitleElements[A6];
        if (A5) {
          A5.innerHTML = A8;
        } else {
          A5 = document.createElement(this.GROUP_TITLE_TAG_NAME);
          A5.innerHTML = A8;
          this._aGroupTitleElements[A6] = A5;
        }
        A4 = this._aGroupTitleElements.length - 1;
        do {
          if (this._aGroupTitleElements[A4]) {
            g.removeClass(this._aGroupTitleElements[A4], AK);
            A3 = A4;
          }
        } while (A4--);
        if (A3 !== null) {
          g.addClass(this._aGroupTitleElements[A3], AK);
        }
        this.changeContentEvent.fire();
      }
    },
    addItem: function (A3, A4) {
      return this._addItemToGroup(A4, A3);
    },
    addItems: function (A7, A6) {
      var A9, A3, A8, A4, A5;
      if (AN.isArray(A7)) {
        A9 = A7.length;
        A3 = [];
        for (A4 = 0; A4 < A9; A4++) {
          A8 = A7[A4];
          if (A8) {
            if (AN.isArray(A8)) {
              A3[A3.length] = this.addItems(A8, A4);
            } else {
              A3[A3.length] = this._addItemToGroup(A6, A8);
            }
          }
        }
        if (A3.length) {
          A5 = A3;
        }
      }
      return A5;
    },
    insertItem: function (A3, A4, A5) {
      return this._addItemToGroup(A5, A3, A4);
    },
    removeItem: function (A3, A5) {
      var A6, A4;
      if (!AN.isUndefined(A3)) {
        if (A3 instanceof YAHOO.widget.MenuItem) {
          A6 = this._removeItemFromGroupByValue(A5, A3);
        } else {
          if (AN.isNumber(A3)) {
            A6 = this._removeItemFromGroupByIndex(A5, A3);
          }
        }
        if (A6) {
          A6.destroy();
          A4 = A6;
        }
      }
      return A4;
    },
    getItems: function () {
      var A6 = this._aItemGroups,
        A4,
        A5,
        A3 = [];
      if (AN.isArray(A6)) {
        A4 = A6.length;
        A5 = A4 == 1 ? A6[0] : Array.prototype.concat.apply(A3, A6);
      }
      return A5;
    },
    getItemGroups: function () {
      return this._aItemGroups;
    },
    getItem: function (A4, A5) {
      var A6, A3;
      if (AN.isNumber(A4)) {
        A6 = this._getItemGroup(A5);
        if (A6) {
          A3 = A6[A4];
        }
      }
      return A3;
    },
    getSubmenus: function () {
      var A4 = this.getItems(),
        A8 = A4.length,
        A3,
        A5,
        A7,
        A6;
      if (A8 > 0) {
        A3 = [];
        for (A6 = 0; A6 < A8; A6++) {
          A7 = A4[A6];
          if (A7) {
            A5 = A7.cfg.getProperty(O);
            if (A5) {
              A3[A3.length] = A5;
            }
          }
        }
      }
      return A3;
    },
    clearContent: function () {
      var A7 = this.getItems(),
        A4 = A7.length,
        A5 = this.element,
        A6 = this.body,
        BB = this.header,
        A3 = this.footer,
        BA,
        A9,
        A8;
      if (A4 > 0) {
        A8 = A4 - 1;
        do {
          BA = A7[A8];
          if (BA) {
            A9 = BA.cfg.getProperty(O);
            if (A9) {
              this.cfg.configChangedEvent.unsubscribe(
                this._onParentMenuConfigChange,
                A9,
              );
              this.renderEvent.unsubscribe(this._onParentMenuRender, A9);
            }
            this.removeItem(BA, BA.groupIndex);
          }
        } while (A8--);
      }
      if (BB) {
        AB.purgeElement(BB);
        A5.removeChild(BB);
      }
      if (A3) {
        AB.purgeElement(A3);
        A5.removeChild(A3);
      }
      if (A6) {
        AB.purgeElement(A6);
        A6.innerHTML = v;
      }
      this.activeItem = null;
      this._aItemGroups = [];
      this._aListElements = [];
      this._aGroupTitleElements = [];
      this.cfg.setProperty(U, null);
    },
    destroy: function () {
      this.clearContent();
      this._aItemGroups = null;
      this._aListElements = null;
      this._aGroupTitleElements = null;
      s.superclass.destroy.call(this);
    },
    setInitialFocus: function () {
      var A3 = this._getFirstEnabledItem();
      if (A3) {
        A3.focus();
      }
    },
    setInitialSelection: function () {
      var A3 = this._getFirstEnabledItem();
      if (A3) {
        A3.cfg.setProperty(z, true);
      }
    },
    clearActiveItem: function (A5) {
      if (this.cfg.getProperty(At) > 0) {
        this._cancelShowDelay();
      }
      var A3 = this.activeItem,
        A6,
        A4;
      if (A3) {
        A6 = A3.cfg;
        if (A5) {
          A3.blur();
          this.getRoot()._hasFocus = true;
        }
        A6.setProperty(z, false);
        A4 = A6.getProperty(O);
        if (A4) {
          A4.hide();
        }
        this.activeItem = null;
      }
    },
    focus: function () {
      if (!this.hasFocus()) {
        this.setInitialFocus();
      }
    },
    blur: function () {
      var A3;
      if (this.hasFocus()) {
        A3 = A1.getFocusedMenuItem();
        if (A3) {
          A3.blur();
        }
      }
    },
    hasFocus: function () {
      return A1.getFocusedMenu() == this.getRoot();
    },
    subscribe: function () {
      function A6(BB, BA, BD) {
        var BE = BA[0],
          BC = BE.cfg.getProperty(O);
        if (BC) {
          BC.subscribe.apply(BC, BD);
        }
      }

      function A9(BB, BA, BD) {
        var BC = this.cfg.getProperty(O);
        if (BC) {
          BC.subscribe.apply(BC, BD);
        }
      }

      s.superclass.subscribe.apply(this, arguments);
      s.superclass.subscribe.call(this, AS, A6, arguments);
      var A3 = this.getItems(),
        A8,
        A7,
        A4,
        A5;
      if (A3) {
        A8 = A3.length;
        if (A8 > 0) {
          A5 = A8 - 1;
          do {
            A7 = A3[A5];
            A4 = A7.cfg.getProperty(O);
            if (A4) {
              A4.subscribe.apply(A4, arguments);
            } else {
              A7.cfg.subscribeToConfigEvent(O, A9, arguments);
            }
          } while (A5--);
        }
      }
    },
    initDefaultConfig: function () {
      s.superclass.initDefaultConfig.call(this);
      var A3 = this.cfg;
      A3.addProperty(AZ.key, {
        handler: this.configVisible,
        value: AZ.value,
        validator: AZ.validator,
      });
      A3.addProperty(AQ.key, {
        handler: this.configConstrainToViewport,
        value: AQ.value,
        validator: AQ.validator,
        supercedes: AQ.supercedes,
      });
      A3.addProperty(AJ.key, {
        value: AJ.value,
        validator: AJ.validator,
        supercedes: AJ.supercedes,
      });
      A3.addProperty(S.key, {
        handler: this.configPosition,
        value: S.value,
        validator: S.validator,
        supercedes: S.supercedes,
      });
      A3.addProperty(A.key, { value: A.value, suppressEvent: A.suppressEvent });
      A3.addProperty(u.key, {
        value: u.value,
        validator: u.validator,
        suppressEvent: u.suppressEvent,
      });
      A3.addProperty(Z.key, {
        value: Z.value,
        validator: Z.validator,
        suppressEvent: Z.suppressEvent,
      });
      A3.addProperty(r.key, {
        handler: this.configHideDelay,
        value: r.value,
        validator: r.validator,
        suppressEvent: r.suppressEvent,
      });
      A3.addProperty(w.key, {
        value: w.value,
        validator: w.validator,
        suppressEvent: w.suppressEvent,
      });
      A3.addProperty(p.key, {
        value: p.value,
        validator: p.validator,
        suppressEvent: p.suppressEvent,
      });
      A3.addProperty(AO.key, {
        handler: this.configContainer,
        value: document.body,
        suppressEvent: AO.suppressEvent,
      });
      A3.addProperty(Ad.key, {
        value: Ad.value,
        validator: Ad.validator,
        supercedes: Ad.supercedes,
        suppressEvent: Ad.suppressEvent,
      });
      A3.addProperty(N.key, {
        value: N.value,
        validator: N.validator,
        supercedes: N.supercedes,
        suppressEvent: N.suppressEvent,
      });
      A3.addProperty(X.key, {
        handler: this.configMaxHeight,
        value: X.value,
        validator: X.validator,
        suppressEvent: X.suppressEvent,
        supercedes: X.supercedes,
      });
      A3.addProperty(W.key, {
        handler: this.configClassName,
        value: W.value,
        validator: W.validator,
        supercedes: W.supercedes,
      });
      A3.addProperty(b.key, {
        handler: this.configDisabled,
        value: b.value,
        validator: b.validator,
        suppressEvent: b.suppressEvent,
      });
      A3.addProperty(I.key, {
        handler: this.configShadow,
        value: I.value,
        validator: I.validator,
      });
      A3.addProperty(Aj.key, { value: Aj.value, validator: Aj.validator });
    },
  });
})();
(function () {
  YAHOO.widget.MenuItem = function (AS, AR) {
    if (AS) {
      if (AR) {
        this.parent = AR.parent;
        this.value = AR.value;
        this.id = AR.id;
      }
      this.init(AS, AR);
    }
  };
  var x = YAHOO.util.Dom,
    j = YAHOO.widget.Module,
    AB = YAHOO.widget.Menu,
    c = YAHOO.widget.MenuItem,
    AK = YAHOO.util.CustomEvent,
    k = YAHOO.env.ua,
    AQ = YAHOO.lang,
    AL = "text",
    O = "#",
    Q = "-",
    L = "helptext",
    n = "url",
    AH = "target",
    A = "emphasis",
    N = "strongemphasis",
    b = "checked",
    w = "submenu",
    H = "disabled",
    B = "selected",
    P = "hassubmenu",
    U = "checked-disabled",
    AI = "hassubmenu-disabled",
    AD = "hassubmenu-selected",
    T = "checked-selected",
    q = "onclick",
    J = "classname",
    AJ = "",
    i = "OPTION",
    v = "OPTGROUP",
    K = "LI",
    AE = "href",
    r = "SELECT",
    X = "DIV",
    AN = '<em class="helptext">',
    a = "<em>",
    I = "</em>",
    W = "<strong>",
    y = "</strong>",
    Y = "preventcontextoverlap",
    h = "obj",
    AG = "scope",
    t = "none",
    V = "visible",
    E = " ",
    m = "MenuItem",
    AA = "click",
    D = "show",
    M = "hide",
    S = "li",
    AF = '<a href="#"></a>',
    p = [
      ["mouseOverEvent", "mouseover"],
      ["mouseOutEvent", "mouseout"],
      ["mouseDownEvent", "mousedown"],
      ["mouseUpEvent", "mouseup"],
      ["clickEvent", AA],
      ["keyPressEvent", "keypress"],
      ["keyDownEvent", "keydown"],
      ["keyUpEvent", "keyup"],
      ["focusEvent", "focus"],
      ["blurEvent", "blur"],
      ["destroyEvent", "destroy"],
    ],
    o = { key: AL, value: AJ, validator: AQ.isString, suppressEvent: true },
    s = { key: L, supercedes: [AL], suppressEvent: true },
    G = { key: n, value: O, suppressEvent: true },
    AO = { key: AH, suppressEvent: true },
    AP = {
      key: A,
      value: false,
      validator: AQ.isBoolean,
      suppressEvent: true,
      supercedes: [AL],
    },
    d = {
      key: N,
      value: false,
      validator: AQ.isBoolean,
      suppressEvent: true,
      supercedes: [AL],
    },
    l = {
      key: b,
      value: false,
      validator: AQ.isBoolean,
      suppressEvent: true,
      supercedes: [H, B],
    },
    F = { key: w, suppressEvent: true, supercedes: [H, B] },
    AM = {
      key: H,
      value: false,
      validator: AQ.isBoolean,
      suppressEvent: true,
      supercedes: [AL, B],
    },
    f = { key: B, value: false, validator: AQ.isBoolean, suppressEvent: true },
    u = { key: q, suppressEvent: true },
    AC = { key: J, value: null, validator: AQ.isString, suppressEvent: true },
    z = { key: "keylistener", value: null, suppressEvent: true },
    C = null,
    e = {};
  var Z = function (AU, AT) {
    var AR = e[AU];
    if (!AR) {
      e[AU] = {};
      AR = e[AU];
    }
    var AS = AR[AT];
    if (!AS) {
      AS = AU + Q + AT;
      AR[AT] = AS;
    }
    return AS;
  };
  var g = function (AR) {
    x.addClass(this.element, Z(this.CSS_CLASS_NAME, AR));
    x.addClass(this._oAnchor, Z(this.CSS_LABEL_CLASS_NAME, AR));
  };
  var R = function (AR) {
    x.removeClass(this.element, Z(this.CSS_CLASS_NAME, AR));
    x.removeClass(this._oAnchor, Z(this.CSS_LABEL_CLASS_NAME, AR));
  };
  c.prototype = {
    CSS_CLASS_NAME: "yuimenuitem",
    CSS_LABEL_CLASS_NAME: "yuimenuitemlabel",
    SUBMENU_TYPE: null,
    _oAnchor: null,
    _oHelpTextEM: null,
    _oSubmenu: null,
    _oOnclickAttributeValue: null,
    _sClassName: null,
    constructor: c,
    index: null,
    groupIndex: null,
    parent: null,
    element: null,
    srcElement: null,
    value: null,
    browser: j.prototype.browser,
    id: null,
    init: function (AR, Ab) {
      if (!this.SUBMENU_TYPE) {
        this.SUBMENU_TYPE = AB;
      }
      this.cfg = new YAHOO.util.Config(this);
      this.initDefaultConfig();
      var AX = this.cfg,
        AY = O,
        AT,
        Aa,
        AZ,
        AS,
        AV,
        AU,
        AW;
      if (AQ.isString(AR)) {
        this._createRootNodeStructure();
        AX.queueProperty(AL, AR);
      } else {
        if (AR && AR.tagName) {
          switch (AR.tagName.toUpperCase()) {
            case i:
              this._createRootNodeStructure();
              AX.queueProperty(AL, AR.text);
              AX.queueProperty(H, AR.disabled);
              this.value = AR.value;
              this.srcElement = AR;
              break;
            case v:
              this._createRootNodeStructure();
              AX.queueProperty(AL, AR.label);
              AX.queueProperty(H, AR.disabled);
              this.srcElement = AR;
              this._initSubTree();
              break;
            case K:
              AZ = x.getFirstChild(AR);
              if (AZ) {
                AY = AZ.getAttribute(AE, 2);
                AS = AZ.getAttribute(AH);
                AV = AZ.innerHTML;
              }
              this.srcElement = AR;
              this.element = AR;
              this._oAnchor = AZ;
              AX.setProperty(AL, AV, true);
              AX.setProperty(n, AY, true);
              AX.setProperty(AH, AS, true);
              this._initSubTree();
              break;
          }
        }
      }
      if (this.element) {
        AU = (this.srcElement || this.element).id;
        if (!AU) {
          AU = this.id || x.generateId();
          this.element.id = AU;
        }
        this.id = AU;
        x.addClass(this.element, this.CSS_CLASS_NAME);
        x.addClass(this._oAnchor, this.CSS_LABEL_CLASS_NAME);
        AW = p.length - 1;
        do {
          Aa = p[AW];
          AT = this.createEvent(Aa[1]);
          AT.signature = AK.LIST;
          this[Aa[0]] = AT;
        } while (AW--);
        if (Ab) {
          AX.applyConfig(Ab);
        }
        AX.fireQueue();
      }
    },
    _createRootNodeStructure: function () {
      var AR, AS;
      if (!C) {
        C = document.createElement(S);
        C.innerHTML = AF;
      }
      AR = C.cloneNode(true);
      AR.className = this.CSS_CLASS_NAME;
      AS = AR.firstChild;
      AS.className = this.CSS_LABEL_CLASS_NAME;
      this.element = AR;
      this._oAnchor = AS;
    },
    _initSubTree: function () {
      var AX = this.srcElement,
        AT = this.cfg,
        AV,
        AU,
        AS,
        AR,
        AW;
      if (AX.childNodes.length > 0) {
        if (
          this.parent.lazyLoad &&
          this.parent.srcElement &&
          this.parent.srcElement.tagName.toUpperCase() == r
        ) {
          AT.setProperty(w, { id: x.generateId(), itemdata: AX.childNodes });
        } else {
          AV = AX.firstChild;
          AU = [];
          do {
            if (AV && AV.tagName) {
              switch (AV.tagName.toUpperCase()) {
                case X:
                  AT.setProperty(w, AV);
                  break;
                case i:
                  AU[AU.length] = AV;
                  break;
              }
            }
          } while ((AV = AV.nextSibling));
          AS = AU.length;
          if (AS > 0) {
            AR = new this.SUBMENU_TYPE(x.generateId());
            AT.setProperty(w, AR);
            for (AW = 0; AW < AS; AW++) {
              AR.addItem(new AR.ITEM_TYPE(AU[AW]));
            }
          }
        }
      }
    },
    configText: function (Aa, AT, AV) {
      var AS = AT[0],
        AU = this.cfg,
        AY = this._oAnchor,
        AR = AU.getProperty(L),
        AZ = AJ,
        AW = AJ,
        AX = AJ;
      if (AS) {
        if (AR) {
          AZ = AN + AR + I;
        }
        if (AU.getProperty(A)) {
          AW = a;
          AX = I;
        }
        if (AU.getProperty(N)) {
          AW = W;
          AX = y;
        }
        AY.innerHTML = AW + AS + AX + AZ;
      }
    },
    configHelpText: function (AT, AS, AR) {
      this.cfg.refireEvent(AL);
    },
    configURL: function (AT, AS, AR) {
      var AV = AS[0];
      if (!AV) {
        AV = O;
      }
      var AU = this._oAnchor;
      if (k.opera) {
        AU.removeAttribute(AE);
      }
      AU.setAttribute(AE, AV);
    },
    configTarget: function (AU, AT, AS) {
      var AR = AT[0],
        AV = this._oAnchor;
      if (AR && AR.length > 0) {
        AV.setAttribute(AH, AR);
      } else {
        AV.removeAttribute(AH);
      }
    },
    configEmphasis: function (AT, AS, AR) {
      var AV = AS[0],
        AU = this.cfg;
      if (AV && AU.getProperty(N)) {
        AU.setProperty(N, false);
      }
      AU.refireEvent(AL);
    },
    configStrongEmphasis: function (AU, AT, AS) {
      var AR = AT[0],
        AV = this.cfg;
      if (AR && AV.getProperty(A)) {
        AV.setProperty(A, false);
      }
      AV.refireEvent(AL);
    },
    configChecked: function (AT, AS, AR) {
      var AV = AS[0],
        AU = this.cfg;
      if (AV) {
        g.call(this, b);
      } else {
        R.call(this, b);
      }
      AU.refireEvent(AL);
      if (AU.getProperty(H)) {
        AU.refireEvent(H);
      }
      if (AU.getProperty(B)) {
        AU.refireEvent(B);
      }
    },
    configDisabled: function (AT, AS, AR) {
      var AV = AS[0],
        AW = this.cfg,
        AU = AW.getProperty(w),
        AX = AW.getProperty(b);
      if (AV) {
        if (AW.getProperty(B)) {
          AW.setProperty(B, false);
        }
        g.call(this, H);
        if (AU) {
          g.call(this, AI);
        }
        if (AX) {
          g.call(this, U);
        }
      } else {
        R.call(this, H);
        if (AU) {
          R.call(this, AI);
        }
        if (AX) {
          R.call(this, U);
        }
      }
    },
    configSelected: function (AT, AS, AR) {
      var AX = this.cfg,
        AW = this._oAnchor,
        AV = AS[0],
        AY = AX.getProperty(b),
        AU = AX.getProperty(w);
      if (k.opera) {
        AW.blur();
      }
      if (AV && !AX.getProperty(H)) {
        g.call(this, B);
        if (AU) {
          g.call(this, AD);
        }
        if (AY) {
          g.call(this, T);
        }
      } else {
        R.call(this, B);
        if (AU) {
          R.call(this, AD);
        }
        if (AY) {
          R.call(this, T);
        }
      }
      if (this.hasFocus() && k.opera) {
        AW.focus();
      }
    },
    _onSubmenuBeforeHide: function (AU, AT) {
      var AV = this.parent,
        AR;

      function AS() {
        AV._oAnchor.blur();
        AR.beforeHideEvent.unsubscribe(AS);
      }

      if (AV.hasFocus()) {
        AR = AV.parent;
        AR.beforeHideEvent.subscribe(AS);
      }
    },
    configSubmenu: function (AY, AT, AW) {
      var AV = AT[0],
        AU = this.cfg,
        AS = this.parent && this.parent.lazyLoad,
        AX,
        AZ,
        AR;
      if (AV) {
        if (AV instanceof AB) {
          AX = AV;
          AX.parent = this;
          AX.lazyLoad = AS;
        } else {
          if (AQ.isObject(AV) && AV.id && !AV.nodeType) {
            AZ = AV.id;
            AR = AV;
            AR.lazyload = AS;
            AR.parent = this;
            AX = new this.SUBMENU_TYPE(AZ, AR);
            AU.setProperty(w, AX, true);
          } else {
            AX = new this.SUBMENU_TYPE(AV, { lazyload: AS, parent: this });
            AU.setProperty(w, AX, true);
          }
        }
        if (AX) {
          AX.cfg.setProperty(Y, true);
          g.call(this, P);
          if (AU.getProperty(n) === O) {
            AU.setProperty(n, O + AX.id);
          }
          this._oSubmenu = AX;
          if (k.opera) {
            AX.beforeHideEvent.subscribe(this._onSubmenuBeforeHide);
          }
        }
      } else {
        R.call(this, P);
        if (this._oSubmenu) {
          this._oSubmenu.destroy();
        }
      }
      if (AU.getProperty(H)) {
        AU.refireEvent(H);
      }
      if (AU.getProperty(B)) {
        AU.refireEvent(B);
      }
    },
    configOnClick: function (AT, AS, AR) {
      var AU = AS[0];
      if (this._oOnclickAttributeValue && this._oOnclickAttributeValue != AU) {
        this.clickEvent.unsubscribe(
          this._oOnclickAttributeValue.fn,
          this._oOnclickAttributeValue.obj,
        );
        this._oOnclickAttributeValue = null;
      }
      if (
        !this._oOnclickAttributeValue &&
        AQ.isObject(AU) &&
        AQ.isFunction(AU.fn)
      ) {
        this.clickEvent.subscribe(
          AU.fn,
          h in AU ? AU.obj : this,
          AG in AU ? AU.scope : null,
        );
        this._oOnclickAttributeValue = AU;
      }
    },
    configClassName: function (AU, AT, AS) {
      var AR = AT[0];
      if (this._sClassName) {
        x.removeClass(this.element, this._sClassName);
      }
      x.addClass(this.element, AR);
      this._sClassName = AR;
    },
    _dispatchClickEvent: function () {
      var AT = this,
        AS,
        AR;
      if (!AT.cfg.getProperty(H)) {
        AS = x.getFirstChild(AT.element);
        if (k.ie) {
          AS.fireEvent(q);
        } else {
          if ((k.gecko && k.gecko >= 1.9) || k.opera || k.webkit) {
            AR = document.createEvent("HTMLEvents");
            AR.initEvent(AA, true, true);
          } else {
            AR = document.createEvent("MouseEvents");
            AR.initMouseEvent(
              AA,
              true,
              true,
              window,
              0,
              0,
              0,
              0,
              0,
              false,
              false,
              false,
              false,
              0,
              null,
            );
          }
          AS.dispatchEvent(AR);
        }
      }
    },
    _createKeyListener: function (AU, AT, AW) {
      var AV = this,
        AS = AV.parent;
      var AR = new YAHOO.util.KeyListener(AS.element.ownerDocument, AW, {
        fn: AV._dispatchClickEvent,
        scope: AV,
        correctScope: true,
      });
      if (AS.cfg.getProperty(V)) {
        AR.enable();
      }
      AS.subscribe(D, AR.enable, null, AR);
      AS.subscribe(M, AR.disable, null, AR);
      AV._keyListener = AR;
      AS.unsubscribe(D, AV._createKeyListener, AW);
    },
    configKeyListener: function (AT, AS) {
      var AV = AS[0],
        AU = this,
        AR = AU.parent;
      if (AU._keyData) {
        AR.unsubscribe(D, AU._createKeyListener, AU._keyData);
        AU._keyData = null;
      }
      if (AU._keyListener) {
        AR.unsubscribe(D, AU._keyListener.enable);
        AR.unsubscribe(M, AU._keyListener.disable);
        AU._keyListener.disable();
        AU._keyListener = null;
      }
      if (AV) {
        AU._keyData = AV;
        AR.subscribe(D, AU._createKeyListener, AV, AU);
      }
    },
    initDefaultConfig: function () {
      var AR = this.cfg;
      AR.addProperty(o.key, {
        handler: this.configText,
        value: o.value,
        validator: o.validator,
        suppressEvent: o.suppressEvent,
      });
      AR.addProperty(s.key, {
        handler: this.configHelpText,
        supercedes: s.supercedes,
        suppressEvent: s.suppressEvent,
      });
      AR.addProperty(G.key, {
        handler: this.configURL,
        value: G.value,
        suppressEvent: G.suppressEvent,
      });
      AR.addProperty(AO.key, {
        handler: this.configTarget,
        suppressEvent: AO.suppressEvent,
      });
      AR.addProperty(AP.key, {
        handler: this.configEmphasis,
        value: AP.value,
        validator: AP.validator,
        suppressEvent: AP.suppressEvent,
        supercedes: AP.supercedes,
      });
      AR.addProperty(d.key, {
        handler: this.configStrongEmphasis,
        value: d.value,
        validator: d.validator,
        suppressEvent: d.suppressEvent,
        supercedes: d.supercedes,
      });
      AR.addProperty(l.key, {
        handler: this.configChecked,
        value: l.value,
        validator: l.validator,
        suppressEvent: l.suppressEvent,
        supercedes: l.supercedes,
      });
      AR.addProperty(AM.key, {
        handler: this.configDisabled,
        value: AM.value,
        validator: AM.validator,
        suppressEvent: AM.suppressEvent,
      });
      AR.addProperty(f.key, {
        handler: this.configSelected,
        value: f.value,
        validator: f.validator,
        suppressEvent: f.suppressEvent,
      });
      AR.addProperty(F.key, {
        handler: this.configSubmenu,
        supercedes: F.supercedes,
        suppressEvent: F.suppressEvent,
      });
      AR.addProperty(u.key, {
        handler: this.configOnClick,
        suppressEvent: u.suppressEvent,
      });
      AR.addProperty(AC.key, {
        handler: this.configClassName,
        value: AC.value,
        validator: AC.validator,
        suppressEvent: AC.suppressEvent,
      });
      AR.addProperty(z.key, {
        handler: this.configKeyListener,
        value: z.value,
        suppressEvent: z.suppressEvent,
      });
    },
    getNextEnabledSibling: function () {
      var AU, AX, AR, AW, AV, AS;

      function AT(AY, AZ) {
        return AY[AZ] || AT(AY, AZ + 1);
      }

      if (this.parent instanceof AB) {
        AU = this.groupIndex;
        AX = this.parent.getItemGroups();
        if (this.index < AX[AU].length - 1) {
          AR = AT(AX[AU], this.index + 1);
        } else {
          if (AU < AX.length - 1) {
            AW = AU + 1;
          } else {
            AW = 0;
          }
          AV = AT(AX, AW);
          AR = AT(AV, 0);
        }
        AS =
          AR.cfg.getProperty(H) || AR.element.style.display == t
            ? AR.getNextEnabledSibling()
            : AR;
      }
      return AS;
    },
    getPreviousEnabledSibling: function () {
      var AW, AY, AS, AR, AV, AU;

      function AX(AZ, Aa) {
        return AZ[Aa] || AX(AZ, Aa - 1);
      }

      function AT(AZ, Aa) {
        return AZ[Aa] ? Aa : AT(AZ, Aa + 1);
      }

      if (this.parent instanceof AB) {
        AW = this.groupIndex;
        AY = this.parent.getItemGroups();
        if (this.index > AT(AY[AW], 0)) {
          AS = AX(AY[AW], this.index - 1);
        } else {
          if (AW > AT(AY, 0)) {
            AR = AW - 1;
          } else {
            AR = AY.length - 1;
          }
          AV = AX(AY, AR);
          AS = AX(AV, AV.length - 1);
        }
        AU =
          AS.cfg.getProperty(H) || AS.element.style.display == t
            ? AS.getPreviousEnabledSibling()
            : AS;
      }
      return AU;
    },
    focus: function () {
      var AU = this.parent,
        AT = this._oAnchor,
        AR = AU.activeItem;

      function AS() {
        try {
          if (!(k.ie && !document.hasFocus())) {
            if (AR) {
              AR.blurEvent.fire();
            }
            AT.focus();
            this.focusEvent.fire();
          }
        } catch (AV) {}
      }

      if (
        !this.cfg.getProperty(H) &&
        AU &&
        AU.cfg.getProperty(V) &&
        this.element.style.display != t
      ) {
        AQ.later(0, this, AS);
      }
    },
    blur: function () {
      var AR = this.parent;
      if (!this.cfg.getProperty(H) && AR && AR.cfg.getProperty(V)) {
        AQ.later(
          0,
          this,
          function () {
            try {
              this._oAnchor.blur();
              this.blurEvent.fire();
            } catch (AS) {}
          },
          0,
        );
      }
    },
    hasFocus: function () {
      return YAHOO.widget.MenuManager.getFocusedMenuItem() == this;
    },
    destroy: function () {
      var AT = this.element,
        AS,
        AR,
        AV,
        AU;
      if (AT) {
        AS = this.cfg.getProperty(w);
        if (AS) {
          AS.destroy();
        }
        AR = AT.parentNode;
        if (AR) {
          AR.removeChild(AT);
          this.destroyEvent.fire();
        }
        AU = p.length - 1;
        do {
          AV = p[AU];
          this[AV[0]].unsubscribeAll();
        } while (AU--);
        this.cfg.configChangedEvent.unsubscribeAll();
      }
    },
    toString: function () {
      var AS = m,
        AR = this.id;
      if (AR) {
        AS += E + AR;
      }
      return AS;
    },
  };
  AQ.augmentProto(c, YAHOO.util.EventProvider);
})();
(function () {
  var B = "xy",
    C = "mousedown",
    F = "ContextMenu",
    J = " ";
  YAHOO.widget.ContextMenu = function (L, K) {
    YAHOO.widget.ContextMenu.superclass.constructor.call(this, L, K);
  };
  var I = YAHOO.util.Event,
    E = YAHOO.env.ua,
    G = YAHOO.widget.ContextMenu,
    A = {
      TRIGGER_CONTEXT_MENU: "triggerContextMenu",
      CONTEXT_MENU: E.opera ? C : "contextmenu",
      CLICK: "click",
    },
    H = { key: "trigger", suppressEvent: true };

  function D(L, K, M) {
    this.cfg.setProperty(B, M);
    this.beforeShowEvent.unsubscribe(D, M);
  }

  YAHOO.lang.extend(G, YAHOO.widget.Menu, {
    _oTrigger: null,
    _bCancelled: false,
    contextEventTarget: null,
    triggerContextMenuEvent: null,
    init: function (L, K) {
      G.superclass.init.call(this, L);
      this.beforeInitEvent.fire(G);
      if (K) {
        this.cfg.applyConfig(K, true);
      }
      this.initEvent.fire(G);
    },
    initEvents: function () {
      G.superclass.initEvents.call(this);
      this.triggerContextMenuEvent = this.createEvent(A.TRIGGER_CONTEXT_MENU);
      this.triggerContextMenuEvent.signature = YAHOO.util.CustomEvent.LIST;
    },
    cancel: function () {
      this._bCancelled = true;
    },
    _removeEventHandlers: function () {
      var K = this._oTrigger;
      if (K) {
        I.removeListener(K, A.CONTEXT_MENU, this._onTriggerContextMenu);
        if (E.opera) {
          I.removeListener(K, A.CLICK, this._onTriggerClick);
        }
      }
    },
    _onTriggerClick: function (L, K) {
      if (L.ctrlKey) {
        I.stopEvent(L);
      }
    },
    _onTriggerContextMenu: function (M, K) {
      var L;
      if (!(M.type == C && !M.ctrlKey)) {
        this.contextEventTarget = I.getTarget(M);
        this.triggerContextMenuEvent.fire(M);
        if (!this._bCancelled) {
          I.stopEvent(M);
          YAHOO.widget.MenuManager.hideVisible();
          L = I.getXY(M);
          if (!YAHOO.util.Dom.inDocument(this.element)) {
            this.beforeShowEvent.subscribe(D, L);
          } else {
            this.cfg.setProperty(B, L);
          }
          this.show();
        }
        this._bCancelled = false;
      }
    },
    toString: function () {
      var L = F,
        K = this.id;
      if (K) {
        L += J + K;
      }
      return L;
    },
    initDefaultConfig: function () {
      G.superclass.initDefaultConfig.call(this);
      this.cfg.addProperty(H.key, {
        handler: this.configTrigger,
        suppressEvent: H.suppressEvent,
      });
    },
    destroy: function () {
      this._removeEventHandlers();
      G.superclass.destroy.call(this);
    },
    configTrigger: function (L, K, N) {
      var M = K[0];
      if (M) {
        if (this._oTrigger) {
          this._removeEventHandlers();
        }
        this._oTrigger = M;
        I.on(M, A.CONTEXT_MENU, this._onTriggerContextMenu, this, true);
        if (E.opera) {
          I.on(M, A.CLICK, this._onTriggerClick, this, true);
        }
      } else {
        this._removeEventHandlers();
      }
    },
  });
})();
YAHOO.widget.ContextMenuItem = YAHOO.widget.MenuItem;
(function () {
  var D = YAHOO.lang,
    N = "static",
    M = "dynamic," + N,
    A = "disabled",
    F = "selected",
    B = "autosubmenudisplay",
    G = "submenu",
    C = "visible",
    Q = " ",
    H = "submenutoggleregion",
    P = "MenuBar";
  YAHOO.widget.MenuBar = function (T, S) {
    YAHOO.widget.MenuBar.superclass.constructor.call(this, T, S);
  };

  function O(T) {
    var S = false;
    if (D.isString(T)) {
      S = M.indexOf(T.toLowerCase()) != -1;
    }
    return S;
  }

  var R = YAHOO.util.Event,
    L = YAHOO.widget.MenuBar,
    K = { key: "position", value: N, validator: O, supercedes: [C] },
    E = { key: "submenualignment", value: ["tl", "bl"] },
    J = { key: B, value: false, validator: D.isBoolean, suppressEvent: true },
    I = { key: H, value: false, validator: D.isBoolean };
  D.extend(L, YAHOO.widget.Menu, {
    init: function (T, S) {
      if (!this.ITEM_TYPE) {
        this.ITEM_TYPE = YAHOO.widget.MenuBarItem;
      }
      L.superclass.init.call(this, T);
      this.beforeInitEvent.fire(L);
      if (S) {
        this.cfg.applyConfig(S, true);
      }
      this.initEvent.fire(L);
    },
    CSS_CLASS_NAME: "yuimenubar",
    SUBMENU_TOGGLE_REGION_WIDTH: 20,
    _onKeyDown: function (U, T, Y) {
      var S = T[0],
        Z = T[1],
        W,
        X,
        V;
      if (Z && !Z.cfg.getProperty(A)) {
        X = Z.cfg;
        switch (S.keyCode) {
          case 37:
          case 39:
            if (Z == this.activeItem && !X.getProperty(F)) {
              X.setProperty(F, true);
            } else {
              V =
                S.keyCode == 37
                  ? Z.getPreviousEnabledSibling()
                  : Z.getNextEnabledSibling();
              if (V) {
                this.clearActiveItem();
                V.cfg.setProperty(F, true);
                W = V.cfg.getProperty(G);
                if (W) {
                  W.show();
                  W.setInitialFocus();
                } else {
                  V.focus();
                }
              }
            }
            R.preventDefault(S);
            break;
          case 40:
            if (this.activeItem != Z) {
              this.clearActiveItem();
              X.setProperty(F, true);
              Z.focus();
            }
            W = X.getProperty(G);
            if (W) {
              if (W.cfg.getProperty(C)) {
                W.setInitialSelection();
                W.setInitialFocus();
              } else {
                W.show();
                W.setInitialFocus();
              }
            }
            R.preventDefault(S);
            break;
        }
      }
      if (S.keyCode == 27 && this.activeItem) {
        W = this.activeItem.cfg.getProperty(G);
        if (W && W.cfg.getProperty(C)) {
          W.hide();
          this.activeItem.focus();
        } else {
          this.activeItem.cfg.setProperty(F, false);
          this.activeItem.blur();
        }
        R.preventDefault(S);
      }
    },
    _onClick: function (e, Y, b) {
      L.superclass._onClick.call(this, e, Y, b);
      var d = Y[1],
        T = true,
        S,
        f,
        U,
        W,
        Z,
        a,
        c,
        V;
      var X = function () {
        if (a.cfg.getProperty(C)) {
          a.hide();
        } else {
          a.show();
        }
      };
      if (d && !d.cfg.getProperty(A)) {
        f = Y[0];
        U = R.getTarget(f);
        W = this.activeItem;
        Z = this.cfg;
        if (W && W != d) {
          this.clearActiveItem();
        }
        d.cfg.setProperty(F, true);
        a = d.cfg.getProperty(G);
        if (a) {
          S = d.element;
          c = YAHOO.util.Dom.getX(S);
          V = c + (S.offsetWidth - this.SUBMENU_TOGGLE_REGION_WIDTH);
          if (Z.getProperty(H)) {
            if (R.getPageX(f) > V) {
              X();
              R.preventDefault(f);
              T = false;
            }
          } else {
            X();
          }
        }
      }
      return T;
    },
    configSubmenuToggle: function (U, T) {
      var S = T[0];
      if (S) {
        this.cfg.setProperty(B, false);
      }
    },
    toString: function () {
      var T = P,
        S = this.id;
      if (S) {
        T += Q + S;
      }
      return T;
    },
    initDefaultConfig: function () {
      L.superclass.initDefaultConfig.call(this);
      var S = this.cfg;
      S.addProperty(K.key, {
        handler: this.configPosition,
        value: K.value,
        validator: K.validator,
        supercedes: K.supercedes,
      });
      S.addProperty(E.key, { value: E.value, suppressEvent: E.suppressEvent });
      S.addProperty(J.key, {
        value: J.value,
        validator: J.validator,
        suppressEvent: J.suppressEvent,
      });
      S.addProperty(I.key, {
        value: I.value,
        validator: I.validator,
        handler: this.configSubmenuToggle,
      });
    },
  });
})();
YAHOO.widget.MenuBarItem = function (B, A) {
  YAHOO.widget.MenuBarItem.superclass.constructor.call(this, B, A);
};
YAHOO.lang.extend(YAHOO.widget.MenuBarItem, YAHOO.widget.MenuItem, {
  init: function (B, A) {
    if (!this.SUBMENU_TYPE) {
      this.SUBMENU_TYPE = YAHOO.widget.Menu;
    }
    YAHOO.widget.MenuBarItem.superclass.init.call(this, B);
    var C = this.cfg;
    if (A) {
      C.applyConfig(A, true);
    }
    C.fireQueue();
  },
  CSS_CLASS_NAME: "yuimenubaritem",
  CSS_LABEL_CLASS_NAME: "yuimenubaritemlabel",
  toString: function () {
    var A = "MenuBarItem";
    if (this.cfg && this.cfg.getProperty("text")) {
      A += ": " + this.cfg.getProperty("text");
    }
    return A;
  },
});
YAHOO.register("menu", YAHOO.widget.Menu, { version: "2.7.0", build: "1799" });
