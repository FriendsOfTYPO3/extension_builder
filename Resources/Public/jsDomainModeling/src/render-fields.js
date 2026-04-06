import { html, nothing } from 'lit';
import { translate } from './translate.js';

/**
 * Renders a single field definition as the appropriate custom element.
 *
 * Maps the `type` property of a field definition object to one of the
 * `eb-*-field` components, passing all relevant `inputParams` as attributes.
 *
 * @param {Object} fieldDef - Field definition with `type` and `inputParams`
 * @returns {import('lit').TemplateResult} Lit template result
 */
export function renderFieldDef(fieldDef) {
    const p = fieldDef.inputParams ?? {};
    const type = fieldDef.type;

    if (!type || p.className?.includes('hiddenField')) {
        return html`<eb-hidden-field name="${p.name}"></eb-hidden-field>`;
    }

    if (p.wirable) {
        return nothing; // rendered as eb-terminal by eb-list-field
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
                ?no-leading-underscore="${p.noLeadingUnderscore}"
                ?no-trailing-underscore="${p.noTrailingUnderscore}"
                forbidden-prefixes="${p.forbiddenPrefixes ?? ''}"
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
                .value="${p.value ?? p.selectValues?.[0] ?? ''}"
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
                ?flatten="${p.flatten}"
                >${renderFields(p.fields ?? [])}</eb-group
            >`;

        case 'list':
            return html` ${p.label
                    ? html`<label class="form-label" style="display:block;font-weight:600;margin-top:0.5rem"
                          >${translate(p.label)}</label
                      >`
                    : ''}
                <eb-list-field
                    name="${p.name}"
                    ?sortable="${p.sortable}"
                    add-label="${translate('add')}"
                    element-type="${JSON.stringify(p.elementType ?? {})}"
                ></eb-list-field>`;

        case 'inplaceedit':
            return html`<eb-inplace-edit name="${p.name ?? ''}" .value="${p.value ?? ''}"></eb-inplace-edit>`;

        default:
            return html`<eb-string-field name="${p.name}" label="${translate(p.label ?? '')}"></eb-string-field>`;
    }
}

/**
 * Renders an array of field definitions.
 *
 * @param {Object[]} fields - Array of field definition objects
 * @returns {import('lit').TemplateResult[]} Array of Lit template results
 */
export function renderFields(fields) {
    return fields.map((f) => renderFieldDef(f));
}
