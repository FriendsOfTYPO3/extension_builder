var advancedFields = {
  type: "group",
  inputParams: {
    collapsible: true,
    flatten: true,
    collapsed: true,
    className: "advancedSettings",
    name: "advancedSettings",
    fields: [
      {
        type: "select",
        inputParams: {
          label: "type",
          name: "relationType",
          selectValues: ["zeroToOne", "zeroToMany", "manyToOne", "manyToMany"],
          selectOptions: ["zeroToOne", "zeroToMany", "manyToOne", "manyToMany"],
        },
      },
      {
        type: "select",
        inputParams: {
          label: "renderType",
          description: "desc_renderType",
          wrapperClassName: "inputEx-fieldWrapper dependant renderType",
          className: "inputEx-Field isDependant",
          name: "renderType",
          //advancedMode: true,
          selectValues: [
            "selectSingleBox",
            "selectCheckBox",
            "selectMultipleSideBySide",
            "inline",
            "selectSingle",
          ],
          selectOptions: [
            "selectSingleBox",
            "selectCheckBox",
            "selectMultipleSideBySide",
            "inline",
            "select",
          ],
        },
      },
      {
        type: "text",
        inputParams: {
          placeholder: "description",
          name: "relationDescription",
          cols: 20,
          rows: 2,
        },
      },
      {
        type: "boolean",
        inputParams: {
          label: "isExcludeField",
          name: "propertyIsExcludeField",
          advancedMode: true,
          value: true,
          description: "descr_isExcludeField",
        },
      },
      {
        type: "boolean",
        inputParams: {
          label: "lazyLoading",
          name: "lazyLoading",
          advancedMode: true,
          description: "descr_lazyLoading",
          value: false,
        },
      },
      {
        type: "string",
        inputParams: {
          label: "foreignRelationClass",
          name: "foreignRelationClass",
          placeholder: "\\Fully\\Qualified\\Classname",
          advancedMode: true,
          description: "descr_foreignRelationClass",
        },
      },
    ],
  },
};

var relationFieldSet =
  extbaseModeling_wiringEditorLanguage.modules[0].container.fields[4]
    .inputParams.fields[0].inputParams.elementType.inputParams.fields ?? [];
relationFieldSet[5] = advancedFields;
Array.prototype.remove = function (from, to) {
  this.splice(
    from,
    !to ||
      1 +
        to -
        from +
        (!((to < 0) ^ (from >= 0)) && (to < 0 || -1) * this.length),
  );
  return this.length;
};
// remove excludeField in first level form
relationFieldSet.remove(2);
// remove Description in first level form
relationFieldSet.remove(2);
