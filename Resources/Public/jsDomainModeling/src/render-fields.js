import { html } from 'lit';

export function renderFieldDef(fieldDef) {
    const p = fieldDef.inputParams ?? {};
    const type = fieldDef.type;

    if (!type || p.className?.includes('hiddenField')) {
        return html`<eb-hidden-field name="${p.name}"></eb-hidden-field>`;
    }

    switch (type) {
        case 'string':
            return html`<eb-string-field
                name="${p.name}"
                label="${p.label ?? ''}"
                ?required="${p.required}"
                type-invite="${p.typeInvite ?? ''}"
                .value="${p.value ?? ''}"
            ></eb-string-field>`;

        case 'text':
            return html`<eb-textarea-field
                name="${p.name}"
                label="${p.label ?? ''}"
                .value="${p.value ?? ''}"
            ></eb-textarea-field>`;

        case 'select':
            return html`<eb-select-field
                name="${p.name}"
                label="${p.label ?? ''}"
                .selectValues="${p.selectValues ?? []}"
                .selectOptions="${p.selectOptions ?? []}"
                .value="${p.value ?? (p.selectValues?.[0] ?? '')}"
            ></eb-select-field>`;

        case 'boolean':
            return html`<eb-boolean-field
                name="${p.name}"
                label="${p.label ?? ''}"
                .value="${p.value ?? false}"
            ></eb-boolean-field>`;

        case 'group':
            return html`<eb-group
                name="${p.name ?? ''}"
                legend="${p.legend ?? ''}"
                ?collapsible="${p.collapsible}"
                ?collapsed="${p.collapsed}"
            >${renderFields(p.fields ?? [])}</eb-group>`;

        case 'list':
            return html`<eb-list-field
                name="${p.name}"
                ?sortable="${p.sortable}"
                element-type="${JSON.stringify(p.elementType ?? {})}"
            ></eb-list-field>`;

        case 'inplaceedit':
            return html`<eb-inplace-edit
                name="${p.name ?? ''}"
                .value="${p.value ?? ''}"
            ></eb-inplace-edit>`;

        default:
            return html`<eb-string-field
                name="${p.name}"
                label="${p.label ?? ''}"
            ></eb-string-field>`;
    }
}

export function renderFields(fields) {
    return fields.map(f => renderFieldDef(f));
}
