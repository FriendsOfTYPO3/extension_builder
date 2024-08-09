/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
(function () {
  function A(E) {
    var I = A.VALUE_UNLIMITED,
      H = YAHOO.lang,
      F,
      B,
      C,
      D,
      G;
    E = H.isObject(E) ? E : {};
    this.initConfig();
    this.initEvents();
    this.set("rowsPerPage", E.rowsPerPage, true);
    if (A.isNumeric(E.totalRecords)) {
      this.set("totalRecords", E.totalRecords, true);
    }
    this.initUIComponents();
    for (F in E) {
      if (H.hasOwnProperty(E, F)) {
        this.set(F, E[F], true);
      }
    }
    B = this.get("initialPage");
    C = this.get("totalRecords");
    D = this.get("rowsPerPage");
    if (B > 1 && D !== I) {
      G = (B - 1) * D;
      if (C === I || G < C) {
        this.set("recordOffset", G, true);
      }
    }
  }

  YAHOO.lang.augmentObject(
    A,
    {
      id: 0,
      ID_BASE: "yui-pg",
      VALUE_UNLIMITED: -1,
      TEMPLATE_DEFAULT:
        "{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}",
      TEMPLATE_ROWS_PER_PAGE:
        "{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink} {RowsPerPageDropdown}",
      ui: {},
      isNumeric: function (B) {
        return isFinite(+B);
      },
      toNumber: function (B) {
        return isFinite(+B) ? +B : null;
      },
    },
    true,
  );
  A.prototype = {
    _containers: [],
    _batch: false,
    _pageChanged: false,
    _state: null,
    initConfig: function () {
      var C = A.VALUE_UNLIMITED,
        B = YAHOO.lang;
      this.setAttributeConfig("rowsPerPage", {
        value: 0,
        validator: A.isNumeric,
        setter: A.toNumber,
      });
      this.setAttributeConfig("containers", {
        value: null,
        validator: function (F) {
          if (!B.isArray(F)) {
            F = [F];
          }
          for (var E = 0, D = F.length; E < D; ++E) {
            if (B.isString(F[E]) || (B.isObject(F[E]) && F[E].nodeType === 1)) {
              continue;
            }
            return false;
          }
          return true;
        },
        method: function (D) {
          D = YAHOO.util.Dom.get(D);
          if (!B.isArray(D)) {
            D = [D];
          }
          this._containers = D;
        },
      });
      this.setAttributeConfig("totalRecords", {
        value: 0,
        validator: A.isNumeric,
        setter: A.toNumber,
      });
      this.setAttributeConfig("recordOffset", {
        value: 0,
        validator: function (E) {
          var D = this.get("totalRecords");
          if (A.isNumeric(E)) {
            E = +E;
            return D === C || D > E || (D === 0 && E === 0);
          }
          return false;
        },
        setter: A.toNumber,
      });
      this.setAttributeConfig("initialPage", {
        value: 1,
        validator: A.isNumeric,
        setter: A.toNumber,
      });
      this.setAttributeConfig("template", {
        value: A.TEMPLATE_DEFAULT,
        validator: B.isString,
      });
      this.setAttributeConfig("containerClass", {
        value: "yui-pg-container",
        validator: B.isString,
      });
      this.setAttributeConfig("alwaysVisible", {
        value: true,
        validator: B.isBoolean,
      });
      this.setAttributeConfig("updateOnChange", {
        value: false,
        validator: B.isBoolean,
      });
      this.setAttributeConfig("id", { value: A.id++, readOnly: true });
      this.setAttributeConfig("rendered", { value: false, readOnly: true });
    },
    initUIComponents: function () {
      var D = A.ui,
        C,
        B;
      for (C in D) {
        if (YAHOO.lang.hasOwnProperty(D, C)) {
          B = D[C];
          if (YAHOO.lang.isObject(B) && YAHOO.lang.isFunction(B.init)) {
            B.init(this);
          }
        }
      }
    },
    initEvents: function () {
      this.createEvent("render");
      this.createEvent("rendered");
      this.createEvent("changeRequest");
      this.createEvent("pageChange");
      this.createEvent("beforeDestroy");
      this.createEvent("destroy");
      this._selfSubscribe();
    },
    _selfSubscribe: function () {
      this.subscribe("totalRecordsChange", this.updateVisibility, this, true);
      this.subscribe("alwaysVisibleChange", this.updateVisibility, this, true);
      this.subscribe("totalRecordsChange", this._handleStateChange, this, true);
      this.subscribe("recordOffsetChange", this._handleStateChange, this, true);
      this.subscribe("rowsPerPageChange", this._handleStateChange, this, true);
      this.subscribe("totalRecordsChange", this._syncRecordOffset, this, true);
    },
    _syncRecordOffset: function (E) {
      var B = E.newValue,
        D,
        C;
      if (E.prevValue !== B) {
        if (B !== A.VALUE_UNLIMITED) {
          D = this.get("rowsPerPage");
          if (D && this.get("recordOffset") >= B) {
            C = this.getState({
              totalRecords: E.prevValue,
              recordOffset: this.get("recordOffset"),
            });
            this.set("recordOffset", C.before.recordOffset);
            this._firePageChange(C);
          }
        }
      }
    },
    _handleStateChange: function (C) {
      if (C.prevValue !== C.newValue) {
        var D = this._state || {},
          B;
        D[C.type.replace(/Change$/, "")] = C.prevValue;
        B = this.getState(D);
        if (B.page !== B.before.page) {
          if (this._batch) {
            this._pageChanged = true;
          } else {
            this._firePageChange(B);
          }
        }
      }
    },
    _firePageChange: function (B) {
      if (YAHOO.lang.isObject(B)) {
        var C = B.before;
        delete B.before;
        this.fireEvent("pageChange", {
          type: "pageChange",
          prevValue: B.page,
          newValue: C.page,
          prevState: B,
          newState: C,
        });
      }
    },
    render: function () {
      if (this.get("rendered")) {
        return;
      }
      var N = this.get("totalRecords"),
        G = YAHOO.util.Dom,
        O = this.get("template"),
        Q = this.get("containerClass"),
        I,
        K,
        M,
        H,
        F,
        E,
        P,
        D,
        C,
        B,
        L,
        J;
      if (
        N !== A.VALUE_UNLIMITED &&
        N < this.get("rowsPerPage") &&
        !this.get("alwaysVisible")
      ) {
        return;
      }
      O = O.replace(
        /\{([a-z0-9_ \-]+)\}/gi,
        '<span class="yui-pg-ui $1"></span>',
      );
      for (I = 0, K = this._containers.length; I < K; ++I) {
        M = this._containers[I];
        H = A.ID_BASE + this.get("id") + "-" + I;
        if (!M) {
          continue;
        }
        M.style.display = "none";
        G.addClass(M, Q);
        M.innerHTML = O;
        F = G.getElementsByClassName("yui-pg-ui", "span", M);
        for (E = 0, P = F.length; E < P; ++E) {
          D = F[E];
          C = D.parentNode;
          B = D.className.replace(/\s*yui-pg-ui\s+/g, "");
          L = A.ui[B];
          if (YAHOO.lang.isFunction(L)) {
            J = new L(this);
            if (YAHOO.lang.isFunction(J.render)) {
              C.replaceChild(J.render(H), D);
            }
          }
        }
        M.style.display = "";
      }
      if (this._containers.length) {
        this.setAttributeConfig("rendered", { value: true });
        this.fireEvent("render", this.getState());
        this.fireEvent("rendered", this.getState());
      }
    },
    destroy: function () {
      this.fireEvent("beforeDestroy");
      this.fireEvent("destroy");
      this.setAttributeConfig("rendered", { value: false });
    },
    updateVisibility: function (G) {
      var C = this.get("alwaysVisible"),
        I,
        H,
        E,
        F,
        D,
        B;
      if (G.type === "alwaysVisibleChange" || !C) {
        I = this.get("totalRecords");
        H = true;
        E = this.get("rowsPerPage");
        F = this.get("rowsPerPageOptions");
        if (YAHOO.lang.isArray(F)) {
          for (D = 0, B = F.length; D < B; ++D) {
            E = Math.min(E, F[D]);
          }
        }
        if (I !== A.VALUE_UNLIMITED && I <= E) {
          H = false;
        }
        H = H || C;
        for (D = 0, B = this._containers.length; D < B; ++D) {
          YAHOO.util.Dom.setStyle(
            this._containers[D],
            "display",
            H ? "" : "none",
          );
        }
      }
    },
    getContainerNodes: function () {
      return this._containers;
    },
    getTotalPages: function () {
      var B = this.get("totalRecords"),
        C = this.get("rowsPerPage");
      if (!C) {
        return null;
      }
      if (B === A.VALUE_UNLIMITED) {
        return A.VALUE_UNLIMITED;
      }
      return Math.ceil(B / C);
    },
    hasPage: function (C) {
      if (!YAHOO.lang.isNumber(C) || C < 1) {
        return false;
      }
      var B = this.getTotalPages();
      return B === A.VALUE_UNLIMITED || B >= C;
    },
    getCurrentPage: function () {
      var B = this.get("rowsPerPage");
      if (!B || !this.get("totalRecords")) {
        return 0;
      }
      return Math.floor(this.get("recordOffset") / B) + 1;
    },
    hasNextPage: function () {
      var B = this.getCurrentPage(),
        C = this.getTotalPages();
      return B && (C === A.VALUE_UNLIMITED || B < C);
    },
    getNextPage: function () {
      return this.hasNextPage() ? this.getCurrentPage() + 1 : null;
    },
    hasPreviousPage: function () {
      return this.getCurrentPage() > 1;
    },
    getPreviousPage: function () {
      return this.hasPreviousPage() ? this.getCurrentPage() - 1 : 1;
    },
    getPageRecords: function (E) {
      if (!YAHOO.lang.isNumber(E)) {
        E = this.getCurrentPage();
      }
      var D = this.get("rowsPerPage"),
        C = this.get("totalRecords"),
        F,
        B;
      if (!E || !D) {
        return null;
      }
      F = (E - 1) * D;
      if (C !== A.VALUE_UNLIMITED) {
        if (F >= C) {
          return null;
        }
        B = Math.min(F + D, C) - 1;
      } else {
        B = F + D - 1;
      }
      return [F, B];
    },
    setPage: function (C, B) {
      if (this.hasPage(C) && C !== this.getCurrentPage()) {
        if (this.get("updateOnChange") || B) {
          this.set("recordOffset", (C - 1) * this.get("rowsPerPage"));
        } else {
          this.fireEvent("changeRequest", this.getState({ page: C }));
        }
      }
    },
    getRowsPerPage: function () {
      return this.get("rowsPerPage");
    },
    setRowsPerPage: function (C, B) {
      if (A.isNumeric(C) && +C > 0 && +C !== this.get("rowsPerPage")) {
        if (this.get("updateOnChange") || B) {
          this.set("rowsPerPage", C);
        } else {
          this.fireEvent("changeRequest", this.getState({ rowsPerPage: +C }));
        }
      }
    },
    getTotalRecords: function () {
      return this.get("totalRecords");
    },
    setTotalRecords: function (C, B) {
      if (A.isNumeric(C) && +C >= 0 && +C !== this.get("totalRecords")) {
        if (this.get("updateOnChange") || B) {
          this.set("totalRecords", C);
        } else {
          this.fireEvent("changeRequest", this.getState({ totalRecords: +C }));
        }
      }
    },
    getStartIndex: function () {
      return this.get("recordOffset");
    },
    setStartIndex: function (C, B) {
      if (A.isNumeric(C) && +C >= 0 && +C !== this.get("recordOffset")) {
        if (this.get("updateOnChange") || B) {
          this.set("recordOffset", C);
        } else {
          this.fireEvent("changeRequest", this.getState({ recordOffset: +C }));
        }
      }
    },
    getState: function (H) {
      var J = A.VALUE_UNLIMITED,
        F = Math,
        G = F.max,
        I = F.ceil,
        D,
        B,
        E;

      function C(M, K, L) {
        if (M <= 0 || K === 0) {
          return 0;
        }
        if (K === J || K > M) {
          return M - (M % L);
        }
        return K - (K % L || L);
      }

      D = {
        paginator: this,
        totalRecords: this.get("totalRecords"),
        rowsPerPage: this.get("rowsPerPage"),
        records: this.getPageRecords(),
      };
      D.recordOffset = C(
        this.get("recordOffset"),
        D.totalRecords,
        D.rowsPerPage,
      );
      D.page = I(D.recordOffset / D.rowsPerPage) + 1;
      if (!H) {
        return D;
      }
      B = {
        paginator: this,
        before: D,
        rowsPerPage: H.rowsPerPage || D.rowsPerPage,
        totalRecords: A.isNumeric(H.totalRecords)
          ? G(H.totalRecords, J)
          : +D.totalRecords,
      };
      if (B.totalRecords === 0) {
        B.recordOffset = B.page = 0;
      } else {
        E = A.isNumeric(H.page)
          ? (H.page - 1) * B.rowsPerPage
          : A.isNumeric(H.recordOffset)
            ? +H.recordOffset
            : D.recordOffset;
        B.recordOffset = C(E, B.totalRecords, B.rowsPerPage);
        B.page = I(B.recordOffset / B.rowsPerPage) + 1;
      }
      B.records = [B.recordOffset, B.recordOffset + B.rowsPerPage - 1];
      if (
        B.totalRecords !== J &&
        B.recordOffset < B.totalRecords &&
        B.records &&
        B.records[1] > B.totalRecords - 1
      ) {
        B.records[1] = B.totalRecords - 1;
      }
      return B;
    },
    setState: function (C) {
      if (YAHOO.lang.isObject(C)) {
        this._state = this.getState({});
        C = {
          page: C.page,
          rowsPerPage: C.rowsPerPage,
          totalRecords: C.totalRecords,
          recordOffset: C.recordOffset,
        };
        if (C.page && C.recordOffset === undefined) {
          C.recordOffset =
            (C.page - 1) * (C.rowsPerPage || this.get("rowsPerPage"));
        }
        this._batch = true;
        this._pageChanged = false;
        for (var B in C) {
          if (C.hasOwnProperty(B)) {
            this.set(B, C[B]);
          }
        }
        this._batch = false;
        if (this._pageChanged) {
          this._pageChanged = false;
          this._firePageChange(this.getState(this._state));
        }
      }
    },
  };
  YAHOO.lang.augmentProto(A, YAHOO.util.AttributeProvider);
  YAHOO.widget.Paginator = A;
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.CurrentPageReport = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("pageReportTemplateChange", this.update, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("pageReportClassChange", this.update, this, true);
  };
  B.ui.CurrentPageReport.init = function (C) {
    C.setAttributeConfig("pageReportClass", {
      value: "yui-pg-current",
      validator: A.isString,
    });
    C.setAttributeConfig("pageReportTemplate", {
      value: "({currentPage} of {totalPages})",
      validator: A.isString,
    });
    C.setAttributeConfig("pageReportValueGenerator", {
      value: function (F) {
        var E = F.getCurrentPage(),
          D = F.getPageRecords();
        return {
          currentPage: D ? E : 0,
          totalPages: F.getTotalPages(),
          startIndex: D ? D[0] : 0,
          endIndex: D ? D[1] : 0,
          startRecord: D ? D[0] + 1 : 0,
          endRecord: D ? D[1] + 1 : 0,
          totalRecords: F.get("totalRecords"),
        };
      },
      validator: A.isFunction,
    });
  };
  B.ui.CurrentPageReport.sprintf = function (D, C) {
    return D.replace(/\{([\w\s\-]+)\}/g, function (E, F) {
      return F in C ? C[F] : "";
    });
  };
  B.ui.CurrentPageReport.prototype = {
    span: null,
    render: function (C) {
      this.span = document.createElement("span");
      this.span.id = C + "-page-report";
      this.span.className = this.paginator.get("pageReportClass");
      this.update();
      return this.span;
    },
    update: function (C) {
      if (C && C.prevValue === C.newValue) {
        return;
      }
      this.span.innerHTML = B.ui.CurrentPageReport.sprintf(
        this.paginator.get("pageReportTemplate"),
        this.paginator.get("pageReportValueGenerator")(this.paginator),
      );
    },
    destroy: function () {
      this.span.parentNode.removeChild(this.span);
      this.span = null;
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.PageLinks = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("pageLinksChange", this.rebuild, this, true);
    C.subscribe("pageLinkClassChange", this.rebuild, this, true);
    C.subscribe("currentPageClassChange", this.rebuild, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("pageLinksContainerClassChange", this.rebuild, this, true);
  };
  B.ui.PageLinks.init = function (C) {
    C.setAttributeConfig("pageLinkClass", {
      value: "yui-pg-page",
      validator: A.isString,
    });
    C.setAttributeConfig("currentPageClass", {
      value: "yui-pg-current-page",
      validator: A.isString,
    });
    C.setAttributeConfig("pageLinksContainerClass", {
      value: "yui-pg-pages",
      validator: A.isString,
    });
    C.setAttributeConfig("pageLinks", { value: 10, validator: B.isNumeric });
    C.setAttributeConfig("pageLabelBuilder", {
      value: function (D, E) {
        return D;
      },
      validator: A.isFunction,
    });
  };
  B.ui.PageLinks.calculateRange = function (E, F, D) {
    var I = B.VALUE_UNLIMITED,
      H,
      C,
      G;
    if (!E || D === 0 || F === 0 || (F === I && D === I)) {
      return [0, -1];
    }
    if (F !== I) {
      D = D === I ? F : Math.min(D, F);
    }
    H = Math.max(1, Math.ceil(E - D / 2));
    if (F === I) {
      C = H + D - 1;
    } else {
      C = Math.min(F, H + D - 1);
    }
    G = D - (C - H + 1);
    H = Math.max(1, H - G);
    return [H, C];
  };
  B.ui.PageLinks.prototype = {
    current: 0,
    container: null,
    render: function (C) {
      var D = this.paginator;
      this.container = document.createElement("span");
      this.container.id = C + "-pages";
      this.container.className = D.get("pageLinksContainerClass");
      YAHOO.util.Event.on(this.container, "click", this.onClick, this, true);
      this.update({ newValue: null, rebuild: true });
      return this.container;
    },
    update: function (J) {
      if (J && J.prevValue === J.newValue) {
        return;
      }
      var E = this.paginator,
        I = E.getCurrentPage();
      if (this.current !== I || !I || J.rebuild) {
        var L = E.get("pageLabelBuilder"),
          H = B.ui.PageLinks.calculateRange(
            I,
            E.getTotalPages(),
            E.get("pageLinks"),
          ),
          D = H[0],
          F = H[1],
          K = "",
          C,
          G;
        C = '<a href="#" class="' + E.get("pageLinkClass") + '" page="';
        for (G = D; G <= F; ++G) {
          if (G === I) {
            K +=
              '<span class="' +
              E.get("currentPageClass") +
              " " +
              E.get("pageLinkClass") +
              '">' +
              L(G, E) +
              "</span>";
          } else {
            K += C + G + '">' + L(G, E) + "</a>";
          }
        }
        this.container.innerHTML = K;
      }
    },
    rebuild: function (C) {
      C.rebuild = true;
      this.update(C);
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.container, true);
      this.container.parentNode.removeChild(this.container);
      this.container = null;
    },
    onClick: function (D) {
      var C = YAHOO.util.Event.getTarget(D);
      if (
        C &&
        YAHOO.util.Dom.hasClass(C, this.paginator.get("pageLinkClass"))
      ) {
        YAHOO.util.Event.stopEvent(D);
        this.paginator.setPage(parseInt(C.getAttribute("page"), 10));
      }
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.FirstPageLink = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("firstPageLinkLabelChange", this.update, this, true);
    C.subscribe("firstPageLinkClassChange", this.update, this, true);
  };
  B.ui.FirstPageLink.init = function (C) {
    C.setAttributeConfig("firstPageLinkLabel", {
      value: "&lt;&lt;&nbsp;first",
      validator: A.isString,
    });
    C.setAttributeConfig("firstPageLinkClass", {
      value: "yui-pg-first",
      validator: A.isString,
    });
  };
  B.ui.FirstPageLink.prototype = {
    current: null,
    link: null,
    span: null,
    render: function (D) {
      var E = this.paginator,
        F = E.get("firstPageLinkClass"),
        C = E.get("firstPageLinkLabel");
      this.link = document.createElement("a");
      this.span = document.createElement("span");
      this.link.id = D + "-first-link";
      this.link.href = "#";
      this.link.className = F;
      this.link.innerHTML = C;
      YAHOO.util.Event.on(this.link, "click", this.onClick, this, true);
      this.span.id = D + "-first-span";
      this.span.className = F;
      this.span.innerHTML = C;
      this.current = E.getCurrentPage() > 1 ? this.link : this.span;
      return this.current;
    },
    update: function (D) {
      if (D && D.prevValue === D.newValue) {
        return;
      }
      var C = this.current ? this.current.parentNode : null;
      if (this.paginator.getCurrentPage() > 1) {
        if (C && this.current === this.span) {
          C.replaceChild(this.link, this.current);
          this.current = this.link;
        }
      } else {
        if (C && this.current === this.link) {
          C.replaceChild(this.span, this.current);
          this.current = this.span;
        }
      }
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.link);
      this.current.parentNode.removeChild(this.current);
      this.link = this.span = null;
    },
    onClick: function (C) {
      YAHOO.util.Event.stopEvent(C);
      this.paginator.setPage(1);
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.LastPageLink = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("lastPageLinkLabelChange", this.update, this, true);
    C.subscribe("lastPageLinkClassChange", this.update, this, true);
  };
  B.ui.LastPageLink.init = function (C) {
    C.setAttributeConfig("lastPageLinkLabel", {
      value: "last&nbsp;&gt;&gt;",
      validator: A.isString,
    });
    C.setAttributeConfig("lastPageLinkClass", {
      value: "yui-pg-last",
      validator: A.isString,
    });
  };
  B.ui.LastPageLink.prototype = {
    current: null,
    link: null,
    span: null,
    na: null,
    render: function (D) {
      var F = this.paginator,
        G = F.get("lastPageLinkClass"),
        C = F.get("lastPageLinkLabel"),
        E = F.getTotalPages();
      this.link = document.createElement("a");
      this.span = document.createElement("span");
      this.na = this.span.cloneNode(false);
      this.link.id = D + "-last-link";
      this.link.href = "#";
      this.link.className = G;
      this.link.innerHTML = C;
      YAHOO.util.Event.on(this.link, "click", this.onClick, this, true);
      this.span.id = D + "-last-span";
      this.span.className = G;
      this.span.innerHTML = C;
      this.na.id = D + "-last-na";
      switch (E) {
        case B.VALUE_UNLIMITED:
          this.current = this.na;
          break;
        case F.getCurrentPage():
          this.current = this.span;
          break;
        default:
          this.current = this.link;
      }
      return this.current;
    },
    update: function (D) {
      if (D && D.prevValue === D.newValue) {
        return;
      }
      var C = this.current ? this.current.parentNode : null,
        E = this.link;
      if (C) {
        switch (this.paginator.getTotalPages()) {
          case B.VALUE_UNLIMITED:
            E = this.na;
            break;
          case this.paginator.getCurrentPage():
            E = this.span;
            break;
        }
        if (this.current !== E) {
          C.replaceChild(E, this.current);
          this.current = E;
        }
      }
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.link);
      this.current.parentNode.removeChild(this.current);
      this.link = this.span = null;
    },
    onClick: function (C) {
      YAHOO.util.Event.stopEvent(C);
      this.paginator.setPage(this.paginator.getTotalPages());
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.NextPageLink = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("nextPageLinkLabelChange", this.update, this, true);
    C.subscribe("nextPageLinkClassChange", this.update, this, true);
  };
  B.ui.NextPageLink.init = function (C) {
    C.setAttributeConfig("nextPageLinkLabel", {
      value: "next&nbsp;&gt;",
      validator: A.isString,
    });
    C.setAttributeConfig("nextPageLinkClass", {
      value: "yui-pg-next",
      validator: A.isString,
    });
  };
  B.ui.NextPageLink.prototype = {
    current: null,
    link: null,
    span: null,
    render: function (D) {
      var F = this.paginator,
        G = F.get("nextPageLinkClass"),
        C = F.get("nextPageLinkLabel"),
        E = F.getTotalPages();
      this.link = document.createElement("a");
      this.span = document.createElement("span");
      this.link.id = D + "-next-link";
      this.link.href = "#";
      this.link.className = G;
      this.link.innerHTML = C;
      YAHOO.util.Event.on(this.link, "click", this.onClick, this, true);
      this.span.id = D + "-next-span";
      this.span.className = G;
      this.span.innerHTML = C;
      this.current = F.getCurrentPage() === E ? this.span : this.link;
      return this.current;
    },
    update: function (E) {
      if (E && E.prevValue === E.newValue) {
        return;
      }
      var D = this.paginator.getTotalPages(),
        C = this.current ? this.current.parentNode : null;
      if (this.paginator.getCurrentPage() !== D) {
        if (C && this.current === this.span) {
          C.replaceChild(this.link, this.current);
          this.current = this.link;
        }
      } else {
        if (this.current === this.link) {
          if (C) {
            C.replaceChild(this.span, this.current);
            this.current = this.span;
          }
        }
      }
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.link);
      this.current.parentNode.removeChild(this.current);
      this.link = this.span = null;
    },
    onClick: function (C) {
      YAHOO.util.Event.stopEvent(C);
      this.paginator.setPage(this.paginator.getNextPage());
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.PreviousPageLink = function (C) {
    this.paginator = C;
    C.subscribe("recordOffsetChange", this.update, this, true);
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("totalRecordsChange", this.update, this, true);
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("previousPageLinkLabelChange", this.update, this, true);
    C.subscribe("previousPageLinkClassChange", this.update, this, true);
  };
  B.ui.PreviousPageLink.init = function (C) {
    C.setAttributeConfig("previousPageLinkLabel", {
      value: "&lt;&nbsp;prev",
      validator: A.isString,
    });
    C.setAttributeConfig("previousPageLinkClass", {
      value: "yui-pg-previous",
      validator: A.isString,
    });
  };
  B.ui.PreviousPageLink.prototype = {
    current: null,
    link: null,
    span: null,
    render: function (D) {
      var E = this.paginator,
        F = E.get("previousPageLinkClass"),
        C = E.get("previousPageLinkLabel");
      this.link = document.createElement("a");
      this.span = document.createElement("span");
      this.link.id = D + "-prev-link";
      this.link.href = "#";
      this.link.className = F;
      this.link.innerHTML = C;
      YAHOO.util.Event.on(this.link, "click", this.onClick, this, true);
      this.span.id = D + "-prev-span";
      this.span.className = F;
      this.span.innerHTML = C;
      this.current = E.getCurrentPage() > 1 ? this.link : this.span;
      return this.current;
    },
    update: function (D) {
      if (D && D.prevValue === D.newValue) {
        return;
      }
      var C = this.current ? this.current.parentNode : null;
      if (this.paginator.getCurrentPage() > 1) {
        if (C && this.current === this.span) {
          C.replaceChild(this.link, this.current);
          this.current = this.link;
        }
      } else {
        if (C && this.current === this.link) {
          C.replaceChild(this.span, this.current);
          this.current = this.span;
        }
      }
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.link);
      this.current.parentNode.removeChild(this.current);
      this.link = this.span = null;
    },
    onClick: function (C) {
      YAHOO.util.Event.stopEvent(C);
      this.paginator.setPage(this.paginator.getPreviousPage());
    },
  };
})();
(function () {
  var B = YAHOO.widget.Paginator,
    A = YAHOO.lang;
  B.ui.RowsPerPageDropdown = function (C) {
    this.paginator = C;
    C.subscribe("rowsPerPageChange", this.update, this, true);
    C.subscribe("rowsPerPageOptionsChange", this.rebuild, this, true);
    C.subscribe(
      "totalRecordsChange",
      this._handleTotalRecordsChange,
      this,
      true,
    );
    C.subscribe("destroy", this.destroy, this, true);
    C.subscribe("rowsPerPageDropdownClassChange", this.rebuild, this, true);
  };
  B.ui.RowsPerPageDropdown.init = function (C) {
    C.setAttributeConfig("rowsPerPageOptions", {
      value: [],
      validator: A.isArray,
    });
    C.setAttributeConfig("rowsPerPageDropdownClass", {
      value: "yui-pg-rpp-options",
      validator: A.isString,
    });
  };
  B.ui.RowsPerPageDropdown.prototype = {
    select: null,
    all: null,
    render: function (C) {
      this.select = document.createElement("select");
      this.select.id = C + "-rpp";
      this.select.className = this.paginator.get("rowsPerPageDropdownClass");
      this.select.title = "Rows per page";
      YAHOO.util.Event.on(this.select, "change", this.onChange, this, true);
      this.rebuild();
      return this.select;
    },
    rebuild: function (J) {
      var C = this.paginator,
        E = this.select,
        K = C.get("rowsPerPageOptions"),
        D,
        I,
        F,
        G,
        H;
      this.all = null;
      for (G = 0, H = K.length; G < H; ++G) {
        I = K[G];
        D = E.options[G] || E.appendChild(document.createElement("option"));
        F = A.isValue(I.value) ? I.value : I;
        D.innerHTML = A.isValue(I.text) ? I.text : I;
        if (A.isString(F) && F.toLowerCase() === "all") {
          this.all = D;
          D.value = C.get("totalRecords");
        } else {
          D.value = F;
        }
      }
      while (E.options.length > K.length) {
        E.removeChild(E.firstChild);
      }
      this.update();
    },
    update: function (G) {
      if (G && G.prevValue === G.newValue) {
        return;
      }
      var F = this.paginator.get("rowsPerPage") + "",
        D = this.select.options,
        E,
        C;
      for (E = 0, C = D.length; E < C; ++E) {
        if (D[E].value === F) {
          D[E].selected = true;
          break;
        }
      }
    },
    onChange: function (C) {
      this.paginator.setRowsPerPage(
        parseInt(this.select.options[this.select.selectedIndex].value, 10),
      );
    },
    _handleTotalRecordsChange: function (C) {
      if (!this.all || (C && C.prevValue === C.newValue)) {
        return;
      }
      this.all.value = C.newValue;
      if (this.all.selected) {
        this.paginator.set("rowsPerPage", C.newValue);
      }
    },
    destroy: function () {
      YAHOO.util.Event.purgeElement(this.select);
      this.select.parentNode.removeChild(this.select);
      this.select = null;
    },
  };
})();
YAHOO.register("paginator", YAHOO.widget.Paginator, {
  version: "2.7.0",
  build: "1799",
});
