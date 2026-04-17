var Xe = Object.defineProperty;
var Ge = (a, e, t) => e in a ? Xe(a, e, { enumerable: !0, configurable: !0, writable: !0, value: t }) : a[e] = t;
var f = (a, e, t) => Ge(a, typeof e != "symbol" ? e + "" : e, t);
import $ from "@typo3/backend/notification.js";
import v from "@typo3/backend/modal.js";
import U from "@typo3/backend/severity.js";
/**
 * @license
 * Copyright 2019 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const X = globalThis, ge = X.ShadowRoot && (X.ShadyCSS === void 0 || X.ShadyCSS.nativeShadow) && "adoptedStyleSheets" in Document.prototype && "replace" in CSSStyleSheet.prototype, ye = Symbol(), $e = /* @__PURE__ */ new WeakMap();
let Ue = class {
  constructor(e, t, i) {
    if (this._$cssResult$ = !0, i !== ye) throw Error("CSSResult is not constructable. Use `unsafeCSS` or `css` instead.");
    this.cssText = e, this.t = t;
  }
  get styleSheet() {
    let e = this.o;
    const t = this.t;
    if (ge && e === void 0) {
      const i = t !== void 0 && t.length === 1;
      i && (e = $e.get(t)), e === void 0 && ((this.o = e = new CSSStyleSheet()).replaceSync(this.cssText), i && $e.set(t, e));
    }
    return e;
  }
  toString() {
    return this.cssText;
  }
};
const Je = (a) => new Ue(typeof a == "string" ? a : a + "", void 0, ye), x = (a, ...e) => {
  const t = a.length === 1 ? a[0] : e.reduce((i, r, s) => i + ((n) => {
    if (n._$cssResult$ === !0) return n.cssText;
    if (typeof n == "number") return n;
    throw Error("Value passed to 'css' function must be a 'css' function result: " + n + ". Use 'unsafeCSS' to pass non-literal values, but take care to ensure page security.");
  })(r) + a[s + 1], a[0]);
  return new Ue(t, a, ye);
}, Ke = (a, e) => {
  if (ge) a.adoptedStyleSheets = e.map((t) => t instanceof CSSStyleSheet ? t : t.styleSheet);
  else for (const t of e) {
    const i = document.createElement("style"), r = X.litNonce;
    r !== void 0 && i.setAttribute("nonce", r), i.textContent = t.cssText, a.appendChild(i);
  }
}, xe = ge ? (a) => a : (a) => a instanceof CSSStyleSheet ? ((e) => {
  let t = "";
  for (const i of e.cssRules) t += i.cssText;
  return Je(t);
})(a) : a;
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const { is: Ze, defineProperty: Qe, getOwnPropertyDescriptor: et, getOwnPropertyNames: tt, getOwnPropertySymbols: it, getPrototypeOf: rt } = Object, A = globalThis, we = A.trustedTypes, st = we ? we.emptyScript : "", K = A.reactiveElementPolyfillSupport, q = (a, e) => a, ie = { toAttribute(a, e) {
  switch (e) {
    case Boolean:
      a = a ? st : null;
      break;
    case Object:
    case Array:
      a = a == null ? a : JSON.stringify(a);
  }
  return a;
}, fromAttribute(a, e) {
  let t = a;
  switch (e) {
    case Boolean:
      t = a !== null;
      break;
    case Number:
      t = a === null ? null : Number(a);
      break;
    case Object:
    case Array:
      try {
        t = JSON.parse(a);
      } catch {
        t = null;
      }
  }
  return t;
} }, ze = (a, e) => !Ze(a, e), Ce = { attribute: !0, type: String, converter: ie, reflect: !1, useDefault: !1, hasChanged: ze };
Symbol.metadata ?? (Symbol.metadata = Symbol("metadata")), A.litPropertyMetadata ?? (A.litPropertyMetadata = /* @__PURE__ */ new WeakMap());
let O = class extends HTMLElement {
  static addInitializer(e) {
    this._$Ei(), (this.l ?? (this.l = [])).push(e);
  }
  static get observedAttributes() {
    return this.finalize(), this._$Eh && [...this._$Eh.keys()];
  }
  static createProperty(e, t = Ce) {
    if (t.state && (t.attribute = !1), this._$Ei(), this.prototype.hasOwnProperty(e) && ((t = Object.create(t)).wrapped = !0), this.elementProperties.set(e, t), !t.noAccessor) {
      const i = Symbol(), r = this.getPropertyDescriptor(e, i, t);
      r !== void 0 && Qe(this.prototype, e, r);
    }
  }
  static getPropertyDescriptor(e, t, i) {
    const { get: r, set: s } = et(this.prototype, e) ?? { get() {
      return this[t];
    }, set(n) {
      this[t] = n;
    } };
    return { get: r, set(n) {
      const o = r == null ? void 0 : r.call(this);
      s == null || s.call(this, n), this.requestUpdate(e, o, i);
    }, configurable: !0, enumerable: !0 };
  }
  static getPropertyOptions(e) {
    return this.elementProperties.get(e) ?? Ce;
  }
  static _$Ei() {
    if (this.hasOwnProperty(q("elementProperties"))) return;
    const e = rt(this);
    e.finalize(), e.l !== void 0 && (this.l = [...e.l]), this.elementProperties = new Map(e.elementProperties);
  }
  static finalize() {
    if (this.hasOwnProperty(q("finalized"))) return;
    if (this.finalized = !0, this._$Ei(), this.hasOwnProperty(q("properties"))) {
      const t = this.properties, i = [...tt(t), ...it(t)];
      for (const r of i) this.createProperty(r, t[r]);
    }
    const e = this[Symbol.metadata];
    if (e !== null) {
      const t = litPropertyMetadata.get(e);
      if (t !== void 0) for (const [i, r] of t) this.elementProperties.set(i, r);
    }
    this._$Eh = /* @__PURE__ */ new Map();
    for (const [t, i] of this.elementProperties) {
      const r = this._$Eu(t, i);
      r !== void 0 && this._$Eh.set(r, t);
    }
    this.elementStyles = this.finalizeStyles(this.styles);
  }
  static finalizeStyles(e) {
    const t = [];
    if (Array.isArray(e)) {
      const i = new Set(e.flat(1 / 0).reverse());
      for (const r of i) t.unshift(xe(r));
    } else e !== void 0 && t.push(xe(e));
    return t;
  }
  static _$Eu(e, t) {
    const i = t.attribute;
    return i === !1 ? void 0 : typeof i == "string" ? i : typeof e == "string" ? e.toLowerCase() : void 0;
  }
  constructor() {
    super(), this._$Ep = void 0, this.isUpdatePending = !1, this.hasUpdated = !1, this._$Em = null, this._$Ev();
  }
  _$Ev() {
    var e;
    this._$ES = new Promise((t) => this.enableUpdating = t), this._$AL = /* @__PURE__ */ new Map(), this._$E_(), this.requestUpdate(), (e = this.constructor.l) == null || e.forEach((t) => t(this));
  }
  addController(e) {
    var t;
    (this._$EO ?? (this._$EO = /* @__PURE__ */ new Set())).add(e), this.renderRoot !== void 0 && this.isConnected && ((t = e.hostConnected) == null || t.call(e));
  }
  removeController(e) {
    var t;
    (t = this._$EO) == null || t.delete(e);
  }
  _$E_() {
    const e = /* @__PURE__ */ new Map(), t = this.constructor.elementProperties;
    for (const i of t.keys()) this.hasOwnProperty(i) && (e.set(i, this[i]), delete this[i]);
    e.size > 0 && (this._$Ep = e);
  }
  createRenderRoot() {
    const e = this.shadowRoot ?? this.attachShadow(this.constructor.shadowRootOptions);
    return Ke(e, this.constructor.elementStyles), e;
  }
  connectedCallback() {
    var e;
    this.renderRoot ?? (this.renderRoot = this.createRenderRoot()), this.enableUpdating(!0), (e = this._$EO) == null || e.forEach((t) => {
      var i;
      return (i = t.hostConnected) == null ? void 0 : i.call(t);
    });
  }
  enableUpdating(e) {
  }
  disconnectedCallback() {
    var e;
    (e = this._$EO) == null || e.forEach((t) => {
      var i;
      return (i = t.hostDisconnected) == null ? void 0 : i.call(t);
    });
  }
  attributeChangedCallback(e, t, i) {
    this._$AK(e, i);
  }
  _$ET(e, t) {
    var s;
    const i = this.constructor.elementProperties.get(e), r = this.constructor._$Eu(e, i);
    if (r !== void 0 && i.reflect === !0) {
      const n = (((s = i.converter) == null ? void 0 : s.toAttribute) !== void 0 ? i.converter : ie).toAttribute(t, i.type);
      this._$Em = e, n == null ? this.removeAttribute(r) : this.setAttribute(r, n), this._$Em = null;
    }
  }
  _$AK(e, t) {
    var s, n;
    const i = this.constructor, r = i._$Eh.get(e);
    if (r !== void 0 && this._$Em !== r) {
      const o = i.getPropertyOptions(r), l = typeof o.converter == "function" ? { fromAttribute: o.converter } : ((s = o.converter) == null ? void 0 : s.fromAttribute) !== void 0 ? o.converter : ie;
      this._$Em = r;
      const c = l.fromAttribute(t, o.type);
      this[r] = c ?? ((n = this._$Ej) == null ? void 0 : n.get(r)) ?? c, this._$Em = null;
    }
  }
  requestUpdate(e, t, i, r = !1, s) {
    var n;
    if (e !== void 0) {
      const o = this.constructor;
      if (r === !1 && (s = this[e]), i ?? (i = o.getPropertyOptions(e)), !((i.hasChanged ?? ze)(s, t) || i.useDefault && i.reflect && s === ((n = this._$Ej) == null ? void 0 : n.get(e)) && !this.hasAttribute(o._$Eu(e, i)))) return;
      this.C(e, t, i);
    }
    this.isUpdatePending === !1 && (this._$ES = this._$EP());
  }
  C(e, t, { useDefault: i, reflect: r, wrapped: s }, n) {
    i && !(this._$Ej ?? (this._$Ej = /* @__PURE__ */ new Map())).has(e) && (this._$Ej.set(e, n ?? t ?? this[e]), s !== !0 || n !== void 0) || (this._$AL.has(e) || (this.hasUpdated || i || (t = void 0), this._$AL.set(e, t)), r === !0 && this._$Em !== e && (this._$Eq ?? (this._$Eq = /* @__PURE__ */ new Set())).add(e));
  }
  async _$EP() {
    this.isUpdatePending = !0;
    try {
      await this._$ES;
    } catch (t) {
      Promise.reject(t);
    }
    const e = this.scheduleUpdate();
    return e != null && await e, !this.isUpdatePending;
  }
  scheduleUpdate() {
    return this.performUpdate();
  }
  performUpdate() {
    var i;
    if (!this.isUpdatePending) return;
    if (!this.hasUpdated) {
      if (this.renderRoot ?? (this.renderRoot = this.createRenderRoot()), this._$Ep) {
        for (const [s, n] of this._$Ep) this[s] = n;
        this._$Ep = void 0;
      }
      const r = this.constructor.elementProperties;
      if (r.size > 0) for (const [s, n] of r) {
        const { wrapped: o } = n, l = this[s];
        o !== !0 || this._$AL.has(s) || l === void 0 || this.C(s, void 0, n, l);
      }
    }
    let e = !1;
    const t = this._$AL;
    try {
      e = this.shouldUpdate(t), e ? (this.willUpdate(t), (i = this._$EO) == null || i.forEach((r) => {
        var s;
        return (s = r.hostUpdate) == null ? void 0 : s.call(r);
      }), this.update(t)) : this._$EM();
    } catch (r) {
      throw e = !1, this._$EM(), r;
    }
    e && this._$AE(t);
  }
  willUpdate(e) {
  }
  _$AE(e) {
    var t;
    (t = this._$EO) == null || t.forEach((i) => {
      var r;
      return (r = i.hostUpdated) == null ? void 0 : r.call(i);
    }), this.hasUpdated || (this.hasUpdated = !0, this.firstUpdated(e)), this.updated(e);
  }
  _$EM() {
    this._$AL = /* @__PURE__ */ new Map(), this.isUpdatePending = !1;
  }
  get updateComplete() {
    return this.getUpdateComplete();
  }
  getUpdateComplete() {
    return this._$ES;
  }
  shouldUpdate(e) {
    return !0;
  }
  update(e) {
    this._$Eq && (this._$Eq = this._$Eq.forEach((t) => this._$ET(t, this[t]))), this._$EM();
  }
  updated(e) {
  }
  firstUpdated(e) {
  }
};
O.elementStyles = [], O.shadowRootOptions = { mode: "open" }, O[q("elementProperties")] = /* @__PURE__ */ new Map(), O[q("finalized")] = /* @__PURE__ */ new Map(), K == null || K({ ReactiveElement: O }), (A.reactiveElementVersions ?? (A.reactiveElementVersions = [])).push("2.1.2");
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const F = globalThis, Se = (a) => a, G = F.trustedTypes, ke = G ? G.createPolicy("lit-html", { createHTML: (a) => a }) : void 0, De = "$lit$", k = `lit$${Math.random().toFixed(9).slice(2)}$`, Be = "?" + k, nt = `<${Be}>`, I = document, H = () => I.createComment(""), j = (a) => a === null || typeof a != "object" && typeof a != "function", ve = Array.isArray, at = (a) => ve(a) || typeof (a == null ? void 0 : a[Symbol.iterator]) == "function", Z = `[ 	
\f\r]`, B = /<(?:(!--|\/[^a-zA-Z])|(\/?[a-zA-Z][^>\s]*)|(\/?$))/g, Ae = /-->/g, Ee = />/g, P = RegExp(`>|${Z}(?:([^\\s"'>=/]+)(${Z}*=${Z}*(?:[^ 	
\f\r"'\`<>=]|("|')|))|$)`, "g"), Pe = /'/g, Te = /"/g, Ve = /^(?:script|style|textarea|title)$/i, We = (a) => (e, ...t) => ({ _$litType$: a, strings: e, values: t }), p = We(1), re = We(2), L = Symbol.for("lit-noChange"), g = Symbol.for("lit-nothing"), Ne = /* @__PURE__ */ new WeakMap(), N = I.createTreeWalker(I, 129);
function qe(a, e) {
  if (!ve(a) || !a.hasOwnProperty("raw")) throw Error("invalid template strings array");
  return ke !== void 0 ? ke.createHTML(e) : e;
}
const ot = (a, e) => {
  const t = a.length - 1, i = [];
  let r, s = e === 2 ? "<svg>" : e === 3 ? "<math>" : "", n = B;
  for (let o = 0; o < t; o++) {
    const l = a[o];
    let c, b, d = -1, m = 0;
    for (; m < l.length && (n.lastIndex = m, b = n.exec(l), b !== null); ) m = n.lastIndex, n === B ? b[1] === "!--" ? n = Ae : b[1] !== void 0 ? n = Ee : b[2] !== void 0 ? (Ve.test(b[2]) && (r = RegExp("</" + b[2], "g")), n = P) : b[3] !== void 0 && (n = P) : n === P ? b[0] === ">" ? (n = r ?? B, d = -1) : b[1] === void 0 ? d = -2 : (d = n.lastIndex - b[2].length, c = b[1], n = b[3] === void 0 ? P : b[3] === '"' ? Te : Pe) : n === Te || n === Pe ? n = P : n === Ae || n === Ee ? n = B : (n = P, r = void 0);
    const h = n === P && a[o + 1].startsWith("/>") ? " " : "";
    s += n === B ? l + nt : d >= 0 ? (i.push(c), l.slice(0, d) + De + l.slice(d) + k + h) : l + k + (d === -2 ? o : h);
  }
  return [qe(a, s + (a[t] || "<?>") + (e === 2 ? "</svg>" : e === 3 ? "</math>" : "")), i];
};
class Y {
  constructor({ strings: e, _$litType$: t }, i) {
    let r;
    this.parts = [];
    let s = 0, n = 0;
    const o = e.length - 1, l = this.parts, [c, b] = ot(e, t);
    if (this.el = Y.createElement(c, i), N.currentNode = this.el.content, t === 2 || t === 3) {
      const d = this.el.content.firstChild;
      d.replaceWith(...d.childNodes);
    }
    for (; (r = N.nextNode()) !== null && l.length < o; ) {
      if (r.nodeType === 1) {
        if (r.hasAttributes()) for (const d of r.getAttributeNames()) if (d.endsWith(De)) {
          const m = b[n++], h = r.getAttribute(d).split(k), u = /([.?@])?(.*)/.exec(m);
          l.push({ type: 1, index: s, name: u[2], strings: h, ctor: u[1] === "." ? dt : u[1] === "?" ? ct : u[1] === "@" ? pt : J }), r.removeAttribute(d);
        } else d.startsWith(k) && (l.push({ type: 6, index: s }), r.removeAttribute(d));
        if (Ve.test(r.tagName)) {
          const d = r.textContent.split(k), m = d.length - 1;
          if (m > 0) {
            r.textContent = G ? G.emptyScript : "";
            for (let h = 0; h < m; h++) r.append(d[h], H()), N.nextNode(), l.push({ type: 2, index: ++s });
            r.append(d[m], H());
          }
        }
      } else if (r.nodeType === 8) if (r.data === Be) l.push({ type: 2, index: s });
      else {
        let d = -1;
        for (; (d = r.data.indexOf(k, d + 1)) !== -1; ) l.push({ type: 7, index: s }), d += k.length - 1;
      }
      s++;
    }
  }
  static createElement(e, t) {
    const i = I.createElement("template");
    return i.innerHTML = e, i;
  }
}
function z(a, e, t = a, i) {
  var n, o;
  if (e === L) return e;
  let r = i !== void 0 ? (n = t._$Co) == null ? void 0 : n[i] : t._$Cl;
  const s = j(e) ? void 0 : e._$litDirective$;
  return (r == null ? void 0 : r.constructor) !== s && ((o = r == null ? void 0 : r._$AO) == null || o.call(r, !1), s === void 0 ? r = void 0 : (r = new s(a), r._$AT(a, t, i)), i !== void 0 ? (t._$Co ?? (t._$Co = []))[i] = r : t._$Cl = r), r !== void 0 && (e = z(a, r._$AS(a, e.values), r, i)), e;
}
class lt {
  constructor(e, t) {
    this._$AV = [], this._$AN = void 0, this._$AD = e, this._$AM = t;
  }
  get parentNode() {
    return this._$AM.parentNode;
  }
  get _$AU() {
    return this._$AM._$AU;
  }
  u(e) {
    const { el: { content: t }, parts: i } = this._$AD, r = ((e == null ? void 0 : e.creationScope) ?? I).importNode(t, !0);
    N.currentNode = r;
    let s = N.nextNode(), n = 0, o = 0, l = i[0];
    for (; l !== void 0; ) {
      if (n === l.index) {
        let c;
        l.type === 2 ? c = new D(s, s.nextSibling, this, e) : l.type === 1 ? c = new l.ctor(s, l.name, l.strings, this, e) : l.type === 6 && (c = new ut(s, this, e)), this._$AV.push(c), l = i[++o];
      }
      n !== (l == null ? void 0 : l.index) && (s = N.nextNode(), n++);
    }
    return N.currentNode = I, r;
  }
  p(e) {
    let t = 0;
    for (const i of this._$AV) i !== void 0 && (i.strings !== void 0 ? (i._$AI(e, i, t), t += i.strings.length - 2) : i._$AI(e[t])), t++;
  }
}
class D {
  get _$AU() {
    var e;
    return ((e = this._$AM) == null ? void 0 : e._$AU) ?? this._$Cv;
  }
  constructor(e, t, i, r) {
    this.type = 2, this._$AH = g, this._$AN = void 0, this._$AA = e, this._$AB = t, this._$AM = i, this.options = r, this._$Cv = (r == null ? void 0 : r.isConnected) ?? !0;
  }
  get parentNode() {
    let e = this._$AA.parentNode;
    const t = this._$AM;
    return t !== void 0 && (e == null ? void 0 : e.nodeType) === 11 && (e = t.parentNode), e;
  }
  get startNode() {
    return this._$AA;
  }
  get endNode() {
    return this._$AB;
  }
  _$AI(e, t = this) {
    e = z(this, e, t), j(e) ? e === g || e == null || e === "" ? (this._$AH !== g && this._$AR(), this._$AH = g) : e !== this._$AH && e !== L && this._(e) : e._$litType$ !== void 0 ? this.$(e) : e.nodeType !== void 0 ? this.T(e) : at(e) ? this.k(e) : this._(e);
  }
  O(e) {
    return this._$AA.parentNode.insertBefore(e, this._$AB);
  }
  T(e) {
    this._$AH !== e && (this._$AR(), this._$AH = this.O(e));
  }
  _(e) {
    this._$AH !== g && j(this._$AH) ? this._$AA.nextSibling.data = e : this.T(I.createTextNode(e)), this._$AH = e;
  }
  $(e) {
    var s;
    const { values: t, _$litType$: i } = e, r = typeof i == "number" ? this._$AC(e) : (i.el === void 0 && (i.el = Y.createElement(qe(i.h, i.h[0]), this.options)), i);
    if (((s = this._$AH) == null ? void 0 : s._$AD) === r) this._$AH.p(t);
    else {
      const n = new lt(r, this), o = n.u(this.options);
      n.p(t), this.T(o), this._$AH = n;
    }
  }
  _$AC(e) {
    let t = Ne.get(e.strings);
    return t === void 0 && Ne.set(e.strings, t = new Y(e)), t;
  }
  k(e) {
    ve(this._$AH) || (this._$AH = [], this._$AR());
    const t = this._$AH;
    let i, r = 0;
    for (const s of e) r === t.length ? t.push(i = new D(this.O(H()), this.O(H()), this, this.options)) : i = t[r], i._$AI(s), r++;
    r < t.length && (this._$AR(i && i._$AB.nextSibling, r), t.length = r);
  }
  _$AR(e = this._$AA.nextSibling, t) {
    var i;
    for ((i = this._$AP) == null ? void 0 : i.call(this, !1, !0, t); e !== this._$AB; ) {
      const r = Se(e).nextSibling;
      Se(e).remove(), e = r;
    }
  }
  setConnected(e) {
    var t;
    this._$AM === void 0 && (this._$Cv = e, (t = this._$AP) == null || t.call(this, e));
  }
}
class J {
  get tagName() {
    return this.element.tagName;
  }
  get _$AU() {
    return this._$AM._$AU;
  }
  constructor(e, t, i, r, s) {
    this.type = 1, this._$AH = g, this._$AN = void 0, this.element = e, this.name = t, this._$AM = r, this.options = s, i.length > 2 || i[0] !== "" || i[1] !== "" ? (this._$AH = Array(i.length - 1).fill(new String()), this.strings = i) : this._$AH = g;
  }
  _$AI(e, t = this, i, r) {
    const s = this.strings;
    let n = !1;
    if (s === void 0) e = z(this, e, t, 0), n = !j(e) || e !== this._$AH && e !== L, n && (this._$AH = e);
    else {
      const o = e;
      let l, c;
      for (e = s[0], l = 0; l < s.length - 1; l++) c = z(this, o[i + l], t, l), c === L && (c = this._$AH[l]), n || (n = !j(c) || c !== this._$AH[l]), c === g ? e = g : e !== g && (e += (c ?? "") + s[l + 1]), this._$AH[l] = c;
    }
    n && !r && this.j(e);
  }
  j(e) {
    e === g ? this.element.removeAttribute(this.name) : this.element.setAttribute(this.name, e ?? "");
  }
}
class dt extends J {
  constructor() {
    super(...arguments), this.type = 3;
  }
  j(e) {
    this.element[this.name] = e === g ? void 0 : e;
  }
}
class ct extends J {
  constructor() {
    super(...arguments), this.type = 4;
  }
  j(e) {
    this.element.toggleAttribute(this.name, !!e && e !== g);
  }
}
class pt extends J {
  constructor(e, t, i, r, s) {
    super(e, t, i, r, s), this.type = 5;
  }
  _$AI(e, t = this) {
    if ((e = z(this, e, t, 0) ?? g) === L) return;
    const i = this._$AH, r = e === g && i !== g || e.capture !== i.capture || e.once !== i.once || e.passive !== i.passive, s = e !== g && (i === g || r);
    r && this.element.removeEventListener(this.name, this, i), s && this.element.addEventListener(this.name, this, e), this._$AH = e;
  }
  handleEvent(e) {
    var t;
    typeof this._$AH == "function" ? this._$AH.call(((t = this.options) == null ? void 0 : t.host) ?? this.element, e) : this._$AH.handleEvent(e);
  }
}
class ut {
  constructor(e, t, i) {
    this.element = e, this.type = 6, this._$AN = void 0, this._$AM = t, this.options = i;
  }
  get _$AU() {
    return this._$AM._$AU;
  }
  _$AI(e) {
    z(this, e);
  }
}
const ht = { I: D }, Q = F.litHtmlPolyfillSupport;
Q == null || Q(Y, D), (F.litHtmlVersions ?? (F.litHtmlVersions = [])).push("3.3.2");
const mt = (a, e, t) => {
  const i = (t == null ? void 0 : t.renderBefore) ?? e;
  let r = i._$litPart$;
  if (r === void 0) {
    const s = (t == null ? void 0 : t.renderBefore) ?? null;
    i._$litPart$ = r = new D(e.insertBefore(H(), s), s, void 0, t ?? {});
  }
  return r._$AI(a), r;
};
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const M = globalThis;
let w = class extends O {
  constructor() {
    super(...arguments), this.renderOptions = { host: this }, this._$Do = void 0;
  }
  createRenderRoot() {
    var t;
    const e = super.createRenderRoot();
    return (t = this.renderOptions).renderBefore ?? (t.renderBefore = e.firstChild), e;
  }
  update(e) {
    const t = this.render();
    this.hasUpdated || (this.renderOptions.isConnected = this.isConnected), super.update(e), this._$Do = mt(t, this.renderRoot, this.renderOptions);
  }
  connectedCallback() {
    var e;
    super.connectedCallback(), (e = this._$Do) == null || e.setConnected(!0);
  }
  disconnectedCallback() {
    var e;
    super.disconnectedCallback(), (e = this._$Do) == null || e.setConnected(!1);
  }
  render() {
    return L;
  }
};
var Oe;
w._$litElement$ = !0, w.finalized = !0, (Oe = M.litElementHydrateSupport) == null || Oe.call(M, { LitElement: w });
const ee = M.litElementPolyfillSupport;
ee == null || ee({ LitElement: w });
(M.litElementVersions ?? (M.litElementVersions = [])).push("4.2.2");
class se extends w {
  connectedCallback() {
    super.connectedCallback(), this.addEventListener("pointerdown", this._onPointerDown.bind(this));
  }
  updated(e) {
    if (e.has("type")) {
      const t = { input: "Input terminal", output: "Output terminal" };
      this.setAttribute("aria-label", t[this.type] ?? "Terminal"), this.setAttribute("role", "img");
    }
  }
  _onPointerDown(e) {
    this.droppable || (e.stopPropagation(), this.dispatchEvent(
      new CustomEvent("terminal-connect", {
        bubbles: !0,
        composed: !0,
        detail: {
          terminalId: this.terminalId,
          uid: this.uid,
          sourceEl: this
        }
      })
    ));
  }
  getCenter() {
    const e = this.getBoundingClientRect(), t = this._getLayerRect();
    return {
      x: e.left - t.left + e.width / 2,
      y: e.top - t.top + e.height / 2
    };
  }
  _getLayerRect() {
    let e = this.parentElement;
    for (; e && e.tagName !== "EB-LAYER"; )
      e = e.parentElement;
    return e ? e.getBoundingClientRect() : { left: 0, top: 0 };
  }
  render() {
    return p``;
  }
}
f(se, "properties", {
  type: { type: String },
  terminalId: { type: String, attribute: "terminal-id" },
  uid: { type: String },
  droppable: { type: Boolean }
}), f(se, "styles", x`
        :host {
            display: block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--eb-terminal-default, #4a90d9);
            border: 2px solid var(--eb-terminal-default-border, #2c5f8a);
            cursor: crosshair;
            position: absolute;
        }
        :host([type='input']) {
            background: var(--eb-terminal-input, #5cb85c);
            border-color: var(--eb-terminal-input-border, #3d7a3d);
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        :host([type='input']:hover) {
            transform: translateX(-50%) scale(1.3);
        }
        :host([type='output']) {
            background: var(--eb-terminal-output, #d9534f);
            border-color: var(--eb-terminal-output-border, #8a2c2c);
        }
        :host([droppable]) {
            position: relative;
            flex-shrink: 0;
            align-self: center;
            background: var(--eb-terminal-relation, #6ea8fe);
            border-color: var(--eb-terminal-relation-border, #3d7bd4);
            transform: none;
        }
        :host([droppable]:hover) {
            transform: scale(1.3);
            box-shadow: 0 0 0 3px rgba(110, 168, 254, 0.35);
        }
    `);
customElements.define("eb-terminal", se);
class ne extends w {
  getPath() {
    const { x1: e, y1: t, x2: i, y2: r } = this, s = t + 80, n = r - 80;
    return `M ${e} ${t} C ${e} ${s}, ${i} ${n}, ${i} ${r}`;
  }
  serialize() {
    return {
      src: { moduleId: this.srcModuleId, terminal: this.srcTerminal, uid: this.srcUid },
      tgt: { moduleId: this.tgtModuleId, terminal: this.tgtTerminal, uid: this.tgtUid }
    };
  }
  render() {
    return re`
            <path
                d="${this.getPath()}"
                stroke-width="2"
                fill="none"
                stroke-linecap="round"
                aria-hidden="true"
            />
        `;
  }
}
f(ne, "properties", {
  x1: { type: Number },
  y1: { type: Number },
  x2: { type: Number },
  y2: { type: Number },
  srcUid: { type: String, attribute: "src-uid" },
  tgtUid: { type: String, attribute: "tgt-uid" },
  srcTerminal: { type: String, attribute: "src-terminal" },
  tgtTerminal: { type: String, attribute: "tgt-terminal" },
  srcModuleId: { type: Number, attribute: "src-module-id" },
  tgtModuleId: { type: Number, attribute: "tgt-module-id" }
}), f(ne, "styles", x`
        :host {
            display: contents;
        }
        path {
            stroke: var(--eb-wire-color, #4a90d9);
        }
    `);
customElements.define("eb-wire", ne);
function _(a) {
  var i, r, s;
  if (!a)
    return "";
  const e = a.replace(/\./g, "_"), t = (s = (r = (i = window.TYPO3) == null ? void 0 : i.settings) == null ? void 0 : r.extensionBuilder) == null ? void 0 : s._LOCAL_LANG;
  return t != null && t[e] ? t[e] : a.replace(/_/g, " ").replace(/([A-Z])/g, " $1").trim().replace(/\b\w/g, (n) => n.toUpperCase());
}
function Fe(a) {
  var i, r, s, n;
  const e = a.inputParams ?? {}, t = a.type;
  if (!t || (i = e.className) != null && i.includes("hiddenField"))
    return p`<eb-hidden-field name="${e.name}"></eb-hidden-field>`;
  if (e.wirable)
    return g;
  switch (t) {
    case "string":
      return p`<eb-string-field
                name="${e.name}"
                label="${_(e.label ?? "")}"
                ?required="${e.required}"
                ?advanced="${e.advancedMode || !1}"
                description="${_(e.description ?? "")}"
                help-link="${e.helpLink ?? ""}"
                type-invite="${e.typeInvite ?? ""}"
                placeholder="${e.placeholder ?? ""}"
                .value="${e.value ?? ""}"
                ?force-alpha-numeric="${e.forceAlphaNumeric}"
                ?force-alpha-numeric-underscore="${e.forceAlphaNumericUnderscore}"
                ?force-lower-case="${e.forceLowerCase}"
                ?no-spaces="${e.noSpaces}"
                ?uc-first="${e.ucFirst}"
                ?lc-first="${e.lcFirst}"
                ?first-char-non-numeric="${e.firstCharNonNumeric}"
                ?no-leading-underscore="${e.noLeadingUnderscore}"
                ?no-trailing-underscore="${e.noTrailingUnderscore}"
                forbidden-prefixes="${e.forbiddenPrefixes ?? ""}"
                min-length="${e.minLength ?? ""}"
                max-length="${e.maxLength ?? ""}"
                data-visible-for="${((r = e.visibleForTypes) == null ? void 0 : r.join(" ")) ?? ""}"
            ></eb-string-field>`;
    case "text":
      return p`<eb-textarea-field
                name="${e.name}"
                label="${_(e.label ?? "")}"
                ?advanced="${e.advancedMode || !1}"
                description="${_(e.description ?? "")}"
                help-link="${e.helpLink ?? ""}"
                placeholder="${e.placeholder ?? ""}"
                .value="${e.value ?? ""}"
            ></eb-textarea-field>`;
    case "select":
      return p`<eb-select-field
                name="${e.name}"
                label="${_(e.label ?? "")}"
                ?advanced="${e.advancedMode || !1}"
                description="${_(e.description ?? "")}"
                help-link="${e.helpLink ?? ""}"
                .selectValues="${e.selectValues ?? []}"
                .selectOptions="${e.selectOptions ?? []}"
                .value="${e.value ?? ((s = e.selectValues) == null ? void 0 : s[0]) ?? ""}"
            ></eb-select-field>`;
    case "boolean":
      return p`<eb-boolean-field
                name="${e.name}"
                label="${_(e.label ?? "")}"
                ?advanced="${e.advancedMode || !1}"
                description="${_(e.description ?? "")}"
                help-link="${e.helpLink ?? ""}"
                .value="${e.value ?? !1}"
                data-visible-for="${((n = e.visibleForTypes) == null ? void 0 : n.join(" ")) ?? ""}"
            ></eb-boolean-field>`;
    case "group":
      return p`<eb-group
                name="${e.name ?? ""}"
                legend="${_(e.legend ?? "")}"
                ?advanced="${e.advancedMode || !1}"
                ?collapsible="${e.collapsible}"
                ?collapsed="${e.collapsed}"
                ?flatten="${e.flatten}"
                >${_e(e.fields ?? [])}</eb-group
            >`;
    case "list":
      return p` ${e.label ? p`<label class="form-label" style="display:block;font-weight:600;margin-top:0.5rem"
                          >${_(e.label)}</label
                      >` : ""}
                <eb-list-field
                    name="${e.name}"
                    ?advanced="${e.advancedMode || !1}"
                    ?sortable="${e.sortable}"
                    add-label="${_("add")}"
                    element-type="${JSON.stringify(e.elementType ?? {})}"
                ></eb-list-field>`;
    case "inplaceedit":
      return p`<eb-inplace-edit name="${e.name ?? ""}" .value="${e.value ?? ""}"></eb-inplace-edit>`;
    default:
      return p`<eb-string-field name="${e.name}" label="${_(e.label ?? "")}"></eb-string-field>`;
  }
}
function _e(a) {
  return a.map((e) => Fe(e));
}
const He = {
  container: {
    xtype: "WireIt.FormContainer",
    title: "Title",
    preventSelfWiring: !1,
    fields: [
      {
        type: "inplaceedit",
        inputParams: {
          name: "name",
          className: "inputEx-Field extbase-modelTitleEditor",
          editorField: {
            type: "string",
            inputParams: {
              required: !0,
              firstCharNonNumeric: !0
            }
          },
          animColors: { from: "#cccccc", to: "#cccccc" }
        }
      },
      {
        type: "group",
        inputParams: {
          collapsible: !0,
          collapsed: !0,
          legend: "domainObjectSettings",
          className: "objectSettings",
          name: "objectsettings",
          fields: [
            {
              inputParams: {
                name: "uid",
                className: "hiddenField"
              }
            },
            {
              type: "select",
              inputParams: {
                name: "type",
                advancedMode: !0,
                label: "objectType",
                description: "descr_objectType",
                selectValues: ["Entity", "ValueObject"],
                selectOptions: ["entity", "valueObject"]
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "aggregateRoot",
                label: "isAggregateRoot",
                description: "descr_isAggregateRoot",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "sorting",
                advancedMode: !0,
                label: "enableSorting",
                description: "descr_enableSorting",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "addDeletedField",
                advancedMode: !0,
                label: "addDeletedField",
                description: "descr_addDeletedField",
                value: !0
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "addHiddenField",
                advancedMode: !0,
                label: "addHiddenField",
                description: "descr_addHiddenField",
                value: !0
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "addStarttimeEndtimeFields",
                advancedMode: !0,
                label: "addStarttimeEndtimeFields",
                description: "descr_addStarttimeEndtimeFields",
                value: !0
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "categorizable",
                advancedMode: !0,
                label: "enableCategorizable",
                description: "descr_enableCategorizable",
                value: !1
              }
            },
            {
              type: "text",
              inputParams: {
                name: "description",
                className: "bottomBorder",
                label: "description",
                placeholder: "description",
                required: !1,
                cols: 20,
                rows: 2
              }
            },
            {
              type: "string",
              inputParams: {
                name: "mapToTable",
                advancedMode: !0,
                label: "mapToTable",
                description: "descr_mapToTable",
                required: !1
              }
            },
            {
              type: "string",
              inputParams: {
                name: "parentClass",
                advancedMode: !0,
                label: "parentClass",
                placeholder: "\\Fully\\Qualified\\Classname",
                description: "descr_parentClass",
                required: !1
              }
            }
          ]
        }
      },
      {
        type: "group",
        inputParams: {
          collapsible: !0,
          collapsed: !0,
          legend: "defaultActions",
          name: "actionGroup",
          className: "actionGroup",
          fields: [
            {
              type: "boolean",
              inputParams: {
                name: "_default0_index",
                label: "index",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "_default1_list",
                label: "list",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "_default2_show",
                label: "show",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "_default3_new_create",
                label: "create_new",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "_default4_edit_update",
                label: "edit_update",
                value: !1
              }
            },
            {
              type: "boolean",
              inputParams: {
                name: "_default5_delete",
                label: "delete",
                value: !1
              }
            },
            {
              type: "list",
              inputParams: {
                label: "Custom actions",
                name: "customActions",
                sortable: !0,
                elementType: {
                  type: "input",
                  inputParams: {
                    name: "customAction",
                    label: "customAction",
                    forceAlphaNumeric: !0,
                    firstCharNonNumeric: !0,
                    lcFirst: !0
                  }
                }
              }
            }
          ]
        }
      },
      {
        type: "group",
        inputParams: {
          collapsible: !0,
          collapsed: !0,
          className: "properties",
          legend: "properties",
          name: "propertyGroup",
          fields: [
            {
              type: "list",
              inputParams: {
                label: "",
                name: "properties",
                wirable: !1,
                sortable: !0,
                elementType: {
                  type: "group",
                  inputParams: {
                    name: "property",
                    className: "propertyGroup",
                    fields: [
                      {
                        type: "hidden",
                        inputParams: {
                          name: "uid",
                          className: "hiddenField"
                        }
                      },
                      {
                        type: "string",
                        inputParams: {
                          name: "propertyName",
                          forceAlphaNumeric: !0,
                          lcFirst: !0,
                          firstCharNonNumeric: !0,
                          placeholder: "propertyName",
                          description: "descr_propertyName",
                          required: !0
                        }
                      },
                      {
                        type: "select",
                        inputParams: {
                          name: "propertyType",
                          description: "descr_propertyType",
                          selectValues: [
                            "String",
                            "Text",
                            "RichText",
                            "Slug",
                            "ColorPicker",
                            "Password",
                            "Email",
                            "Integer",
                            "Float",
                            "Boolean",
                            "InputLink",
                            "NativeDate",
                            "NativeDateTime",
                            "Date",
                            "DateTime",
                            "NativeTime",
                            "Time",
                            "TimeSec",
                            "Select",
                            "File",
                            "Image",
                            "PassThrough",
                            "None"
                          ],
                          selectOptions: [
                            "string",
                            "text",
                            "richText",
                            "slug",
                            "colorPicker",
                            "password",
                            "email",
                            "integer",
                            "floatingPoint",
                            "boolean",
                            "inputLink",
                            "nativeDate",
                            "nativeDateTime",
                            "date",
                            "dateTime",
                            "nativeTime",
                            "time",
                            "timeSec",
                            "selectList",
                            "file",
                            "image",
                            "passThrough",
                            "none"
                          ]
                        }
                      },
                      {
                        type: "group",
                        inputParams: {
                          collapsible: !0,
                          collapsed: !0,
                          flatten: !0,
                          className: "advancedSettings",
                          name: "advancedSettings",
                          legend: "advancedOptions",
                          fields: [
                            {
                              type: "text",
                              inputParams: {
                                name: "propertyDescription",
                                placeholder: "description",
                                cols: 23,
                                rows: 2
                              }
                            },
                            {
                              type: "string",
                              inputParams: {
                                visibleForTypes: ["File"],
                                label: "allowedFileTypes",
                                description: "descr_allowedFileTypes",
                                name: "allowedFileTypes"
                              }
                            },
                            {
                              type: "string",
                              inputParams: {
                                visibleForTypes: ["File", "Image"],
                                label: "maxItems",
                                name: "maxItems",
                                value: 1
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                label: "isRequired",
                                name: "propertyIsRequired",
                                value: !1
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                visibleForTypes: [
                                  "String",
                                  "Text",
                                  "RichText",
                                  "Slug",
                                  "ColorPicker",
                                  "Password",
                                  "Email",
                                  "Integer",
                                  "Float",
                                  "Boolean",
                                  "InputLink",
                                  "NativeDate",
                                  "NativeDateTime",
                                  "Date",
                                  "DateTime",
                                  "NativeTime",
                                  "Time",
                                  "TimeSec",
                                  "Select"
                                ],
                                label: "isNullable",
                                name: "propertyIsNullable",
                                value: !1
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                label: "isExcludeField",
                                name: "propertyIsExcludeField",
                                description: "descr_isExcludeField",
                                value: !0
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                label: "isL10nModeExclude",
                                name: "propertyIsL10nModeExclude",
                                description: "descr_isL10nModeExclude",
                                value: !1
                              }
                            }
                          ]
                        }
                      }
                    ]
                  }
                }
              }
            }
          ]
        }
      },
      {
        type: "group",
        inputParams: {
          collapsible: !0,
          collapsed: !1,
          legend: "relations",
          name: "relationGroup",
          fields: [
            {
              type: "list",
              inputParams: {
                name: "relations",
                className: "relations",
                wirable: !1,
                sortable: !0,
                elementType: {
                  type: "group",
                  inputParams: {
                    name: "relation",
                    className: "relationGroup",
                    fields: [
                      {
                        type: "hidden",
                        inputParams: {
                          name: "uid",
                          className: "hiddenField"
                        }
                      },
                      {
                        type: "string",
                        inputParams: {
                          placeholder: "relationName",
                          name: "relationName",
                          forceAlphaNumeric: !0,
                          firstCharNonNumeric: !0,
                          lcFirst: !0,
                          description: "descr_relationName",
                          required: !0
                        }
                      },
                      {
                        type: "string",
                        inputParams: {
                          label: "",
                          name: "relationWire",
                          required: !1,
                          wirable: !0,
                          className: "terminalFieldWrap",
                          ddConfig: {
                            type: "input",
                            allowedTypes: ["output"]
                          }
                        }
                      },
                      {
                        type: "group",
                        inputParams: {
                          collapsible: !0,
                          flatten: !0,
                          collapsed: !0,
                          className: "advancedSettings",
                          name: "advancedSettings",
                          fields: [
                            {
                              type: "select",
                              inputParams: {
                                label: "type",
                                name: "relationType",
                                selectValues: [
                                  "zeroToOne",
                                  "zeroToMany",
                                  "manyToOne",
                                  "manyToMany"
                                ],
                                selectOptions: [
                                  "1:1 (zeroToOne)",
                                  "1:n (zeroToMany)",
                                  "n:1 (manyToOne)",
                                  "m:n (manyToMany)"
                                ]
                              }
                            },
                            {
                              type: "select",
                              inputParams: {
                                label: "renderType",
                                description: "desc_renderType",
                                wrapperClassName: "inputEx-fieldWrapper dependant renderType",
                                className: "inputEx-Field isDependant",
                                name: "renderType",
                                selectValues: [
                                  "selectSingleBox",
                                  "selectCheckBox",
                                  "selectMultipleSideBySide",
                                  "inline",
                                  "selectSingle"
                                ],
                                selectOptions: [
                                  "Single box",
                                  "Checkboxes",
                                  "Side by side multi select",
                                  "Inline (IRRE)",
                                  "Dropdown"
                                ]
                              }
                            },
                            {
                              type: "text",
                              inputParams: {
                                placeholder: "description",
                                name: "relationDescription",
                                cols: 20,
                                rows: 2
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                label: "isExcludeField",
                                name: "propertyIsExcludeField",
                                advancedMode: !0,
                                value: !0,
                                description: "descr_isExcludeField"
                              }
                            },
                            {
                              type: "boolean",
                              inputParams: {
                                label: "lazyLoading",
                                name: "lazyLoading",
                                advancedMode: !0,
                                description: "descr_lazyLoading",
                                value: !1
                              }
                            },
                            {
                              type: "string",
                              inputParams: {
                                label: "foreignRelationClass",
                                name: "foreignRelationClass",
                                placeholder: "\\Fully\\Qualified\\Classname",
                                advancedMode: !0,
                                description: "descr_foreignRelationClass"
                              }
                            }
                          ]
                        }
                      }
                    ]
                  }
                }
              }
            }
          ]
        }
      }
    ],
    terminals: [
      {
        name: "SOURCES",
        direction: [0, -1],
        offsetPosition: {
          left: 5,
          top: -2
        },
        ddConfig: {
          type: "output",
          allowedTypes: ["input"]
        }
      }
    ]
  }
}, E = x`
    eb-string-field,
    eb-textarea-field,
    eb-select-field,
    eb-boolean-field,
    eb-hidden-field {
        display: block;
        margin-bottom: 0.75rem;
    }

    .form-group {
        margin-bottom: 0.75rem;
    }

    .form-label {
        display: inline-block;
        margin-bottom: 0.25rem;
        font-size: var(--bs-body-font-size, 0.875rem);
        font-weight: 600;
        color: var(--bs-body-color, #495057);
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: var(--bs-body-font-size, 0.875rem);
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color, #495057);
        background-color: var(--bs-body-bg, #fff);
        background-clip: padding-box;
        border: 1px solid var(--bs-border-color, #ced4da);
        border-radius: var(--bs-border-radius, 0.25rem);
        box-sizing: border-box;
        transition:
            border-color 0.15s ease-in-out,
            box-shadow 0.15s ease-in-out;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-control:focus {
        border-color: var(--bs-primary, #86b7fe);
        outline: 0;
        box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary, #0d6efd) 25%, transparent);
    }

    .form-control::placeholder {
        color: var(--bs-secondary-color, #6c757d);
        opacity: 1;
    }

    textarea.form-control {
        resize: vertical;
        min-height: calc(1.5em + 0.75rem + 2px);
    }

    .form-select {
        display: block;
        width: 100%;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        font-size: var(--bs-body-font-size, 0.875rem);
        font-weight: 400;
        line-height: 1.5;
        color: var(--bs-body-color, #495057);
        background-color: var(--bs-body-bg, #fff);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        border: 1px solid var(--bs-border-color, #ced4da);
        border-radius: var(--bs-border-radius, 0.25rem);
        box-sizing: border-box;
        appearance: none;
        -webkit-appearance: none;
    }

    .form-select:focus {
        border-color: var(--bs-primary, #86b7fe);
        outline: 0;
        box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary, #0d6efd) 25%, transparent);
    }

    .form-check {
        display: block;
        min-height: 1.5rem;
        padding-left: 1.5em;
        margin-bottom: 0.125rem;
    }

    .form-check-input {
        width: 1em;
        height: 1em;
        margin-top: 0.25em;
        margin-left: -1.5em;
        vertical-align: top;
        background-color: var(--bs-body-bg, #fff);
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border: 1px solid var(--bs-border-color, #adb5bd);
        appearance: none;
        -webkit-appearance: none;
        float: left;
    }

    .form-check-input[type='checkbox'] {
        border-radius: 0.25em;
    }

    .form-check-input:checked {
        background-color: var(--bs-primary, #0d6efd);
        border-color: var(--bs-primary, #0d6efd);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
    }

    .form-check-input:focus {
        border-color: var(--bs-primary, #86b7fe);
        outline: 0;
        box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary, #0d6efd) 25%, transparent);
    }

    .form-check-label {
        cursor: pointer;
        font-size: var(--bs-body-font-size, 0.875rem);
        color: var(--bs-body-color, #495057);
    }

    .form-control.is-invalid {
        border-color: var(--bs-danger, #dc3545);
    }

    .form-control.is-invalid:focus {
        border-color: var(--bs-danger, #dc3545);
        box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-danger, #dc3545) 25%, transparent);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: var(--bs-danger, #dc3545);
    }
`;
class ae extends w {
  constructor() {
    super(), this.posX = 10, this.posY = 10, this.moduleData = {}, this._name = "", this._dragging = !1, this._dragOffsetX = 0, this._dragOffsetY = 0, this._resizeWidth = null, this._resizeHeight = null, this._resizing = !1, this._resizeStartX = 0, this._resizeStartY = 0, this._resizeStartW = 0, this._resizeStartH = 0;
  }
  updated(e) {
    var t, i;
    (e.has("posX") || e.has("posY")) && (this.style.transform = `translate(${this.posX}px, ${this.posY}px)`), (e.has("_resizeWidth") || e.has("_resizeHeight")) && (this._resizeWidth !== null && (this.style.width = `${this._resizeWidth}px`), this._resizeHeight !== null && (this.style.minHeight = `${this._resizeHeight}px`)), e.has("moduleData") && (this._name = ((i = (t = this.moduleData) == null ? void 0 : t.value) == null ? void 0 : i.name) ?? "", this._populateFromValue());
  }
  connectedCallback() {
    super.connectedCallback(), this.style.transform = `translate(${this.posX}px, ${this.posY}px)`, this.addEventListener("pointerdown", this._onPointerDown.bind(this)), this.addEventListener("pointermove", this._onPointerMove.bind(this)), this.addEventListener("pointerup", this._onPointerUp.bind(this));
  }
  _onPointerDown(e) {
    e.composedPath().some((i) => {
      var s;
      if (!(i instanceof Element))
        return !1;
      const r = i.tagName.toUpperCase();
      return !!(["BUTTON", "INPUT", "SELECT", "TEXTAREA", "A", "EB-TERMINAL", "EB-INPLACE-EDIT"].includes(r) || ((s = i.getAttribute) == null ? void 0 : s.call(i, "role")) === "button");
    }) || (e.preventDefault(), this._dragging = !0, this._dragOffsetX = e.clientX - this.posX, this._dragOffsetY = e.clientY - this.posY, this.setPointerCapture(e.pointerId));
  }
  _onPointerMove(e) {
    this._dragging && (this.posX = e.clientX - this._dragOffsetX, this.posY = e.clientY - this._dragOffsetY, this.style.transform = `translate(${this.posX}px, ${this.posY}px)`, this.dispatchEvent(
      new CustomEvent("container-moved", {
        bubbles: !0,
        composed: !0,
        detail: { moduleId: this.moduleId, x: this.posX, y: this.posY }
      })
    ));
  }
  _onPointerUp(e) {
    this._dragging && (this._dragging = !1, this.releasePointerCapture(e.pointerId));
  }
  _onNameChange(e) {
    this._name = e.detail.value;
  }
  _onDeleteClick(e) {
    e.stopPropagation(), this.dispatchEvent(
      new CustomEvent("container-removed", {
        bubbles: !0,
        composed: !0,
        detail: { moduleId: this.moduleId }
      })
    );
  }
  _onResizePointerDown(e) {
    e.stopPropagation(), e.preventDefault(), this._resizing = !0, this._resizeStartX = e.clientX, this._resizeStartY = e.clientY, this._resizeStartW = this.offsetWidth, this._resizeStartH = this.offsetHeight, e.currentTarget.setPointerCapture(e.pointerId);
  }
  _onResizePointerMove(e) {
    if (!this._resizing)
      return;
    const t = Math.min(600, Math.max(300, this._resizeStartW + (e.clientX - this._resizeStartX))), i = Math.max(80, this._resizeStartH + (e.clientY - this._resizeStartY));
    this._resizeWidth = t, this._resizeHeight = i, this.dispatchEvent(
      new CustomEvent("container-resized", {
        bubbles: !0,
        composed: !0,
        detail: { moduleId: this.moduleId, width: t, height: i }
      })
    );
  }
  _onResizePointerUp(e) {
    this._resizing && (this._resizing = !1, e.currentTarget.releasePointerCapture(e.pointerId));
  }
  get _fields() {
    return He.container.fields;
  }
  _populateFromValue() {
    var i, r;
    const e = ((i = this.moduleData) == null ? void 0 : i.value) ?? {}, t = (r = this.shadowRoot) == null ? void 0 : r.querySelector(".card-body");
    t && Array.from(t.children).forEach((s) => {
      const n = s.getAttribute("name");
      n !== null && e[n] !== void 0 && typeof s.setValue == "function" && s.setValue(e[n]);
    });
  }
  _collectValues() {
    var i;
    const e = { name: this._name ?? "" }, t = (i = this.shadowRoot) == null ? void 0 : i.querySelector(".card-body");
    return t && Array.from(t.children).forEach((r) => {
      const s = r.getAttribute("name");
      s !== null && typeof r.getValue == "function" && (e[s] = r.getValue());
    }), e;
  }
  serialize() {
    return {
      config: { position: [this.posX, this.posY] },
      value: this._collectValues()
    };
  }
  render() {
    var e, t, i;
    return p`
            <div class="card-header">
                <span class="drag-handle" aria-hidden="true">⠿</span>
                <eb-terminal
                    type="input"
                    terminal-id="SOURCES"
                    uid="${((i = (t = (e = this.moduleData) == null ? void 0 : e.value) == null ? void 0 : t.objectsettings) == null ? void 0 : i.uid) ?? ""}"
                >
                </eb-terminal>
                <eb-inplace-edit
                    name="name"
                    .value="${this._name || "New Model Object"}"
                    @inplace-change="${this._onNameChange}"
                >
                </eb-inplace-edit>
                <button class="delete-btn" @click="${this._onDeleteClick}" title="Remove model object">×</button>
            </div>
            <div class="card-body">${_e(this._fields.slice(1))}</div>
            <div
                class="resize-handle"
                @pointerdown="${this._onResizePointerDown}"
                @pointermove="${this._onResizePointerMove}"
                @pointerup="${this._onResizePointerUp}"
            ></div>
        `;
  }
}
f(ae, "properties", {
  moduleId: { type: Number, attribute: "module-id" },
  posX: { type: Number, attribute: "pos-x" },
  posY: { type: Number, attribute: "pos-y" },
  moduleData: { type: Object },
  _name: { state: !0 },
  _resizeWidth: { state: !0 },
  _resizeHeight: { state: !0 }
}), f(ae, "styles", [
  E,
  x`
            :host {
                display: block;
                position: absolute;
                min-width: 300px;
                max-width: 600px;
                background: var(--bs-body-bg, #fff);
                color: var(--bs-body-color, #000);
                border: 1px solid var(--bs-border-color, #dee2e6);
                border-radius: var(--bs-border-radius, 0.25rem);
                box-shadow: var(--bs-box-shadow-sm, 2px 2px 6px rgba(0, 0, 0, 0.15));
                user-select: none;
                cursor: grab;
            }
            :host(:active) {
                cursor: grabbing;
            }
            .card-header {
                background-color: var(--eb-brand-color, #ff8700);
                color: #fff;
                padding: 0.5rem 0.75rem;
                font-weight: bold;
                font-size: 13px;
                border-radius: calc(var(--bs-border-radius, 0.25rem) - 1px) calc(var(--bs-border-radius, 0.25rem) - 1px)
                    0 0;
                position: relative;
            }
            .card-body {
                padding: 0.5rem 0.75rem;
                font-size: 12px;
                max-height: 60vh;
                overflow-y: auto;
            }
            .delete-btn {
                position: absolute;
                top: 0.25rem;
                right: 0.25rem;
                background: none;
                border: none;
                color: rgba(255, 255, 255, 0.8);
                cursor: pointer;
                font-size: 16px;
                line-height: 1;
                padding: 0 2px;
            }
            .delete-btn:hover {
                color: #fff;
            }
            .drag-handle {
                display: inline-flex;
                align-items: center;
                margin-right: 0.4rem;
                color: rgba(255, 255, 255, 0.6);
                cursor: grab;
                font-size: 14px;
                line-height: 1;
                user-select: none;
            }
            .drag-handle:hover {
                color: #fff;
            }
            .resize-handle {
                position: absolute;
                bottom: 0;
                right: 0;
                width: 12px;
                height: 12px;
                cursor: nwse-resize;
                background: linear-gradient(
                    135deg,
                    transparent 40%,
                    var(--bs-border-color, #dee2e6) 40%,
                    var(--bs-border-color, #dee2e6) 60%,
                    transparent 60%
                );
            }
        `
]);
customElements.define("eb-container", ae);
class oe extends w {
  constructor() {
    super(), this._wires = [], this._containers = [], this._drawingWire = null, this._tempWire = null, this._hoveredWireId = null, this._panOffset = { x: 0, y: 0 }, this._isPanning = !1, this._panStartX = 0, this._panStartY = 0;
  }
  connectedCallback() {
    super.connectedCallback(), this.addEventListener("terminal-connect", this._onTerminalConnect.bind(this)), this.addEventListener("container-moved", this._onContainerMoved.bind(this)), this.addEventListener("container-removed", this._onContainerRemoved.bind(this)), this._boundPointerMove = this._onPointerMove.bind(this), this._boundPointerUp = this._onPointerUp.bind(this), window.addEventListener("pointermove", this._boundPointerMove), window.addEventListener("pointerup", this._boundPointerUp);
  }
  disconnectedCallback() {
    super.disconnectedCallback(), window.removeEventListener("pointermove", this._boundPointerMove), window.removeEventListener("pointerup", this._boundPointerUp);
  }
  _onCanvasPointerDown(e) {
    const t = this.shadowRoot.querySelector("#canvas"), i = this.shadowRoot.querySelector("#pan-surface");
    e.target !== t && e.target !== i || (this._isPanning = !0, this._panStartX = e.clientX - this._panOffset.x, this._panStartY = e.clientY - this._panOffset.y, t.style.cursor = "grabbing");
  }
  _onTerminalConnect(e) {
    var d;
    const { terminalId: t, uid: i, sourceEl: r } = e.detail, s = this.getBoundingClientRect(), n = r.getBoundingClientRect(), o = n.left - s.left + n.width / 2, l = n.top - s.top + n.height / 2, c = (d = r.getRootNode()) == null ? void 0 : d.host, b = parseInt((c == null ? void 0 : c.getAttribute("module-id")) ?? "-1");
    this._drawingWire = {
      terminalId: t,
      uid: i,
      sourceEl: r,
      moduleId: b,
      startX: o,
      startY: l,
      mouseX: o,
      mouseY: l
    };
  }
  _onContainerMoved(e) {
    this._updateWirePositions();
  }
  _onContainerRemoved(e) {
    const { moduleId: t } = e.detail;
    this._containers = this._containers.filter((i) => i.moduleId !== t), this._wires = this._wires.filter((i) => i.srcModuleId !== t && i.tgtModuleId !== t);
  }
  _onPointerMove(e) {
    if (this._isPanning) {
      this._panOffset = {
        x: e.clientX - this._panStartX,
        y: e.clientY - this._panStartY
      };
      return;
    }
    if (!this._drawingWire)
      return;
    const t = this.getBoundingClientRect(), i = e.clientX - t.left, r = e.clientY - t.top;
    this._drawingWire = { ...this._drawingWire, mouseX: i, mouseY: r }, this._tempWire = {
      x1: this._drawingWire.startX,
      y1: this._drawingWire.startY,
      x2: i,
      y2: r
    };
  }
  _onPointerUp(e) {
    var m, h;
    if (this._isPanning) {
      this._isPanning = !1, this.shadowRoot.querySelector("#canvas").style.cursor = "grab", this._updateWirePositions();
      return;
    }
    if (!this._drawingWire)
      return;
    const t = this._drawingWire;
    this._drawingWire = null, this._tempWire = null;
    const i = e.composedPath().find((u) => u.tagName === "EB-TERMINAL" && u.hasAttribute("droppable"));
    if (!i)
      return;
    const r = i.getAttribute("terminal-id"), s = i.uid ?? i.getAttribute("uid") ?? "";
    let n = null, o = (m = i.getRootNode()) == null ? void 0 : m.host;
    for (; o; ) {
      if (o.tagName === "EB-CONTAINER") {
        n = parseInt(o.getAttribute("module-id"));
        break;
      }
      o = (h = o.getRootNode()) == null ? void 0 : h.host;
    }
    if (n === null || n === t.moduleId || this._wires.some(
      (u) => u.srcModuleId === n && u.tgtModuleId === t.moduleId && u.srcTerminal === r
    ))
      return;
    const c = this._findTerminalEl(r, n), b = this._findTerminalEl(t.terminalId, t.moduleId), d = c && b ? this._getWirePositions(c, b) : { x1: 0, y1: 0, x2: 0, y2: 0 };
    this._wires = [
      ...this._wires,
      {
        id: `wire-${n}-${r}-${t.moduleId}-${t.terminalId}`,
        srcTerminal: r,
        tgtTerminal: t.terminalId,
        srcUid: s,
        tgtUid: t.uid,
        srcModuleId: n,
        tgtModuleId: t.moduleId,
        ...d
      }
    ];
  }
  _deleteWire(e) {
    this._wires = this._wires.filter((t) => t.id !== e);
  }
  _updateWirePositions() {
    this.updateComplete.then(() => {
      const e = this._wires.map((t) => {
        const i = this._findTerminalEl(t.srcTerminal, t.srcModuleId), r = this._findTerminalEl(t.tgtTerminal, t.tgtModuleId);
        return !i || !r ? t : { ...t, ...this._getWirePositions(i, r) };
      });
      this._wires = e;
    });
  }
  _findTerminalEl(e, t) {
    const i = e.replace(/^relationWire_(\d+)$/, "REL_$1"), r = this.shadowRoot.querySelector(`eb-container[module-id="${t}"]`);
    return r ? this._deepQuerySelector(r, `eb-terminal[terminal-id="${i}"]`) : null;
  }
  _deepQuerySelector(e, t) {
    const i = e.shadowRoot;
    if (!i)
      return null;
    const r = i.querySelector(t);
    if (r)
      return r;
    for (const s of i.querySelectorAll("*"))
      if (s.shadowRoot) {
        const n = this._deepQuerySelector(s, t);
        if (n)
          return n;
      }
    return null;
  }
  _getWirePositions(e, t) {
    const i = this.getBoundingClientRect(), r = e.getBoundingClientRect(), s = t.getBoundingClientRect();
    return {
      x1: r.left - i.left + r.width / 2,
      y1: r.top - i.top + r.height / 2,
      x2: s.left - i.left + s.width / 2,
      y2: s.top - i.top + s.height / 2
    };
  }
  addContainer(e) {
    var s, n, o;
    const t = this._containers.length, i = parseInt(Date.now() * Math.random()) || Date.now(), r = {
      ...e,
      value: {
        ...e.value,
        objectsettings: {
          ...(s = e.value) == null ? void 0 : s.objectsettings,
          uid: ((o = (n = e.value) == null ? void 0 : n.objectsettings) == null ? void 0 : o.uid) || i
        }
      }
    };
    this._containers = [
      ...this._containers,
      {
        moduleId: t,
        posX: 20 + t * 20,
        posY: 20 + t * 20,
        moduleData: r
      }
    ];
  }
  addContainers(e) {
    this._containers = e.map((t, i) => {
      var r, s, n, o;
      return {
        moduleId: i,
        posX: ((s = (r = t.config) == null ? void 0 : r.position) == null ? void 0 : s[0]) ?? 10 + i * 180,
        posY: ((o = (n = t.config) == null ? void 0 : n.position) == null ? void 0 : o[1]) ?? 10,
        moduleData: t
      };
    });
  }
  async _awaitAllUpdates(e) {
    if (!e.shadowRoot)
      return;
    const t = Array.from(e.shadowRoot.querySelectorAll("*")).filter(
      (i) => i.updateComplete instanceof Promise
    );
    t.length !== 0 && (await Promise.all(t.map((i) => i.updateComplete)), await Promise.all(t.map((i) => this._awaitAllUpdates(i))));
  }
  addWires(e, t) {
    this.updateComplete.then(async () => {
      const i = Array.from(this.shadowRoot.querySelectorAll("eb-container"));
      await Promise.all(i.map((r) => r.updateComplete)), await Promise.all(i.map((r) => this._awaitAllUpdates(r))), this._wires = e.map((r) => {
        const s = this._findTerminalEl(r.src.terminal, r.src.moduleId), n = this._findTerminalEl(r.tgt.terminal, r.tgt.moduleId), o = s && n ? this._getWirePositions(s, n) : { x1: 0, y1: 0, x2: 0, y2: 0 };
        return {
          id: `wire-${r.src.moduleId}-${r.src.terminal}-${r.tgt.moduleId}`,
          srcTerminal: r.src.terminal,
          tgtTerminal: r.tgt.terminal,
          srcUid: r.src.uid,
          tgtUid: r.tgt.uid,
          srcModuleId: r.src.moduleId,
          tgtModuleId: r.tgt.moduleId,
          ...o
        };
      });
    });
  }
  serialize() {
    const t = Array.from(this.shadowRoot.querySelectorAll("eb-container")).map((r) => r.serialize()), i = this._wires.map((r) => ({
      src: { moduleId: r.srcModuleId, terminal: r.srcTerminal, uid: r.srcUid },
      tgt: { moduleId: r.tgtModuleId, terminal: r.tgtTerminal, uid: r.tgtUid }
    }));
    return { modules: t, wires: i };
  }
  _wireMidpoint(e) {
    return { x: (e.x1 + e.x2) / 2, y: (e.y1 + e.y2) / 2 };
  }
  _wirePath(e) {
    return `M ${e.x1} ${e.y1} C ${e.x1} ${e.y1 + 80}, ${e.x2} ${e.y2 - 80}, ${e.x2} ${e.y2}`;
  }
  render() {
    const { x: e, y: t } = this._panOffset;
    return p`
            <div id="canvas" @pointerdown="${this._onCanvasPointerDown}">
                <div id="pan-surface" style="transform: translate(${e}px, ${t}px)">
                    ${this._containers.map(
      (i) => p`
                            <eb-container
                                module-id="${i.moduleId}"
                                pos-x="${i.posX}"
                                pos-y="${i.posY}"
                                .moduleData="${i.moduleData}"
                            >
                            </eb-container>
                        `
    )}
                </div>
                <svg id="wire-overlay">
                    ${this._wires.map((i) => {
      const r = this._wireMidpoint(i), s = this._wirePath(i);
      return re`
                            <g class="wire-group">
                                <path
                                    class="wire-hit-area"
                                    d="${s}"
                                    stroke-width="12"
                                    fill="none"
                                />
                                <path
                                    class="wire-path"
                                    d="${s}"
                                    stroke-width="2"
                                    fill="none"
                                    stroke-linecap="round"
                                    pointer-events="none"
                                />
                                <g
                                    class="wire-delete-btn"
                                    @click="${() => this._deleteWire(i.id)}"
                                    aria-label="Delete wire"
                                    role="button"
                                >
                                    <circle cx="${r.x}" cy="${r.y}" r="9" />
                                    <text x="${r.x}" y="${r.y}">×</text>
                                </g>
                            </g>
                        `;
    })}
                    ${this._tempWire ? re`
                        <path
                            class="wire-temp-path"
                            d="M ${this._tempWire.x1} ${this._tempWire.y1} L ${this._tempWire.x2} ${this._tempWire.y2}"
                            stroke-width="1.5"
                            stroke-dasharray="4 4"
                            fill="none"
                        />
                    ` : ""}
                </svg>
            </div>
        `;
  }
}
f(oe, "properties", {
  _wires: { state: !0 },
  _containers: { state: !0 },
  _drawingWire: { state: !0 },
  _tempWire: { state: !0 },
  _hoveredWireId: { state: !0 },
  _panOffset: { state: !0 }
}), f(oe, "styles", x`
        :host {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            width: 100%;
            overflow: hidden;
            background: var(--bs-body-bg, #fff);
        }
        #canvas {
            position: relative;
            flex: 1;
            width: 100%;
            overflow: hidden;
            cursor: grab;
        }
        #pan-surface {
            position: absolute;
            top: 0;
            left: 0;
        }
        #wire-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .wire-group {
            pointer-events: none;
        }
        .wire-path {
            stroke: var(--eb-wire-color, #4a90d9);
        }
        .wire-hit-area {
            stroke: transparent;
            pointer-events: stroke;
            cursor: pointer;
        }
        .wire-temp-path {
            stroke: var(--eb-wire-temp-color, #aaa);
        }
        .wire-delete-btn {
            opacity: 0;
            pointer-events: all;
            cursor: pointer;
            transition: opacity 0.15s;
        }
        .wire-delete-btn circle {
            fill: var(--eb-wire-delete-bg, #dc3545);
        }
        .wire-delete-btn text {
            fill: #fff;
            font-size: 12px;
            font-family: sans-serif;
            dominant-baseline: central;
            text-anchor: middle;
            pointer-events: none;
        }
        .wire-group:hover .wire-delete-btn {
            opacity: 1;
        }
    `);
customElements.define("eb-layer", oe);
const je = x`
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        user-select: none;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: var(--bs-body-font-size, 0.875rem);
        line-height: 1.5;
        border-radius: var(--bs-border-radius, 0.25rem);
        transition:
            color 0.15s ease-in-out,
            background-color 0.15s ease-in-out,
            border-color 0.15s ease-in-out,
            box-shadow 0.15s ease-in-out;
        text-decoration: none;
    }

    .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgb(0 123 255 / 25%);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: var(--bs-body-font-size-sm, 0.75rem);
        border-radius: var(--bs-border-radius-sm, 0.2rem);
    }

    /* Primary — TYPO3 orange */
    .btn-primary {
        color: #fff;
        background-color: var(--eb-brand-color, #ff8700);
        border-color: var(--eb-brand-color, #ff8700);
    }

    .btn-primary:hover {
        color: #fff;
        background-color: color-mix(in srgb, var(--eb-brand-color, #ff8700) 85%, #000);
        border-color: color-mix(in srgb, var(--eb-brand-color, #ff8700) 80%, #000);
    }

    /* Default — light gray with border (TYPO3-specific, not in vanilla Bootstrap 5) */
    .btn-default {
        color: var(--bs-body-color, #333);
        background-color: var(--bs-secondary-bg, #e9ecef);
        border-color: var(--bs-border-color, #dee2e6);
    }

    .btn-default:hover {
        color: var(--bs-body-color, #333);
        background-color: color-mix(in srgb, var(--bs-secondary-bg, #e9ecef) 85%, #000);
        border-color: var(--bs-border-color, #dee2e6);
    }

    /* Danger — red */
    .btn-danger {
        color: #fff;
        background-color: var(--bs-danger, #dc3545);
        border-color: var(--bs-danger, #dc3545);
    }

    .btn-danger:hover {
        color: #fff;
        background-color: color-mix(in srgb, var(--bs-danger, #dc3545) 85%, #000);
        border-color: color-mix(in srgb, var(--bs-danger, #dc3545) 80%, #000);
    }

    /* Warning — yellow */
    .btn-warning {
        color: var(--bs-dark, #212529);
        background-color: var(--bs-warning, #ffc107);
        border-color: var(--bs-warning, #ffc107);
    }

    .btn-warning:hover {
        color: var(--bs-dark, #212529);
        background-color: color-mix(in srgb, var(--bs-warning, #ffc107) 85%, #000);
        border-color: color-mix(in srgb, var(--bs-warning, #ffc107) 80%, #000);
    }
`;
class C extends w {
  /**
   * Dispatches a `field-updated` CustomEvent that bubbles through Shadow DOM
   * boundaries so parent components can collect updated values.
   */
  _fireUpdated() {
    this.dispatchEvent(
      new CustomEvent("field-updated", {
        bubbles: !0,
        composed: !0,
        detail: { name: this.name, value: this.getValue() }
      })
    );
  }
  /**
   * Returns the current field value.
   * @returns {*} Current value
   */
  getValue() {
    return this.value;
  }
  /**
   * Programmatically sets the field value and requests a re-render.
   * @param {*} v - New value
   */
  setValue(e) {
    this.value = e;
  }
  /**
   * Validates the current value against field constraints.
   * @returns {boolean} true if valid
   */
  validate() {
    return !0;
  }
}
f(C, "properties", {
  /** Field identifier, used as the key when collecting form values. */
  name: { type: String },
  /** Human-readable label rendered above the input. */
  label: { type: String },
  /** Current field value. */
  value: {},
  /** Whether the field must have a non-empty value before saving. */
  required: { type: Boolean },
  /** When true, the field is only shown in advanced mode. */
  advanced: { type: Boolean }
});
class le extends C {
  _getValidationError(e) {
    if (this.required && !e)
      return "Required";
    if (!e)
      return null;
    if (this.minLength && e.length < this.minLength)
      return `Minimum ${this.minLength} characters`;
    if (this.maxLength && e.length > this.maxLength)
      return `Maximum ${this.maxLength} characters`;
    if (this.firstCharNonNumeric && /^[0-9]/.test(e))
      return "Must not start with a number";
    if (this.noLeadingUnderscore && e.startsWith("_"))
      return "Must not start with an underscore";
    if (this.noTrailingUnderscore && e.endsWith("_"))
      return "Must not end with an underscore";
    if (this.forbiddenPrefixes) {
      const t = this.forbiddenPrefixes.split(" ").filter(Boolean).find((i) => e.startsWith(i));
      if (t)
        return `Must not start with "${t}"`;
    }
    return null;
  }
  _onInput(e) {
    let t = e.target.value;
    this.forceAlphaNumericUnderscore ? t = t.replace(/\s/g, "_").replace(/[^a-zA-Z0-9_]/g, "") : this.forceAlphaNumeric ? t = t.replace(/[^a-zA-Z0-9]/g, "") : this.noSpaces && (t = t.replace(/\s/g, "")), this.forceLowerCase && (t = t.toLowerCase()), this.lcFirst && t.length > 0 && (t = t.charAt(0).toLowerCase() + t.slice(1)), this.ucFirst && t.length > 0 && (t = t.charAt(0).toUpperCase() + t.slice(1)), t !== e.target.value && (e.target.value = t), this._error = this._getValidationError(t), this.value = t, this._fireUpdated();
  }
  getValue() {
    return this.value ?? "";
  }
  setValue(e) {
    this.value = e, this.requestUpdate();
  }
  validate() {
    return this._getValidationError(this.getValue()) === null;
  }
  render() {
    const e = this.placeholder || this.typeInvite || "", t = `eb-str-${this.name}`, i = `${t}-error`;
    return p`
            ${this.label ? p`<label class="form-label" for="${t}">${this.label}</label>` : ""}
            ${this.helpLink ? p`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
            <input
                id="${t}"
                class="form-control${this._error ? " is-invalid" : ""}"
                type="text"
                .value="${this.value ?? ""}"
                placeholder="${e}"
                ?aria-required="${this.required}"
                aria-invalid="${this._error ? "true" : "false"}"
                aria-describedby="${this._error ? i : ""}"
                @input="${this._onInput}"
            />
            ${this._error ? p`<div id="${i}" class="invalid-feedback" role="alert">${this._error}</div>` : ""}
            ${this.description ? p`<small class="help-text">${_(this.description)}</small>` : ""}
        `;
  }
}
f(le, "properties", {
  ...C.properties,
  placeholder: { type: String },
  typeInvite: { type: String, attribute: "type-invite" },
  forceAlphaNumeric: { type: Boolean, attribute: "force-alpha-numeric" },
  forceAlphaNumericUnderscore: { type: Boolean, attribute: "force-alpha-numeric-underscore" },
  forceLowerCase: { type: Boolean, attribute: "force-lower-case" },
  noSpaces: { type: Boolean, attribute: "no-spaces" },
  lcFirst: { type: Boolean, attribute: "lc-first" },
  ucFirst: { type: Boolean, attribute: "uc-first" },
  firstCharNonNumeric: { type: Boolean, attribute: "first-char-non-numeric" },
  noLeadingUnderscore: { type: Boolean, attribute: "no-leading-underscore" },
  noTrailingUnderscore: { type: Boolean, attribute: "no-trailing-underscore" },
  forbiddenPrefixes: { type: String, attribute: "forbidden-prefixes" },
  minLength: { type: Number, attribute: "min-length" },
  maxLength: { type: Number, attribute: "max-length" },
  description: { type: String },
  helpLink: { type: String, attribute: "help-link" },
  _error: { state: !0 }
}), f(le, "styles", [
  E,
  x`
            .help-link {
                font-size: 0.75em;
                color: var(--bs-secondary-color, #6c757d);
                text-decoration: none;
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .help-link:hover {
                opacity: 1;
                text-decoration: underline;
            }
            .help-text {
                display: block;
                margin-top: 0.2rem;
                font-size: 0.8em;
                color: var(--bs-secondary-color, #6c757d);
            }
        `
]);
customElements.define("eb-string-field", le);
class de extends C {
  constructor() {
    super(), this.rows = 4;
  }
  _onInput(e) {
    this.value = e.target.value, this._fireUpdated();
  }
  getValue() {
    return this.value ?? "";
  }
  setValue(e) {
    this.value = e, this.requestUpdate();
  }
  render() {
    const e = `eb-ta-${this.name}`;
    return p`
            <div class="form-group">
                ${this.label ? p`<label class="form-label" for="${e}">${this.label}</label>` : ""}
                ${this.helpLink ? p`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
                <textarea
                    id="${e}"
                    class="form-control"
                    rows="${this.rows}"
                    ?aria-required="${this.required}"
                    @input="${this._onInput}"
                >
${this.value ?? ""}</textarea
                >
                ${this.description ? p`<small class="help-text">${_(this.description)}</small>` : ""}
            </div>
        `;
  }
}
f(de, "properties", {
  ...C.properties,
  /** Number of visible text rows. Defaults to 4. */
  rows: { type: Number },
  description: { type: String },
  helpLink: { type: String, attribute: "help-link" }
}), f(de, "styles", [
  E,
  x`
            .help-link {
                font-size: 0.75em;
                color: var(--bs-secondary-color, #6c757d);
                text-decoration: none;
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .help-link:hover {
                opacity: 1;
                text-decoration: underline;
            }
            .help-text {
                display: block;
                margin-top: 0.2rem;
                font-size: 0.8em;
                color: var(--bs-secondary-color, #6c757d);
            }
        `
]);
customElements.define("eb-textarea-field", de);
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const bt = { CHILD: 2 }, ft = (a) => (...e) => ({ _$litDirective$: a, values: e });
let gt = class {
  constructor(e) {
  }
  get _$AU() {
    return this._$AM._$AU;
  }
  _$AT(e, t, i) {
    this._$Ct = e, this._$AM = t, this._$Ci = i;
  }
  _$AS(e, t) {
    return this.update(e, t);
  }
  update(e, t) {
    return this.render(...t);
  }
};
/**
 * @license
 * Copyright 2020 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const { I: yt } = ht, Me = (a) => a, Ie = () => document.createComment(""), V = (a, e, t) => {
  var s;
  const i = a._$AA.parentNode, r = e === void 0 ? a._$AB : e._$AA;
  if (t === void 0) {
    const n = i.insertBefore(Ie(), r), o = i.insertBefore(Ie(), r);
    t = new yt(n, o, a, a.options);
  } else {
    const n = t._$AB.nextSibling, o = t._$AM, l = o !== a;
    if (l) {
      let c;
      (s = t._$AQ) == null || s.call(t, a), t._$AM = a, t._$AP !== void 0 && (c = a._$AU) !== o._$AU && t._$AP(c);
    }
    if (n !== r || l) {
      let c = t._$AA;
      for (; c !== n; ) {
        const b = Me(c).nextSibling;
        Me(i).insertBefore(c, r), c = b;
      }
    }
  }
  return t;
}, T = (a, e, t = a) => (a._$AI(e, t), a), vt = {}, _t = (a, e = vt) => a._$AH = e, $t = (a) => a._$AH, te = (a) => {
  a._$AR(), a._$AA.remove();
};
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
const Le = (a, e, t) => {
  const i = /* @__PURE__ */ new Map();
  for (let r = e; r <= t; r++) i.set(a[r], r);
  return i;
}, Ye = ft(class extends gt {
  constructor(a) {
    if (super(a), a.type !== bt.CHILD) throw Error("repeat() can only be used in text expressions");
  }
  dt(a, e, t) {
    let i;
    t === void 0 ? t = e : e !== void 0 && (i = e);
    const r = [], s = [];
    let n = 0;
    for (const o of a) r[n] = i ? i(o, n) : n, s[n] = t(o, n), n++;
    return { values: s, keys: r };
  }
  render(a, e, t) {
    return this.dt(a, e, t).values;
  }
  update(a, [e, t, i]) {
    const r = $t(a), { values: s, keys: n } = this.dt(e, t, i);
    if (!Array.isArray(r)) return this.ut = n, s;
    const o = this.ut ?? (this.ut = []), l = [];
    let c, b, d = 0, m = r.length - 1, h = 0, u = s.length - 1;
    for (; d <= m && h <= u; ) if (r[d] === null) d++;
    else if (r[m] === null) m--;
    else if (o[d] === n[h]) l[h] = T(r[d], s[h]), d++, h++;
    else if (o[m] === n[u]) l[u] = T(r[m], s[u]), m--, u--;
    else if (o[d] === n[u]) l[u] = T(r[d], s[u]), V(a, l[u + 1], r[d]), d++, u--;
    else if (o[m] === n[h]) l[h] = T(r[m], s[h]), V(a, r[d], r[m]), m--, h++;
    else if (c === void 0 && (c = Le(n, h, u), b = Le(o, d, m)), c.has(o[d])) if (c.has(o[m])) {
      const y = b.get(n[h]), S = y !== void 0 ? r[y] : null;
      if (S === null) {
        const R = V(a, r[d]);
        T(R, s[h]), l[h] = R;
      } else l[h] = T(S, s[h]), V(a, r[d], S), r[y] = null;
      h++;
    } else te(r[m]), m--;
    else te(r[d]), d++;
    for (; h <= u; ) {
      const y = V(a, l[u + 1]);
      T(y, s[h]), l[h++] = y;
    }
    for (; d <= m; ) {
      const y = r[d++];
      y !== null && te(y);
    }
    return this.ut = n, _t(a, l), L;
  }
});
class ce extends C {
  _getOptions() {
    const e = this.selectValues ?? [], t = this.selectOptions ?? e;
    return e.map((i, r) => ({ value: i, label: t[r] ?? i }));
  }
  _visibleOptions() {
    const e = this._getOptions();
    return this.allowedValues ? e.filter((t) => this.allowedValues.includes(t.value)) : e;
  }
  updated(e) {
    if (e.has("allowedValues") && this.allowedValues) {
      const t = this._visibleOptions();
      t.length > 0 && !t.some((i) => i.value === this.value) && (this.value = t[0].value, this._fireUpdated());
    }
  }
  _onChange(e) {
    this.value = e.target.value, this._fireUpdated();
  }
  getValue() {
    return this.value ?? "";
  }
  setValue(e) {
    this.value = e, this.requestUpdate();
  }
  render() {
    const e = this._visibleOptions(), t = `eb-sel-${this.name}`;
    return p`
            <div class="form-group">
                ${this.label ? p`<label class="form-label" for="${t}">${this.label}</label>` : ""}
                ${this.helpLink ? p`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
                <select
                    id="${t}"
                    class="form-select"
                    aria-label="${this.label || this.name}"
                    @change="${this._onChange}"
                >
                    ${Ye(
      e,
      (i) => i.value,
      (i) => p`
                            <option value="${i.value}" ?selected="${this.value === i.value}">${i.label}</option>
                        `
    )}
                </select>
                ${this.description ? p`<small class="help-text">${_(this.description)}</small>` : ""}
            </div>
        `;
  }
}
f(ce, "properties", {
  ...C.properties,
  /** Array of option values (e.g. ['inline', 'selectSingle']). */
  selectValues: { type: Array, attribute: "select-values" },
  /** Array of option labels, parallel to selectValues. Falls back to selectValues if omitted. */
  selectOptions: { type: Array, attribute: "select-options" },
  /** When set, only options whose value is in this array are shown. */
  allowedValues: { type: Array },
  description: { type: String },
  helpLink: { type: String, attribute: "help-link" }
}), f(ce, "styles", [
  E,
  x`
            .help-link {
                font-size: 0.75em;
                color: var(--bs-secondary-color, #6c757d);
                text-decoration: none;
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .help-link:hover {
                opacity: 1;
                text-decoration: underline;
            }
            .help-text {
                display: block;
                margin-top: 0.2rem;
                font-size: 0.8em;
                color: var(--bs-secondary-color, #6c757d);
            }
        `
]);
customElements.define("eb-select-field", ce);
class pe extends C {
  _onChange(e) {
    this.value = e.target.checked, this._fireUpdated();
  }
  getValue() {
    return !!this.value;
  }
  setValue(e) {
    this.value = !!e, this.requestUpdate();
  }
  render() {
    return p`
            <div class="form-check form-check-type-toggle">
                <input
                    class="form-check-input"
                    type="checkbox"
                    .checked="${!!this.value}"
                    aria-checked="${!!this.value}"
                    @change="${this._onChange}"
                    id="eb-bool-${this.name}"
                />
                <label class="form-check-label" for="eb-bool-${this.name}"> ${this.label || ""} </label>
                ${this.helpLink ? p`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
            </div>
            ${this.description ? p`<small class="help-text">${_(this.description)}</small>` : ""}
        `;
  }
}
f(pe, "properties", {
  ...C.properties,
  description: { type: String },
  helpLink: { type: String, attribute: "help-link" }
}), f(pe, "styles", [
  E,
  x`
            .help-link {
                font-size: 0.75em;
                color: var(--bs-secondary-color, #6c757d);
                text-decoration: none;
                margin-left: 0.25rem;
                opacity: 0.7;
            }
            .help-link:hover {
                opacity: 1;
                text-decoration: underline;
            }
            .help-text {
                display: block;
                margin-top: 0.2rem;
                font-size: 0.8em;
                color: var(--bs-secondary-color, #6c757d);
            }
        `
]);
customElements.define("eb-boolean-field", pe);
class ue extends C {
  getValue() {
    return this.value ?? "";
  }
  setValue(e) {
    this.value = e;
  }
  render() {
    return p`<input type="hidden" .value="${this.value ?? ""}" />`;
  }
}
f(ue, "properties", {
  ...C.properties
}), f(ue, "styles", x`
        :host {
            display: none;
        }
    `);
customElements.define("eb-hidden-field", ue);
class he extends w {
  connectedCallback() {
    super.connectedCallback(), this.addEventListener("field-updated", this._onFieldUpdated), this._onAdvancedModeChanged = (e) => {
      this.advancedMode = e.detail.enabled;
    }, window.addEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
  }
  disconnectedCallback() {
    super.disconnectedCallback(), this.removeEventListener("field-updated", this._onFieldUpdated), window.removeEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
  }
  _onFieldUpdated(e) {
    var t, i;
    if (((t = e.detail) == null ? void 0 : t.name) === "relationType") {
      const r = this.querySelector("[name=renderType]");
      if (!r)
        return;
      const s = {
        zeroToOne: ["selectSingle", "selectMultipleSideBySide", "inline"],
        manyToOne: ["selectSingle", "selectMultipleSideBySide"],
        zeroToMany: ["inline", "selectMultipleSideBySide"],
        manyToMany: ["selectMultipleSideBySide", "selectSingleBox", "selectCheckBox"]
      };
      r.allowedValues = s[e.detail.value] ?? null;
    }
    ((i = e.detail) == null ? void 0 : i.name) === "propertyType" && this._applyPropertyTypeVisibility(e.detail.value);
  }
  _applyPropertyTypeVisibility(e) {
    this.querySelectorAll("[data-visible-for]").forEach((t) => {
      const i = (t.getAttribute("data-visible-for") || "").split(" ").filter(Boolean);
      i.length > 0 && (t.style.display = i.includes(e) ? "" : "none");
    });
  }
  _initPropertyTypes() {
    var i;
    const e = this.querySelector("[name=propertyType]");
    if (!e)
      return;
    const t = ((i = e.getValue) == null ? void 0 : i.call(e)) ?? e.value;
    t && this._applyPropertyTypeVisibility(t);
  }
  _initRelationTypes() {
    this.querySelectorAll("[name=relationType]").forEach((e) => {
      var s;
      const t = e.value ?? ((s = e.getValue) == null ? void 0 : s.call(e));
      if (!t)
        return;
      const i = this.querySelector("[name=renderType]");
      if (!i)
        return;
      const r = {
        zeroToOne: ["selectSingle", "selectMultipleSideBySide", "inline"],
        manyToOne: ["selectSingle", "selectMultipleSideBySide"],
        zeroToMany: ["inline", "selectMultipleSideBySide"],
        manyToMany: ["selectMultipleSideBySide", "selectSingleBox", "selectCheckBox"]
      };
      i.allowedValues = r[t] ?? null;
    });
  }
  _toggleCollapse() {
    this.collapsible && (this.collapsed = !this.collapsed);
  }
  _onHeaderKeyDown(e) {
    (e.key === "Enter" || e.key === " ") && (e.preventDefault(), this._toggleCollapse());
  }
  _onSlotChange() {
    this.requestUpdate(), this._initRelationTypes(), this._initPropertyTypes();
  }
  /**
   * Collects values from direct slotted children only (not deep descendants)
   * to avoid double-collecting fields that belong to nested eb-group elements.
   *
   * When a direct child eb-group has flatten:true its values are spread into
   * the result object rather than nested under the group name key.
   *
   * @returns {Object} Flat or nested values object depending on `flatten`.
   */
  getValue() {
    var r;
    const e = {}, t = (r = this.shadowRoot) == null ? void 0 : r.querySelector("slot");
    return (t ? t.assignedElements({ flatten: !1 }) : Array.from(this.children)).forEach((s) => {
      var o;
      if (typeof s.getValue != "function")
        return;
      if (((o = s.tagName) == null ? void 0 : o.toLowerCase()) === "eb-group" && s.flatten) {
        Object.assign(e, s.getValue());
        return;
      }
      const n = s.getAttribute("name");
      n !== null && (e[n] = s.getValue());
    }), e;
  }
  /**
   * Distributes values to direct slotted children.
   *
   * For flatten child groups the full values object is forwarded so the
   * child group can populate its own fields from the flat key space.
   *
   * @param {Object} obj - Values object to distribute.
   */
  setValue(e) {
    var r;
    if (!e)
      return;
    const t = (r = this.shadowRoot) == null ? void 0 : r.querySelector("slot");
    (t ? t.assignedElements({ flatten: !1 }) : Array.from(this.children)).forEach((s) => {
      var o;
      if (typeof s.setValue != "function")
        return;
      if (((o = s.tagName) == null ? void 0 : o.toLowerCase()) === "eb-group" && s.flatten) {
        s.setValue(e);
        return;
      }
      const n = s.getAttribute("name");
      n !== null && e[n] !== void 0 && s.setValue(e[n]);
    }), this._initPropertyTypes();
  }
  render() {
    return p`
            <div class="card" role="group" aria-label="${this.legend || this.name || "Group"}">
                ${this.legend ? p`
                          <div
                              class="card-header"
                              @click="${this._toggleCollapse}"
                              @keydown="${this._onHeaderKeyDown}"
                              role="${this.collapsible ? "button" : g}"
                              tabindex="${this.collapsible ? "0" : g}"
                              aria-expanded="${this.collapsible ? String(!this.collapsed) : g}"
                          >
                              ${this.legend}
                          </div>
                      ` : ""}
                <div class="card-body">
                    <slot @slotchange="${this._onSlotChange}"></slot>
                </div>
            </div>
        `;
  }
}
f(he, "properties", {
  name: { type: String },
  legend: { type: String },
  collapsible: { type: Boolean },
  collapsed: { type: Boolean, reflect: !0 },
  advancedMode: { type: Boolean, attribute: "advanced-mode", reflect: !0 },
  /**
   * When true, getValue() returns the flat child-values object directly
   * instead of wrapping it under the group name. The parent container is
   * expected to spread these values into its own result object.
   */
  flatten: { type: Boolean }
}), f(he, "styles", x`
        :host {
            display: block;
        }
        .card {
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: var(--bs-border-radius, 0.25rem);
            background-color: var(--bs-body-bg, #fff);
            color: var(--bs-body-color, #000);
            margin-bottom: 0.5rem;
        }
        .card-header {
            padding: 0.4rem 0.6rem;
            background-color: var(--bs-secondary-bg, transparent);
            border-bottom: 1px solid var(--bs-border-color, #dee2e6);
            font-weight: bold;
        }
        :host([collapsible]) .card-header {
            cursor: pointer;
            user-select: none;
        }
        .card-header::before {
            content: '▼ ';
        }
        :host([collapsed]) .card-header::before {
            content: '▶ ';
        }
        .card-body {
            padding: 0.4rem 0.6rem;
        }
        :host([collapsed]) .card-body {
            display: none;
        }
        ::slotted([advanced]) {
            display: none;
        }
        :host([advanced-mode]) ::slotted([advanced]) {
            display: block;
        }
    `);
customElements.define("eb-group", he);
function xt(a) {
  var t, i, r, s;
  const e = ((s = (r = (i = (t = window.TYPO3) == null ? void 0 : t.settings) == null ? void 0 : i.extensionBuilder) == null ? void 0 : r.publicResourceWebPath) == null ? void 0 : s.core) ?? "";
  return e ? `${e}Icons/T3Icons/sprites/actions.svg#${a}` : "";
}
const wt = {
  "actions-caret-up": "↑",
  "actions-caret-down": "↓",
  "actions-delete": "✕",
  "actions-view-list-collapse": "▼",
  "actions-view-list-expand": "▶"
};
function W(a) {
  const e = xt(a);
  return e ? p`
        <svg width="16" height="16" aria-hidden="true">
            <use href="${e}"></use>
        </svg>
    ` : p`<span aria-hidden="true">${wt[a] ?? a}</span>`;
}
const Ct = /* @__PURE__ */ new Set(["propertyName", "relationName", "customAction", "name"]);
class me extends w {
  constructor() {
    super(), this.sortable = !0, this.addLabel = "add", this._items = [], this._boundOnFieldUpdated = this._onFieldUpdated.bind(this);
  }
  connectedCallback() {
    super.connectedCallback(), this.addEventListener("field-updated", this._boundOnFieldUpdated);
  }
  disconnectedCallback() {
    super.disconnectedCallback(), this.removeEventListener("field-updated", this._boundOnFieldUpdated);
  }
  _onFieldUpdated(e) {
    var n, o;
    if (!Ct.has((n = e.detail) == null ? void 0 : n.name))
      return;
    const t = e.composedPath().find((l) => {
      var c;
      return l instanceof Element && ((c = l.classList) == null ? void 0 : c.contains("item-content"));
    });
    if (!t)
      return;
    const r = Array.from(((o = this.shadowRoot) == null ? void 0 : o.querySelectorAll(".item-content")) ?? []).indexOf(t);
    if (r < 0)
      return;
    const s = [...this._items];
    s[r] = { ...s[r], label: e.detail.value }, this._items = s;
  }
  get _elementTypeDef() {
    try {
      return JSON.parse(this.elementType || "null");
    } catch {
      return null;
    }
  }
  get _isWirable() {
    var t, i;
    return (((i = (t = this._elementTypeDef) == null ? void 0 : t.inputParams) == null ? void 0 : i.fields) ?? []).some((r) => {
      var s;
      return (s = r.inputParams) == null ? void 0 : s.wirable;
    });
  }
  _addItem() {
    const e = parseInt(Date.now() * Math.random()) || Date.now(), t = this._items.length;
    this._items = [...this._items, { uid: e, collapsed: !1, label: "" }], this.updateComplete.then(() => {
      var n, o;
      const r = Array.from(((n = this.shadowRoot) == null ? void 0 : n.querySelectorAll(".item-content")) ?? [])[t];
      if (!r)
        return;
      const s = r.querySelector('[name="uid"]');
      (o = s == null ? void 0 : s.setValue) == null || o.call(s, String(e));
    }), this._fireUpdated();
  }
  _removeItem(e) {
    this._items = this._items.filter((t, i) => i !== e), this._fireUpdated();
  }
  _toggleCollapse(e) {
    const t = [...this._items];
    t[e] = { ...t[e], collapsed: !t[e].collapsed }, this._items = t;
  }
  _moveUp(e) {
    if (e === 0)
      return;
    const t = [...this._items];
    [t[e - 1], t[e]] = [t[e], t[e - 1]], this._items = t, this._fireUpdated();
  }
  _moveDown(e) {
    if (e >= this._items.length - 1)
      return;
    const t = [...this._items];
    [t[e], t[e + 1]] = [t[e + 1], t[e]], this._items = t, this._fireUpdated();
  }
  _fireUpdated() {
    this.dispatchEvent(
      new CustomEvent("list-updated", {
        bubbles: !0,
        composed: !0,
        detail: { value: this.getValue() }
      })
    );
  }
  getValue() {
    var t;
    const e = ((t = this.shadowRoot) == null ? void 0 : t.querySelectorAll(".item-content")) ?? [];
    return Array.from(e).map((i) => {
      var n;
      const r = i.querySelector("eb-group");
      if (r != null && r.getValue)
        return r.getValue();
      const s = i.querySelector("[name]");
      return ((n = s == null ? void 0 : s.getValue) == null ? void 0 : n.call(s)) ?? null;
    });
  }
  setValue(e) {
    Array.isArray(e) && (this._items = e.map((t, i) => ({ uid: i, collapsed: !1, label: "" })), this.updateComplete.then(() => {
      var i;
      const t = ((i = this.shadowRoot) == null ? void 0 : i.querySelectorAll(".item-content")) ?? [];
      e.forEach((r, s) => {
        var c;
        if (!r)
          return;
        const n = t[s];
        if (!n)
          return;
        const o = n.querySelector("eb-group");
        if (o != null && o.setValue) {
          o.setValue(r);
          return;
        }
        const l = n.querySelector("[name]");
        (c = l == null ? void 0 : l.setValue) == null || c.call(l, r);
      });
    }));
  }
  render() {
    const e = this._elementTypeDef, t = this._isWirable;
    return p`
            ${Ye(
      this._items,
      (i) => i.uid,
      (i, r) => p`
                    <div class="item-row">
                        ${t ? p`
                                  <div class="item-terminal">
                                      <eb-terminal droppable terminal-id="REL_${r}"></eb-terminal>
                                  </div>
                              ` : g}
                        <div class="item-content ${i.collapsed ? "is-collapsed" : ""}">
                            ${e ? Fe(e) : g}
                        </div>
                        ${i.collapsed ? p`<span class="item-collapsed-label">${i.label || `Item ${r + 1}`}</span>` : g}
                        <div class="item-actions">
                            <button
                                class="btn btn-default btn-sm btn-collapse"
                                @click="${() => this._toggleCollapse(r)}"
                                title="${i.collapsed ? "Expand" : "Collapse"}"
                            >
                                ${i.collapsed ? W("actions-view-list-expand") : W("actions-view-list-collapse")}
                            </button>
                            ${this.sortable ? p`
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveUp(r)}"
                                          title="Move up"
                                      >
                                          ${W("actions-caret-up")}
                                      </button>
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveDown(r)}"
                                          title="Move down"
                                      >
                                          ${W("actions-caret-down")}
                                      </button>
                                  ` : g}
                            <button
                                class="btn btn-default btn-sm btn-delete"
                                @click="${() => this._removeItem(r)}"
                                title="Remove"
                            >
                                ${W("actions-delete")}
                            </button>
                        </div>
                    </div>
                `
    )}
            <button class="btn btn-default btn-sm add-btn" @click="${this._addItem}">${this.addLabel}</button>
        `;
  }
}
f(me, "properties", {
  name: { type: String },
  sortable: { type: Boolean },
  addLabel: { type: String, attribute: "add-label" },
  elementType: { type: String, attribute: "element-type" },
  _items: { state: !0 }
}), f(me, "styles", [
  je,
  E,
  x`
            :host {
                display: block;
            }
            .item-row {
                display: flex;
                align-items: flex-start;
                gap: 4px;
                margin-bottom: 4px;
            }
            .item-content {
                flex: 1;
            }
            .item-content.is-collapsed {
                display: none;
            }
            .item-collapsed-label {
                flex: 1;
                font-size: 12px;
                color: var(--bs-secondary-color, #6c757d);
                padding: 2px 0;
                font-style: italic;
            }
            .item-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2px;
                padding-top: 2px;
            }
            .item-actions .btn-delete {
                grid-column: 1 / -1;
            }
            .item-actions .btn-collapse {
                grid-column: 1 / -1;
                margin-bottom: 2px;
            }
            .add-btn {
                margin-top: 4px;
            }
            .item-terminal {
                display: flex;
                align-items: center;
                padding-top: 4px;
            }
        `
]);
customElements.define("eb-list-field", me);
const St = [
  {
    type: "string",
    inputParams: {
      name: "name",
      label: "name",
      typeInvite: "extensionTitle",
      required: !0
    }
  },
  {
    type: "string",
    inputParams: {
      name: "vendorName",
      label: "vendorName",
      placeholder: "vendorName",
      helpLink: "https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Namespaces/#usage-in-extensions",
      ucFirst: !0,
      regexp: /^[A-Za-z]/,
      minLength: 2,
      forceAlphaNumeric: !0,
      cols: 30,
      description: "descr_vendorName",
      required: !0
    }
  },
  {
    type: "string",
    inputParams: {
      name: "extensionKey",
      label: "key",
      helpLink: "https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ExtensionKey/",
      typeInvite: "extensionKey",
      forceLowerCase: !0,
      forceAlphaNumericUnderscore: !0,
      firstCharNonNumeric: !0,
      noLeadingUnderscore: !0,
      noTrailingUnderscore: !0,
      forbiddenPrefixes: "tx user_ pages tt_ sys_ ts_language csh_",
      minLength: 3,
      maxLength: 30,
      cols: 30,
      description: "descr_extensionKey",
      required: !0
    }
  },
  {
    inputParams: {
      name: "originalExtensionKey",
      className: "hiddenField"
    }
  },
  {
    inputParams: {
      name: "originalVendorName",
      className: "hiddenField"
    }
  },
  {
    type: "text",
    inputParams: {
      name: "description",
      label: "description",
      typeInvite: "description",
      cols: 30
    }
  },
  {
    type: "group",
    inputParams: {
      collapsible: !0,
      collapsed: !0,
      className: "emConf mainGroup",
      legend: "moreOptions",
      name: "emConf",
      fields: [
        {
          type: "select",
          inputParams: {
            label: "category",
            name: "category",
            description: "descr_category",
            selectValues: [
              "plugin",
              "module",
              "misc",
              "be",
              "fe",
              "services",
              "templates",
              "distribution",
              "example",
              "doc"
            ],
            selectOptions: [
              "plugins",
              "backendModules",
              "misc",
              "backend",
              "frontend",
              "services",
              "templates",
              "distribution",
              "examples",
              "documentation"
            ]
          }
        },
        {
          type: "string",
          inputParams: {
            name: "custom_category",
            label: "custom_category",
            description: "descr_custom_category",
            cols: 30
          }
        },
        {
          type: "string",
          inputParams: {
            name: "version",
            label: "version",
            required: !1,
            size: 5,
            value: "1.0.0"
          }
        },
        {
          type: "select",
          inputParams: {
            name: "state",
            label: "state",
            selectValues: ["alpha", "beta", "stable", "experimental", "test"],
            selectOptions: ["alpha", "beta", "stable", "experimental", "test"]
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "disableVersioning",
            label: "disableVersioning",
            description: "descr_disableVersioning",
            value: 0
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "disableLocalization",
            label: "disableLocalization",
            description: "descr_disableLocalization",
            value: 0
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateDocumentationTemplate",
            label: "generateDocumentationTemplate",
            description: "descr_generateDocumentationTemplate",
            value: 1
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateEmptyGitRepository",
            label: "generateEmptyGitRepository",
            description: "descr_generateEmptyGitRepository",
            value: 1
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateEditorConfig",
            label: "generateEditorConfig",
            description: "descr_generateEditorConfig",
            value: 1
          }
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateSiteSet",
            label: "generateSiteSet",
            description: "descr_generateSiteSet",
            value: 0
          }
        },
        {
          type: "string",
          inputParams: {
            name: "sourceLanguage",
            description: "descr_sourceLanguage",
            label: "sourceLanguage",
            value: "en",
            cols: 30
          }
        },
        {
          type: "select",
          inputParams: {
            name: "targetVersion",
            id: "targetVersionSelector",
            label: "target_version",
            description: "descr_target_version",
            selectValues: ["13.4.0-13.4.99"],
            selectOptions: ["TYPO3 v13.4"],
            value: "13.4.0-13.4.99"
          }
        },
        {
          type: "text",
          inputParams: {
            label: "dependsOn",
            name: "dependsOn",
            id: "extensionDependencies",
            description: "descr_dependsOn",
            cols: 20,
            rows: 6,
            value: `typo3 => 12.4.0-12.4.99
`
          }
        }
      ]
    }
  },
  {
    type: "list",
    inputParams: {
      label: "persons",
      name: "persons",
      sortable: !0,
      className: "persons mainGroup",
      elementType: {
        type: "group",
        inputParams: {
          name: "property",
          fields: [
            {
              type: "string",
              inputParams: {
                label: "name",
                name: "name",
                required: !0
              }
            },
            {
              type: "select",
              inputParams: {
                name: "role",
                label: "role",
                selectValues: ["Developer", "Product Manager"],
                selectOptions: ["developer", "product_manager"]
              }
            },
            {
              type: "string",
              inputParams: {
                name: "email",
                label: "email",
                required: !1
              }
            },
            {
              type: "string",
              inputParams: {
                name: "company",
                label: "company",
                required: !1
              }
            }
          ]
        }
      }
    }
  },
  {
    type: "list",
    inputParams: {
      name: "plugins",
      label: "plugins",
      sortable: !0,
      className: "plugins mainGroup",
      elementType: {
        type: "group",
        inputParams: {
          name: "property",
          fields: [
            {
              type: "string",
              inputParams: {
                name: "name",
                label: "name",
                required: !0
              }
            },
            {
              type: "string",
              inputParams: {
                name: "key",
                label: "key",
                required: !0,
                forceLowerCase: !0,
                forceAlphaNumeric: !0,
                noSpaces: !0,
                description: "uniqueInThisModel"
              }
            },
            {
              type: "text",
              inputParams: {
                name: "description",
                label: "description",
                required: !1,
                cols: 20,
                rows: 6
              }
            },
            {
              type: "group",
              inputParams: {
                collapsible: !0,
                collapsed: !0,
                legend: "advancedOptions",
                name: "actions",
                className: "wideTextfields",
                fields: [
                  {
                    type: "text",
                    inputParams: {
                      name: "controllerActionCombinations",
                      label: "controller_action_combinations",
                      description: "descr_controller_action_combinations",
                      placeholder: "ControllerName => action1,action2",
                      cols: 38,
                      rows: 3
                    }
                  },
                  {
                    type: "text",
                    inputParams: {
                      name: "noncacheableActions",
                      label: "noncacheable_actions",
                      placeholder: "ControllerName => action1,action2",
                      description: "descr_noncacheable_actions",
                      cols: 38,
                      rows: 3
                    }
                  }
                ]
              }
            }
          ]
        }
      }
    }
  },
  {
    type: "list",
    inputParams: {
      label: "backendModules",
      name: "backendModules",
      className: "bottomBorder mainGroup",
      sortable: !0,
      elementType: {
        type: "group",
        className: "smallBottomBorder",
        inputParams: {
          name: "properties",
          fields: [
            {
              type: "string",
              inputParams: {
                label: "name",
                name: "name",
                required: !0
              }
            },
            {
              type: "string",
              inputParams: {
                label: "key",
                name: "key",
                required: !0,
                forceLowerCase: !0,
                forceAlphaNumeric: !0,
                noSpaces: !0,
                description: "uniqueInThisModel"
              }
            },
            {
              type: "text",
              inputParams: {
                label: "short_description",
                name: "description",
                required: !1,
                cols: 20,
                rows: 6
              }
            },
            {
              type: "string",
              inputParams: {
                label: "tab_label",
                name: "tabLabel"
              }
            },
            {
              type: "select",
              inputParams: {
                label: "mainModule",
                name: "mainModule",
                required: !0,
                selectValues: ["web", "site", "file", "user", "tools", "system", "help"]
              }
            },
            {
              type: "group",
              inputParams: {
                collapsible: !0,
                collapsed: !0,
                legend: "advancedOptions",
                name: "actions",
                className: "wideTextfields",
                fields: [
                  {
                    type: "text",
                    inputParams: {
                      name: "controllerActionCombinations",
                      label: "controller_action_combinations",
                      placeholder: "ControllerName => action1,action2",
                      description: "descr_controller_action_combinations",
                      cols: 38,
                      rows: 3
                    }
                  }
                ]
              }
            }
          ]
        }
      }
    }
  }
];
class be extends w {
  constructor() {
    super(), this.smdUrl = "", this.extensionName = "", this.initialWarnings = [], this.composerWarning = "", this._loading = !1, this._extensionData = null, this._advancedMode = !1, this._leftCollapsed = !1;
  }
  async firstUpdated() {
    this.composerWarning && this._showComposerWarningModal();
  }
  /**
   * Show a TYPO3 Modal warning dialog when TYPO3 runs in composer mode but
   * no local path repository for "packages/*" is configured in composer.json.
   */
  _showComposerWarningModal() {
    const e = document.createElement("div"), t = document.createElement("p");
    t.textContent = 'TYPO3 is running in composer mode, but no local path repository for "packages/*" is configured in your composer.json. The Extension Builder cannot save extensions without this configuration.';
    const i = document.createElement("p");
    i.textContent = "Run the following command in your project root to fix this:";
    const r = document.createElement("pre");
    r.style.cssText = "background:var(--bs-tertiary-bg);color:var(--bs-body-color);border:1px solid var(--bs-border-color);padding:0.75rem 1rem;border-radius:4px;font-size:0.9em;white-space:pre-wrap;", r.textContent = 'mkdir -p packages && composer config repositories.local path "packages/*"', e.appendChild(t), e.appendChild(i), e.appendChild(r), v.confirm("Composer Mode — Configuration Required", e, U.warning, [
      {
        text: "Close",
        btnClass: "btn-warning",
        trigger: () => v.dismiss()
      }
    ]);
  }
  async connectedCallback() {
    var e;
    super.connectedCallback(), ((e = this.initialWarnings) == null ? void 0 : e.length) > 0 && this.initialWarnings.forEach((t) => $.warning("Configuration", t)), this.addEventListener("field-updated", this._onFieldUpdated), this.extensionName && await this.load();
  }
  disconnectedCallback() {
    super.disconnectedCallback(), this.removeEventListener("field-updated", this._onFieldUpdated);
  }
  _onFieldUpdated(e) {
    var s, n, o;
    if (((s = e.detail) == null ? void 0 : s.name) !== "targetVersion")
      return;
    const t = this.querySelector("[name=dependsOn]");
    if (!t)
      return;
    const r = (((n = t.getValue) == null ? void 0 : n.call(t)) ?? t.value ?? "").split(`
`).map((l) => l.includes("typo3") ? `typo3 => ${e.detail.value}` : l).join(`
`);
    (o = t.setValue) == null || o.call(t, r);
  }
  _toggleAdvancedMode() {
    this._advancedMode = !this._advancedMode, window.dispatchEvent(
      new CustomEvent("eb-advanced-mode-changed", {
        detail: { enabled: this._advancedMode }
      })
    );
  }
  _toggleLeftPanel() {
    this._leftCollapsed = !this._leftCollapsed;
  }
  async load() {
    if (this.extensionName) {
      this._loading = !0;
      try {
        const t = await (await fetch(this.smdUrl, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ method: "listWirings", params: {} })
        })).json();
        if (t.error)
          throw new Error(t.error);
        const r = (t.result ?? []).find((s) => s.name === this.extensionName);
        if (!r)
          throw new Error(`Extension "${this.extensionName}" not found`);
        this._extensionData = JSON.parse(r.working);
      } catch (e) {
        $.error("Load failed", e.message);
      } finally {
        this._loading = !1;
      }
      this._extensionData && (await this.updateComplete, this._populateLayer(), this._populateProperties());
    }
  }
  _populateProperties() {
    var t;
    const e = ((t = this._extensionData) == null ? void 0 : t.properties) ?? {};
    this.shadowRoot.querySelectorAll("[name]").forEach((i) => {
      e[i.name] !== void 0 && typeof i.setValue == "function" && i.setValue(e[i.name]);
    });
  }
  _populateLayer() {
    const e = this.shadowRoot.querySelector("eb-layer");
    if (!e || !this._extensionData)
      return;
    const t = this._extensionData.modules ?? [], i = this._extensionData.wires ?? [];
    e.addContainers(t), i.length > 0 && e.addWires(i, t);
  }
  _collectProperties() {
    const e = {};
    return this.shadowRoot.querySelectorAll("[name]").forEach((t) => {
      typeof t.getValue == "function" && (e[t.name] = t.getValue());
    }), e;
  }
  _serializeWorking() {
    const e = this.shadowRoot.querySelector("eb-layer");
    if (!e)
      return null;
    const { modules: t, wires: i } = e.serialize();
    return JSON.stringify({ modules: t, wires: i, properties: this._collectProperties() });
  }
  async _fetchPreviewChanges(e) {
    try {
      return await (await fetch(this.smdUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ method: "previewChanges", params: { name: this.extensionName, working: e } })
      })).json();
    } catch {
      return null;
    }
  }
  _buildPreviewContent(e) {
    var r, s;
    const t = [];
    if ((r = e.modifiedFiles) != null && r.length) {
      t.push(`Files that will be modified:
`);
      for (const n of e.modifiedFiles) {
        let o = "  • " + n.path;
        n.renamedTo && (o += "  →  " + n.renamedTo), t.push(o + `
`);
        for (const l of n.changes ?? [])
          l.type === "renamed" ? t.push("      ↻ " + l.from + " → " + l.to + `
`) : l.type === "removed" ? t.push("      − " + l.method + ` (removed)
`) : l.type === "added" && t.push("      + " + l.method + ` (added)
`);
      }
    }
    if ((s = e.deletedFiles) != null && s.length) {
      t.push(`
Files that will be deleted:
`);
      for (const n of e.deletedFiles)
        t.push("  • " + n + `
`);
    }
    const i = document.createElement("pre");
    return i.style.cssText = "font-size:0.9em;max-height:60vh;overflow:auto;white-space:pre-wrap;", i.textContent = t.join(""), i;
  }
  async save(e = {}) {
    var s;
    const t = this._serializeWorking();
    if (!t)
      return;
    if (!e._previewDone) {
      const n = await this._fetchPreviewChanges(t);
      if (n != null && n.hasChanges) {
        v.confirm(
          "Review changes before generating",
          this._buildPreviewContent(n),
          U.warning,
          [
            { text: "Cancel", btnClass: "btn-default", trigger: () => v.dismiss() },
            {
              text: "Generate",
              btnClass: "btn-warning",
              trigger: () => {
                v.dismiss(), this.save({ ...e, _previewDone: !0 });
              }
            }
          ]
        );
        return;
      }
    }
    const r = await (await fetch(this.smdUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        method: "saveWiring",
        params: { name: this.extensionName, working: t, ...e }
      })
    })).json();
    if ((s = r.errors) != null && s.length) {
      r.errors.forEach((n) => $.error("Validation error", n));
      return;
    }
    if (r.error) {
      $.error("Error", r.error);
      return;
    }
    if (r.confirm) {
      v.confirm("Warning", r.confirm, U.warning, [
        { text: "Cancel", btnClass: "btn-default", trigger: () => v.dismiss() },
        {
          text: "Save anyway",
          btnClass: "btn-warning",
          trigger: () => {
            v.dismiss(), this._saveWithConfirmation(r.confirmFieldName);
          }
        }
      ]);
      return;
    }
    r.warning && $.warning("Warning", r.warning), (r.warnings ?? []).forEach((n) => $.warning("Roundtrip warning", n)), r.success && ($.success("Saved", r.success), (r.installationHints ?? []).forEach((n) => $.info("Next steps", n)));
  }
  _saveWithConfirmation(e) {
    this.save({ [e]: !0 });
  }
  reset() {
    this.extensionName = "", this._extensionData = null;
    const e = this.shadowRoot.querySelector("eb-layer");
    e && (e._containers = [], e._wires = []), this.shadowRoot.querySelectorAll("[name]").forEach((t) => {
      var i;
      (i = t.setValue) == null || i.call(t, "");
    });
  }
  addModelObject() {
    const e = this.shadowRoot.querySelector("eb-layer");
    e && e.addContainer(He.container);
  }
  render() {
    return p`
            <div class="toolbar">
                <button class="btn btn-primary btn-sm" @click="${this.addModelObject}">+ Model Object</button>
                <button
                    class="btn btn-default btn-sm"
                    @click="${this._toggleAdvancedMode}"
                    aria-pressed="${this._advancedMode}"
                >
                    ${this._advancedMode ? "Hide Advanced Options" : "Show Advanced Options"}
                </button>
            </div>
            <div class="content ${this._advancedMode ? "advanced-mode" : ""}">
                <div class="left-panel ${this._leftCollapsed ? "collapsed" : ""}">
                    <div class="left-panel-header">
                        <button
                            class="btn-toggle-panel"
                            @click="${this._toggleLeftPanel}"
                            aria-label="${this._leftCollapsed ? "Expand properties panel" : "Collapse properties panel"}"
                            aria-expanded="${!this._leftCollapsed}"
                        >
                            <span aria-hidden="true">☰</span>
                        </button>
                    </div>
                    <div class="left-panel-content">${_e(St)}</div>
                </div>
                <div class="center-panel" role="main">
                    ${this._loading ? p`<div class="loading">Loading...</div>` : p`<eb-layer></eb-layer>`}
                </div>
            </div>
        `;
  }
}
f(be, "properties", {
  smdUrl: { type: String, attribute: "smd-url" },
  extensionName: { type: String, attribute: "extension-name" },
  initialWarnings: { type: Array, attribute: "initial-warnings" },
  composerWarning: { type: String, attribute: "composer-warning" },
  _loading: { state: !0 },
  _extensionData: { state: !0 },
  _advancedMode: { state: !0 },
  _leftCollapsed: { state: !0 }
}), f(be, "styles", [
  je,
  E,
  x`
            :host {
                display: flex;
                flex-direction: column;
                width: 100%;
                height: 100%;
                font-family: sans-serif;
                background: var(--bs-body-bg, #fff);
                color: var(--bs-body-color, #000);
            }
            .toolbar {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: var(--module-docheader-padding-y, 5px) var(--module-docheader-padding-x, 24px);
                background: var(--module-docheader-bg, #eee);
                border-bottom: 1px solid var(--module-docheader-border, #c3c3c3);
            }
            .content {
                display: flex;
                flex-direction: row;
                flex: 1;
                min-height: 0;
                overflow: hidden;
            }
            .left-panel {
                width: 280px;
                min-width: 120px;
                max-width: 600px;
                overflow-y: auto;
                border-right: 1px solid var(--bs-border-color, #dee2e6);
                background: var(--bs-secondary-bg, #f8f9fa);
                padding: 0;
                resize: horizontal;
            }
            .left-panel.collapsed {
                width: auto;
                min-width: 0;
                overflow: hidden;
                resize: none;
            }
            .left-panel.collapsed .left-panel-content {
                display: none;
            }
            .left-panel-header {
                display: flex;
                justify-content: flex-end;
                padding: 4px;
            }
            .left-panel-content {
                padding: var(--bs-card-spacer-y, 1rem) var(--bs-card-spacer-x, 1rem);
                padding-top: 0;
            }
            .btn-toggle-panel {
                background: #515151;
                border: none;
                border-radius: 3px;
                padding: 4px 6px;
                cursor: pointer;
                color: #f4f4f4;
                font-size: 1rem;
                line-height: 1;
            }
            .btn-toggle-panel:hover {
                background: #3a3a3a;
            }
            .center-panel {
                flex: 1;
                min-height: 0;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                position: relative;
            }
            eb-layer {
                flex: 1;
                min-height: 0;
                width: 100%;
            }
            .loading {
                padding: 20px;
                color: var(--eb-text-muted, #666);
            }
            :host [advanced] {
                display: none;
            }
            .advanced-mode ::slotted([advanced]),
            .advanced-mode [advanced] {
                display: block;
            }
        `
]);
customElements.define("eb-wiring-editor", be);
class fe extends w {
  constructor() {
    super(), this._editing = !1;
  }
  _startEdit() {
    this._editing = !0, this.updateComplete.then(() => {
      var e;
      (e = this.shadowRoot.querySelector("input")) == null || e.focus();
    });
  }
  _confirm(e) {
    const t = e.target.value;
    this._editing = !1, t !== this.value && (this.value = t, this.dispatchEvent(
      new CustomEvent("inplace-change", {
        bubbles: !0,
        composed: !0,
        detail: { value: t }
      })
    ));
  }
  _cancel() {
    this._editing = !1;
  }
  _onKey(e) {
    e.key === "Enter" ? (e.preventDefault(), this._confirm(e)) : e.key === "Escape" && this._cancel();
  }
  getValue() {
    return this.value ?? "";
  }
  setValue(e) {
    this.value = e;
  }
  render() {
    return this._editing ? p`
                <div aria-live="polite">
                    <input
                        class="form-control form-control-sm"
                        .value="${this.value ?? ""}"
                        @blur="${this._confirm}"
                        @keydown="${this._onKey}"
                    />
                </div>
            ` : p`
            <div aria-live="polite">
                <span
                    style="cursor:pointer;border-bottom:1px dashed currentColor;min-width:1em;display:inline-block;"
                    role="button"
                    tabindex="0"
                    aria-label="Edit: ${this.value ?? ""}"
                    @click="${this._startEdit}"
                    @keydown="${(e) => e.key === "Enter" && this._startEdit()}"
                    >${this.value ?? ""}</span
                >
            </div>
        `;
  }
}
f(fe, "properties", {
  value: { type: String },
  _editing: { type: Boolean, state: !0 }
}), f(fe, "styles", [E]);
customElements.define("eb-inplace-edit", fe);
function Re() {
  var e, t, i, r, s;
  const a = document.querySelector("eb-wiring-editor");
  a && ((e = document.getElementById("WiringEditor-saveButton-button")) == null || e.addEventListener("click", (n) => {
    n.preventDefault(), a.save();
  }), (t = document.getElementById("WiringEditor-newButton-button")) == null || t.addEventListener("click", (n) => {
    n.preventDefault(), a.reset();
  }), (i = document.getElementById("toggleAdvancedOptions")) == null || i.addEventListener("click", (n) => {
    n.preventDefault(), a._toggleAdvancedMode();
  }), (r = document.getElementById("WiringEditor-loadButton-button")) == null || r.addEventListener("click", async (n) => {
    n.preventDefault();
    const o = a.getAttribute("smd-url"), b = (await (await fetch(o, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ method: "listWirings", params: {} })
    })).json()).result ?? [];
    if (b.length === 0) {
      $.info("Open Extension", "No extensions found.");
      return;
    }
    const d = document.createElement("select");
    d.size = 8, d.style.minWidth = "240px", b.forEach((h) => {
      const u = document.createElement("option");
      u.value = h.name, u.textContent = h.name, d.appendChild(u);
    });
    const m = v.advanced({
      title: "Open Extension",
      content: p``,
      severity: U.info,
      size: "small",
      staticBackdrop: !1,
      buttons: [
        {
          text: "Cancel",
          btnClass: "btn-default",
          trigger: () => v.dismiss()
        },
        {
          text: "Open",
          btnClass: "btn-primary",
          active: !0,
          trigger: () => {
            var u;
            const h = (u = m.querySelector(".t3js-modal-body select")) == null ? void 0 : u.value;
            v.dismiss(), h && (a.extensionName = h, a.load());
          }
        }
      ],
      callback: (h) => {
        const u = h.querySelector(".t3js-modal-body");
        u && (u.replaceChildren(d), d.focus());
      }
    });
  }), (s = document.getElementById("WiringEditor-backupsButton-button")) == null || s.addEventListener("click", async (n) => {
    n.preventDefault();
    const o = a.getAttribute("smd-url");
    if (!a.extensionName) {
      $.info("Restore backup", "Please load an extension first.");
      return;
    }
    const l = a._serializeWorking();
    if (!l) {
      $.info("Restore backup", "No extension loaded.");
      return;
    }
    const d = (await (await fetch(o, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ method: "listBackups", params: { name: a.extensionName, working: l } })
    })).json()).result ?? [];
    if (d.length === 0) {
      $.info("Restore backup", "No backups found for this extension.");
      return;
    }
    const m = document.createElement("select");
    m.size = Math.min(d.length, 8), m.style.cssText = "min-width:320px;display:block;margin-bottom:8px;", d.forEach((u) => {
      const y = document.createElement("option");
      y.value = u.directory, y.textContent = u.label + "  (" + u.fileCount + " files)", m.appendChild(y);
    });
    const h = v.advanced({
      title: "Restore backup",
      content: p``,
      severity: U.warning,
      staticBackdrop: !1,
      buttons: [
        {
          text: "Cancel",
          btnClass: "btn-default",
          trigger: () => v.dismiss()
        },
        {
          text: "Restore",
          btnClass: "btn-danger",
          trigger: async () => {
            var y;
            const u = (y = h.querySelector(".t3js-modal-body select")) == null ? void 0 : y.value;
            v.dismiss(), u && v.confirm(
              "Confirm restore",
              "Restore backup from " + u + "? The current extension will be overwritten.",
              U.warning,
              [
                { text: "Cancel", btnClass: "btn-default", trigger: () => v.dismiss() },
                {
                  text: "Restore",
                  btnClass: "btn-danger",
                  trigger: async () => {
                    v.dismiss();
                    const R = await (await fetch(o, {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify({
                        method: "restoreBackup",
                        params: { name: a.extensionName, working: l, backupDirectory: u }
                      })
                    })).json();
                    R.error ? $.error("Restore failed", R.error) : $.success(
                      "Backup restored",
                      R.success ?? "Extension restored."
                    );
                  }
                }
              ]
            );
          }
        }
      ],
      callback: (u) => {
        const y = u.querySelector(".t3js-modal-body");
        if (y) {
          const S = document.createElement("p");
          S.textContent = "Restoring a backup will overwrite all current extension files. The current state will be backed up first.", S.className = "text-danger", y.replaceChildren(S, m);
        }
      }
    });
  }));
}
document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", Re) : Re();
