import { html } from 'lit';
import { translate } from './translate.js';

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
                label="${translate(p.label ?? '')}"
                ?required="${p.required}"
                type-invite="${p.typeInvite ?? ''}"
                placeholder="${p.placeholder ?? ''}"
                .value="${p.value ?? ''}"
                ?force-alpha-numeric="${p.forceAlphaNumeric}"
                ?force-alpha-numeric-underscore="${p.forceAlphaNumericUnderscore}"
                ?force-lower-case="${p.forceLowerCase}"
                ?no-spaces="${p.noSpaces}"
                ?uc-first="${p.ucFirst}"
                ?lc-first="${p.lcFirst}"
                ?first-char-non-numeric="${p.firstCharNonNumeric}"
                min-length="${p.minLength ?? ''}"
                max-length="${p.maxLength ?? ''}"
            ></eb-string-field>`;

        case 'text':
            return html`<eb-textarea-field
                name="${p.name}"
                label="${translate(p.label ?? '')}"
                placeholder="${p.placeholder ?? ''}"
                .value="${p.value ?? ''}"
            ></eb-textarea-field>`;

        case 'select':
            return html`<eb-select-field
                name="${p.name}"
                label="${translate(p.label ?? '')}"
                .selectValues="${p.selectValues ?? []}"
                .selectOptions="${p.selectOptions ?? []}"
                .value="${p.value ?? (p.selectValues?.[0] ?? '')}"
            ></eb-select-field>`;

        case 'boolean':
            return html`<eb-boolean-field
                name="${p.name}"
                label="${translate(p.label ?? '')}"
                .value="${p.value ?? false}"
            ></eb-boolean-field>`;

        case 'group':
            return html`<eb-group
                name="${p.name ?? ''}"
                legend="${translate(p.legend ?? '')}"
                ?collapsible="${p.collapsible}"
                ?collapsed="${p.collapsed}"
            >${renderFields(p.fields ?? [])}</eb-group>`;

        case 'list':
            return html`
                ${p.label ? html`<label class="form-label" style="display:block;font-weight:600;margin-top:0.5rem">${translate(p.label)}</label>` : ''}
                <eb-list-field
                    name="${p.name}"
                    ?sortable="${p.sortable}"
                    add-label="${translate('add')}"
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
                label="${translate(p.label ?? '')}"
            ></eb-string-field>`;
    }
}

export function renderFields(fields) {
    return fields.map(f => renderFieldDef(f));
}
