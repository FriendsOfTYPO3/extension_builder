/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  var D = YAHOO.util.Dom,
    B = YAHOO.util.Event,
    F = YAHOO.lang,
    E = YAHOO.widget;
  YAHOO.widget.TreeView = function (H, G) {
    if (H) {
      this.init(H);
    }
    if (G) {
      if (!F.isArray(G)) {
        G = [G];
      }
      this.buildTreeFromObject(G);
    } else {
      if (F.trim(this._el.innerHTML)) {
        this.buildTreeFromMarkup(H);
      }
    }
  };
  var C = E.TreeView;
  C.prototype = {
    id: null,
    _el: null,
    _nodes: null,
    locked: false,
    _expandAnim: null,
    _collapseAnim: null,
    _animCount: 0,
    maxAnim: 2,
    _hasDblClickSubscriber: false,
    _dblClickTimer: null,
    currentFocus: null,
    singleNodeHighlight: false,
    _currentlyHighlighted: null,
    setExpandAnim: function (G) {
      this._expandAnim = E.TVAnim.isValid(G) ? G : null;
    },
    setCollapseAnim: function (G) {
      this._collapseAnim = E.TVAnim.isValid(G) ? G : null;
    },
    animateExpand: function (I, J) {
      if (this._expandAnim && this._animCount < this.maxAnim) {
        var G = this;
        var H = E.TVAnim.getAnim(this._expandAnim, I, function () {
          G.expandComplete(J);
        });
        if (H) {
          ++this._animCount;
          this.fireEvent("animStart", { node: J, type: "expand" });
          H.animate();
        }
        return true;
      }
      return false;
    },
    animateCollapse: function (I, J) {
      if (this._collapseAnim && this._animCount < this.maxAnim) {
        var G = this;
        var H = E.TVAnim.getAnim(this._collapseAnim, I, function () {
          G.collapseComplete(J);
        });
        if (H) {
          ++this._animCount;
          this.fireEvent("animStart", { node: J, type: "collapse" });
          H.animate();
        }
        return true;
      }
      return false;
    },
    expandComplete: function (G) {
      --this._animCount;
      this.fireEvent("animComplete", { node: G, type: "expand" });
    },
    collapseComplete: function (G) {
      --this._animCount;
      this.fireEvent("animComplete", { node: G, type: "collapse" });
    },
    init: function (I) {
      this._el = D.get(I);
      this.id = D.generateId(this._el, "yui-tv-auto-id-");
      this.createEvent("animStart", this);
      this.createEvent("animComplete", this);
      this.createEvent("collapse", this);
      this.createEvent("collapseComplete", this);
      this.createEvent("expand", this);
      this.createEvent("expandComplete", this);
      this.createEvent("enterKeyPressed", this);
      this.createEvent("clickEvent", this);
      this.createEvent("focusChanged", this);
      var G = this;
      this.createEvent("dblClickEvent", {
        scope: this,
        onSubscribeCallback: function () {
          G._hasDblClickSubscriber = true;
        },
      });
      this.createEvent("labelClick", this);
      this.createEvent("highlightEvent", this);
      this._nodes = [];
      C.trees[this.id] = this;
      this.root = new E.RootNode(this);
      var H = E.LogWriter;
    },
    buildTreeFromObject: function (G) {
      var H = function (P, M) {
        var L, Q, K, J, O, I, N;
        for (L = 0; L < M.length; L++) {
          Q = M[L];
          if (F.isString(Q)) {
            K = new E.TextNode(Q, P);
          } else {
            if (F.isObject(Q)) {
              J = Q.children;
              delete Q.children;
              O = Q.type || "text";
              delete Q.type;
              switch (F.isString(O) && O.toLowerCase()) {
                case "text":
                  K = new E.TextNode(Q, P);
                  break;
                case "menu":
                  K = new E.MenuNode(Q, P);
                  break;
                case "html":
                  K = new E.HTMLNode(Q, P);
                  break;
                default:
                  if (F.isString(O)) {
                    I = E[O];
                  } else {
                    I = O;
                  }
                  if (F.isObject(I)) {
                    for (
                      N = I;
                      N && N !== E.Node;
                      N = N.superclass.constructor
                    ) {}
                    if (N) {
                      K = new I(Q, P);
                    } else {
                    }
                  } else {
                  }
              }
              if (J) {
                H(K, J);
              }
            } else {
            }
          }
        }
      };
      H(this.root, G);
    },
    buildTreeFromMarkup: function (I) {
      var H = function (J) {
        var N,
          Q,
          M = [],
          L = {},
          K,
          O;
        for (N = D.getFirstChild(J); N; N = D.getNextSibling(N)) {
          switch (N.tagName.toUpperCase()) {
            case "LI":
              K = "";
              L = {
                expanded: D.hasClass(N, "expanded"),
                title: N.title || N.alt || null,
                className:
                  F.trim(N.className.replace(/\bexpanded\b/, "")) || null,
              };
              Q = N.firstChild;
              if (Q.nodeType == 3) {
                K = F.trim(Q.nodeValue.replace(/[\n\t\r]*/g, ""));
                if (K) {
                  L.type = "text";
                  L.label = K;
                } else {
                  Q = D.getNextSibling(Q);
                }
              }
              if (!K) {
                if (Q.tagName.toUpperCase() == "A") {
                  L.type = "text";
                  L.label = Q.innerHTML;
                  L.href = Q.href;
                  L.target = Q.target;
                  L.title = Q.title || Q.alt || L.title;
                } else {
                  L.type = "html";
                  var P = document.createElement("div");
                  P.appendChild(Q.cloneNode(true));
                  L.html = P.innerHTML;
                  L.hasIcon = true;
                }
              }
              Q = D.getNextSibling(Q);
              switch (Q && Q.tagName.toUpperCase()) {
                case "UL":
                case "OL":
                  L.children = H(Q);
                  break;
              }
              if (YAHOO.lang.JSON) {
                O = N.getAttribute("yuiConfig");
                if (O) {
                  O = YAHOO.lang.JSON.parse(O);
                  L = YAHOO.lang.merge(L, O);
                }
              }
              M.push(L);
              break;
            case "UL":
            case "OL":
              L = { type: "text", label: "", children: H(Q) };
              M.push(L);
              break;
          }
        }
        return M;
      };
      var G = D.getChildrenBy(D.get(I), function (K) {
        var J = K.tagName.toUpperCase();
        return J == "UL" || J == "OL";
      });
      if (G.length) {
        this.buildTreeFromObject(H(G[0]));
      } else {
      }
    },
    _getEventTargetTdEl: function (H) {
      var I = B.getTarget(H);
      while (
        I &&
        !(
          I.tagName.toUpperCase() == "TD" && D.hasClass(I.parentNode, "ygtvrow")
        )
      ) {
        I = D.getAncestorByTagName(I, "td");
      }
      if (F.isNull(I)) {
        return null;
      }
      if (/\bygtv(blank)?depthcell/.test(I.className)) {
        return null;
      }
      if (I.id) {
        var G = I.id.match(/\bygtv([^\d]*)(.*)/);
        if (G && G[2] && this._nodes[G[2]]) {
          return I;
        }
      }
      return null;
    },
    _onClickEvent: function (J) {
      var H = this,
        L = this._getEventTargetTdEl(J),
        I,
        K,
        G = function () {
          I.toggle();
          I.focus();
          try {
            B.preventDefault(J);
          } catch (M) {}
        };
      if (!L) {
        return;
      }
      I = this.getNodeByElement(L);
      if (!I) {
        return;
      }
      K = B.getTarget(J);
      if (
        D.hasClass(K, I.labelStyle) ||
        D.getAncestorByClassName(K, I.labelStyle)
      ) {
        this.fireEvent("labelClick", I);
      }
      if (/\bygtv[tl][mp]h?h?/.test(L.className)) {
        G();
      } else {
        if (this._dblClickTimer) {
          window.clearTimeout(this._dblClickTimer);
          this._dblClickTimer = null;
        } else {
          if (this._hasDblClickSubscriber) {
            this._dblClickTimer = window.setTimeout(function () {
              H._dblClickTimer = null;
              if (H.fireEvent("clickEvent", { event: J, node: I }) !== false) {
                G();
              }
            }, 200);
          } else {
            if (H.fireEvent("clickEvent", { event: J, node: I }) !== false) {
              G();
            }
          }
        }
      }
    },
    _onDblClickEvent: function (G) {
      if (!this._hasDblClickSubscriber) {
        return;
      }
      var H = this._getEventTargetTdEl(G);
      if (!H) {
        return;
      }
      if (!/\bygtv[tl][mp]h?h?/.test(H.className)) {
        this.fireEvent("dblClickEvent", {
          event: G,
          node: this.getNodeByElement(H),
        });
        if (this._dblClickTimer) {
          window.clearTimeout(this._dblClickTimer);
          this._dblClickTimer = null;
        }
      }
    },
    _onMouseOverEvent: function (G) {
      var H;
      if (
        (H = this._getEventTargetTdEl(G)) &&
        (H = this.getNodeByElement(H)) &&
        (H = H.getToggleEl())
      ) {
        H.className = H.className.replace(
          /\bygtv([lt])([mp])\b/gi,
          "ygtv$1$2h",
        );
      }
    },
    _onMouseOutEvent: function (G) {
      var H;
      if (
        (H = this._getEventTargetTdEl(G)) &&
        (H = this.getNodeByElement(H)) &&
        (H = H.getToggleEl())
      ) {
        H.className = H.className.replace(
          /\bygtv([lt])([mp])h\b/gi,
          "ygtv$1$2",
        );
      }
    },
    _onKeyDownEvent: function (J) {
      var K = B.getTarget(J),
        I = this.getNodeByElement(K),
        H = I,
        G = YAHOO.util.KeyListener.KEY;
      switch (J.keyCode) {
        case G.UP:
          do {
            if (H.previousSibling) {
              H = H.previousSibling;
            } else {
              H = H.parent;
            }
          } while (H && !H._canHaveFocus());
          if (H) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case G.DOWN:
          do {
            if (H.nextSibling) {
              H = H.nextSibling;
            } else {
              H.expand();
              H = (H.children.length || null) && H.children[0];
            }
          } while (H && !H._canHaveFocus);
          if (H) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case G.LEFT:
          do {
            if (H.parent) {
              H = H.parent;
            } else {
              H = H.previousSibling;
            }
          } while (H && !H._canHaveFocus());
          if (H) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case G.RIGHT:
          do {
            H.expand();
            if (H.children.length) {
              H = H.children[0];
            } else {
              H = H.nextSibling;
            }
          } while (H && !H._canHaveFocus());
          if (H) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case G.ENTER:
          if (I.href) {
            if (I.target) {
              window.open(I.href, I.target);
            } else {
              window.location(I.href);
            }
          } else {
            I.toggle();
          }
          this.fireEvent("enterKeyPressed", I);
          B.preventDefault(J);
          break;
        case G.HOME:
          H = this.getRoot();
          if (H.children.length) {
            H = H.children[0];
          }
          if (H._canHaveFocus()) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case G.END:
          H = H.parent.children;
          H = H[H.length - 1];
          if (H._canHaveFocus()) {
            H.focus();
          }
          B.preventDefault(J);
          break;
        case 107:
          if (J.shiftKey) {
            I.parent.expandAll();
          } else {
            I.expand();
          }
          break;
        case 109:
          if (J.shiftKey) {
            I.parent.collapseAll();
          } else {
            I.collapse();
          }
          break;
        default:
          break;
      }
    },
    render: function () {
      var G = this.root.getHtml(),
        H = this.getEl();
      H.innerHTML = G;
      if (!this._hasEvents) {
        B.on(H, "click", this._onClickEvent, this, true);
        B.on(H, "dblclick", this._onDblClickEvent, this, true);
        B.on(H, "mouseover", this._onMouseOverEvent, this, true);
        B.on(H, "mouseout", this._onMouseOutEvent, this, true);
        B.on(H, "keydown", this._onKeyDownEvent, this, true);
      }
      this._hasEvents = true;
    },
    getEl: function () {
      if (!this._el) {
        this._el = D.get(this.id);
      }
      return this._el;
    },
    regNode: function (G) {
      this._nodes[G.index] = G;
    },
    getRoot: function () {
      return this.root;
    },
    setDynamicLoad: function (G, H) {
      this.root.setDynamicLoad(G, H);
    },
    expandAll: function () {
      if (!this.locked) {
        this.root.expandAll();
      }
    },
    collapseAll: function () {
      if (!this.locked) {
        this.root.collapseAll();
      }
    },
    getNodeByIndex: function (H) {
      var G = this._nodes[H];
      return G ? G : null;
    },
    getNodeByProperty: function (I, H) {
      for (var G in this._nodes) {
        if (this._nodes.hasOwnProperty(G)) {
          var J = this._nodes[G];
          if ((I in J && J[I] == H) || (J.data && H == J.data[I])) {
            return J;
          }
        }
      }
      return null;
    },
    getNodesByProperty: function (J, I) {
      var G = [];
      for (var H in this._nodes) {
        if (this._nodes.hasOwnProperty(H)) {
          var K = this._nodes[H];
          if ((J in K && K[J] == I) || (K.data && I == K.data[J])) {
            G.push(K);
          }
        }
      }
      return G.length ? G : null;
    },
    getNodeByElement: function (I) {
      var J = I,
        G,
        H = /ygtv([^\d]*)(.*)/;
      do {
        if (J && J.id) {
          G = J.id.match(H);
          if (G && G[2]) {
            return this.getNodeByIndex(G[2]);
          }
        }
        J = J.parentNode;
        if (!J || !J.tagName) {
          break;
        }
      } while (J.id !== this.id && J.tagName.toLowerCase() !== "body");
      return null;
    },
    removeNode: function (H, G) {
      if (H.isRoot()) {
        return false;
      }
      var I = H.parent;
      if (I.parent) {
        I = I.parent;
      }
      this._deleteNode(H);
      if (G && I && I.childrenRendered) {
        I.refresh();
      }
      return true;
    },
    _removeChildren_animComplete: function (G) {
      this.unsubscribe(this._removeChildren_animComplete);
      this.removeChildren(G.node);
    },
    removeChildren: function (G) {
      if (G.expanded) {
        if (this._collapseAnim) {
          this.subscribe(
            "animComplete",
            this._removeChildren_animComplete,
            this,
            true,
          );
          E.Node.prototype.collapse.call(G);
          return;
        }
        G.collapse();
      }
      while (G.children.length) {
        this._deleteNode(G.children[0]);
      }
      if (G.isRoot()) {
        E.Node.prototype.expand.call(G);
      }
      G.childrenRendered = false;
      G.dynamicLoadComplete = false;
      G.updateIcon();
    },
    _deleteNode: function (G) {
      this.removeChildren(G);
      this.popNode(G);
    },
    popNode: function (J) {
      var K = J.parent;
      var H = [];
      for (var I = 0, G = K.children.length; I < G; ++I) {
        if (K.children[I] != J) {
          H[H.length] = K.children[I];
        }
      }
      K.children = H;
      K.childrenRendered = false;
      if (J.previousSibling) {
        J.previousSibling.nextSibling = J.nextSibling;
      }
      if (J.nextSibling) {
        J.nextSibling.previousSibling = J.previousSibling;
      }
      J.parent = null;
      J.previousSibling = null;
      J.nextSibling = null;
      J.tree = null;
      delete this._nodes[J.index];
    },
    destroy: function () {
      if (this._destroyEditor) {
        this._destroyEditor();
      }
      var H = this.getEl();
      B.removeListener(H, "click");
      B.removeListener(H, "dblclick");
      B.removeListener(H, "mouseover");
      B.removeListener(H, "mouseout");
      B.removeListener(H, "keydown");
      for (var G = 0; G < this._nodes.length; G++) {
        var I = this._nodes[G];
        if (I && I.destroy) {
          I.destroy();
        }
      }
      H.innerHTML = "";
      this._hasEvents = false;
    },
    toString: function () {
      return "TreeView " + this.id;
    },
    getNodeCount: function () {
      return this.getRoot().getNodeCount();
    },
    getTreeDefinition: function () {
      return this.getRoot().getNodeDefinition();
    },
    onExpand: function (G) {},
    onCollapse: function (G) {},
    setNodesProperty: function (G, I, H) {
      this.root.setNodesProperty(G, I);
      if (H) {
        this.root.refresh();
      }
    },
    onEventToggleHighlight: function (H) {
      var G;
      if ("node" in H && H.node instanceof E.Node) {
        G = H.node;
      } else {
        if (H instanceof E.Node) {
          G = H;
        } else {
          return false;
        }
      }
      G.toggleHighlight();
      return false;
    },
  };
  var A = C.prototype;
  A.draw = A.render;
  YAHOO.augment(C, YAHOO.util.EventProvider);
  C.nodeCount = 0;
  C.trees = [];
  C.getTree = function (H) {
    var G = C.trees[H];
    return G ? G : null;
  };
  C.getNode = function (H, I) {
    var G = C.getTree(H);
    return G ? G.getNodeByIndex(I) : null;
  };
  C.FOCUS_CLASS_NAME = "ygtvfocus";
  C.preload = function (L, K) {
    K = K || "ygtv";
    var I = [
      "tn",
      "tm",
      "tmh",
      "tp",
      "tph",
      "ln",
      "lm",
      "lmh",
      "lp",
      "lph",
      "loading",
    ];
    var M = [];
    for (var G = 1; G < I.length; G = G + 1) {
      M[M.length] = '<span class="' + K + I[G] + '">&#160;</span>';
    }
    var J = document.createElement("div");
    var H = J.style;
    H.className = K + I[0];
    H.position = "absolute";
    H.height = "1px";
    H.width = "1px";
    H.top = "-1000px";
    H.left = "-1000px";
    J.innerHTML = M.join("");
    document.body.appendChild(J);
    B.removeListener(window, "load", C.preload);
  };
  B.addListener(window, "load", C.preload);
})();
(function () {
  var B = YAHOO.util.Dom,
    C = YAHOO.lang,
    A = YAHOO.util.Event;
  YAHOO.widget.Node = function (F, E, D) {
    if (F) {
      this.init(F, E, D);
    }
  };
  YAHOO.widget.Node.prototype = {
    index: 0,
    children: null,
    tree: null,
    data: null,
    parent: null,
    depth: -1,
    expanded: false,
    multiExpand: true,
    renderHidden: false,
    childrenRendered: false,
    dynamicLoadComplete: false,
    previousSibling: null,
    nextSibling: null,
    _dynLoad: false,
    dataLoader: null,
    isLoading: false,
    hasIcon: true,
    iconMode: 0,
    nowrap: false,
    isLeaf: false,
    contentStyle: "",
    contentElId: null,
    enableHighlight: true,
    highlightState: 0,
    propagateHighlightUp: false,
    propagateHighlightDown: false,
    className: null,
    _type: "Node",
    init: function (G, F, D) {
      this.data = {};
      this.children = [];
      this.index = YAHOO.widget.TreeView.nodeCount;
      ++YAHOO.widget.TreeView.nodeCount;
      this.contentElId = "ygtvcontentel" + this.index;
      if (C.isObject(G)) {
        for (var E in G) {
          if (G.hasOwnProperty(E)) {
            if (
              E.charAt(0) != "_" &&
              !C.isUndefined(this[E]) &&
              !C.isFunction(this[E])
            ) {
              this[E] = G[E];
            } else {
              this.data[E] = G[E];
            }
          }
        }
      }
      if (!C.isUndefined(D)) {
        this.expanded = D;
      }
      this.createEvent("parentChange", this);
      if (F) {
        F.appendChild(this);
      }
    },
    applyParent: function (E) {
      if (!E) {
        return false;
      }
      this.tree = E.tree;
      this.parent = E;
      this.depth = E.depth + 1;
      this.tree.regNode(this);
      E.childrenRendered = false;
      for (var F = 0, D = this.children.length; F < D; ++F) {
        this.children[F].applyParent(this);
      }
      this.fireEvent("parentChange");
      return true;
    },
    appendChild: function (E) {
      if (this.hasChildren()) {
        var D = this.children[this.children.length - 1];
        D.nextSibling = E;
        E.previousSibling = D;
      }
      this.children[this.children.length] = E;
      E.applyParent(this);
      if (this.childrenRendered && this.expanded) {
        this.getChildrenEl().style.display = "";
      }
      return E;
    },
    appendTo: function (D) {
      return D.appendChild(this);
    },
    insertBefore: function (D) {
      var F = D.parent;
      if (F) {
        if (this.tree) {
          this.tree.popNode(this);
        }
        var E = D.isChildOf(F);
        F.children.splice(E, 0, this);
        if (D.previousSibling) {
          D.previousSibling.nextSibling = this;
        }
        this.previousSibling = D.previousSibling;
        this.nextSibling = D;
        D.previousSibling = this;
        this.applyParent(F);
      }
      return this;
    },
    insertAfter: function (D) {
      var F = D.parent;
      if (F) {
        if (this.tree) {
          this.tree.popNode(this);
        }
        var E = D.isChildOf(F);
        if (!D.nextSibling) {
          this.nextSibling = null;
          return this.appendTo(F);
        }
        F.children.splice(E + 1, 0, this);
        D.nextSibling.previousSibling = this;
        this.previousSibling = D;
        this.nextSibling = D.nextSibling;
        D.nextSibling = this;
        this.applyParent(F);
      }
      return this;
    },
    isChildOf: function (E) {
      if (E && E.children) {
        for (var F = 0, D = E.children.length; F < D; ++F) {
          if (E.children[F] === this) {
            return F;
          }
        }
      }
      return -1;
    },
    getSiblings: function () {
      var D = this.parent.children.slice(0);
      for (var E = 0; E < D.length && D[E] != this; E++) {}
      D.splice(E, 1);
      if (D.length) {
        return D;
      }
      return null;
    },
    showChildren: function () {
      if (!this.tree.animateExpand(this.getChildrenEl(), this)) {
        if (this.hasChildren()) {
          this.getChildrenEl().style.display = "";
        }
      }
    },
    hideChildren: function () {
      if (!this.tree.animateCollapse(this.getChildrenEl(), this)) {
        this.getChildrenEl().style.display = "none";
      }
    },
    getElId: function () {
      return "ygtv" + this.index;
    },
    getChildrenElId: function () {
      return "ygtvc" + this.index;
    },
    getToggleElId: function () {
      return "ygtvt" + this.index;
    },
    getEl: function () {
      return B.get(this.getElId());
    },
    getChildrenEl: function () {
      return B.get(this.getChildrenElId());
    },
    getToggleEl: function () {
      return B.get(this.getToggleElId());
    },
    getContentEl: function () {
      return B.get(this.contentElId);
    },
    collapse: function () {
      if (!this.expanded) {
        return;
      }
      var D = this.tree.onCollapse(this);
      if (false === D) {
        return;
      }
      D = this.tree.fireEvent("collapse", this);
      if (false === D) {
        return;
      }
      if (!this.getEl()) {
        this.expanded = false;
      } else {
        this.hideChildren();
        this.expanded = false;
        this.updateIcon();
      }
      D = this.tree.fireEvent("collapseComplete", this);
    },
    expand: function (F) {
      if (this.expanded && !F) {
        return;
      }
      var D = true;
      if (!F) {
        D = this.tree.onExpand(this);
        if (false === D) {
          return;
        }
        D = this.tree.fireEvent("expand", this);
      }
      if (false === D) {
        return;
      }
      if (!this.getEl()) {
        this.expanded = true;
        return;
      }
      if (!this.childrenRendered) {
        this.getChildrenEl().innerHTML = this.renderChildren();
      } else {
      }
      this.expanded = true;
      this.updateIcon();
      if (this.isLoading) {
        this.expanded = false;
        return;
      }
      if (!this.multiExpand) {
        var G = this.getSiblings();
        for (var E = 0; G && E < G.length; ++E) {
          if (G[E] != this && G[E].expanded) {
            G[E].collapse();
          }
        }
      }
      this.showChildren();
      D = this.tree.fireEvent("expandComplete", this);
    },
    updateIcon: function () {
      if (this.hasIcon) {
        var D = this.getToggleEl();
        if (D) {
          D.className = D.className.replace(
            /\bygtv(([tl][pmn]h?)|(loading))\b/gi,
            this.getStyle(),
          );
        }
      }
    },
    getStyle: function () {
      if (this.isLoading) {
        return "ygtvloading";
      } else {
        var E = this.nextSibling ? "t" : "l";
        var D = "n";
        if (
          this.hasChildren(true) ||
          (this.isDynamic() && !this.getIconMode())
        ) {
          D = this.expanded ? "m" : "p";
        }
        return "ygtv" + E + D;
      }
    },
    getHoverStyle: function () {
      var D = this.getStyle();
      if (this.hasChildren(true) && !this.isLoading) {
        D += "h";
      }
      return D;
    },
    expandAll: function () {
      var D = this.children.length;
      for (var E = 0; E < D; ++E) {
        var F = this.children[E];
        if (F.isDynamic()) {
          break;
        } else {
          if (!F.multiExpand) {
            break;
          } else {
            F.expand();
            F.expandAll();
          }
        }
      }
    },
    collapseAll: function () {
      for (var D = 0; D < this.children.length; ++D) {
        this.children[D].collapse();
        this.children[D].collapseAll();
      }
    },
    setDynamicLoad: function (D, E) {
      if (D) {
        this.dataLoader = D;
        this._dynLoad = true;
      } else {
        this.dataLoader = null;
        this._dynLoad = false;
      }
      if (E) {
        this.iconMode = E;
      }
    },
    isRoot: function () {
      return this == this.tree.root;
    },
    isDynamic: function () {
      if (this.isLeaf) {
        return false;
      } else {
        return !this.isRoot() && (this._dynLoad || this.tree.root._dynLoad);
      }
    },
    getIconMode: function () {
      return this.iconMode || this.tree.root.iconMode;
    },
    hasChildren: function (D) {
      if (this.isLeaf) {
        return false;
      } else {
        return (
          this.children.length > 0 ||
          (D && this.isDynamic() && !this.dynamicLoadComplete)
        );
      }
    },
    toggle: function () {
      if (!this.tree.locked && (this.hasChildren(true) || this.isDynamic())) {
        if (this.expanded) {
          this.collapse();
        } else {
          this.expand();
        }
      }
    },
    getHtml: function () {
      this.childrenRendered = false;
      return [
        '<div class="ygtvitem" id="',
        this.getElId(),
        '">',
        this.getNodeHtml(),
        this.getChildrenHtml(),
        "</div>",
      ].join("");
    },
    getChildrenHtml: function () {
      var D = [];
      D[D.length] =
        '<div class="ygtvchildren" id="' + this.getChildrenElId() + '"';
      if (!this.expanded || !this.hasChildren()) {
        D[D.length] = ' style="display:none;"';
      }
      D[D.length] = ">";
      if (
        (this.hasChildren(true) && this.expanded) ||
        (this.renderHidden && !this.isDynamic())
      ) {
        D[D.length] = this.renderChildren();
      }
      D[D.length] = "</div>";
      return D.join("");
    },
    renderChildren: function () {
      var D = this;
      if (this.isDynamic() && !this.dynamicLoadComplete) {
        this.isLoading = true;
        this.tree.locked = true;
        if (this.dataLoader) {
          setTimeout(function () {
            D.dataLoader(D, function () {
              D.loadComplete();
            });
          }, 10);
        } else {
          if (this.tree.root.dataLoader) {
            setTimeout(function () {
              D.tree.root.dataLoader(D, function () {
                D.loadComplete();
              });
            }, 10);
          } else {
            return "Error: data loader not found or not specified.";
          }
        }
        return "";
      } else {
        return this.completeRender();
      }
    },
    completeRender: function () {
      var E = [];
      for (var D = 0; D < this.children.length; ++D) {
        E[E.length] = this.children[D].getHtml();
      }
      this.childrenRendered = true;
      return E.join("");
    },
    loadComplete: function () {
      this.getChildrenEl().innerHTML = this.completeRender();
      this.dynamicLoadComplete = true;
      this.isLoading = false;
      this.expand(true);
      this.tree.locked = false;
    },
    getAncestor: function (E) {
      if (E >= this.depth || E < 0) {
        return null;
      }
      var D = this.parent;
      while (D.depth > E) {
        D = D.parent;
      }
      return D;
    },
    getDepthStyle: function (D) {
      return this.getAncestor(D).nextSibling
        ? "ygtvdepthcell"
        : "ygtvblankdepthcell";
    },
    getNodeHtml: function () {
      var E = [];
      E[E.length] =
        '<table id="ygtvtableel' +
        this.index +
        '"border="0" cellpadding="0" cellspacing="0" class="ygtvtable ygtvdepth' +
        this.depth;
      if (this.enableHighlight) {
        E[E.length] = " ygtv-highlight" + this.highlightState;
      }
      if (this.className) {
        E[E.length] = " " + this.className;
      }
      E[E.length] = '"><tr class="ygtvrow">';
      for (var D = 0; D < this.depth; ++D) {
        E[E.length] =
          '<td class="ygtvcell ' +
          this.getDepthStyle(D) +
          '"><div class="ygtvspacer"></div></td>';
      }
      if (this.hasIcon) {
        E[E.length] = '<td id="' + this.getToggleElId();
        E[E.length] = '" class="ygtvcell ';
        E[E.length] = this.getStyle();
        E[E.length] = '"><a href="#" class="ygtvspacer">&nbsp;</a></td>';
      }
      E[E.length] = '<td id="' + this.contentElId;
      E[E.length] = '" class="ygtvcell ';
      E[E.length] = this.contentStyle + ' ygtvcontent" ';
      E[E.length] = this.nowrap ? ' nowrap="nowrap" ' : "";
      E[E.length] = " >";
      E[E.length] = this.getContentHtml();
      E[E.length] = "</td></tr></table>";
      return E.join("");
    },
    getContentHtml: function () {
      return "";
    },
    refresh: function () {
      this.getChildrenEl().innerHTML = this.completeRender();
      if (this.hasIcon) {
        var D = this.getToggleEl();
        if (D) {
          D.className = D.className.replace(
            /\bygtv[lt][nmp]h*\b/gi,
            this.getStyle(),
          );
        }
      }
    },
    toString: function () {
      return this._type + " (" + this.index + ")";
    },
    _focusHighlightedItems: [],
    _focusedItem: null,
    _canHaveFocus: function () {
      return this.getEl().getElementsByTagName("a").length > 0;
    },
    _removeFocus: function () {
      if (this._focusedItem) {
        A.removeListener(this._focusedItem, "blur");
        this._focusedItem = null;
      }
      var D;
      while ((D = this._focusHighlightedItems.shift())) {
        B.removeClass(D, YAHOO.widget.TreeView.FOCUS_CLASS_NAME);
      }
    },
    focus: function () {
      var F = false,
        D = this;
      if (this.tree.currentFocus) {
        this.tree.currentFocus._removeFocus();
      }
      var E = function (G) {
        if (G.parent) {
          E(G.parent);
          G.parent.expand();
        }
      };
      E(this);
      B.getElementsBy(
        function (G) {
          return /ygtv(([tl][pmn]h?)|(content))/.test(G.className);
        },
        "td",
        D.getEl().firstChild,
        function (H) {
          B.addClass(H, YAHOO.widget.TreeView.FOCUS_CLASS_NAME);
          if (!F) {
            var G = H.getElementsByTagName("a");
            if (G.length) {
              G = G[0];
              G.focus();
              D._focusedItem = G;
              A.on(G, "blur", function () {
                D.tree.fireEvent("focusChanged", {
                  oldNode: D.tree.currentFocus,
                  newNode: null,
                });
                D.tree.currentFocus = null;
                D._removeFocus();
              });
              F = true;
            }
          }
          D._focusHighlightedItems.push(H);
        },
      );
      if (F) {
        this.tree.fireEvent("focusChanged", {
          oldNode: this.tree.currentFocus,
          newNode: this,
        });
        this.tree.currentFocus = this;
      } else {
        this.tree.fireEvent("focusChanged", {
          oldNode: D.tree.currentFocus,
          newNode: null,
        });
        this.tree.currentFocus = null;
        this._removeFocus();
      }
      return F;
    },
    getNodeCount: function () {
      for (var D = 0, E = 0; D < this.children.length; D++) {
        E += this.children[D].getNodeCount();
      }
      return E + 1;
    },
    getNodeDefinition: function () {
      if (this.isDynamic()) {
        return false;
      }
      var G,
        D = C.merge(this.data),
        F = [];
      if (this.expanded) {
        D.expanded = this.expanded;
      }
      if (!this.multiExpand) {
        D.multiExpand = this.multiExpand;
      }
      if (!this.renderHidden) {
        D.renderHidden = this.renderHidden;
      }
      if (!this.hasIcon) {
        D.hasIcon = this.hasIcon;
      }
      if (this.nowrap) {
        D.nowrap = this.nowrap;
      }
      if (this.className) {
        D.className = this.className;
      }
      if (this.editable) {
        D.editable = this.editable;
      }
      if (this.enableHighlight) {
        D.enableHighlight = this.enableHighlight;
      }
      if (this.highlightState) {
        D.highlightState = this.highlightState;
      }
      if (this.propagateHighlightUp) {
        D.propagateHighlightUp = this.propagateHighlightUp;
      }
      if (this.propagateHighlightDown) {
        D.propagateHighlightDown = this.propagateHighlightDown;
      }
      D.type = this._type;
      for (var E = 0; E < this.children.length; E++) {
        G = this.children[E].getNodeDefinition();
        if (G === false) {
          return false;
        }
        F.push(G);
      }
      if (F.length) {
        D.children = F;
      }
      return D;
    },
    getToggleLink: function () {
      return "return false;";
    },
    setNodesProperty: function (D, G, F) {
      if (
        D.charAt(0) != "_" &&
        !C.isUndefined(this[D]) &&
        !C.isFunction(this[D])
      ) {
        this[D] = G;
      } else {
        this.data[D] = G;
      }
      for (var E = 0; E < this.children.length; E++) {
        this.children[E].setNodesProperty(D, G);
      }
      if (F) {
        this.refresh();
      }
    },
    toggleHighlight: function () {
      if (this.enableHighlight) {
        if (this.highlightState == 1) {
          this.unhighlight();
        } else {
          this.highlight();
        }
      }
    },
    highlight: function (E) {
      if (this.enableHighlight) {
        if (this.tree.singleNodeHighlight) {
          if (this.tree._currentlyHighlighted) {
            this.tree._currentlyHighlighted.unhighlight();
          }
          this.tree._currentlyHighlighted = this;
        }
        this.highlightState = 1;
        this._setHighlightClassName();
        if (this.propagateHighlightDown) {
          for (var D = 0; D < this.children.length; D++) {
            this.children[D].highlight(true);
          }
        }
        if (this.propagateHighlightUp) {
          if (this.parent) {
            this.parent._childrenHighlighted();
          }
        }
        if (!E) {
          this.tree.fireEvent("highlightEvent", this);
        }
      }
    },
    unhighlight: function (E) {
      if (this.enableHighlight) {
        this.highlightState = 0;
        this._setHighlightClassName();
        if (this.propagateHighlightDown) {
          for (var D = 0; D < this.children.length; D++) {
            this.children[D].unhighlight(true);
          }
        }
        if (this.propagateHighlightUp) {
          if (this.parent) {
            this.parent._childrenHighlighted();
          }
        }
        if (!E) {
          this.tree.fireEvent("highlightEvent", this);
        }
      }
    },
    _childrenHighlighted: function () {
      var F = false,
        E = false;
      if (this.enableHighlight) {
        for (var D = 0; D < this.children.length; D++) {
          switch (this.children[D].highlightState) {
            case 0:
              E = true;
              break;
            case 1:
              F = true;
              break;
            case 2:
              F = E = true;
              break;
          }
        }
        if (F && E) {
          this.highlightState = 2;
        } else {
          if (F) {
            this.highlightState = 1;
          } else {
            this.highlightState = 0;
          }
        }
        this._setHighlightClassName();
        if (this.propagateHighlightUp) {
          if (this.parent) {
            this.parent._childrenHighlighted();
          }
        }
      }
    },
    _setHighlightClassName: function () {
      var D = B.get("ygtvtableel" + this.index);
      if (D) {
        D.className = D.className.replace(
          /\bygtv-highlight\d\b/gi,
          "ygtv-highlight" + this.highlightState,
        );
      }
    },
  };
  YAHOO.augment(YAHOO.widget.Node, YAHOO.util.EventProvider);
})();
YAHOO.widget.RootNode = function (A) {
  this.init(null, null, true);
  this.tree = A;
};
YAHOO.extend(YAHOO.widget.RootNode, YAHOO.widget.Node, {
  _type: "RootNode",
  getNodeHtml: function () {
    return "";
  },
  toString: function () {
    return this._type;
  },
  loadComplete: function () {
    this.tree.draw();
  },
  getNodeCount: function () {
    for (var A = 0, B = 0; A < this.children.length; A++) {
      B += this.children[A].getNodeCount();
    }
    return B;
  },
  getNodeDefinition: function () {
    for (var C, A = [], B = 0; B < this.children.length; B++) {
      C = this.children[B].getNodeDefinition();
      if (C === false) {
        return false;
      }
      A.push(C);
    }
    return A;
  },
  collapse: function () {},
  expand: function () {},
  getSiblings: function () {
    return null;
  },
  focus: function () {},
});
(function () {
  var B = YAHOO.util.Dom,
    C = YAHOO.lang,
    A = YAHOO.util.Event;
  YAHOO.widget.TextNode = function (F, E, D) {
    if (F) {
      if (C.isString(F)) {
        F = { label: F };
      }
      this.init(F, E, D);
      this.setUpLabel(F);
    }
  };
  YAHOO.extend(YAHOO.widget.TextNode, YAHOO.widget.Node, {
    labelStyle: "ygtvlabel",
    labelElId: null,
    label: null,
    title: null,
    href: null,
    target: "_self",
    _type: "TextNode",
    setUpLabel: function (D) {
      if (C.isString(D)) {
        D = { label: D };
      } else {
        if (D.style) {
          this.labelStyle = D.style;
        }
      }
      this.label = D.label;
      this.labelElId = "ygtvlabelel" + this.index;
    },
    getLabelEl: function () {
      return B.get(this.labelElId);
    },
    getContentHtml: function () {
      var D = [];
      D[D.length] = this.href ? "<a" : "<span";
      D[D.length] = ' id="' + this.labelElId + '"';
      D[D.length] = ' class="' + this.labelStyle + '"';
      if (this.href) {
        D[D.length] = ' href="' + this.href + '"';
        D[D.length] = ' target="' + this.target + '"';
      }
      if (this.title) {
        D[D.length] = ' title="' + this.title + '"';
      }
      D[D.length] = " >";
      D[D.length] = this.label;
      D[D.length] = this.href ? "</a>" : "</span>";
      return D.join("");
    },
    getNodeDefinition: function () {
      var D = YAHOO.widget.TextNode.superclass.getNodeDefinition.call(this);
      if (D === false) {
        return false;
      }
      D.label = this.label;
      if (this.labelStyle != "ygtvlabel") {
        D.style = this.labelStyle;
      }
      if (this.title) {
        D.title = this.title;
      }
      if (this.href) {
        D.href = this.href;
      }
      if (this.target != "_self") {
        D.target = this.target;
      }
      return D;
    },
    toString: function () {
      return (
        YAHOO.widget.TextNode.superclass.toString.call(this) + ": " + this.label
      );
    },
    onLabelClick: function () {
      return false;
    },
    refresh: function () {
      YAHOO.widget.TextNode.superclass.refresh.call(this);
      var D = this.getLabelEl();
      D.innerHTML = this.label;
      if (D.tagName.toUpperCase() == "A") {
        D.href = this.href;
        D.target = this.target;
      }
    },
  });
})();
YAHOO.widget.MenuNode = function (C, B, A) {
  YAHOO.widget.MenuNode.superclass.constructor.call(this, C, B, A);
  this.multiExpand = false;
};
YAHOO.extend(YAHOO.widget.MenuNode, YAHOO.widget.TextNode, {
  _type: "MenuNode",
});
(function () {
  var B = YAHOO.util.Dom,
    C = YAHOO.lang,
    A = YAHOO.util.Event;
  YAHOO.widget.HTMLNode = function (G, F, E, D) {
    if (G) {
      this.init(G, F, E);
      this.initContent(G, D);
    }
  };
  YAHOO.extend(YAHOO.widget.HTMLNode, YAHOO.widget.Node, {
    contentStyle: "ygtvhtml",
    html: null,
    _type: "HTMLNode",
    initContent: function (E, D) {
      this.setHtml(E);
      this.contentElId = "ygtvcontentel" + this.index;
      if (!C.isUndefined(D)) {
        this.hasIcon = D;
      }
    },
    setHtml: function (E) {
      this.html = typeof E === "string" ? E : E.html;
      var D = this.getContentEl();
      if (D) {
        D.innerHTML = this.html;
      }
    },
    getContentHtml: function () {
      return this.html;
    },
    getNodeDefinition: function () {
      var D = YAHOO.widget.HTMLNode.superclass.getNodeDefinition.call(this);
      if (D === false) {
        return false;
      }
      D.html = this.html;
      return D;
    },
  });
})();
(function () {
  var B = YAHOO.util.Dom,
    C = YAHOO.lang,
    A = YAHOO.util.Event,
    D = YAHOO.widget.Calendar;
  YAHOO.widget.DateNode = function (G, F, E) {
    YAHOO.widget.DateNode.superclass.constructor.call(this, G, F, E);
  };
  YAHOO.extend(YAHOO.widget.DateNode, YAHOO.widget.TextNode, {
    _type: "DateNode",
    calendarConfig: null,
    fillEditorContainer: function (G) {
      var H,
        F = G.inputContainer;
      if (C.isUndefined(D)) {
        B.replaceClass(
          G.editorPanel,
          "ygtv-edit-DateNode",
          "ygtv-edit-TextNode",
        );
        YAHOO.widget.DateNode.superclass.fillEditorContainer.call(this, G);
        return;
      }
      if (G.nodeType != this._type) {
        G.nodeType = this._type;
        G.saveOnEnter = false;
        G.node.destroyEditorContents(G);
        G.inputObject = H = new D(F.appendChild(document.createElement("div")));
        if (this.calendarConfig) {
          H.cfg.applyConfig(this.calendarConfig, true);
          H.cfg.fireQueue();
        }
        H.selectEvent.subscribe(
          function () {
            this.tree._closeEditor(true);
          },
          this,
          true,
        );
      } else {
        H = G.inputObject;
      }
      H.cfg.setProperty("selected", this.label, false);
      var I = H.cfg.getProperty("DATE_FIELD_DELIMITER");
      var E = this.label.split(I);
      H.cfg.setProperty(
        "pagedate",
        E[H.cfg.getProperty("MDY_MONTH_POSITION") - 1] +
          I +
          E[H.cfg.getProperty("MDY_YEAR_POSITION") - 1],
      );
      H.cfg.fireQueue();
      H.render();
      H.oDomContainer.focus();
    },
    saveEditorValue: function (F) {
      var I = F.node,
        H = I.tree.validator,
        J;
      if (C.isUndefined(D)) {
        J = F.inputElement.value;
      } else {
        var K = F.inputObject,
          G = K.getSelectedDates()[0],
          E = [];
        E[K.cfg.getProperty("MDY_DAY_POSITION") - 1] = G.getDate();
        E[K.cfg.getProperty("MDY_MONTH_POSITION") - 1] = G.getMonth() + 1;
        E[K.cfg.getProperty("MDY_YEAR_POSITION") - 1] = G.getFullYear();
        J = E.join(K.cfg.getProperty("DATE_FIELD_DELIMITER"));
      }
      if (C.isFunction(H)) {
        J = H(J, I.label, I);
        if (C.isUndefined(J)) {
          return false;
        }
      }
      I.label = J;
      I.getLabelEl().innerHTML = J;
    },
    getNodeDefinition: function () {
      var E = YAHOO.widget.DateNode.superclass.getNodeDefinition.call(this);
      if (E === false) {
        return false;
      }
      if (this.calendarConfig) {
        E.calendarConfig = this.calendarConfig;
      }
      return E;
    },
  });
})();
(function () {
  var E = YAHOO.util.Dom,
    F = YAHOO.lang,
    B = YAHOO.util.Event,
    D = YAHOO.widget.TreeView,
    C = D.prototype;
  D.editorData = {
    active: false,
    whoHasIt: null,
    nodeType: null,
    editorPanel: null,
    inputContainer: null,
    buttonsContainer: null,
    node: null,
    saveOnEnter: true,
  };
  C.validator = null;
  C._nodeEditing = function (M) {
    if (M.fillEditorContainer && M.editable) {
      var I,
        K,
        L,
        J,
        H = D.editorData;
      H.active = true;
      H.whoHasIt = this;
      if (!H.nodeType) {
        H.editorPanel = I = document.body.appendChild(
          document.createElement("div"),
        );
        E.addClass(I, "ygtv-label-editor");
        L = H.buttonsContainer = I.appendChild(document.createElement("div"));
        E.addClass(L, "ygtv-button-container");
        J = L.appendChild(document.createElement("button"));
        E.addClass(J, "ygtvok");
        J.innerHTML = " ";
        J = L.appendChild(document.createElement("button"));
        E.addClass(J, "ygtvcancel");
        J.innerHTML = " ";
        B.on(
          L,
          "click",
          function (O) {
            var P = B.getTarget(O);
            var N = D.editorData.node;
            if (E.hasClass(P, "ygtvok")) {
              B.stopEvent(O);
              this._closeEditor(true);
            }
            if (E.hasClass(P, "ygtvcancel")) {
              B.stopEvent(O);
              this._closeEditor(false);
            }
          },
          this,
          true,
        );
        H.inputContainer = I.appendChild(document.createElement("div"));
        E.addClass(H.inputContainer, "ygtv-input");
        B.on(
          I,
          "keydown",
          function (P) {
            var O = D.editorData,
              N = YAHOO.util.KeyListener.KEY;
            switch (P.keyCode) {
              case N.ENTER:
                B.stopEvent(P);
                if (O.saveOnEnter) {
                  this._closeEditor(true);
                }
                break;
              case N.ESCAPE:
                B.stopEvent(P);
                this._closeEditor(false);
                break;
            }
          },
          this,
          true,
        );
      } else {
        I = H.editorPanel;
      }
      H.node = M;
      if (H.nodeType) {
        E.removeClass(I, "ygtv-edit-" + H.nodeType);
      }
      E.addClass(I, " ygtv-edit-" + M._type);
      K = E.getXY(M.getContentEl());
      E.setStyle(I, "left", K[0] + "px");
      E.setStyle(I, "top", K[1] + "px");
      E.setStyle(I, "display", "block");
      I.focus();
      M.fillEditorContainer(H);
      return true;
    }
  };
  C.onEventEditNode = function (H) {
    if (H instanceof YAHOO.widget.Node) {
      H.editNode();
    } else {
      if (H.node instanceof YAHOO.widget.Node) {
        H.node.editNode();
      }
    }
  };
  C._closeEditor = function (J) {
    var H = D.editorData,
      I = H.node,
      K = true;
    if (J) {
      K = H.node.saveEditorValue(H) !== false;
    }
    if (K) {
      E.setStyle(H.editorPanel, "display", "none");
      H.active = false;
      I.focus();
    }
  };
  C._destroyEditor = function () {
    var H = D.editorData;
    if (H && H.nodeType && (!H.active || H.whoHasIt === this)) {
      B.removeListener(H.editorPanel, "keydown");
      B.removeListener(H.buttonContainer, "click");
      H.node.destroyEditorContents(H);
      document.body.removeChild(H.editorPanel);
      H.nodeType =
        H.editorPanel =
        H.inputContainer =
        H.buttonsContainer =
        H.whoHasIt =
        H.node =
          null;
      H.active = false;
    }
  };
  var G = YAHOO.widget.Node.prototype;
  G.editable = false;
  G.editNode = function () {
    this.tree._nodeEditing(this);
  };
  G.fillEditorContainer = null;
  G.destroyEditorContents = function (H) {
    B.purgeElement(H.inputContainer, true);
    H.inputContainer.innerHTML = "";
  };
  G.saveEditorValue = function (H) {};
  var A = YAHOO.widget.TextNode.prototype;
  A.fillEditorContainer = function (I) {
    var H;
    if (I.nodeType != this._type) {
      I.nodeType = this._type;
      I.saveOnEnter = true;
      I.node.destroyEditorContents(I);
      I.inputElement = H = I.inputContainer.appendChild(
        document.createElement("input"),
      );
    } else {
      H = I.inputElement;
    }
    H.value = this.label;
    H.focus();
    H.select();
  };
  A.saveEditorValue = function (H) {
    var J = H.node,
      K = H.inputElement.value,
      I = J.tree.validator;
    if (F.isFunction(I)) {
      K = I(K, J.label, J);
      if (F.isUndefined(K)) {
        return false;
      }
    }
    J.label = K;
    J.getLabelEl().innerHTML = K;
  };
  A.destroyEditorContents = function (H) {
    H.inputContainer.innerHTML = "";
  };
})();
YAHOO.register("treeview", YAHOO.widget.TreeView, {
  version: "2.7.0",
  build: "1799",
});
