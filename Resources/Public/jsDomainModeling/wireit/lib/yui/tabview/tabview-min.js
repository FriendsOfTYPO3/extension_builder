/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  var B = YAHOO.util,
    C = B.Dom,
    H = B.Event,
    F = window.document,
    J = "active",
    D = "activeIndex",
    E = "activeTab",
    A = "contentEl",
    G = "element",
    I = function (L, K) {
      K = K || {};
      if (arguments.length == 1 && !YAHOO.lang.isString(L) && !L.nodeName) {
        K = L;
        L = K.element || null;
      }
      if (!L && !K.element) {
        L = this._createTabViewElement(K);
      }
      I.superclass.constructor.call(this, L, K);
    };
  YAHOO.extend(I, B.Element, {
    CLASSNAME: "yui-navset",
    TAB_PARENT_CLASSNAME: "yui-nav",
    CONTENT_PARENT_CLASSNAME: "yui-content",
    _tabParent: null,
    _contentParent: null,
    addTab: function (P, L) {
      var N = this.get("tabs"),
        Q = this.getTab(L),
        R = this._tabParent,
        K = this._contentParent,
        M = P.get(G),
        O = P.get(A);
      if (!N) {
        this._queue[this._queue.length] = ["addTab", arguments];
        return false;
      }
      L = L === undefined ? N.length : L;
      if (Q) {
        R.insertBefore(M, Q.get(G));
      } else {
        R.appendChild(M);
      }
      if (O && !C.isAncestor(K, O)) {
        K.appendChild(O);
      }
      if (!P.get(J)) {
        P.set("contentVisible", false, true);
      } else {
        this.set(E, P, true);
      }
      this._initTabEvents(P);
      N.splice(L, 0, P);
    },
    _initTabEvents: function (K) {
      K.addListener(K.get("activationEvent"), K._onActivate, this, K);
      K.addListener("activationEventChange", function (L) {
        if (L.prevValue != L.newValue) {
          K.removeListener(L.prevValue, K._onActivate);
          K.addListener(L.newValue, K._onActivate, this, K);
        }
      });
    },
    DOMEventHandler: function (P) {
      var Q = H.getTarget(P),
        S = this._tabParent,
        R = this.get("tabs"),
        M,
        L,
        K;
      if (C.isAncestor(S, Q)) {
        for (var N = 0, O = R.length; N < O; N++) {
          L = R[N].get(G);
          K = R[N].get(A);
          if (Q == L || C.isAncestor(L, Q)) {
            M = R[N];
            break;
          }
        }
        if (M) {
          M.fireEvent(P.type, P);
        }
      }
    },
    getTab: function (K) {
      return this.get("tabs")[K];
    },
    getTabIndex: function (O) {
      var L = null,
        N = this.get("tabs");
      for (var M = 0, K = N.length; M < K; ++M) {
        if (O == N[M]) {
          L = M;
          break;
        }
      }
      return L;
    },
    removeTab: function (M) {
      var L = this.get("tabs").length,
        K = this.getTabIndex(M);
      if (M === this.get(E)) {
        if (L > 1) {
          if (K + 1 === L) {
            this.set(D, K - 1);
          } else {
            this.set(D, K + 1);
          }
        } else {
          this.set(E, null);
        }
      }
      this._tabParent.removeChild(M.get(G));
      this._contentParent.removeChild(M.get(A));
      this._configs.tabs.value.splice(K, 1);
      M.fireEvent("remove", { type: "remove", tabview: this });
    },
    toString: function () {
      var K = this.get("id") || this.get("tagName");
      return "TabView " + K;
    },
    contentTransition: function (L, K) {
      if (L) {
        L.set("contentVisible", true);
      }
      if (K) {
        K.set("contentVisible", false);
      }
    },
    initAttributes: function (K) {
      I.superclass.initAttributes.call(this, K);
      if (!K.orientation) {
        K.orientation = "top";
      }
      var M = this.get(G);
      if (!C.hasClass(M, this.CLASSNAME)) {
        C.addClass(M, this.CLASSNAME);
      }
      this.setAttributeConfig("tabs", { value: [], readOnly: true });
      this._tabParent =
        this.getElementsByClassName(this.TAB_PARENT_CLASSNAME, "ul")[0] ||
        this._createTabParent();
      this._contentParent =
        this.getElementsByClassName(this.CONTENT_PARENT_CLASSNAME, "div")[0] ||
        this._createContentParent();
      this.setAttributeConfig("orientation", {
        value: K.orientation,
        method: function (N) {
          var O = this.get("orientation");
          this.addClass("yui-navset-" + N);
          if (O != N) {
            this.removeClass("yui-navset-" + O);
          }
          if (N === "bottom") {
            this.appendChild(this._tabParent);
          }
        },
      });
      this.setAttributeConfig(D, {
        value: K.activeIndex,
        method: function (N) {},
        validator: function (O) {
          var N = true;
          if (O && this.getTab(O).get("disabled")) {
            N = false;
          }
          return N;
        },
      });
      this.setAttributeConfig(E, {
        value: K.activeTab,
        method: function (O) {
          var N = this.get(E);
          if (O) {
            O.set(J, true);
          }
          if (N && N !== O) {
            N.set(J, false);
          }
          if (N && O !== N) {
            this.contentTransition(O, N);
          } else {
            if (O) {
              O.set("contentVisible", true);
            }
          }
        },
        validator: function (O) {
          var N = true;
          if (O && O.get("disabled")) {
            N = false;
          }
          return N;
        },
      });
      this.on("activeTabChange", this._onActiveTabChange);
      this.on("activeIndexChange", this._onActiveIndexChange);
      if (this._tabParent) {
        this._initTabs();
      }
      this.DOM_EVENTS.submit = false;
      this.DOM_EVENTS.focus = false;
      this.DOM_EVENTS.blur = false;
      for (var L in this.DOM_EVENTS) {
        if (YAHOO.lang.hasOwnProperty(this.DOM_EVENTS, L)) {
          this.addListener.call(this, L, this.DOMEventHandler);
        }
      }
    },
    deselectTab: function (K) {
      if (this.getTab(K) === this.get("activeTab")) {
        this.set("activeTab", null);
      }
    },
    selectTab: function (K) {
      this.set("activeTab", this.getTab(K));
    },
    _onActiveTabChange: function (M) {
      var K = this.get(D),
        L = this.getTabIndex(M.newValue);
      if (K !== L) {
        if (!this.set(D, L)) {
          this.set(E, M.prevValue);
        }
      }
    },
    _onActiveIndexChange: function (K) {
      if (K.newValue !== this.getTabIndex(this.get(E))) {
        if (!this.set(E, this.getTab(K.newValue))) {
          this.set(D, K.prevValue);
        }
      }
    },
    _initTabs: function () {
      var P = C.getChildren(this._tabParent),
        N = C.getChildren(this._contentParent),
        M = this.get(D),
        Q,
        L,
        R;
      for (var O = 0, K = P.length; O < K; ++O) {
        L = {};
        if (N[O]) {
          L.contentEl = N[O];
        }
        Q = new YAHOO.widget.Tab(P[O], L);
        this.addTab(Q);
        if (Q.hasClass(Q.ACTIVE_CLASSNAME)) {
          R = Q;
        }
      }
      if (M) {
        this.set(E, this.getTab(M));
      } else {
        this._configs.activeTab.value = R;
        this._configs.activeIndex.value = this.getTabIndex(R);
      }
    },
    _createTabViewElement: function (K) {
      var L = F.createElement("div");
      if (this.CLASSNAME) {
        L.className = this.CLASSNAME;
      }
      return L;
    },
    _createTabParent: function (K) {
      var L = F.createElement("ul");
      if (this.TAB_PARENT_CLASSNAME) {
        L.className = this.TAB_PARENT_CLASSNAME;
      }
      this.get(G).appendChild(L);
      return L;
    },
    _createContentParent: function (K) {
      var L = F.createElement("div");
      if (this.CONTENT_PARENT_CLASSNAME) {
        L.className = this.CONTENT_PARENT_CLASSNAME;
      }
      this.get(G).appendChild(L);
      return L;
    },
  });
  YAHOO.widget.TabView = I;
})();
(function () {
  var D = YAHOO.util,
    I = D.Dom,
    L = YAHOO.lang,
    M = "activeTab",
    J = "label",
    G = "labelEl",
    Q = "content",
    C = "contentEl",
    O = "element",
    P = "cacheData",
    B = "dataSrc",
    H = "dataLoaded",
    A = "dataTimeout",
    N = "loadMethod",
    F = "postData",
    K = "disabled",
    E = function (S, R) {
      R = R || {};
      if (arguments.length == 1 && !L.isString(S) && !S.nodeName) {
        R = S;
        S = R.element;
      }
      if (!S && !R.element) {
        S = this._createTabElement(R);
      }
      this.loadHandler = {
        success: function (T) {
          this.set(Q, T.responseText);
        },
        failure: function (T) {},
      };
      E.superclass.constructor.call(this, S, R);
      this.DOM_EVENTS = {};
    };
  YAHOO.extend(E, YAHOO.util.Element, {
    LABEL_TAGNAME: "em",
    ACTIVE_CLASSNAME: "selected",
    HIDDEN_CLASSNAME: "yui-hidden",
    ACTIVE_TITLE: "active",
    DISABLED_CLASSNAME: K,
    LOADING_CLASSNAME: "loading",
    dataConnection: null,
    loadHandler: null,
    _loading: false,
    toString: function () {
      var R = this.get(O),
        S = R.id || R.tagName;
      return "Tab " + S;
    },
    initAttributes: function (R) {
      R = R || {};
      E.superclass.initAttributes.call(this, R);
      this.setAttributeConfig("activationEvent", {
        value: R.activationEvent || "click",
      });
      this.setAttributeConfig(G, {
        value: R[G] || this._getLabelEl(),
        method: function (S) {
          S = I.get(S);
          var T = this.get(G);
          if (T) {
            if (T == S) {
              return false;
            }
            T.parentNode.replaceChild(S, T);
            this.set(J, S.innerHTML);
          }
        },
      });
      this.setAttributeConfig(J, {
        value: R.label || this._getLabel(),
        method: function (T) {
          var S = this.get(G);
          if (!S) {
            this.set(G, this._createLabelEl());
          }
          S.innerHTML = T;
        },
      });
      this.setAttributeConfig(C, {
        value: R[C] || document.createElement("div"),
        method: function (S) {
          S = I.get(S);
          var T = this.get(C);
          if (T) {
            if (T === S) {
              return false;
            }
            if (!this.get("selected")) {
              I.addClass(S, "yui-hidden");
            }
            T.parentNode.replaceChild(S, T);
            this.set(Q, S.innerHTML);
          }
        },
      });
      this.setAttributeConfig(Q, {
        value: R[Q],
        method: function (S) {
          this.get(C).innerHTML = S;
        },
      });
      this.setAttributeConfig(B, { value: R.dataSrc });
      this.setAttributeConfig(P, {
        value: R.cacheData || false,
        validator: L.isBoolean,
      });
      this.setAttributeConfig(N, {
        value: R.loadMethod || "GET",
        validator: L.isString,
      });
      this.setAttributeConfig(H, {
        value: false,
        validator: L.isBoolean,
        writeOnce: true,
      });
      this.setAttributeConfig(A, {
        value: R.dataTimeout || null,
        validator: L.isNumber,
      });
      this.setAttributeConfig(F, { value: R.postData || null });
      this.setAttributeConfig("active", {
        value: R.active || this.hasClass(this.ACTIVE_CLASSNAME),
        method: function (S) {
          if (S === true) {
            this.addClass(this.ACTIVE_CLASSNAME);
            this.set("title", this.ACTIVE_TITLE);
          } else {
            this.removeClass(this.ACTIVE_CLASSNAME);
            this.set("title", "");
          }
        },
        validator: function (S) {
          return L.isBoolean(S) && !this.get(K);
        },
      });
      this.setAttributeConfig(K, {
        value: R.disabled || this.hasClass(this.DISABLED_CLASSNAME),
        method: function (S) {
          if (S === true) {
            I.addClass(this.get(O), this.DISABLED_CLASSNAME);
          } else {
            I.removeClass(this.get(O), this.DISABLED_CLASSNAME);
          }
        },
        validator: L.isBoolean,
      });
      this.setAttributeConfig("href", {
        value:
          R.href ||
          this.getElementsByTagName("a")[0].getAttribute("href", 2) ||
          "#",
        method: function (S) {
          this.getElementsByTagName("a")[0].href = S;
        },
        validator: L.isString,
      });
      this.setAttributeConfig("contentVisible", {
        value: R.contentVisible,
        method: function (S) {
          if (S) {
            I.removeClass(this.get(C), this.HIDDEN_CLASSNAME);
            if (this.get(B)) {
              if (!this._loading && !(this.get(H) && this.get(P))) {
                this._dataConnect();
              }
            }
          } else {
            I.addClass(this.get(C), this.HIDDEN_CLASSNAME);
          }
        },
        validator: L.isBoolean,
      });
    },
    _dataConnect: function () {
      if (!D.Connect) {
        return false;
      }
      I.addClass(this.get(C).parentNode, this.LOADING_CLASSNAME);
      this._loading = true;
      this.dataConnection = D.Connect.asyncRequest(
        this.get(N),
        this.get(B),
        {
          success: function (R) {
            this.loadHandler.success.call(this, R);
            this.set(H, true);
            this.dataConnection = null;
            I.removeClass(this.get(C).parentNode, this.LOADING_CLASSNAME);
            this._loading = false;
          },
          failure: function (R) {
            this.loadHandler.failure.call(this, R);
            this.dataConnection = null;
            I.removeClass(this.get(C).parentNode, this.LOADING_CLASSNAME);
            this._loading = false;
          },
          scope: this,
          timeout: this.get(A),
        },
        this.get(F),
      );
    },
    _createTabElement: function (R) {
      var V = document.createElement("li"),
        S = document.createElement("a"),
        U = R.label || null,
        T = R.labelEl || null;
      S.href = R.href || "#";
      V.appendChild(S);
      if (T) {
        if (!U) {
          U = this._getLabel();
        }
      } else {
        T = this._createLabelEl();
      }
      S.appendChild(T);
      return V;
    },
    _getLabelEl: function () {
      return this.getElementsByTagName(this.LABEL_TAGNAME)[0];
    },
    _createLabelEl: function () {
      var R = document.createElement(this.LABEL_TAGNAME);
      return R;
    },
    _getLabel: function () {
      var R = this.get(G);
      if (!R) {
        return undefined;
      }
      return R.innerHTML;
    },
    _onActivate: function (U, T) {
      var S = this,
        R = false;
      D.Event.preventDefault(U);
      if (S === T.get(M)) {
        R = true;
      }
      T.set(M, S, R);
    },
  });
  YAHOO.widget.Tab = E;
})();
YAHOO.register("tabview", YAHOO.widget.TabView, {
  version: "2.7.0",
  build: "1799",
});
