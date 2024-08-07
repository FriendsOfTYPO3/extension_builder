(function () {
  var G = YAHOO.util.Dom,
    F = YAHOO.util.Event,
    I = YAHOO.util.Anim;
  var A = function (L, K) {
    L = G.get(L);
    K = K || {};
    if (!L) {
      L = document.createElement(this.CONFIG.TAG_NAME);
    }
    if (L.id) {
      K.id = L.id;
    }
    YAHOO.widget.AccordionView.superclass.constructor.call(this, L, K);
    this.initList(L, K);
    this.refresh(["id", "width", "hoverActivated"], true);
  };
  var D = "panelClose";
  var E = "panelOpen";
  var B = "afterPanelClose";
  var J = "afterPanelOpen";
  var C = "stateChanged";
  var H = "beforeStateChange";
  YAHOO.widget.AccordionView = A;
  YAHOO.extend(A, YAHOO.util.Element, {
    initAttributes: function (K) {
      A.superclass.initAttributes.call(this, K);
      var L = YAHOO.env.modules.animation ? true : false;
      this.setAttributeConfig("id", {
        writeOnce: true,
        validator: function (M) {
          return /^[a-zA-Z][\w0-9\-_.:]*$/.test(M);
        },
        value: G.generateId(),
        method: function (M) {
          this.get("element").id = M;
        },
      });
      this.setAttributeConfig("width", {
        value: "400px",
        method: function (M) {
          this.setStyle("width", M);
        },
      });
      this.setAttributeConfig("animationSpeed", { value: 0.7 });
      this.setAttributeConfig("animate", {
        value: L,
        validator: YAHOO.lang.isBoolean,
      });
      this.setAttributeConfig("collapsible", {
        value: false,
        validator: YAHOO.lang.isBoolean,
      });
      this.setAttributeConfig("expandable", {
        value: false,
        validator: YAHOO.lang.isBoolean,
      });
      this.setAttributeConfig("effect", {
        value: YAHOO.util.Easing.easeBoth,
        validator: YAHOO.lang.isString,
      });
      this.setAttributeConfig("hoverActivated", {
        value: false,
        validator: YAHOO.lang.isBoolean,
        method: function (M) {
          if (M) {
            F.on(this, "mouseover", this._onMouseOver, this, true);
          } else {
            F.removeListener(this, "mouseover", this._onMouseOver);
          }
        },
      });
      this.setAttributeConfig("_hoverTimeout", {
        value: 500,
        validator: YAHOO.lang.isInteger,
      });
    },
    CONFIG: {
      TAG_NAME: "UL",
      ITEM_WRAPPER_TAG_NAME: "LI",
      CONTENT_WRAPPER_TAG_NAME: "DIV",
    },
    CLASSES: {
      ACCORDION: "yui-accordionview",
      PANEL: "yui-accordion-panel",
      TOGGLE: "yui-accordion-toggle",
      CONTENT: "yui-accordion-content",
      ACTIVE: "active",
      HIDDEN: "hidden",
      INDICATOR: "indicator",
    },
    _idCounter: "1",
    _hoverTimer: null,
    _panels: null,
    _opening: false,
    _closing: false,
    _ff2: YAHOO.env.ua.gecko > 0 && YAHOO.env.ua.gecko < 1.9,
    _ie: YAHOO.env.ua.ie < 8 && YAHOO.env.ua.ie > 0,
    _ARIACapable: YAHOO.env.ua.ie > 7 || YAHOO.env.ua.gecko >= 1.9,
    initList: function (O, K) {
      G.addClass(O, this.CLASSES.ACCORDION);
      this._setARIA(O, "role", "tree");
      var N = [];
      var Q = O.getElementsByTagName(this.CONFIG.ITEM_WRAPPER_TAG_NAME);
      for (var M = 0; M < Q.length; M++) {
        if (G.hasClass(Q[M], "nopanel")) {
          N.push({
            label: "SINGLE_LINK",
            content: Q[M].innerHTML.replace(/^\s\s*/, "").replace(/\s\s*$/, ""),
          });
        } else {
          if (Q[M].parentNode === O) {
            for (
              var P = Q[M].firstChild;
              P && P.nodeType != 1;
              P = P.nextSibling
            ) {}
            if (P) {
              for (
                var R = P.nextSibling;
                R && R.nodeType != 1;
                R = R.nextSibling
              ) {}
              N.push({ label: P.innerHTML, content: R && R.innerHTML });
            }
          }
        }
      }
      O.innerHTML = "";
      if (N.length > 0) {
        this.addPanels(N);
      }
      if (K.expandItem === 0 || K.expandItem > 0) {
        var L = this._panels[K.expandItem].firstChild;
        var R = this._panels[K.expandItem].firstChild.nextSibling;
        G.removeClass(R, this.CLASSES.HIDDEN);
        if (L && R) {
          G.addClass(L, this.CLASSES.ACTIVE);
          L.tabIndex = 0;
          this._setARIA(L, "aria-expanded", "true");
          this._setARIA(R, "aria-hidden", "false");
        }
      }
      this.initEvents();
    },
    initEvents: function () {
      if (true === this.get("hoverActivated")) {
        this.on("mouseover", this._onMouseOver, this, true);
        this.on("mouseout", this._onMouseOut, this, true);
      }
      this.on("click", this._onClick, this, true);
      this.on("keydown", this._onKeydown, this, true);
      this.on(
        "panelOpen",
        function () {
          this._opening = true;
        },
        this,
        true,
      );
      this.on(
        "panelClose",
        function () {
          this._closing = true;
        },
        this,
        true,
      );
      this.on(
        "afterPanelClose",
        function () {
          this._closing = false;
          if (!this._closing && !this._opening) {
            this._fixTabIndexes();
          }
        },
        this,
        true,
      );
      this.on(
        "afterPanelOpen",
        function () {
          this._opening = false;
          if (!this._closing && !this._opening) {
            this._fixTabIndexes();
          }
        },
        this,
        true,
      );
      if (this._ARIACapable) {
        this.on("keypress", function (K) {
          var L = G.getAncestorByClassName(F.getTarget(K), this.CLASSES.PANEL);
          var M = F.getCharCode(K);
          if (M === 13) {
            this._onClick(L.firstChild);
            return false;
          }
        });
      }
    },
    _setARIA: function (L, K, M) {
      if (this._ARIACapable) {
        L.setAttribute(K, M);
      }
    },
    _collapseAccordion: function () {
      G.batch(
        this._panels,
        function (L) {
          var K = this.firstChild.nextSibling;
          if (K) {
            G.removeClass(L.firstChild, this.CLASSES.ACTIVE);
            G.addClass(K, this.CLASSES.HIDDEN);
            this._setARIA(K, "aria-hidden", "true");
          }
        },
        this,
      );
    },
    _fixTabIndexes: function () {
      var M = this._panels.length;
      var K = true;
      for (var L = 0; L < M; L++) {
        if (G.hasClass(this._panels[L].firstChild, this.CLASSES.ACTIVE)) {
          this._panels[L].firstChild.tabIndex = 0;
          K = false;
        } else {
          this._panels[L].firstChild.tabIndex = -1;
        }
      }
      if (K) {
        this._panels[0].firstChild.tabIndex = 0;
      }
      this.fireEvent(C);
    },
    addPanel: function (N, M) {
      var L = document.createElement(this.CONFIG.ITEM_WRAPPER_TAG_NAME);
      G.addClass(L, this.CLASSES.PANEL);
      if (N.label === "SINGLE_LINK") {
        L.innerHTML = N.content;
        G.addClass(L.firstChild, this.CLASSES.TOGGLE);
        G.addClass(L.firstChild, "link");
      } else {
        var K = document.createElement("span");
        G.addClass(K, this.CLASSES.INDICATOR);
        var P = L.appendChild(document.createElement("A"));
        P.id = this.get("element").id + "-" + this._idCounter + "-label";
        P.innerHTML = N.label || "";
        P.appendChild(K);
        if (this._ARIACapable) {
          if (N.href) {
            P.href = N.href;
          }
        } else {
          P.href = N.href || "#toggle";
        }
        P.tabIndex = -1;
        G.addClass(P, this.CLASSES.TOGGLE);
        var Q = document.createElement(this.CONFIG.CONTENT_WRAPPER_TAG_NAME);
        Q.innerHTML = N.content || "";
        G.addClass(Q, this.CLASSES.CONTENT);
        L.appendChild(Q);
        this._setARIA(L, "role", "presentation");
        this._setARIA(P, "role", "treeitem");
        this._setARIA(Q, "aria-labelledby", P.id);
        this._setARIA(K, "role", "presentation");
      }
      this._idCounter++;
      if (this._panels === null) {
        this._panels = [];
      }
      if (M !== null && M !== undefined) {
        var O = this.getPanel(M);
        this.insertBefore(L, O);
        var R = this._panels.slice(0, M);
        var T = this._panels.slice(M);
        R.push(L);
        for (i = 0; i < T.length; i++) {
          R.push(T[i]);
        }
        this._panels = R;
      } else {
        this.appendChild(L);
        if (this.get("element") === L.parentNode) {
          this._panels[this._panels.length] = L;
        }
      }
      if (N.label !== "SINGLE_LINK") {
        if (N.expand) {
          if (!this.get("expandable")) {
            this._collapseAccordion();
          }
          G.removeClass(Q, this.CLASSES.HIDDEN);
          G.addClass(P, this.CLASSES.ACTIVE);
          this._setARIA(Q, "aria-hidden", "false");
          this._setARIA(P, "aria-expanded", "true");
        } else {
          G.addClass(Q, "hidden");
          this._setARIA(Q, "aria-hidden", "true");
          this._setARIA(P, "aria-expanded", "false");
        }
      }
      var S = YAHOO.lang.later(0, this, function () {
        this._fixTabIndexes();
        this.fireEvent(C);
      });
    },
    addPanels: function (L) {
      for (var K = 0; K < L.length; K++) {
        this.addPanel(L[K]);
      }
    },
    removePanel: function (K) {
      this.removeChild(
        G.getElementsByClassName(
          this.CLASSES.PANEL,
          this.CONFIG.ITEM_WRAPPER_TAG_NAME,
          this,
        )[K],
      );
      var N = [];
      var O = this._panels.length;
      for (var M = 0; M < O; M++) {
        if (M !== K) {
          N.push(this._panels[M]);
        }
      }
      this._panels = N;
      var L = YAHOO.lang.later(0, this, function () {
        this._fixTabIndexes();
        this.fireEvent(C);
      });
    },
    getPanel: function (K) {
      return this._panels[K];
    },
    getPanels: function () {
      return this._panels;
    },
    openPanel: function (K) {
      var L = this._panels[K];
      if (!L) {
        return false;
      }
      if (G.hasClass(L.firstChild, this.CLASSES.ACTIVE)) {
        return false;
      }
      this._onClick(L.firstChild);
      return true;
    },
    closePanel: function (K) {
      var L = this._panels;
      var O = L[K];
      if (!O) {
        return false;
      }
      var N = O.firstChild;
      if (!G.hasClass(N, this.CLASSES.ACTIVE)) {
        return true;
      }
      if (this.get("collapsible") === false) {
        if (this.get("expandable") === true) {
          this.set("collapsible", true);
          for (var M = 0; M < L.length; M++) {
            if (G.hasClass(L[M].firstChild, this.CLASSES.ACTIVE) && M !== K) {
              this._onClick(N);
              this.set("collapsible", false);
              return true;
            }
          }
          this.set("collapsible", false);
        }
      }
      this._onClick(N);
      return true;
    },
    _onKeydown: function (L) {
      var N = G.getAncestorByClassName(F.getTarget(L), this.CLASSES.PANEL);
      var O = F.getCharCode(L);
      var M = this._panels.length;
      if (O === 37 || O === 38) {
        for (var K = 0; K < M; K++) {
          if (N === this._panels[K] && K > 0) {
            this._panels[K - 1].firstChild.focus();
            return;
          }
        }
      }
      if (O === 39 || O === 40) {
        for (var K = 0; K < M; K++) {
          if (N === this._panels[K] && K < M - 1) {
            this._panels[K + 1].firstChild.focus();
            return;
          }
        }
      }
    },
    _onMouseOver: function (K) {
      F.stopPropagation(K);
      var L = F.getTarget(K);
      this._hoverTimer = YAHOO.lang.later(
        this.get("_hoverTimeout"),
        this,
        function () {
          this._onClick(L);
        },
      );
    },
    _onMouseOut: function () {
      if (this._hoverTimer) {
        this._hoverTimer.cancel();
        this._hoverTimer = null;
      }
    },
    _onClick: function (T) {
      var Q;
      if (T.nodeType === undefined) {
        Q = F.getTarget(T);
        if (
          !G.hasClass(Q, this.CLASSES.TOGGLE) &&
          !G.hasClass(Q, this.CLASSES.INDICATOR)
        ) {
          return false;
        }
        if (G.hasClass(Q, "link")) {
          return true;
        }
        F.preventDefault(T);
        F.stopPropagation(T);
      } else {
        Q = T;
      }
      var R = Q;
      var O = this;

      function S(V, X) {
        if (O._ie) {
          var W = G.getElementsByClassName(
            O.CLASSES.ACCORDION,
            O.CONFIG.TAG_NAME,
            V,
          );
          if (W[0]) {
            G.setStyle(W[0], "visibility", X);
          }
        }
      }

      function P(W, Y) {
        var Z = this;

        function e(h, f) {
          if (!G.hasClass(f, Z.CLASSES.PANEL)) {
            f = G.getAncestorByClassName(f, Z.CLASSES.PANEL);
          }
          for (var g = 0, j = f; j.previousSibling; g++) {
            j = j.previousSibling;
          }
          return Z.fireEvent(h, { panel: f, index: g });
        }

        if (!Y) {
          if (!W) {
            return false;
          }
          Y = W.parentNode.firstChild;
        }
        var b = {};
        var c = 0;
        var a = !G.hasClass(W, this.CLASSES.HIDDEN);
        if (this.get("animate")) {
          if (!a) {
            if (this._ff2) {
              G.addClass(W, "almosthidden");
              G.setStyle(W, "width", this.get("width"));
            }
            G.removeClass(W, this.CLASSES.HIDDEN);
            c = W.offsetHeight;
            G.setStyle(W, "height", 0);
            if (this._ff2) {
              G.removeClass(W, "almosthidden");
              G.setStyle(W, "width", "auto");
            }
            b = { height: { from: 0, to: c } };
          } else {
            c = W.offsetHeight;
            b = { height: { from: c, to: 0 } };
          }
          var d = this.get("animationSpeed") ? this.get("animationSpeed") : 0.5;
          var X = this.get("effect")
            ? this.get("effect")
            : YAHOO.util.Easing.easeBoth;
          var V = new I(W, b, d, X);
          if (a) {
            if (this.fireEvent(D, W) === false) {
              return;
            }
            G.removeClass(Y, Z.CLASSES.ACTIVE);
            Y.tabIndex = -1;
            S(W, "hidden");
            Z._setARIA(W, "aria-hidden", "true");
            Z._setARIA(Y, "aria-expanded", "false");
            V.onComplete.subscribe(function () {
              G.addClass(W, Z.CLASSES.HIDDEN);
              G.setStyle(W, "height", "auto");
              e("afterPanelClose", W);
            });
          } else {
            if (e(E, W) === false) {
              return;
            }
            S(W, "hidden");
            V.onComplete.subscribe(function () {
              G.setStyle(W, "height", "auto");
              S(W, "visible");
              Z._setARIA(W, "aria-hidden", "false");
              Z._setARIA(Y, "aria-expanded", "true");
              Y.tabIndex = 0;
              e(J, W);
            });
            G.addClass(Y, this.CLASSES.ACTIVE);
          }
          V.animate();
        } else {
          if (a) {
            if (e(D, W) === false) {
              return;
            }
            G.addClass(W, Z.CLASSES.HIDDEN);
            G.setStyle(W, "height", "auto");
            G.removeClass(Y, Z.CLASSES.ACTIVE);
            Z._setARIA(W, "aria-hidden", "true");
            Z._setARIA(Y, "aria-expanded", "false");
            Y.tabIndex = -1;
            e(B, W);
          } else {
            if (e(E, W) === false) {
              return;
            }
            G.removeClass(W, Z.CLASSES.HIDDEN);
            G.setStyle(W, "height", "auto");
            G.addClass(Y, Z.CLASSES.ACTIVE);
            Z._setARIA(W, "aria-hidden", "false");
            Z._setARIA(Y, "aria-expanded", "true");
            Y.tabIndex = 0;
            e(J, W);
          }
        }
        return true;
      }

      var K =
        R.nodeName.toUpperCase() === "SPAN"
          ? R.parentNode.parentNode
          : R.parentNode;
      var N = G.getElementsByClassName(
        this.CLASSES.CONTENT,
        this.CONFIG.CONTENT_WRAPPER_TAG_NAME,
        K,
      )[0];
      if (this.fireEvent(H, this) === false) {
        return;
      }
      if (this.get("collapsible") === false) {
        if (!G.hasClass(N, this.CLASSES.HIDDEN)) {
          return false;
        }
      } else {
        if (!G.hasClass(N, this.CLASSES.HIDDEN)) {
          P.call(this, N);
          return false;
        }
      }
      if (this.get("expandable") !== true) {
        var U = this._panels.length;
        for (var M = 0; M < U; M++) {
          var L = G.hasClass(
            this._panels[M].firstChild.nextSibling,
            this.CLASSES.HIDDEN,
          );
          if (!L) {
            P.call(this, this._panels[M].firstChild.nextSibling);
          }
        }
      }
      if (R.nodeName.toUpperCase() === "SPAN") {
        P.call(this, N, R.parentNode);
      } else {
        P.call(this, N, R);
      }
      return true;
    },
    toString: function () {
      var K = this.get("id") || this.get("tagName");
      return "AccordionView " + K;
    },
  });
})();
YAHOO.register("accordionview", YAHOO.widget.AccordionView, {
  version: "0.99",
  build: "33",
});
