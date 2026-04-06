import { LitElement, html, css, svg } from 'lit';
import './eb-container.js';
import './eb-wire.js';

/**
 * Canvas layer that hosts draggable containers and SVG wire overlays.
 *
 * Manages the full list of `eb-container` instances and `eb-wire` paths.
 * Listens for `terminal-connect` events to start wire drawing and
 * `container-moved` events to update wire endpoint positions.
 * Dropping on a target `eb-container` creates a wire; hovering a wire
 * reveals a trash icon that deletes it.
 *
 * @element eb-layer
 */
export class EbLayer extends LitElement {
    static properties = {
        _wires: { state: true },
        _containers: { state: true },
        _drawingWire: { state: true },
        _tempWire: { state: true },
        _hoveredWireId: { state: true },
        _panOffset: { state: true },
    };

    static styles = css`
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
        super();
        this._wires = [];
        this._containers = [];
        this._drawingWire = null;
        this._tempWire = null;
        this._hoveredWireId = null;
        this._panOffset = { x: 0, y: 0 };
        this._isPanning = false;
        this._panStartX = 0;
        this._panStartY = 0;
    }

    connectedCallback() {
        super.connectedCallback();
        this.addEventListener('terminal-connect', this._onTerminalConnect.bind(this));
        this.addEventListener('container-moved', this._onContainerMoved.bind(this));
        this.addEventListener('container-removed', this._onContainerRemoved.bind(this));
        this._boundPointerMove = this._onPointerMove.bind(this);
        this._boundPointerUp = this._onPointerUp.bind(this);
        window.addEventListener('pointermove', this._boundPointerMove);
        window.addEventListener('pointerup', this._boundPointerUp);
    }

    disconnectedCallback() {
        super.disconnectedCallback();
        window.removeEventListener('pointermove', this._boundPointerMove);
        window.removeEventListener('pointerup', this._boundPointerUp);
    }

    _onCanvasPointerDown(e) {
        const canvas = this.shadowRoot.querySelector('#canvas');
        const panSurface = this.shadowRoot.querySelector('#pan-surface');
        if (e.target !== canvas && e.target !== panSurface) {
            return;
        }

        this._isPanning = true;
        this._panStartX = e.clientX - this._panOffset.x;
        this._panStartY = e.clientY - this._panOffset.y;
        canvas.style.cursor = 'grabbing';
    }

    _onTerminalConnect(e) {
        const { terminalId, uid, sourceEl } = e.detail;

        const layerRect = this.getBoundingClientRect();
        const termRect = sourceEl.getBoundingClientRect();
        const startX = termRect.left - layerRect.left + termRect.width / 2;
        const startY = termRect.top - layerRect.top + termRect.height / 2;

        const container = sourceEl.getRootNode()?.host;
        const moduleId = parseInt(container?.getAttribute('module-id') ?? '-1');

        this._drawingWire = {
            terminalId,
            uid,
            sourceEl,
            moduleId,
            startX,
            startY,
            mouseX: startX,
            mouseY: startY,
        };
    }

    _onContainerMoved(e) {
        this._updateWirePositions();
    }

    _onContainerRemoved(e) {
        const { moduleId } = e.detail;
        this._containers = this._containers.filter((c) => c.moduleId !== moduleId);
        this._wires = this._wires.filter((w) => w.srcModuleId !== moduleId && w.tgtModuleId !== moduleId);
    }

    _onPointerMove(e) {
        if (this._isPanning) {
            this._panOffset = {
                x: e.clientX - this._panStartX,
                y: e.clientY - this._panStartY,
            };
            return;
        }
        if (!this._drawingWire) {
            return;
        }
        const layerRect = this.getBoundingClientRect();
        const mouseX = e.clientX - layerRect.left;
        const mouseY = e.clientY - layerRect.top;
        this._drawingWire = { ...this._drawingWire, mouseX, mouseY };
        this._tempWire = {
            x1: this._drawingWire.startX,
            y1: this._drawingWire.startY,
            x2: mouseX,
            y2: mouseY,
        };
    }

    _onPointerUp(e) {
        if (this._isPanning) {
            this._isPanning = false;
            this.shadowRoot.querySelector('#canvas').style.cursor = 'grab';
            this._updateWirePositions();
            return;
        }
        if (!this._drawingWire) {
            return;
        }
        const src = this._drawingWire;
        this._drawingWire = null;
        this._tempWire = null;

        // Find a droppable eb-terminal in the event path (relation terminals only)
        const tgtTerminalEl = e
            .composedPath()
            .find((el) => el.tagName === 'EB-TERMINAL' && el.hasAttribute('droppable'));
        if (!tgtTerminalEl) {
            return;
        }

        const tgtTerminalId = tgtTerminalEl.getAttribute('terminal-id');
        const tgtUid = tgtTerminalEl.uid ?? tgtTerminalEl.getAttribute('uid') ?? '';

        // Traverse shadow DOM host chain to find the parent eb-container
        let tgtModuleId = null;
        let node = tgtTerminalEl.getRootNode()?.host;
        while (node) {
            if (node.tagName === 'EB-CONTAINER') {
                tgtModuleId = parseInt(node.getAttribute('module-id'));
                break;
            }
            node = node.getRootNode()?.host;
        }
        if (tgtModuleId === null || tgtModuleId === src.moduleId) {
            return;
        }

        // PHP's reArrangeRelations() expects src = relation terminal (REL_N),
        // tgt = SOURCES terminal. The UI drag is reversed (user starts from SOURCES,
        // drops on the relation terminal), so we swap here before storing.
        const duplicate = this._wires.some(
            (w) => w.srcModuleId === tgtModuleId && w.tgtModuleId === src.moduleId && w.srcTerminal === tgtTerminalId
        );
        if (duplicate) {
            return;
        }

        const srcEl = this._findTerminalEl(tgtTerminalId, tgtModuleId);
        const tgtEl = this._findTerminalEl(src.terminalId, src.moduleId);
        const pos = srcEl && tgtEl ? this._getWirePositions(srcEl, tgtEl) : { x1: 0, y1: 0, x2: 0, y2: 0 };

        this._wires = [
            ...this._wires,
            {
                id: `wire-${tgtModuleId}-${tgtTerminalId}-${src.moduleId}-${src.terminalId}`,
                srcTerminal: tgtTerminalId,
                tgtTerminal: src.terminalId,
                srcUid: tgtUid,
                tgtUid: src.uid,
                srcModuleId: tgtModuleId,
                tgtModuleId: src.moduleId,
                ...pos,
            },
        ];
    }

    _deleteWire(wireId) {
        this._wires = this._wires.filter((w) => w.id !== wireId);
    }

    _updateWirePositions() {
        this.updateComplete.then(() => {
            const updatedWires = this._wires.map((wire) => {
                const srcEl = this._findTerminalEl(wire.srcTerminal, wire.srcModuleId);
                const tgtEl = this._findTerminalEl(wire.tgtTerminal, wire.tgtModuleId);
                if (!srcEl || !tgtEl) {
                    return wire;
                }
                return { ...wire, ...this._getWirePositions(srcEl, tgtEl) };
            });
            this._wires = updatedWires;
        });
    }

    _findTerminalEl(terminalId, moduleId) {
        // PHP's reArrangeRelations() normalises relation terminal ids to
        // "relationWire_N", but the DOM uses "REL_N". Map between the two.
        const domTerminalId = terminalId.replace(/^relationWire_(\d+)$/, 'REL_$1');
        const container = this.shadowRoot.querySelector(`eb-container[module-id="${moduleId}"]`);
        if (!container) {
            return null;
        }
        return this._deepQuerySelector(container, `eb-terminal[terminal-id="${domTerminalId}"]`);
    }

    _deepQuerySelector(element, selector) {
        const root = element.shadowRoot;
        if (!root) {
            return null;
        }
        const direct = root.querySelector(selector);
        if (direct) {
            return direct;
        }
        for (const child of root.querySelectorAll('*')) {
            if (child.shadowRoot) {
                const found = this._deepQuerySelector(child, selector);
                if (found) {
                    return found;
                }
            }
        }
        return null;
    }

    _getWirePositions(srcEl, tgtEl) {
        const layerRect = this.getBoundingClientRect();
        const srcRect = srcEl.getBoundingClientRect();
        const tgtRect = tgtEl.getBoundingClientRect();
        return {
            x1: srcRect.left - layerRect.left + srcRect.width / 2,
            y1: srcRect.top - layerRect.top + srcRect.height / 2,
            x2: tgtRect.left - layerRect.left + tgtRect.width / 2,
            y2: tgtRect.top - layerRect.top + tgtRect.height / 2,
        };
    }

    addContainer(moduleData) {
        const nextId = this._containers.length;
        const uid = parseInt(Date.now() * Math.random()) || Date.now();
        const moduleDataWithUid = {
            ...moduleData,
            value: {
                ...moduleData.value,
                objectsettings: {
                    ...moduleData.value?.objectsettings,
                    uid: moduleData.value?.objectsettings?.uid || uid,
                },
            },
        };
        this._containers = [
            ...this._containers,
            {
                moduleId: nextId,
                posX: 20 + nextId * 20,
                posY: 20 + nextId * 20,
                moduleData: moduleDataWithUid,
            },
        ];
    }

    addContainers(modules) {
        this._containers = modules.map((mod, index) => ({
            moduleId: index,
            posX: mod.config?.position?.[0] ?? 10 + index * 180,
            posY: mod.config?.position?.[1] ?? 10,
            moduleData: mod,
        }));
    }

    async _awaitAllUpdates(element) {
        if (!element.shadowRoot) {
            return;
        }
        const litChildren = Array.from(element.shadowRoot.querySelectorAll('*')).filter(
            (el) => el.updateComplete instanceof Promise
        );
        if (litChildren.length === 0) {
            return;
        }
        await Promise.all(litChildren.map((el) => el.updateComplete));
        await Promise.all(litChildren.map((el) => this._awaitAllUpdates(el)));
    }

    addWires(wires, modules) {
        this.updateComplete.then(async () => {
            const containers = Array.from(this.shadowRoot.querySelectorAll('eb-container'));
            await Promise.all(containers.map((c) => c.updateComplete));
            await Promise.all(containers.map((c) => this._awaitAllUpdates(c)));
            this._wires = wires.map((wire) => {
                const srcEl = this._findTerminalEl(wire.src.terminal, wire.src.moduleId);
                const tgtEl = this._findTerminalEl(wire.tgt.terminal, wire.tgt.moduleId);
                const pos = srcEl && tgtEl ? this._getWirePositions(srcEl, tgtEl) : { x1: 0, y1: 0, x2: 0, y2: 0 };
                return {
                    id: `wire-${wire.src.moduleId}-${wire.src.terminal}-${wire.tgt.moduleId}`,
                    srcTerminal: wire.src.terminal,
                    tgtTerminal: wire.tgt.terminal,
                    srcUid: wire.src.uid,
                    tgtUid: wire.tgt.uid,
                    srcModuleId: wire.src.moduleId,
                    tgtModuleId: wire.tgt.moduleId,
                    ...pos,
                };
            });
        });
    }

    serialize() {
        const containers = Array.from(this.shadowRoot.querySelectorAll('eb-container'));
        const modules = containers.map((c) => c.serialize());
        const wires = this._wires.map((w) => ({
            src: { moduleId: w.srcModuleId, terminal: w.srcTerminal, uid: w.srcUid },
            tgt: { moduleId: w.tgtModuleId, terminal: w.tgtTerminal, uid: w.tgtUid },
        }));
        return { modules, wires };
    }

    _wireMidpoint(w) {
        return { x: (w.x1 + w.x2) / 2, y: (w.y1 + w.y2) / 2 };
    }

    _wirePath(w) {
        return `M ${w.x1} ${w.y1} C ${w.x1} ${w.y1 + 80}, ${w.x2} ${w.y2 - 80}, ${w.x2} ${w.y2}`;
    }

    render() {
        const { x, y } = this._panOffset;
        return html`
            <div id="canvas" @pointerdown="${this._onCanvasPointerDown}">
                <div id="pan-surface" style="transform: translate(${x}px, ${y}px)">
                    ${this._containers.map(
                        (c) => html`
                            <eb-container
                                module-id="${c.moduleId}"
                                pos-x="${c.posX}"
                                pos-y="${c.posY}"
                                .moduleData="${c.moduleData}"
                            >
                            </eb-container>
                        `
                    )}
                </div>
                <svg id="wire-overlay">
                    ${this._wires.map((w) => {
                        const mid = this._wireMidpoint(w);
                        const d = this._wirePath(w);
                        return svg`
                            <g class="wire-group">
                                <path
                                    class="wire-hit-area"
                                    d="${d}"
                                    stroke-width="12"
                                    fill="none"
                                />
                                <path
                                    class="wire-path"
                                    d="${d}"
                                    stroke-width="2"
                                    fill="none"
                                    stroke-linecap="round"
                                    pointer-events="none"
                                />
                                <g
                                    class="wire-delete-btn"
                                    @click="${() => this._deleteWire(w.id)}"
                                    aria-label="Delete wire"
                                    role="button"
                                >
                                    <circle cx="${mid.x}" cy="${mid.y}" r="9" />
                                    <text x="${mid.x}" y="${mid.y}">×</text>
                                </g>
                            </g>
                        `;
                    })}
                    ${this._tempWire
                        ? svg`
                        <path
                            class="wire-temp-path"
                            d="M ${this._tempWire.x1} ${this._tempWire.y1} L ${this._tempWire.x2} ${this._tempWire.y2}"
                            stroke-width="1.5"
                            stroke-dasharray="4 4"
                            fill="none"
                        />
                    `
                        : ''}
                </svg>
            </div>
        `;
    }
}

customElements.define('eb-layer', EbLayer);
