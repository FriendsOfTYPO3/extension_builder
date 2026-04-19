import e from "@typo3/backend/notification.js";
import t from "@typo3/backend/modal.js";
import n from "@typo3/backend/severity.js";
//#region node_modules/.pnpm/@lit+reactive-element@2.1.2/node_modules/@lit/reactive-element/css-tag.js
var r = globalThis, i = r.ShadowRoot && (r.ShadyCSS === void 0 || r.ShadyCSS.nativeShadow) && "adoptedStyleSheets" in Document.prototype && "replace" in CSSStyleSheet.prototype, a = Symbol(), o = /* @__PURE__ */ new WeakMap(), s = class {
	constructor(e, t, n) {
		if (this._$cssResult$ = !0, n !== a) throw Error("CSSResult is not constructable. Use `unsafeCSS` or `css` instead.");
		this.cssText = e, this.t = t;
	}
	get styleSheet() {
		let e = this.o, t = this.t;
		if (i && e === void 0) {
			let n = t !== void 0 && t.length === 1;
			n && (e = o.get(t)), e === void 0 && ((this.o = e = new CSSStyleSheet()).replaceSync(this.cssText), n && o.set(t, e));
		}
		return e;
	}
	toString() {
		return this.cssText;
	}
}, c = (e) => new s(typeof e == "string" ? e : e + "", void 0, a), l = (e, ...t) => new s(e.length === 1 ? e[0] : t.reduce((t, n, r) => t + ((e) => {
	if (!0 === e._$cssResult$) return e.cssText;
	if (typeof e == "number") return e;
	throw Error("Value passed to 'css' function must be a 'css' function result: " + e + ". Use 'unsafeCSS' to pass non-literal values, but take care to ensure page security.");
})(n) + e[r + 1], e[0]), e, a), u = (e, t) => {
	if (i) e.adoptedStyleSheets = t.map((e) => e instanceof CSSStyleSheet ? e : e.styleSheet);
	else for (let n of t) {
		let t = document.createElement("style"), i = r.litNonce;
		i !== void 0 && t.setAttribute("nonce", i), t.textContent = n.cssText, e.appendChild(t);
	}
}, d = i ? (e) => e : (e) => e instanceof CSSStyleSheet ? ((e) => {
	let t = "";
	for (let n of e.cssRules) t += n.cssText;
	return c(t);
})(e) : e, { is: f, defineProperty: p, getOwnPropertyDescriptor: m, getOwnPropertyNames: ee, getOwnPropertySymbols: te, getPrototypeOf: ne } = Object, h = globalThis, re = h.trustedTypes, ie = re ? re.emptyScript : "", ae = h.reactiveElementPolyfillSupport, g = (e, t) => e, _ = {
	toAttribute(e, t) {
		switch (t) {
			case Boolean:
				e = e ? ie : null;
				break;
			case Object:
			case Array: e = e == null ? e : JSON.stringify(e);
		}
		return e;
	},
	fromAttribute(e, t) {
		let n = e;
		switch (t) {
			case Boolean:
				n = e !== null;
				break;
			case Number:
				n = e === null ? null : Number(e);
				break;
			case Object:
			case Array: try {
				n = JSON.parse(e);
			} catch {
				n = null;
			}
		}
		return n;
	}
}, v = (e, t) => !f(e, t), oe = {
	attribute: !0,
	type: String,
	converter: _,
	reflect: !1,
	useDefault: !1,
	hasChanged: v
};
Symbol.metadata ??= Symbol("metadata"), h.litPropertyMetadata ??= /* @__PURE__ */ new WeakMap();
var y = class extends HTMLElement {
	static addInitializer(e) {
		this._$Ei(), (this.l ??= []).push(e);
	}
	static get observedAttributes() {
		return this.finalize(), this._$Eh && [...this._$Eh.keys()];
	}
	static createProperty(e, t = oe) {
		if (t.state && (t.attribute = !1), this._$Ei(), this.prototype.hasOwnProperty(e) && ((t = Object.create(t)).wrapped = !0), this.elementProperties.set(e, t), !t.noAccessor) {
			let n = Symbol(), r = this.getPropertyDescriptor(e, n, t);
			r !== void 0 && p(this.prototype, e, r);
		}
	}
	static getPropertyDescriptor(e, t, n) {
		let { get: r, set: i } = m(this.prototype, e) ?? {
			get() {
				return this[t];
			},
			set(e) {
				this[t] = e;
			}
		};
		return {
			get: r,
			set(t) {
				let a = r?.call(this);
				i?.call(this, t), this.requestUpdate(e, a, n);
			},
			configurable: !0,
			enumerable: !0
		};
	}
	static getPropertyOptions(e) {
		return this.elementProperties.get(e) ?? oe;
	}
	static _$Ei() {
		if (this.hasOwnProperty(g("elementProperties"))) return;
		let e = ne(this);
		e.finalize(), e.l !== void 0 && (this.l = [...e.l]), this.elementProperties = new Map(e.elementProperties);
	}
	static finalize() {
		if (this.hasOwnProperty(g("finalized"))) return;
		if (this.finalized = !0, this._$Ei(), this.hasOwnProperty(g("properties"))) {
			let e = this.properties, t = [...ee(e), ...te(e)];
			for (let n of t) this.createProperty(n, e[n]);
		}
		let e = this[Symbol.metadata];
		if (e !== null) {
			let t = litPropertyMetadata.get(e);
			if (t !== void 0) for (let [e, n] of t) this.elementProperties.set(e, n);
		}
		this._$Eh = /* @__PURE__ */ new Map();
		for (let [e, t] of this.elementProperties) {
			let n = this._$Eu(e, t);
			n !== void 0 && this._$Eh.set(n, e);
		}
		this.elementStyles = this.finalizeStyles(this.styles);
	}
	static finalizeStyles(e) {
		let t = [];
		if (Array.isArray(e)) {
			let n = new Set(e.flat(Infinity).reverse());
			for (let e of n) t.unshift(d(e));
		} else e !== void 0 && t.push(d(e));
		return t;
	}
	static _$Eu(e, t) {
		let n = t.attribute;
		return !1 === n ? void 0 : typeof n == "string" ? n : typeof e == "string" ? e.toLowerCase() : void 0;
	}
	constructor() {
		super(), this._$Ep = void 0, this.isUpdatePending = !1, this.hasUpdated = !1, this._$Em = null, this._$Ev();
	}
	_$Ev() {
		this._$ES = new Promise((e) => this.enableUpdating = e), this._$AL = /* @__PURE__ */ new Map(), this._$E_(), this.requestUpdate(), this.constructor.l?.forEach((e) => e(this));
	}
	addController(e) {
		(this._$EO ??= /* @__PURE__ */ new Set()).add(e), this.renderRoot !== void 0 && this.isConnected && e.hostConnected?.();
	}
	removeController(e) {
		this._$EO?.delete(e);
	}
	_$E_() {
		let e = /* @__PURE__ */ new Map(), t = this.constructor.elementProperties;
		for (let n of t.keys()) this.hasOwnProperty(n) && (e.set(n, this[n]), delete this[n]);
		e.size > 0 && (this._$Ep = e);
	}
	createRenderRoot() {
		let e = this.shadowRoot ?? this.attachShadow(this.constructor.shadowRootOptions);
		return u(e, this.constructor.elementStyles), e;
	}
	connectedCallback() {
		this.renderRoot ??= this.createRenderRoot(), this.enableUpdating(!0), this._$EO?.forEach((e) => e.hostConnected?.());
	}
	enableUpdating(e) {}
	disconnectedCallback() {
		this._$EO?.forEach((e) => e.hostDisconnected?.());
	}
	attributeChangedCallback(e, t, n) {
		this._$AK(e, n);
	}
	_$ET(e, t) {
		let n = this.constructor.elementProperties.get(e), r = this.constructor._$Eu(e, n);
		if (r !== void 0 && !0 === n.reflect) {
			let i = (n.converter?.toAttribute === void 0 ? _ : n.converter).toAttribute(t, n.type);
			this._$Em = e, i == null ? this.removeAttribute(r) : this.setAttribute(r, i), this._$Em = null;
		}
	}
	_$AK(e, t) {
		let n = this.constructor, r = n._$Eh.get(e);
		if (r !== void 0 && this._$Em !== r) {
			let e = n.getPropertyOptions(r), i = typeof e.converter == "function" ? { fromAttribute: e.converter } : e.converter?.fromAttribute === void 0 ? _ : e.converter;
			this._$Em = r;
			let a = i.fromAttribute(t, e.type);
			this[r] = a ?? this._$Ej?.get(r) ?? a, this._$Em = null;
		}
	}
	requestUpdate(e, t, n, r = !1, i) {
		if (e !== void 0) {
			let a = this.constructor;
			if (!1 === r && (i = this[e]), n ??= a.getPropertyOptions(e), !((n.hasChanged ?? v)(i, t) || n.useDefault && n.reflect && i === this._$Ej?.get(e) && !this.hasAttribute(a._$Eu(e, n)))) return;
			this.C(e, t, n);
		}
		!1 === this.isUpdatePending && (this._$ES = this._$EP());
	}
	C(e, t, { useDefault: n, reflect: r, wrapped: i }, a) {
		n && !(this._$Ej ??= /* @__PURE__ */ new Map()).has(e) && (this._$Ej.set(e, a ?? t ?? this[e]), !0 !== i || a !== void 0) || (this._$AL.has(e) || (this.hasUpdated || n || (t = void 0), this._$AL.set(e, t)), !0 === r && this._$Em !== e && (this._$Eq ??= /* @__PURE__ */ new Set()).add(e));
	}
	async _$EP() {
		this.isUpdatePending = !0;
		try {
			await this._$ES;
		} catch (e) {
			Promise.reject(e);
		}
		let e = this.scheduleUpdate();
		return e != null && await e, !this.isUpdatePending;
	}
	scheduleUpdate() {
		return this.performUpdate();
	}
	performUpdate() {
		if (!this.isUpdatePending) return;
		if (!this.hasUpdated) {
			if (this.renderRoot ??= this.createRenderRoot(), this._$Ep) {
				for (let [e, t] of this._$Ep) this[e] = t;
				this._$Ep = void 0;
			}
			let e = this.constructor.elementProperties;
			if (e.size > 0) for (let [t, n] of e) {
				let { wrapped: e } = n, r = this[t];
				!0 !== e || this._$AL.has(t) || r === void 0 || this.C(t, void 0, n, r);
			}
		}
		let e = !1, t = this._$AL;
		try {
			e = this.shouldUpdate(t), e ? (this.willUpdate(t), this._$EO?.forEach((e) => e.hostUpdate?.()), this.update(t)) : this._$EM();
		} catch (t) {
			throw e = !1, this._$EM(), t;
		}
		e && this._$AE(t);
	}
	willUpdate(e) {}
	_$AE(e) {
		this._$EO?.forEach((e) => e.hostUpdated?.()), this.hasUpdated || (this.hasUpdated = !0, this.firstUpdated(e)), this.updated(e);
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
		this._$Eq &&= this._$Eq.forEach((e) => this._$ET(e, this[e])), this._$EM();
	}
	updated(e) {}
	firstUpdated(e) {}
};
y.elementStyles = [], y.shadowRootOptions = { mode: "open" }, y[g("elementProperties")] = /* @__PURE__ */ new Map(), y[g("finalized")] = /* @__PURE__ */ new Map(), ae?.({ ReactiveElement: y }), (h.reactiveElementVersions ??= []).push("2.1.2");
//#endregion
//#region node_modules/.pnpm/lit-html@3.3.2/node_modules/lit-html/lit-html.js
var b = globalThis, se = (e) => e, x = b.trustedTypes, ce = x ? x.createPolicy("lit-html", { createHTML: (e) => e }) : void 0, S = "$lit$", C = `lit$${Math.random().toFixed(9).slice(2)}$`, w = "?" + C, le = `<${w}>`, T = document, E = () => T.createComment(""), D = (e) => e === null || typeof e != "object" && typeof e != "function", O = Array.isArray, ue = (e) => O(e) || typeof e?.[Symbol.iterator] == "function", k = "[ 	\n\f\r]", A = /<(?:(!--|\/[^a-zA-Z])|(\/?[a-zA-Z][^>\s]*)|(\/?$))/g, de = /-->/g, fe = />/g, j = RegExp(`>|${k}(?:([^\\s"'>=/]+)(${k}*=${k}*(?:[^ \t\n\f\r"'\`<>=]|("|')|))|$)`, "g"), pe = /'/g, me = /"/g, he = /^(?:script|style|textarea|title)$/i, ge = (e) => (t, ...n) => ({
	_$litType$: e,
	strings: t,
	values: n
}), M = ge(1), N = ge(2), P = Symbol.for("lit-noChange"), F = Symbol.for("lit-nothing"), I = /* @__PURE__ */ new WeakMap(), L = T.createTreeWalker(T, 129);
function _e(e, t) {
	if (!O(e) || !e.hasOwnProperty("raw")) throw Error("invalid template strings array");
	return ce === void 0 ? t : ce.createHTML(t);
}
var ve = (e, t) => {
	let n = e.length - 1, r = [], i, a = t === 2 ? "<svg>" : t === 3 ? "<math>" : "", o = A;
	for (let t = 0; t < n; t++) {
		let n = e[t], s, c, l = -1, u = 0;
		for (; u < n.length && (o.lastIndex = u, c = o.exec(n), c !== null);) u = o.lastIndex, o === A ? c[1] === "!--" ? o = de : c[1] === void 0 ? c[2] === void 0 ? c[3] !== void 0 && (o = j) : (he.test(c[2]) && (i = RegExp("</" + c[2], "g")), o = j) : o = fe : o === j ? c[0] === ">" ? (o = i ?? A, l = -1) : c[1] === void 0 ? l = -2 : (l = o.lastIndex - c[2].length, s = c[1], o = c[3] === void 0 ? j : c[3] === "\"" ? me : pe) : o === me || o === pe ? o = j : o === de || o === fe ? o = A : (o = j, i = void 0);
		let d = o === j && e[t + 1].startsWith("/>") ? " " : "";
		a += o === A ? n + le : l >= 0 ? (r.push(s), n.slice(0, l) + S + n.slice(l) + C + d) : n + C + (l === -2 ? t : d);
	}
	return [_e(e, a + (e[n] || "<?>") + (t === 2 ? "</svg>" : t === 3 ? "</math>" : "")), r];
}, R = class e {
	constructor({ strings: t, _$litType$: n }, r) {
		let i;
		this.parts = [];
		let a = 0, o = 0, s = t.length - 1, c = this.parts, [l, u] = ve(t, n);
		if (this.el = e.createElement(l, r), L.currentNode = this.el.content, n === 2 || n === 3) {
			let e = this.el.content.firstChild;
			e.replaceWith(...e.childNodes);
		}
		for (; (i = L.nextNode()) !== null && c.length < s;) {
			if (i.nodeType === 1) {
				if (i.hasAttributes()) for (let e of i.getAttributeNames()) if (e.endsWith(S)) {
					let t = u[o++], n = i.getAttribute(e).split(C), r = /([.?@])?(.*)/.exec(t);
					c.push({
						type: 1,
						index: a,
						name: r[2],
						strings: n,
						ctor: r[1] === "." ? be : r[1] === "?" ? xe : r[1] === "@" ? Se : V
					}), i.removeAttribute(e);
				} else e.startsWith(C) && (c.push({
					type: 6,
					index: a
				}), i.removeAttribute(e));
				if (he.test(i.tagName)) {
					let e = i.textContent.split(C), t = e.length - 1;
					if (t > 0) {
						i.textContent = x ? x.emptyScript : "";
						for (let n = 0; n < t; n++) i.append(e[n], E()), L.nextNode(), c.push({
							type: 2,
							index: ++a
						});
						i.append(e[t], E());
					}
				}
			} else if (i.nodeType === 8) if (i.data === w) c.push({
				type: 2,
				index: a
			});
			else {
				let e = -1;
				for (; (e = i.data.indexOf(C, e + 1)) !== -1;) c.push({
					type: 7,
					index: a
				}), e += C.length - 1;
			}
			a++;
		}
	}
	static createElement(e, t) {
		let n = T.createElement("template");
		return n.innerHTML = e, n;
	}
};
function z(e, t, n = e, r) {
	if (t === P) return t;
	let i = r === void 0 ? n._$Cl : n._$Co?.[r], a = D(t) ? void 0 : t._$litDirective$;
	return i?.constructor !== a && (i?._$AO?.(!1), a === void 0 ? i = void 0 : (i = new a(e), i._$AT(e, n, r)), r === void 0 ? n._$Cl = i : (n._$Co ??= [])[r] = i), i !== void 0 && (t = z(e, i._$AS(e, t.values), i, r)), t;
}
var ye = class {
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
		let { el: { content: t }, parts: n } = this._$AD, r = (e?.creationScope ?? T).importNode(t, !0);
		L.currentNode = r;
		let i = L.nextNode(), a = 0, o = 0, s = n[0];
		for (; s !== void 0;) {
			if (a === s.index) {
				let t;
				s.type === 2 ? t = new B(i, i.nextSibling, this, e) : s.type === 1 ? t = new s.ctor(i, s.name, s.strings, this, e) : s.type === 6 && (t = new Ce(i, this, e)), this._$AV.push(t), s = n[++o];
			}
			a !== s?.index && (i = L.nextNode(), a++);
		}
		return L.currentNode = T, r;
	}
	p(e) {
		let t = 0;
		for (let n of this._$AV) n !== void 0 && (n.strings === void 0 ? n._$AI(e[t]) : (n._$AI(e, n, t), t += n.strings.length - 2)), t++;
	}
}, B = class e {
	get _$AU() {
		return this._$AM?._$AU ?? this._$Cv;
	}
	constructor(e, t, n, r) {
		this.type = 2, this._$AH = F, this._$AN = void 0, this._$AA = e, this._$AB = t, this._$AM = n, this.options = r, this._$Cv = r?.isConnected ?? !0;
	}
	get parentNode() {
		let e = this._$AA.parentNode, t = this._$AM;
		return t !== void 0 && e?.nodeType === 11 && (e = t.parentNode), e;
	}
	get startNode() {
		return this._$AA;
	}
	get endNode() {
		return this._$AB;
	}
	_$AI(e, t = this) {
		e = z(this, e, t), D(e) ? e === F || e == null || e === "" ? (this._$AH !== F && this._$AR(), this._$AH = F) : e !== this._$AH && e !== P && this._(e) : e._$litType$ === void 0 ? e.nodeType === void 0 ? ue(e) ? this.k(e) : this._(e) : this.T(e) : this.$(e);
	}
	O(e) {
		return this._$AA.parentNode.insertBefore(e, this._$AB);
	}
	T(e) {
		this._$AH !== e && (this._$AR(), this._$AH = this.O(e));
	}
	_(e) {
		this._$AH !== F && D(this._$AH) ? this._$AA.nextSibling.data = e : this.T(T.createTextNode(e)), this._$AH = e;
	}
	$(e) {
		let { values: t, _$litType$: n } = e, r = typeof n == "number" ? this._$AC(e) : (n.el === void 0 && (n.el = R.createElement(_e(n.h, n.h[0]), this.options)), n);
		if (this._$AH?._$AD === r) this._$AH.p(t);
		else {
			let e = new ye(r, this), n = e.u(this.options);
			e.p(t), this.T(n), this._$AH = e;
		}
	}
	_$AC(e) {
		let t = I.get(e.strings);
		return t === void 0 && I.set(e.strings, t = new R(e)), t;
	}
	k(t) {
		O(this._$AH) || (this._$AH = [], this._$AR());
		let n = this._$AH, r, i = 0;
		for (let a of t) i === n.length ? n.push(r = new e(this.O(E()), this.O(E()), this, this.options)) : r = n[i], r._$AI(a), i++;
		i < n.length && (this._$AR(r && r._$AB.nextSibling, i), n.length = i);
	}
	_$AR(e = this._$AA.nextSibling, t) {
		for (this._$AP?.(!1, !0, t); e !== this._$AB;) {
			let t = se(e).nextSibling;
			se(e).remove(), e = t;
		}
	}
	setConnected(e) {
		this._$AM === void 0 && (this._$Cv = e, this._$AP?.(e));
	}
}, V = class {
	get tagName() {
		return this.element.tagName;
	}
	get _$AU() {
		return this._$AM._$AU;
	}
	constructor(e, t, n, r, i) {
		this.type = 1, this._$AH = F, this._$AN = void 0, this.element = e, this.name = t, this._$AM = r, this.options = i, n.length > 2 || n[0] !== "" || n[1] !== "" ? (this._$AH = Array(n.length - 1).fill(/* @__PURE__ */ new String()), this.strings = n) : this._$AH = F;
	}
	_$AI(e, t = this, n, r) {
		let i = this.strings, a = !1;
		if (i === void 0) e = z(this, e, t, 0), a = !D(e) || e !== this._$AH && e !== P, a && (this._$AH = e);
		else {
			let r = e, o, s;
			for (e = i[0], o = 0; o < i.length - 1; o++) s = z(this, r[n + o], t, o), s === P && (s = this._$AH[o]), a ||= !D(s) || s !== this._$AH[o], s === F ? e = F : e !== F && (e += (s ?? "") + i[o + 1]), this._$AH[o] = s;
		}
		a && !r && this.j(e);
	}
	j(e) {
		e === F ? this.element.removeAttribute(this.name) : this.element.setAttribute(this.name, e ?? "");
	}
}, be = class extends V {
	constructor() {
		super(...arguments), this.type = 3;
	}
	j(e) {
		this.element[this.name] = e === F ? void 0 : e;
	}
}, xe = class extends V {
	constructor() {
		super(...arguments), this.type = 4;
	}
	j(e) {
		this.element.toggleAttribute(this.name, !!e && e !== F);
	}
}, Se = class extends V {
	constructor(e, t, n, r, i) {
		super(e, t, n, r, i), this.type = 5;
	}
	_$AI(e, t = this) {
		if ((e = z(this, e, t, 0) ?? F) === P) return;
		let n = this._$AH, r = e === F && n !== F || e.capture !== n.capture || e.once !== n.once || e.passive !== n.passive, i = e !== F && (n === F || r);
		r && this.element.removeEventListener(this.name, this, n), i && this.element.addEventListener(this.name, this, e), this._$AH = e;
	}
	handleEvent(e) {
		typeof this._$AH == "function" ? this._$AH.call(this.options?.host ?? this.element, e) : this._$AH.handleEvent(e);
	}
}, Ce = class {
	constructor(e, t, n) {
		this.element = e, this.type = 6, this._$AN = void 0, this._$AM = t, this.options = n;
	}
	get _$AU() {
		return this._$AM._$AU;
	}
	_$AI(e) {
		z(this, e);
	}
}, we = {
	M: S,
	P: C,
	A: w,
	C: 1,
	L: ve,
	R: ye,
	D: ue,
	V: z,
	I: B,
	H: V,
	N: xe,
	U: Se,
	B: be,
	F: Ce
}, Te = b.litHtmlPolyfillSupport;
Te?.(R, B), (b.litHtmlVersions ??= []).push("3.3.2");
var Ee = (e, t, n) => {
	let r = n?.renderBefore ?? t, i = r._$litPart$;
	if (i === void 0) {
		let e = n?.renderBefore ?? null;
		r._$litPart$ = i = new B(t.insertBefore(E(), e), e, void 0, n ?? {});
	}
	return i._$AI(e), i;
}, H = globalThis, U = class extends y {
	constructor() {
		super(...arguments), this.renderOptions = { host: this }, this._$Do = void 0;
	}
	createRenderRoot() {
		let e = super.createRenderRoot();
		return this.renderOptions.renderBefore ??= e.firstChild, e;
	}
	update(e) {
		let t = this.render();
		this.hasUpdated || (this.renderOptions.isConnected = this.isConnected), super.update(e), this._$Do = Ee(t, this.renderRoot, this.renderOptions);
	}
	connectedCallback() {
		super.connectedCallback(), this._$Do?.setConnected(!0);
	}
	disconnectedCallback() {
		super.disconnectedCallback(), this._$Do?.setConnected(!1);
	}
	render() {
		return P;
	}
};
U._$litElement$ = !0, U.finalized = !0, H.litElementHydrateSupport?.({ LitElement: U });
var De = H.litElementPolyfillSupport;
De?.({ LitElement: U }), (H.litElementVersions ??= []).push("4.2.2");
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-terminal.js
var Oe = class extends U {
	static properties = {
		type: { type: String },
		terminalId: {
			type: String,
			attribute: "terminal-id"
		},
		uid: { type: String },
		droppable: { type: Boolean }
	};
	static styles = l`
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
    `;
	connectedCallback() {
		super.connectedCallback(), this.addEventListener("pointerdown", this._onPointerDown.bind(this));
	}
	updated(e) {
		e.has("type") && (this.setAttribute("aria-label", {
			input: "Input terminal",
			output: "Output terminal"
		}[this.type] ?? "Terminal"), this.setAttribute("role", "img"));
	}
	_onPointerDown(e) {
		this.droppable || (e.stopPropagation(), this.dispatchEvent(new CustomEvent("terminal-connect", {
			bubbles: !0,
			composed: !0,
			detail: {
				terminalId: this.terminalId,
				uid: this.uid,
				sourceEl: this
			}
		})));
	}
	getCenter() {
		let e = this.getBoundingClientRect(), t = this._getLayerRect();
		return {
			x: e.left - t.left + e.width / 2,
			y: e.top - t.top + e.height / 2
		};
	}
	_getLayerRect() {
		let e = this.parentElement;
		for (; e && e.tagName !== "EB-LAYER";) e = e.parentElement;
		return e ? e.getBoundingClientRect() : {
			left: 0,
			top: 0
		};
	}
	render() {
		return M``;
	}
};
customElements.define("eb-terminal", Oe);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-wire.js
var ke = class extends U {
	static properties = {
		x1: { type: Number },
		y1: { type: Number },
		x2: { type: Number },
		y2: { type: Number },
		srcUid: {
			type: String,
			attribute: "src-uid"
		},
		tgtUid: {
			type: String,
			attribute: "tgt-uid"
		},
		srcTerminal: {
			type: String,
			attribute: "src-terminal"
		},
		tgtTerminal: {
			type: String,
			attribute: "tgt-terminal"
		},
		srcModuleId: {
			type: Number,
			attribute: "src-module-id"
		},
		tgtModuleId: {
			type: Number,
			attribute: "tgt-module-id"
		}
	};
	static styles = l`
        :host {
            display: contents;
        }
        path {
            stroke: var(--eb-wire-color, #4a90d9);
        }
    `;
	getPath() {
		let { x1: e, y1: t, x2: n, y2: r } = this;
		return `M ${e} ${t} C ${e} ${t + 80}, ${n} ${r - 80}, ${n} ${r}`;
	}
	serialize() {
		return {
			src: {
				moduleId: this.srcModuleId,
				terminal: this.srcTerminal,
				uid: this.srcUid
			},
			tgt: {
				moduleId: this.tgtModuleId,
				terminal: this.tgtTerminal,
				uid: this.tgtUid
			}
		};
	}
	render() {
		return N`
            <path
                d="${this.getPath()}"
                stroke-width="2"
                fill="none"
                stroke-linecap="round"
                aria-hidden="true"
            />
        `;
	}
};
customElements.define("eb-wire", ke);
//#endregion
//#region Resources/Public/jsDomainModeling/src/translate.js
function W(e) {
	if (!e) return "";
	let t = e.replace(/\./g, "_"), n = window.TYPO3?.settings?.extensionBuilder?._LOCAL_LANG;
	return n?.[t] ? n[t] : e.replace(/_/g, " ").replace(/([A-Z])/g, " $1").trim().replace(/\b\w/g, (e) => e.toUpperCase());
}
//#endregion
//#region Resources/Public/jsDomainModeling/src/render-fields.js
function Ae(e) {
	let t = e.inputParams ?? {}, n = e.type;
	if (!n || t.className?.includes("hiddenField")) return M`<eb-hidden-field name="${t.name}"></eb-hidden-field>`;
	if (t.wirable) return F;
	switch (n) {
		case "string": return M`<eb-string-field
                name="${t.name}"
                label="${W(t.label ?? "")}"
                ?required="${t.required}"
                ?advanced="${t.advancedMode || !1}"
                description="${W(t.description ?? "")}"
                help-link="${t.helpLink ?? ""}"
                type-invite="${t.typeInvite ?? ""}"
                placeholder="${t.placeholder ?? ""}"
                .value="${t.value ?? ""}"
                ?force-alpha-numeric="${t.forceAlphaNumeric}"
                ?force-alpha-numeric-underscore="${t.forceAlphaNumericUnderscore}"
                ?force-lower-case="${t.forceLowerCase}"
                ?no-spaces="${t.noSpaces}"
                ?uc-first="${t.ucFirst}"
                ?lc-first="${t.lcFirst}"
                ?first-char-non-numeric="${t.firstCharNonNumeric}"
                ?no-leading-underscore="${t.noLeadingUnderscore}"
                ?no-trailing-underscore="${t.noTrailingUnderscore}"
                forbidden-prefixes="${t.forbiddenPrefixes ?? ""}"
                min-length="${t.minLength ?? ""}"
                max-length="${t.maxLength ?? ""}"
                data-visible-for="${t.visibleForTypes?.join(" ") ?? ""}"
                data-hidden-for="${t.hiddenForTypes?.join(" ") ?? ""}"
            ></eb-string-field>`;
		case "text": return M`<eb-textarea-field
                name="${t.name}"
                label="${W(t.label ?? "")}"
                ?advanced="${t.advancedMode || !1}"
                description="${W(t.description ?? "")}"
                help-link="${t.helpLink ?? ""}"
                placeholder="${t.placeholder ?? ""}"
                .value="${t.value ?? ""}"
            ></eb-textarea-field>`;
		case "select": return M`<eb-select-field
                name="${t.name}"
                label="${W(t.label ?? "")}"
                ?advanced="${t.advancedMode || !1}"
                description="${W(t.description ?? "")}"
                help-link="${t.helpLink ?? ""}"
                .selectValues="${t.selectValues ?? []}"
                .selectOptions="${t.selectOptions ?? []}"
                .value="${t.value ?? t.selectValues?.[0] ?? ""}"
            ></eb-select-field>`;
		case "boolean": return M`<eb-boolean-field
                name="${t.name}"
                label="${W(t.label ?? "")}"
                ?advanced="${t.advancedMode || !1}"
                description="${W(t.description ?? "")}"
                help-link="${t.helpLink ?? ""}"
                .value="${t.value ?? !1}"
                data-visible-for="${t.visibleForTypes?.join(" ") ?? ""}"
                data-hidden-for="${t.hiddenForTypes?.join(" ") ?? ""}"
            ></eb-boolean-field>`;
		case "group": return M`<eb-group
                name="${t.name ?? ""}"
                legend="${W(t.legend ?? "")}"
                ?advanced="${t.advancedMode || !1}"
                ?collapsible="${t.collapsible}"
                ?collapsed="${t.collapsed}"
                ?flatten="${t.flatten}"
                >${G(t.fields ?? [])}</eb-group
            >`;
		case "list": return M` ${t.label ? M`<label
                          class="form-label"
                          style="display:block;font-weight:600;margin-top:0.5rem"
                          ?advanced="${t.advancedMode || !1}"
                          data-visible-for="${t.visibleForTypes?.join(" ") ?? ""}"
                          data-hidden-for="${t.hiddenForTypes?.join(" ") ?? ""}"
                          >${W(t.label)}</label
                      >` : ""}
                ${t.description ? M`<small
                          style="display:block;margin-bottom:0.4rem;font-size:0.8em;color:var(--bs-secondary-color, #6c757d)"
                          >${W(t.description)}</small
                      >` : ""}
                <eb-list-field
                    name="${t.name}"
                    ?advanced="${t.advancedMode || !1}"
                    ?sortable="${t.sortable}"
                    add-label="${W("add")}"
                    element-type="${JSON.stringify(t.elementType ?? {})}"
                    data-visible-for="${t.visibleForTypes?.join(" ") ?? ""}"
                    data-hidden-for="${t.hiddenForTypes?.join(" ") ?? ""}"
                ></eb-list-field>`;
		case "inplaceedit": return M`<eb-inplace-edit name="${t.name ?? ""}" .value="${t.value ?? ""}"></eb-inplace-edit>`;
		default: return M`<eb-string-field name="${t.name}" label="${W(t.label ?? "")}"></eb-string-field>`;
	}
}
function G(e) {
	return e.map((e) => Ae(e));
}
//#endregion
//#region Resources/Public/jsDomainModeling/src/config/modelObject.js
var je = {
	name: "New Model Object",
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
					animColors: {
						from: "#cccccc",
						to: "#cccccc"
					}
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
						{ inputParams: {
							name: "uid",
							className: "hiddenField"
						} },
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
					fields: [{
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
											type: "text",
											inputParams: {
												name: "propertyDescription",
												advancedMode: !0,
												placeholder: "description",
												cols: 23,
												rows: 2
											}
										},
										{
											type: "string",
											inputParams: {
												visibleForTypes: ["File"],
												advancedMode: !0,
												label: "allowedFileTypes",
												description: "descr_allowedFileTypes",
												name: "allowedFileTypes"
											}
										},
										{
											type: "string",
											inputParams: {
												visibleForTypes: ["File", "Image"],
												advancedMode: !0,
												label: "maxItems",
												name: "maxItems",
												description: "descr_maxItems",
												value: 1
											}
										},
										{
											type: "boolean",
											inputParams: {
												label: "isRequired",
												name: "propertyIsRequired",
												advancedMode: !0,
												description: "descr_isRequired",
												value: !1
											}
										},
										{
											type: "boolean",
											inputParams: {
												hiddenForTypes: [
													"File",
													"Image",
													"PassThrough",
													"None"
												],
												label: "isNullable",
												name: "propertyIsNullable",
												advancedMode: !0,
												description: "descr_isNullable",
												value: !1
											}
										},
										{
											type: "boolean",
											inputParams: {
												label: "isExcludeField",
												name: "propertyIsExcludeField",
												advancedMode: !0,
												description: "descr_isExcludeField",
												value: !0
											}
										},
										{
											type: "boolean",
											inputParams: {
												label: "isL10nModeExclude",
												name: "propertyIsL10nModeExclude",
												advancedMode: !0,
												description: "descr_isL10nModeExclude",
												value: !1
											}
										},
										{
											type: "list",
											inputParams: {
												visibleForTypes: ["Select"],
												advancedMode: !0,
												label: "selectItems",
												name: "selectItems",
												sortable: !0,
												elementType: {
													type: "group",
													inputParams: {
														name: "selectItem",
														fields: [{
															type: "string",
															inputParams: {
																name: "label",
																placeholder: "label",
																required: !0
															}
														}, {
															type: "string",
															inputParams: {
																name: "value",
																placeholder: "value",
																required: !0
															}
														}]
													}
												}
											}
										}
									]
								}
							}
						}
					}]
				}
			},
			{
				type: "group",
				inputParams: {
					collapsible: !0,
					collapsed: !1,
					legend: "relations",
					name: "relationGroup",
					fields: [{
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
											type: "select",
											inputParams: {
												label: "type",
												name: "relationType",
												advancedMode: !0,
												description: "descr_relationType",
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
												name: "renderType",
												advancedMode: !0,
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
												advancedMode: !0,
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
						}
					}]
				}
			}
		],
		terminals: [{
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
		}]
	}
}, K = l`
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
`, Me = class extends U {
	static properties = {
		moduleId: {
			type: Number,
			attribute: "module-id"
		},
		posX: {
			type: Number,
			attribute: "pos-x"
		},
		posY: {
			type: Number,
			attribute: "pos-y"
		},
		moduleData: { type: Object },
		_name: { state: !0 },
		_resizeWidth: { state: !0 },
		_resizeHeight: { state: !0 },
		_advancedMode: { state: !0 }
	};
	static styles = [K, l`
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
            [advanced] {
                display: none;
            }
            :host([advanced-mode]) [advanced] {
                display: block;
            }
        `];
	constructor() {
		super(), this.posX = 10, this.posY = 10, this.moduleData = {}, this._name = "", this._advancedMode = !1, this._dragging = !1, this._dragOffsetX = 0, this._dragOffsetY = 0, this._resizeWidth = null, this._resizeHeight = null, this._resizing = !1, this._resizeStartX = 0, this._resizeStartY = 0, this._resizeStartW = 0, this._resizeStartH = 0;
	}
	updated(e) {
		(e.has("posX") || e.has("posY")) && (this.style.transform = `translate(${this.posX}px, ${this.posY}px)`), (e.has("_resizeWidth") || e.has("_resizeHeight")) && (this._resizeWidth !== null && (this.style.width = `${this._resizeWidth}px`), this._resizeHeight !== null && (this.style.minHeight = `${this._resizeHeight}px`)), e.has("moduleData") && (this._name = this.moduleData?.value?.name ?? "", this._populateFromValue());
	}
	connectedCallback() {
		super.connectedCallback(), this.style.transform = `translate(${this.posX}px, ${this.posY}px)`, this.addEventListener("pointerdown", this._onPointerDown.bind(this)), this.addEventListener("pointermove", this._onPointerMove.bind(this)), this.addEventListener("pointerup", this._onPointerUp.bind(this)), this._onAdvancedModeChanged = (e) => {
			this._advancedMode = e.detail.enabled, this.toggleAttribute("advanced-mode", e.detail.enabled);
		}, window.addEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	disconnectedCallback() {
		super.disconnectedCallback(), window.removeEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	_onPointerDown(e) {
		e.composedPath().some((e) => {
			if (!(e instanceof Element)) return !1;
			let t = e.tagName.toUpperCase();
			return !!([
				"BUTTON",
				"INPUT",
				"SELECT",
				"TEXTAREA",
				"A",
				"EB-TERMINAL",
				"EB-INPLACE-EDIT"
			].includes(t) || e.getAttribute?.("role") === "button");
		}) || (e.preventDefault(), this._dragging = !0, this._dragOffsetX = e.clientX - this.posX, this._dragOffsetY = e.clientY - this.posY, this.setPointerCapture(e.pointerId));
	}
	_onPointerMove(e) {
		this._dragging && (this.posX = e.clientX - this._dragOffsetX, this.posY = e.clientY - this._dragOffsetY, this.style.transform = `translate(${this.posX}px, ${this.posY}px)`, this.dispatchEvent(new CustomEvent("container-moved", {
			bubbles: !0,
			composed: !0,
			detail: {
				moduleId: this.moduleId,
				x: this.posX,
				y: this.posY
			}
		})));
	}
	_onPointerUp(e) {
		this._dragging && (this._dragging = !1, this.releasePointerCapture(e.pointerId));
	}
	_onNameChange(e) {
		this._name = e.detail.value;
	}
	_onDeleteClick(e) {
		e.stopPropagation(), this.dispatchEvent(new CustomEvent("container-removed", {
			bubbles: !0,
			composed: !0,
			detail: { moduleId: this.moduleId }
		}));
	}
	_onResizePointerDown(e) {
		e.stopPropagation(), e.preventDefault(), this._resizing = !0, this._resizeStartX = e.clientX, this._resizeStartY = e.clientY, this._resizeStartW = this.offsetWidth, this._resizeStartH = this.offsetHeight, e.currentTarget.setPointerCapture(e.pointerId);
	}
	_onResizePointerMove(e) {
		if (!this._resizing) return;
		let t = Math.min(600, Math.max(300, this._resizeStartW + (e.clientX - this._resizeStartX))), n = Math.max(80, this._resizeStartH + (e.clientY - this._resizeStartY));
		this._resizeWidth = t, this._resizeHeight = n, this.dispatchEvent(new CustomEvent("container-resized", {
			bubbles: !0,
			composed: !0,
			detail: {
				moduleId: this.moduleId,
				width: t,
				height: n
			}
		}));
	}
	_onResizePointerUp(e) {
		this._resizing && (this._resizing = !1, e.currentTarget.releasePointerCapture(e.pointerId));
	}
	get _fields() {
		return je.container.fields;
	}
	_populateFromValue() {
		let e = this.moduleData?.value ?? {}, t = this.shadowRoot?.querySelector(".card-body");
		t && Array.from(t.children).forEach((t) => {
			let n = t.getAttribute("name");
			n !== null && e[n] !== void 0 && typeof t.setValue == "function" && t.setValue(e[n]);
		});
	}
	_collectValues() {
		let e = { name: this._name ?? "" }, t = this.shadowRoot?.querySelector(".card-body");
		return t && Array.from(t.children).forEach((t) => {
			let n = t.getAttribute("name");
			n !== null && typeof t.getValue == "function" && (e[n] = t.getValue());
		}), e;
	}
	serialize() {
		return {
			config: { position: [this.posX, this.posY] },
			value: this._collectValues()
		};
	}
	render() {
		return M`
            <div class="card-header">
                <span class="drag-handle" aria-hidden="true">⠿</span>
                <eb-terminal
                    type="input"
                    terminal-id="SOURCES"
                    uid="${this.moduleData?.value?.objectsettings?.uid ?? ""}"
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
            <div class="card-body">${G(this._fields.slice(1))}</div>
            <div
                class="resize-handle"
                @pointerdown="${this._onResizePointerDown}"
                @pointermove="${this._onResizePointerMove}"
                @pointerup="${this._onResizePointerUp}"
            ></div>
        `;
	}
};
customElements.define("eb-container", Me);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-layer.js
var Ne = class extends U {
	static properties = {
		_wires: { state: !0 },
		_containers: { state: !0 },
		_drawingWire: { state: !0 },
		_tempWire: { state: !0 },
		_hoveredWireId: { state: !0 },
		_panOffset: { state: !0 }
	};
	static styles = l`
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
    `;
	constructor() {
		super(), this._wires = [], this._containers = [], this._drawingWire = null, this._tempWire = null, this._hoveredWireId = null, this._panOffset = {
			x: 0,
			y: 0
		}, this._isPanning = !1, this._panStartX = 0, this._panStartY = 0;
	}
	connectedCallback() {
		super.connectedCallback(), this.addEventListener("terminal-connect", this._onTerminalConnect.bind(this)), this.addEventListener("container-moved", this._onContainerMoved.bind(this)), this.addEventListener("container-removed", this._onContainerRemoved.bind(this)), this._boundPointerMove = this._onPointerMove.bind(this), this._boundPointerUp = this._onPointerUp.bind(this), window.addEventListener("pointermove", this._boundPointerMove), window.addEventListener("pointerup", this._boundPointerUp);
	}
	disconnectedCallback() {
		super.disconnectedCallback(), window.removeEventListener("pointermove", this._boundPointerMove), window.removeEventListener("pointerup", this._boundPointerUp);
	}
	_onCanvasPointerDown(e) {
		let t = this.shadowRoot.querySelector("#canvas"), n = this.shadowRoot.querySelector("#pan-surface");
		e.target !== t && e.target !== n || (this._isPanning = !0, this._panStartX = e.clientX - this._panOffset.x, this._panStartY = e.clientY - this._panOffset.y, t.style.cursor = "grabbing");
	}
	_onTerminalConnect(e) {
		let { terminalId: t, uid: n, sourceEl: r } = e.detail, i = this.getBoundingClientRect(), a = r.getBoundingClientRect(), o = a.left - i.left + a.width / 2, s = a.top - i.top + a.height / 2, c = r.getRootNode()?.host;
		this._drawingWire = {
			terminalId: t,
			uid: n,
			sourceEl: r,
			moduleId: parseInt(c?.getAttribute("module-id") ?? "-1"),
			startX: o,
			startY: s,
			mouseX: o,
			mouseY: s
		};
	}
	_onContainerMoved(e) {
		this._updateWirePositions();
	}
	_onContainerRemoved(e) {
		let { moduleId: r } = e.detail, i = this._wires.filter((e) => e.srcModuleId === r || e.tgtModuleId === r);
		if (i.length === 0) {
			this._removeContainer(r);
			return;
		}
		t.confirm("Delete model object", `This model object has ${i.length} relation(s) connected to it. Deleting it will also remove those relations. Continue?`, n.warning, [{
			text: "Cancel",
			btnClass: "btn-default",
			trigger: () => t.dismiss()
		}, {
			text: "Delete",
			btnClass: "btn-danger",
			trigger: () => {
				t.dismiss(), this._removeContainer(r);
			}
		}]);
	}
	_removeContainer(e) {
		this._containers = this._containers.filter((t) => t.moduleId !== e), this._wires = this._wires.filter((t) => t.srcModuleId !== e && t.tgtModuleId !== e), this._dispatchChanged();
	}
	_onPointerMove(e) {
		if (this._isPanning) {
			this._panOffset = {
				x: e.clientX - this._panStartX,
				y: e.clientY - this._panStartY
			};
			return;
		}
		if (!this._drawingWire) return;
		let t = this.getBoundingClientRect(), n = e.clientX - t.left, r = e.clientY - t.top;
		this._drawingWire = {
			...this._drawingWire,
			mouseX: n,
			mouseY: r
		}, this._tempWire = {
			x1: this._drawingWire.startX,
			y1: this._drawingWire.startY,
			x2: n,
			y2: r
		};
	}
	_onPointerUp(e) {
		if (this._isPanning) {
			this._isPanning = !1, this.shadowRoot.querySelector("#canvas").style.cursor = "grab", this._updateWirePositions();
			return;
		}
		if (!this._drawingWire) return;
		let t = this._drawingWire;
		this._drawingWire = null, this._tempWire = null;
		let n = e.composedPath().find((e) => e.tagName === "EB-TERMINAL" && e.hasAttribute("droppable"));
		if (!n) return;
		let r = n.getAttribute("terminal-id"), i = n.uid ?? n.getAttribute("uid") ?? "", a = null, o = n.getRootNode()?.host;
		for (; o;) {
			if (o.tagName === "EB-CONTAINER") {
				a = parseInt(o.getAttribute("module-id"));
				break;
			}
			o = o.getRootNode()?.host;
		}
		if (a === null || a === t.moduleId || this._wires.some((e) => e.srcModuleId === a && e.tgtModuleId === t.moduleId && e.srcTerminal === r)) return;
		let s = this._findTerminalEl(r, a), c = this._findTerminalEl(t.terminalId, t.moduleId), l = s && c ? this._getWirePositions(s, c) : {
			x1: 0,
			y1: 0,
			x2: 0,
			y2: 0
		};
		this._wires = [...this._wires, {
			id: `wire-${a}-${r}-${t.moduleId}-${t.terminalId}`,
			srcTerminal: r,
			tgtTerminal: t.terminalId,
			srcUid: i,
			tgtUid: t.uid,
			srcModuleId: a,
			tgtModuleId: t.moduleId,
			...l
		}], this._dispatchChanged();
	}
	_deleteWire(e) {
		this._wires = this._wires.filter((t) => t.id !== e), this._dispatchChanged();
	}
	_updateWirePositions() {
		this.updateComplete.then(() => {
			this._wires = this._wires.map((e) => {
				let t = this._findTerminalEl(e.srcTerminal, e.srcModuleId), n = this._findTerminalEl(e.tgtTerminal, e.tgtModuleId);
				return !t || !n ? e : {
					...e,
					...this._getWirePositions(t, n)
				};
			});
		});
	}
	_findTerminalEl(e, t) {
		let n = e.replace(/^relationWire_(\d+)$/, "REL_$1"), r = this.shadowRoot.querySelector(`eb-container[module-id="${t}"]`);
		return r ? this._deepQuerySelector(r, `eb-terminal[terminal-id="${n}"]`) : null;
	}
	_deepQuerySelector(e, t) {
		let n = e.shadowRoot;
		if (!n) return null;
		let r = n.querySelector(t);
		if (r) return r;
		for (let e of n.querySelectorAll("*")) if (e.shadowRoot) {
			let n = this._deepQuerySelector(e, t);
			if (n) return n;
		}
		return null;
	}
	_getWirePositions(e, t) {
		let n = this.getBoundingClientRect(), r = e.getBoundingClientRect(), i = t.getBoundingClientRect();
		return {
			x1: r.left - n.left + r.width / 2,
			y1: r.top - n.top + r.height / 2,
			x2: i.left - n.left + i.width / 2,
			y2: i.top - n.top + i.height / 2
		};
	}
	_dispatchChanged() {
		this.dispatchEvent(new CustomEvent("eb-layer-changed", {
			bubbles: !0,
			composed: !0
		}));
	}
	addContainer(e) {
		let t = this._containers.length, n = parseInt(Date.now() * Math.random()) || Date.now(), r = {
			...e,
			value: {
				...e.value,
				objectsettings: {
					...e.value?.objectsettings,
					uid: e.value?.objectsettings?.uid || n
				}
			}
		};
		this._containers = [...this._containers, {
			moduleId: t,
			posX: 20 + t * 20,
			posY: 20 + t * 20,
			moduleData: r
		}], this._dispatchChanged();
	}
	addContainers(e) {
		this._containers = e.map((e, t) => ({
			moduleId: t,
			posX: e.config?.position?.[0] ?? 10 + t * 180,
			posY: e.config?.position?.[1] ?? 10,
			moduleData: e
		}));
	}
	async _awaitAllUpdates(e) {
		if (!e.shadowRoot) return;
		let t = Array.from(e.shadowRoot.querySelectorAll("*")).filter((e) => e.updateComplete instanceof Promise);
		t.length !== 0 && (await Promise.all(t.map((e) => e.updateComplete)), await Promise.all(t.map((e) => this._awaitAllUpdates(e))));
	}
	addWires(e, t) {
		this.updateComplete.then(async () => {
			let t = Array.from(this.shadowRoot.querySelectorAll("eb-container"));
			await Promise.all(t.map((e) => e.updateComplete)), await Promise.all(t.map((e) => this._awaitAllUpdates(e))), this._wires = e.map((e) => {
				let t = this._findTerminalEl(e.src.terminal, e.src.moduleId), n = this._findTerminalEl(e.tgt.terminal, e.tgt.moduleId), r = t && n ? this._getWirePositions(t, n) : {
					x1: 0,
					y1: 0,
					x2: 0,
					y2: 0
				};
				return {
					id: `wire-${e.src.moduleId}-${e.src.terminal}-${e.tgt.moduleId}`,
					srcTerminal: e.src.terminal,
					tgtTerminal: e.tgt.terminal,
					srcUid: e.src.uid,
					tgtUid: e.tgt.uid,
					srcModuleId: e.src.moduleId,
					tgtModuleId: e.tgt.moduleId,
					...r
				};
			});
		});
	}
	serialize() {
		return {
			modules: Array.from(this.shadowRoot.querySelectorAll("eb-container")).map((e) => e.serialize()),
			wires: this._wires.filter((e) => e.srcTerminal && this._findTerminalEl(e.srcTerminal, e.srcModuleId) !== null).map((e) => ({
				src: {
					moduleId: e.srcModuleId,
					terminal: e.srcTerminal,
					uid: e.srcUid
				},
				tgt: {
					moduleId: e.tgtModuleId,
					terminal: e.tgtTerminal,
					uid: e.tgtUid
				}
			}))
		};
	}
	_wireMidpoint(e) {
		return {
			x: (e.x1 + e.x2) / 2,
			y: (e.y1 + e.y2) / 2
		};
	}
	_wirePath(e) {
		return `M ${e.x1} ${e.y1} C ${e.x1} ${e.y1 + 80}, ${e.x2} ${e.y2 - 80}, ${e.x2} ${e.y2}`;
	}
	render() {
		let { x: e, y: t } = this._panOffset;
		return M`
            <div id="canvas" @pointerdown="${this._onCanvasPointerDown}">
                <div id="pan-surface" style="transform: translate(${e}px, ${t}px)">
                    ${this._containers.map((e) => M`
                            <eb-container
                                module-id="${e.moduleId}"
                                pos-x="${e.posX}"
                                pos-y="${e.posY}"
                                .moduleData="${e.moduleData}"
                            >
                            </eb-container>
                        `)}
                </div>
                <svg id="wire-overlay">
                    ${this._wires.map((e) => {
			let t = this._wireMidpoint(e), n = this._wirePath(e);
			return N`
                            <g class="wire-group">
                                <path
                                    class="wire-hit-area"
                                    d="${n}"
                                    stroke-width="12"
                                    fill="none"
                                />
                                <path
                                    class="wire-path"
                                    d="${n}"
                                    stroke-width="2"
                                    fill="none"
                                    stroke-linecap="round"
                                    pointer-events="none"
                                />
                                <g
                                    class="wire-delete-btn"
                                    @click="${() => this._deleteWire(e.id)}"
                                    aria-label="Delete wire"
                                    role="button"
                                >
                                    <circle cx="${t.x}" cy="${t.y}" r="9" />
                                    <text x="${t.x}" y="${t.y}">×</text>
                                </g>
                            </g>
                        `;
		})}
                    ${this._tempWire ? N`
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
};
customElements.define("eb-layer", Ne);
//#endregion
//#region Resources/Public/jsDomainModeling/src/styles/button-styles.js
var Pe = l`
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
`, q = class extends U {
	static properties = {
		name: { type: String },
		label: { type: String },
		value: {},
		required: { type: Boolean },
		advanced: { type: Boolean }
	};
	_fireUpdated() {
		this.dispatchEvent(new CustomEvent("field-updated", {
			bubbles: !0,
			composed: !0,
			detail: {
				name: this.name,
				value: this.getValue()
			}
		}));
	}
	getValue() {
		return this.value;
	}
	setValue(e) {
		this.value = e;
	}
	validate() {
		return !0;
	}
}, Fe = class extends q {
	static properties = {
		...q.properties,
		placeholder: { type: String },
		typeInvite: {
			type: String,
			attribute: "type-invite"
		},
		forceAlphaNumeric: {
			type: Boolean,
			attribute: "force-alpha-numeric"
		},
		forceAlphaNumericUnderscore: {
			type: Boolean,
			attribute: "force-alpha-numeric-underscore"
		},
		forceLowerCase: {
			type: Boolean,
			attribute: "force-lower-case"
		},
		noSpaces: {
			type: Boolean,
			attribute: "no-spaces"
		},
		lcFirst: {
			type: Boolean,
			attribute: "lc-first"
		},
		ucFirst: {
			type: Boolean,
			attribute: "uc-first"
		},
		firstCharNonNumeric: {
			type: Boolean,
			attribute: "first-char-non-numeric"
		},
		noLeadingUnderscore: {
			type: Boolean,
			attribute: "no-leading-underscore"
		},
		noTrailingUnderscore: {
			type: Boolean,
			attribute: "no-trailing-underscore"
		},
		forbiddenPrefixes: {
			type: String,
			attribute: "forbidden-prefixes"
		},
		minLength: {
			type: Number,
			attribute: "min-length"
		},
		maxLength: {
			type: Number,
			attribute: "max-length"
		},
		description: { type: String },
		helpLink: {
			type: String,
			attribute: "help-link"
		},
		_error: { state: !0 }
	};
	static styles = [K, l`
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
        `];
	_getValidationError(e) {
		if (this.required && !e) return "Required";
		if (!e) return null;
		if (this.minLength && e.length < this.minLength) return `Minimum ${this.minLength} characters`;
		if (this.maxLength && e.length > this.maxLength) return `Maximum ${this.maxLength} characters`;
		if (this.firstCharNonNumeric && /^[0-9]/.test(e)) return "Must not start with a number";
		if (this.noLeadingUnderscore && e.startsWith("_")) return "Must not start with an underscore";
		if (this.noTrailingUnderscore && e.endsWith("_")) return "Must not end with an underscore";
		if (this.forbiddenPrefixes) {
			let t = this.forbiddenPrefixes.split(" ").filter(Boolean).find((t) => e.startsWith(t));
			if (t) return `Must not start with "${t}"`;
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
		let e = this.placeholder || this.typeInvite || "", t = `eb-str-${this.name}`, n = `${t}-error`;
		return M`
            ${this.label ? M`<label class="form-label" for="${t}">${this.label}</label>` : ""}
            ${this.helpLink ? M`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
            <input
                id="${t}"
                class="form-control${this._error ? " is-invalid" : ""}"
                type="text"
                .value="${this.value ?? ""}"
                placeholder="${e}"
                ?aria-required="${this.required}"
                aria-invalid="${this._error ? "true" : "false"}"
                aria-describedby="${this._error ? n : ""}"
                @input="${this._onInput}"
            />
            ${this._error ? M`<div id="${n}" class="invalid-feedback" role="alert">${this._error}</div>` : ""}
            ${this.description ? M`<small class="help-text">${this.description}</small>` : ""}
        `;
	}
};
customElements.define("eb-string-field", Fe);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-textarea-field.js
var Ie = class extends q {
	static properties = {
		...q.properties,
		rows: { type: Number },
		description: { type: String },
		helpLink: {
			type: String,
			attribute: "help-link"
		}
	};
	static styles = [K, l`
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
        `];
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
		let e = `eb-ta-${this.name}`;
		return M`
            <div class="form-group">
                ${this.label ? M`<label class="form-label" for="${e}">${this.label}</label>` : ""}
                ${this.helpLink ? M`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
                <textarea
                    id="${e}"
                    class="form-control"
                    rows="${this.rows}"
                    ?aria-required="${this.required}"
                    @input="${this._onInput}"
                >
${this.value ?? ""}</textarea
                >
                ${this.description ? M`<small class="help-text">${this.description}</small>` : ""}
            </div>
        `;
	}
};
customElements.define("eb-textarea-field", Ie);
//#endregion
//#region node_modules/.pnpm/lit-html@3.3.2/node_modules/lit-html/directive.js
var Le = {
	ATTRIBUTE: 1,
	CHILD: 2,
	PROPERTY: 3,
	BOOLEAN_ATTRIBUTE: 4,
	EVENT: 5,
	ELEMENT: 6
}, Re = (e) => (...t) => ({
	_$litDirective$: e,
	values: t
}), ze = class {
	constructor(e) {}
	get _$AU() {
		return this._$AM._$AU;
	}
	_$AT(e, t, n) {
		this._$Ct = e, this._$AM = t, this._$Ci = n;
	}
	_$AS(e, t) {
		return this.update(e, t);
	}
	update(e, t) {
		return this.render(...t);
	}
}, { I: Be } = we, Ve = (e) => e, He = () => document.createComment(""), J = (e, t, n) => {
	let r = e._$AA.parentNode, i = t === void 0 ? e._$AB : t._$AA;
	if (n === void 0) n = new Be(r.insertBefore(He(), i), r.insertBefore(He(), i), e, e.options);
	else {
		let t = n._$AB.nextSibling, a = n._$AM, o = a !== e;
		if (o) {
			let t;
			n._$AQ?.(e), n._$AM = e, n._$AP !== void 0 && (t = e._$AU) !== a._$AU && n._$AP(t);
		}
		if (t !== i || o) {
			let e = n._$AA;
			for (; e !== t;) {
				let t = Ve(e).nextSibling;
				Ve(r).insertBefore(e, i), e = t;
			}
		}
	}
	return n;
}, Y = (e, t, n = e) => (e._$AI(t, n), e), Ue = {}, We = (e, t = Ue) => e._$AH = t, Ge = (e) => e._$AH, X = (e) => {
	e._$AR(), e._$AA.remove();
}, Ke = (e, t, n) => {
	let r = /* @__PURE__ */ new Map();
	for (let i = t; i <= n; i++) r.set(e[i], i);
	return r;
}, Z = Re(class extends ze {
	constructor(e) {
		if (super(e), e.type !== Le.CHILD) throw Error("repeat() can only be used in text expressions");
	}
	dt(e, t, n) {
		let r;
		n === void 0 ? n = t : t !== void 0 && (r = t);
		let i = [], a = [], o = 0;
		for (let t of e) i[o] = r ? r(t, o) : o, a[o] = n(t, o), o++;
		return {
			values: a,
			keys: i
		};
	}
	render(e, t, n) {
		return this.dt(e, t, n).values;
	}
	update(e, [t, n, r]) {
		let i = Ge(e), { values: a, keys: o } = this.dt(t, n, r);
		if (!Array.isArray(i)) return this.ut = o, a;
		let s = this.ut ??= [], c = [], l, u, d = 0, f = i.length - 1, p = 0, m = a.length - 1;
		for (; d <= f && p <= m;) if (i[d] === null) d++;
		else if (i[f] === null) f--;
		else if (s[d] === o[p]) c[p] = Y(i[d], a[p]), d++, p++;
		else if (s[f] === o[m]) c[m] = Y(i[f], a[m]), f--, m--;
		else if (s[d] === o[m]) c[m] = Y(i[d], a[m]), J(e, c[m + 1], i[d]), d++, m--;
		else if (s[f] === o[p]) c[p] = Y(i[f], a[p]), J(e, i[d], i[f]), f--, p++;
		else if (l === void 0 && (l = Ke(o, p, m), u = Ke(s, d, f)), l.has(s[d])) if (l.has(s[f])) {
			let t = u.get(o[p]), n = t === void 0 ? null : i[t];
			if (n === null) {
				let t = J(e, i[d]);
				Y(t, a[p]), c[p] = t;
			} else c[p] = Y(n, a[p]), J(e, i[d], n), i[t] = null;
			p++;
		} else X(i[f]), f--;
		else X(i[d]), d++;
		for (; p <= m;) {
			let t = J(e, c[m + 1]);
			Y(t, a[p]), c[p++] = t;
		}
		for (; d <= f;) {
			let e = i[d++];
			e !== null && X(e);
		}
		return this.ut = o, We(e, c), P;
	}
}), qe = class extends q {
	static properties = {
		...q.properties,
		selectValues: {
			type: Array,
			attribute: "select-values"
		},
		selectOptions: {
			type: Array,
			attribute: "select-options"
		},
		allowedValues: { type: Array },
		description: { type: String },
		helpLink: {
			type: String,
			attribute: "help-link"
		}
	};
	static styles = [K, l`
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
        `];
	_getOptions() {
		let e = this.selectValues ?? [], t = this.selectOptions ?? e;
		return e.map((e, n) => ({
			value: e,
			label: t[n] ?? e
		}));
	}
	_visibleOptions() {
		let e = this._getOptions();
		return this.allowedValues ? e.filter((e) => this.allowedValues.includes(e.value)) : e;
	}
	updated(e) {
		if (e.has("allowedValues") && this.allowedValues) {
			let e = this._visibleOptions();
			e.length > 0 && !e.some((e) => e.value === this.value) && (this.value = e[0].value, this._fireUpdated());
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
		let e = this._visibleOptions(), t = `eb-sel-${this.name}`;
		return M`
            <div class="form-group">
                ${this.label ? M`<label class="form-label" for="${t}">${this.label}</label>` : ""}
                ${this.helpLink ? M`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
                <select
                    id="${t}"
                    class="form-select"
                    aria-label="${this.label || this.name}"
                    @change="${this._onChange}"
                >
                    ${Z(e, (e) => e.value, (e) => M`
                            <option value="${e.value}" ?selected="${this.value === e.value}">${e.label}</option>
                        `)}
                </select>
                ${this.description ? M`<small class="help-text">${this.description}</small>` : ""}
            </div>
        `;
	}
};
customElements.define("eb-select-field", qe);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-boolean-field.js
var Je = class extends q {
	static properties = {
		...q.properties,
		description: { type: String },
		helpLink: {
			type: String,
			attribute: "help-link"
		}
	};
	static styles = [K, l`
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
        `];
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
		return M`
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
                ${this.helpLink ? M`<a href="${this.helpLink}" target="_blank" class="help-link" title="Documentation">?</a>` : ""}
            </div>
            ${this.description ? M`<small class="help-text">${this.description}</small>` : ""}
        `;
	}
};
customElements.define("eb-boolean-field", Je);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-hidden-field.js
var Ye = class extends q {
	static properties = { ...q.properties };
	static styles = l`
        :host {
            display: none;
        }
    `;
	getValue() {
		return this.value ?? "";
	}
	setValue(e) {
		this.value = e;
	}
	render() {
		return M`<input type="hidden" .value="${this.value ?? ""}" />`;
	}
};
customElements.define("eb-hidden-field", Ye);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-group.js
var Xe = class extends U {
	static properties = {
		name: { type: String },
		legend: { type: String },
		collapsible: { type: Boolean },
		collapsed: {
			type: Boolean,
			reflect: !0
		},
		advancedMode: {
			type: Boolean,
			attribute: "advanced-mode",
			reflect: !0
		},
		flatten: { type: Boolean }
	};
	static styles = l`
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
    `;
	connectedCallback() {
		super.connectedCallback(), this.addEventListener("field-updated", this._onFieldUpdated), this._onAdvancedModeChanged = (e) => {
			this.advancedMode = e.detail.enabled;
		}, window.addEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	disconnectedCallback() {
		super.disconnectedCallback(), this.removeEventListener("field-updated", this._onFieldUpdated), window.removeEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	_onFieldUpdated(e) {
		if (e.detail?.name === "relationType") {
			let t = this.querySelector("[name=renderType]");
			if (!t) return;
			t.allowedValues = {
				zeroToOne: [
					"selectSingle",
					"selectMultipleSideBySide",
					"inline"
				],
				manyToOne: ["selectSingle", "selectMultipleSideBySide"],
				zeroToMany: ["inline", "selectMultipleSideBySide"],
				manyToMany: [
					"selectMultipleSideBySide",
					"selectSingleBox",
					"selectCheckBox"
				]
			}[e.detail.value] ?? null;
		}
		e.detail?.name === "propertyType" && this._applyPropertyTypeVisibility(e.detail.value);
	}
	_applyPropertyTypeVisibility(e) {
		this.querySelectorAll("[data-visible-for]").forEach((t) => {
			let n = (t.getAttribute("data-visible-for") || "").split(" ").filter(Boolean);
			n.length > 0 && (t.style.display = n.includes(e) ? "" : "none");
		}), this.querySelectorAll("[data-hidden-for]").forEach((t) => {
			let n = (t.getAttribute("data-hidden-for") || "").split(" ").filter(Boolean);
			n.length > 0 && (t.style.display = n.includes(e) ? "none" : "");
		});
	}
	_initPropertyTypes() {
		let e = this.querySelector("[name=propertyType]");
		if (!e) return;
		let t = e.getValue?.() ?? e.value;
		t && this._applyPropertyTypeVisibility(t);
	}
	_initRelationTypes() {
		this.querySelectorAll("[name=relationType]").forEach((e) => {
			let t = e.value ?? e.getValue?.();
			if (!t) return;
			let n = this.querySelector("[name=renderType]");
			n && (n.allowedValues = {
				zeroToOne: [
					"selectSingle",
					"selectMultipleSideBySide",
					"inline"
				],
				manyToOne: ["selectSingle", "selectMultipleSideBySide"],
				zeroToMany: ["inline", "selectMultipleSideBySide"],
				manyToMany: [
					"selectMultipleSideBySide",
					"selectSingleBox",
					"selectCheckBox"
				]
			}[t] ?? null);
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
	getValue() {
		let e = {};
		return Array.from(this.children).forEach((t) => {
			if (typeof t.getValue != "function") return;
			if (t.tagName?.toLowerCase() === "eb-group" && t.flatten) {
				Object.assign(e, t.getValue());
				return;
			}
			let n = t.getAttribute("name");
			n !== null && (e[n] = t.getValue());
		}), e;
	}
	setValue(e) {
		e && (Array.from(this.children).forEach((t) => {
			if (typeof t.setValue != "function") return;
			if (t.tagName?.toLowerCase() === "eb-group" && t.flatten) {
				t.setValue(e);
				return;
			}
			let n = t.getAttribute("name");
			n !== null && e[n] !== void 0 && t.setValue(e[n]);
		}), this._initPropertyTypes());
	}
	render() {
		return M`
            <div class="card" role="group" aria-label="${this.legend || this.name || "Group"}">
                ${this.legend ? M`
                          <div
                              class="card-header"
                              @click="${this._toggleCollapse}"
                              @keydown="${this._onHeaderKeyDown}"
                              role="${this.collapsible ? "button" : F}"
                              tabindex="${this.collapsible ? "0" : F}"
                              aria-expanded="${this.collapsible ? String(!this.collapsed) : F}"
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
};
customElements.define("eb-group", Xe);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-list-field.js
function Ze(e) {
	let t = window.TYPO3?.settings?.extensionBuilder?.publicResourceWebPath?.core ?? "";
	return t ? `${t}Icons/T3Icons/sprites/actions.svg#${e}` : "";
}
var Qe = {
	"actions-caret-up": "↑",
	"actions-caret-down": "↓",
	"actions-delete": "✕"
};
function Q(e) {
	let t = Ze(e);
	return t ? M`
        <svg width="16" height="16" aria-hidden="true">
            <use href="${t}"></use>
        </svg>
    ` : M`<span aria-hidden="true">${Qe[e] ?? e}</span>`;
}
var $e = class extends U {
	static properties = {
		name: { type: String },
		sortable: { type: Boolean },
		addLabel: {
			type: String,
			attribute: "add-label"
		},
		elementType: {
			type: String,
			attribute: "element-type"
		},
		_items: { state: !0 }
	};
	static styles = [
		Pe,
		K,
		l`
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
            .item-actions {
                display: flex;
                gap: 2px;
                align-items: flex-start;
                padding-top: 2px;
                flex-shrink: 0;
            }
            .item-actions .btn {
                padding: 2px 4px;
                line-height: 1;
            }
            .add-btn {
                margin-top: 4px;
            }
            .item-terminal {
                display: flex;
                align-items: center;
                padding-top: 4px;
            }
            [advanced] {
                display: none;
            }
            :host([advanced-mode]) [advanced] {
                display: block;
            }
        `
	];
	constructor() {
		super(), this.sortable = !0, this.addLabel = "add", this._items = [];
	}
	connectedCallback() {
		super.connectedCallback(), this._onAdvancedModeChanged = (e) => {
			this.toggleAttribute("advanced-mode", e.detail.enabled);
		}, window.addEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	disconnectedCallback() {
		super.disconnectedCallback(), window.removeEventListener("eb-advanced-mode-changed", this._onAdvancedModeChanged);
	}
	get _elementTypeDef() {
		try {
			return JSON.parse(this.elementType || "null");
		} catch {
			return null;
		}
	}
	get _isWirable() {
		return (this._elementTypeDef?.inputParams?.fields ?? []).some((e) => e.inputParams?.wirable);
	}
	_addItem() {
		let e = parseInt(Date.now() * Math.random()) || Date.now(), t = this._items.length;
		this._items = [...this._items, { uid: e }], this.updateComplete.then(() => {
			let n = Array.from(this.shadowRoot?.querySelectorAll(".item-content") ?? [])[t];
			n && n.querySelector("[name=\"uid\"]")?.setValue?.(String(e));
		}), this._fireUpdated();
	}
	_removeItem(e) {
		this._items = this._items.filter((t, n) => n !== e), this._fireUpdated();
	}
	_moveUp(e) {
		if (e === 0) return;
		let t = [...this._items];
		[t[e - 1], t[e]] = [t[e], t[e - 1]], this._items = t, this._fireUpdated();
	}
	_moveDown(e) {
		if (e >= this._items.length - 1) return;
		let t = [...this._items];
		[t[e], t[e + 1]] = [t[e + 1], t[e]], this._items = t, this._fireUpdated();
	}
	_fireUpdated() {
		this.dispatchEvent(new CustomEvent("list-updated", {
			bubbles: !0,
			composed: !0,
			detail: { value: this.getValue() }
		}));
	}
	getValue() {
		let e = this.shadowRoot?.querySelectorAll(".item-content") ?? [];
		return Array.from(e).map((e) => {
			let t = e.querySelector("eb-group");
			return t?.getValue ? t.getValue() : e.querySelector("[name]")?.getValue?.() ?? null;
		});
	}
	setValue(e) {
		Array.isArray(e) && (this._items = e.map((e, t) => ({ uid: t })), this.updateComplete.then(() => {
			let t = this.shadowRoot?.querySelectorAll(".item-content") ?? [];
			e.forEach((e, n) => {
				if (!e) return;
				let r = t[n];
				if (!r) return;
				let i = r.querySelector("eb-group");
				if (i?.setValue) {
					i.setValue(e);
					return;
				}
				r.querySelector("[name]")?.setValue?.(e);
			});
		}));
	}
	render() {
		let e = this._elementTypeDef, t = this._isWirable;
		return M`
            ${Z(this._items, (e) => e.uid, (n, r) => M`
                    <div class="item-row">
                        ${t ? M`
                                  <div class="item-terminal">
                                      <eb-terminal droppable terminal-id="REL_${r}" uid="${n.uid}"></eb-terminal>
                                  </div>
                              ` : F}
                        <div class="item-content">${e ? Ae(e) : F}</div>
                        <div class="item-actions">
                            ${this.sortable ? M`
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveUp(r)}"
                                          title="Move up"
                                      >
                                          ${Q("actions-caret-up")}
                                      </button>
                                      <button
                                          class="btn btn-default btn-sm"
                                          @click="${() => this._moveDown(r)}"
                                          title="Move down"
                                      >
                                          ${Q("actions-caret-down")}
                                      </button>
                                  ` : F}
                            <button
                                class="btn btn-default btn-sm btn-delete"
                                @click="${() => this._removeItem(r)}"
                                title="Remove"
                            >
                                ${Q("actions-delete")}
                            </button>
                        </div>
                    </div>
                `)}
            <button class="btn btn-default btn-sm add-btn" @click="${this._addItem}">${this.addLabel}</button>
        `;
	}
};
customElements.define("eb-list-field", $e);
//#endregion
//#region Resources/Public/jsDomainModeling/src/config/extensionProperties.js
var et = [
	{
		type: "string",
		inputParams: {
			name: "name",
			label: "name",
			typeInvite: "extensionTitle",
			description: "descr_name",
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
	{ inputParams: {
		name: "originalExtensionKey",
		className: "hiddenField"
	} },
	{ inputParams: {
		name: "originalVendorName",
		className: "hiddenField"
	} },
	{
		type: "text",
		inputParams: {
			name: "description",
			label: "description",
			typeInvite: "description",
			description: "descr_description",
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
						description: "descr_version",
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
						description: "descr_state",
						selectValues: [
							"alpha",
							"beta",
							"stable",
							"experimental",
							"test"
						],
						selectOptions: [
							"alpha",
							"beta",
							"stable",
							"experimental",
							"test"
						]
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
						selectValues: ["14.0.0-14.3.99"],
						selectOptions: ["TYPO3 v14.3"],
						value: "14.0.0-14.3.99"
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
						value: "typo3 => 14.0.0-14.3.99\n"
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
			description: "descr_persons",
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
								fields: [{
									type: "text",
									inputParams: {
										name: "controllerActionCombinations",
										label: "controller_action_combinations",
										description: "descr_controller_action_combinations",
										placeholder: "ControllerName => action1,action2",
										cols: 38,
										rows: 3
									}
								}, {
									type: "text",
									inputParams: {
										name: "noncacheableActions",
										label: "noncacheable_actions",
										placeholder: "ControllerName => action1,action2",
										description: "descr_noncacheable_actions",
										cols: 38,
										rows: 3
									}
								}]
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
								name: "tabLabel",
								description: "descr_tabLabel"
							}
						},
						{
							type: "select",
							inputParams: {
								label: "mainModule",
								name: "mainModule",
								description: "descr_mainModule",
								required: !0,
								selectValues: [
									"web",
									"site",
									"file",
									"user",
									"tools",
									"system",
									"help"
								]
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
								fields: [{
									type: "text",
									inputParams: {
										name: "controllerActionCombinations",
										label: "controller_action_combinations",
										placeholder: "ControllerName => action1,action2",
										description: "descr_controller_action_combinations",
										cols: 38,
										rows: 3
									}
								}]
							}
						}
					]
				}
			}
		}
	}
], tt = class extends U {
	static properties = {
		smdUrl: {
			type: String,
			attribute: "smd-url"
		},
		extensionName: {
			type: String,
			attribute: "extension-name"
		},
		initialWarnings: {
			type: Array,
			attribute: "initial-warnings"
		},
		composerWarning: {
			type: String,
			attribute: "composer-warning"
		},
		_loading: { state: !0 },
		_extensionData: { state: !0 },
		_advancedMode: { state: !0 },
		_leftCollapsed: { state: !0 },
		_isDirty: { state: !0 }
	};
	static styles = [
		Pe,
		K,
		l`
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
	];
	constructor() {
		super(), this.smdUrl = "", this.extensionName = "", this.initialWarnings = [], this.composerWarning = "", this._loading = !1, this._extensionData = null, this._advancedMode = !1, this._leftCollapsed = !1, this._isDirty = !1;
	}
	async firstUpdated() {
		this.composerWarning && this._showComposerWarningModal();
	}
	_showComposerWarningModal() {
		let e = document.createElement("div"), r = document.createElement("p");
		r.textContent = "TYPO3 is running in composer mode, but no local path repository for \"packages/*\" is configured in your composer.json. The Extension Builder cannot save extensions without this configuration.";
		let i = document.createElement("p");
		i.textContent = "Run the following command in your project root to fix this:";
		let a = document.createElement("pre");
		a.style.cssText = "background:var(--bs-tertiary-bg);color:var(--bs-body-color);border:1px solid var(--bs-border-color);padding:0.75rem 1rem;border-radius:4px;font-size:0.9em;white-space:pre-wrap;", a.textContent = "mkdir -p packages && composer config repositories.local path \"packages/*\"", e.appendChild(r), e.appendChild(i), e.appendChild(a), t.confirm("Composer Mode — Configuration Required", e, n.warning, [{
			text: "Close",
			btnClass: "btn-warning",
			trigger: () => t.dismiss()
		}]);
	}
	async connectedCallback() {
		super.connectedCallback(), this.initialWarnings?.length > 0 && this.initialWarnings.forEach((t) => e.warning("Configuration", t)), this.addEventListener("field-updated", this._onFieldUpdated), this._boundMarkDirty = this._markDirty.bind(this), this.addEventListener("container-moved", this._boundMarkDirty), this.addEventListener("container-removed", this._boundMarkDirty), this.addEventListener("container-resized", this._boundMarkDirty), this.addEventListener("eb-layer-changed", this._boundMarkDirty), this._beforeUnload = (e) => {
			this._isDirty && e.preventDefault();
		}, window.addEventListener("beforeunload", this._beforeUnload), this.extensionName && await this.load();
	}
	disconnectedCallback() {
		super.disconnectedCallback(), this.removeEventListener("field-updated", this._onFieldUpdated), this.removeEventListener("container-moved", this._boundMarkDirty), this.removeEventListener("container-removed", this._boundMarkDirty), this.removeEventListener("container-resized", this._boundMarkDirty), this.removeEventListener("eb-layer-changed", this._boundMarkDirty), window.removeEventListener("beforeunload", this._beforeUnload);
	}
	_markDirty() {
		this._isDirty = !0;
	}
	async confirmDiscard() {
		return this._isDirty ? new Promise((e) => {
			t.confirm("Unsaved changes", "You have unsaved changes. Discard them and continue?", n.warning, [{
				text: "Cancel",
				btnClass: "btn-default",
				trigger: () => {
					t.dismiss(), e(!1);
				}
			}, {
				text: "Discard",
				btnClass: "btn-warning",
				trigger: () => {
					t.dismiss(), e(!0);
				}
			}]);
		}) : !0;
	}
	_onFieldUpdated(e) {
		if (this._markDirty(), e.detail?.name !== "targetVersion") return;
		let t = this.querySelector("[name=dependsOn]");
		if (!t) return;
		let n = (t.getValue?.() ?? t.value ?? "").split("\n").map((t) => t.includes("typo3") ? `typo3 => ${e.detail.value}` : t).join("\n");
		t.setValue?.(n);
	}
	_toggleAdvancedMode() {
		this._advancedMode = !this._advancedMode, window.dispatchEvent(new CustomEvent("eb-advanced-mode-changed", { detail: { enabled: this._advancedMode } }));
	}
	_toggleLeftPanel() {
		this._leftCollapsed = !this._leftCollapsed;
	}
	async load() {
		if (this.extensionName) {
			this._loading = !0;
			try {
				let e = await (await fetch(this.smdUrl, {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({
						method: "listWirings",
						params: {}
					})
				})).json();
				if (e.error) throw Error(e.error);
				let t = (e.result ?? []).find((e) => e.name === this.extensionName);
				if (!t) throw Error(`Extension "${this.extensionName}" not found`);
				this._extensionData = JSON.parse(t.working);
			} catch (t) {
				e.error("Load failed", t.message);
			} finally {
				this._loading = !1;
			}
			this._extensionData && (await this.updateComplete, this._populateLayer(), this._populateProperties(), this._isDirty = !1);
		}
	}
	_populateProperties() {
		let e = this._extensionData?.properties ?? {};
		this.shadowRoot.querySelectorAll("[name]").forEach((t) => {
			if (typeof t.setValue != "function" || t.tagName?.toLowerCase() === "eb-group") return;
			let n = t.name, r = t.parentElement?.closest("eb-group[name]"), i;
			i = r ? e[r.getAttribute("name")]?.[n] : e[n], i !== void 0 && t.setValue(i);
		});
	}
	_populateLayer() {
		let e = this.shadowRoot.querySelector("eb-layer");
		if (!e || !this._extensionData) return;
		let t = this._extensionData.modules ?? [], n = this._extensionData.wires ?? [];
		e.addContainers(t), n.length > 0 && e.addWires(n, t);
	}
	_collectProperties() {
		let e = {};
		return this.shadowRoot.querySelectorAll("[name]").forEach((t) => {
			typeof t.getValue == "function" && (e[t.name] = t.getValue());
		}), e;
	}
	_serializeWorking() {
		let e = this.shadowRoot.querySelector("eb-layer");
		if (!e) return null;
		let { modules: t, wires: n } = e.serialize();
		return JSON.stringify({
			modules: t,
			wires: n,
			properties: this._collectProperties()
		});
	}
	async _fetchPreviewChanges(e) {
		try {
			return await (await fetch(this.smdUrl, {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({
					method: "previewChanges",
					params: {
						name: this.extensionName,
						working: e
					}
				})
			})).json();
		} catch {
			return null;
		}
	}
	_buildPreviewContent(e) {
		let t = [];
		if (e.modifiedFiles?.length) {
			t.push("Files that will be modified:\n");
			for (let n of e.modifiedFiles) {
				let e = "  • " + n.path;
				n.renamedTo && (e += "  →  " + n.renamedTo), t.push(e + "\n");
				for (let e of n.changes ?? []) e.type === "renamed" ? t.push("      ↻ " + e.from + " → " + e.to + "\n") : e.type === "removed" ? t.push("      − " + e.method + " (removed)\n") : e.type === "added" && t.push("      + " + e.method + " (added)\n");
			}
		}
		if (e.deletedFiles?.length) {
			t.push("\nFiles that will be deleted:\n");
			for (let n of e.deletedFiles) t.push("  • " + n + "\n");
		}
		let n = document.createElement("pre");
		return n.style.cssText = "font-size:0.9em;max-height:60vh;overflow:auto;white-space:pre-wrap;", n.textContent = t.join(""), n;
	}
	async save(r = {}) {
		let i = this._serializeWorking();
		if (!i) return;
		if (!r._previewDone) {
			let e = await this._fetchPreviewChanges(i);
			if (e?.hasChanges) {
				t.confirm("Review changes before generating", this._buildPreviewContent(e), n.warning, [{
					text: "Cancel",
					btnClass: "btn-default",
					trigger: () => t.dismiss()
				}, {
					text: "Generate",
					btnClass: "btn-warning",
					trigger: () => {
						t.dismiss(), this.save({
							...r,
							_previewDone: !0
						});
					}
				}]);
				return;
			}
		}
		let a = await (await fetch(this.smdUrl, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				method: "saveWiring",
				params: {
					name: this.extensionName,
					working: i,
					...r
				}
			})
		})).json();
		if (a.errors?.length) {
			a.errors.forEach((t) => e.error("Validation error", t));
			return;
		}
		if (a.error) {
			e.error("Error", a.error);
			return;
		}
		if (a.confirm) {
			t.confirm("Warning", a.confirm, n.warning, [{
				text: "Cancel",
				btnClass: "btn-default",
				trigger: () => t.dismiss()
			}, {
				text: "Save anyway",
				btnClass: "btn-warning",
				trigger: () => {
					t.dismiss(), this._saveWithConfirmation(a.confirmFieldName);
				}
			}]);
			return;
		}
		a.warning && e.warning("Warning", a.warning), (a.warnings ?? []).forEach((t) => e.warning("Roundtrip warning", t)), a.success && (e.success("Saved", a.success), this._isDirty = !1, (a.installationHints ?? []).forEach((t) => e.info("Next steps", t)));
	}
	_saveWithConfirmation(e) {
		this.save({ [e]: !0 });
	}
	reset() {
		this.extensionName = "", this._extensionData = null, this._isDirty = !1;
		let e = this.shadowRoot.querySelector("eb-layer");
		e && (e._containers = [], e._wires = []), this.shadowRoot.querySelectorAll("[name]").forEach((e) => {
			e.setValue?.("");
		});
	}
	addModelObject() {
		let e = this.shadowRoot.querySelector("eb-layer");
		e && e.addContainer(je.container);
	}
	render() {
		return M`
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
                    <div class="left-panel-content">${G(et)}</div>
                </div>
                <div class="center-panel" role="main">
                    ${this._loading ? M`<div class="loading">Loading...</div>` : M`<eb-layer></eb-layer>`}
                </div>
            </div>
        `;
	}
};
customElements.define("eb-wiring-editor", tt);
//#endregion
//#region Resources/Public/jsDomainModeling/src/eb-inplace-edit.js
var nt = class extends U {
	static properties = {
		value: { type: String },
		_editing: {
			type: Boolean,
			state: !0
		}
	};
	static styles = [K];
	constructor() {
		super(), this._editing = !1;
	}
	_startEdit() {
		this._editing = !0, this.updateComplete.then(() => {
			this.shadowRoot.querySelector("input")?.focus();
		});
	}
	_confirm(e) {
		let t = e.target.value;
		this._editing = !1, t !== this.value && (this.value = t, this.dispatchEvent(new CustomEvent("inplace-change", {
			bubbles: !0,
			composed: !0,
			detail: { value: t }
		})));
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
		return this._editing ? M`
                <div aria-live="polite">
                    <input
                        class="form-control form-control-sm"
                        .value="${this.value ?? ""}"
                        @blur="${this._confirm}"
                        @keydown="${this._onKey}"
                    />
                </div>
            ` : M`
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
};
customElements.define("eb-inplace-edit", nt);
//#endregion
//#region Resources/Public/jsDomainModeling/src/main.js
function $() {
	let r = document.querySelector("eb-wiring-editor");
	r && (document.getElementById("WiringEditor-saveButton-button")?.addEventListener("click", (e) => {
		e.preventDefault(), r.save();
	}), document.getElementById("WiringEditor-newButton-button")?.addEventListener("click", async (e) => {
		e.preventDefault(), await r.confirmDiscard() && r.reset();
	}), document.getElementById("toggleAdvancedOptions")?.addEventListener("click", (e) => {
		e.preventDefault(), r._toggleAdvancedMode();
	}), document.getElementById("WiringEditor-loadButton-button")?.addEventListener("click", async (i) => {
		if (i.preventDefault(), !await r.confirmDiscard()) return;
		let a = r.getAttribute("smd-url"), o = (await (await fetch(a, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				method: "listWirings",
				params: {}
			})
		})).json()).result ?? [];
		if (o.length === 0) {
			e.info("Open Extension", "No extensions found.");
			return;
		}
		let s = document.createElement("select");
		s.size = 8, s.style.minWidth = "240px", o.forEach((e) => {
			let t = document.createElement("option");
			t.value = e.name, t.textContent = e.name, s.appendChild(t);
		});
		let c = t.advanced({
			title: "Open Extension",
			content: M``,
			severity: n.info,
			size: "small",
			staticBackdrop: !1,
			buttons: [{
				text: "Cancel",
				btnClass: "btn-default",
				trigger: () => t.dismiss()
			}, {
				text: "Open",
				btnClass: "btn-primary",
				active: !0,
				trigger: () => {
					let e = c.querySelector(".t3js-modal-body select")?.value;
					t.dismiss(), e && (r.extensionName = e, r.load());
				}
			}],
			callback: (e) => {
				let t = e.querySelector(".t3js-modal-body");
				t && (t.replaceChildren(s), s.focus());
			}
		});
	}), document.getElementById("WiringEditor-backupsButton-button")?.addEventListener("click", async (i) => {
		i.preventDefault();
		let a = r.getAttribute("smd-url");
		if (!r.extensionName) {
			e.info("Restore backup", "Please load an extension first.");
			return;
		}
		let o = r._serializeWorking();
		if (!o) {
			e.info("Restore backup", "No extension loaded.");
			return;
		}
		let s = (await (await fetch(a, {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				method: "listBackups",
				params: {
					name: r.extensionName,
					working: o
				}
			})
		})).json()).result ?? [];
		if (s.length === 0) {
			e.info("Restore backup", "No backups found for this extension.");
			return;
		}
		let c = document.createElement("select");
		c.size = Math.min(s.length, 8), c.style.cssText = "min-width:320px;display:block;margin-bottom:8px;", s.forEach((e) => {
			let t = document.createElement("option");
			t.value = e.directory, t.textContent = e.label + "  (" + e.fileCount + " files)", c.appendChild(t);
		});
		let l = t.advanced({
			title: "Restore backup",
			content: M``,
			severity: n.warning,
			staticBackdrop: !1,
			buttons: [{
				text: "Cancel",
				btnClass: "btn-default",
				trigger: () => t.dismiss()
			}, {
				text: "Restore",
				btnClass: "btn-danger",
				trigger: async () => {
					let i = l.querySelector(".t3js-modal-body select")?.value;
					t.dismiss(), i && t.confirm("Confirm restore", "Restore backup from " + i + "? The current extension will be overwritten.", n.warning, [{
						text: "Cancel",
						btnClass: "btn-default",
						trigger: () => t.dismiss()
					}, {
						text: "Restore",
						btnClass: "btn-danger",
						trigger: async () => {
							t.dismiss();
							let n = await (await fetch(a, {
								method: "POST",
								headers: { "Content-Type": "application/json" },
								body: JSON.stringify({
									method: "restoreBackup",
									params: {
										name: r.extensionName,
										working: o,
										backupDirectory: i
									}
								})
							})).json();
							n.error ? e.error("Restore failed", n.error) : e.success("Backup restored", n.success ?? "Extension restored.");
						}
					}]);
				}
			}],
			callback: (e) => {
				let t = e.querySelector(".t3js-modal-body");
				if (t) {
					let e = document.createElement("p");
					e.textContent = "Restoring a backup will overwrite all current extension files. The current state will be backed up first.", e.className = "text-danger", t.replaceChildren(e, c);
				}
			}
		});
	}));
}
document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", $) : $();
//#endregion
