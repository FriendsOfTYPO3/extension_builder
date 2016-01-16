var advancedFields = {
		type: "group",
		inputParams: {
			collapsible: true,
			flatten:true,
			collapsed: true,
			className:"advancedSettings",
			name: "advancedSettings",
			fields: [
				{
					type: "select",
					inputParams: {
						label: TYPO3.settings.extensionBuilder._LOCAL_LANG.type,
						name: "relationType",
						selectValues: ["zeroToOne", "zeroToMany", "manyToOne", "manyToMany"],
						selectOptions: [
							TYPO3.settings.extensionBuilder._LOCAL_LANG.zeroToOne,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.zeroToMany,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.manyToOne,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.manyToMany
						]
					}
				},
				{
					type: "select",
					inputParams: {
						label: TYPO3.settings.extensionBuilder._LOCAL_LANG.renderType,
						description: TYPO3.settings.extensionBuilder._LOCAL_LANG.desc_renderType,
						wrapperClassName: 'inputEx-fieldWrapper dependant renderType',
						className: 'inputEx-Field isDependant',
						name: "renderType",
						//advancedMode: true,
						selectValues: ["selectMultipleSideBySide", "selectSingleBox", "selectCheckBox", "inline", "selectSingle"],
						selectOptions: [
							TYPO3.settings.extensionBuilder._LOCAL_LANG.selectSingleBox,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.selectCheckBox,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.selectMultipleSideBySide,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.inline,
							TYPO3.settings.extensionBuilder._LOCAL_LANG.select
						]
					}
				},
				{
					type: "text",
					inputParams: {
						placeholder: TYPO3.settings.extensionBuilder._LOCAL_LANG.description,
						name: "relationDescription",
						cols:20,
						rows:2
					}
				},
				{
					type: "boolean",
					inputParams: {
						label: TYPO3.settings.extensionBuilder._LOCAL_LANG.isExcludeField,
						name: "propertyIsExcludeField",
						advancedMode: true,
						value: true,
						description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_isExcludeField
					}
				},
				{
					type: 'boolean',
					inputParams: {
						label: TYPO3.settings.extensionBuilder._LOCAL_LANG.lazyLoading,
						name: 'lazyLoading',
						advancedMode: true,
						description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_lazyLoading,
						value: false
					}
				},
				{
					type: "string",
					inputParams: {
						label: TYPO3.settings.extensionBuilder._LOCAL_LANG.foreignRelationClass,
						name: "foreignRelationClass",
						placeholder: '\\Fully\\Qualified\\Classname',
						advancedMode: true,
						description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_foreignRelationClass
					}
				}

			]
		}
	};

var relationFieldSet = extbaseModeling_wiringEditorLanguage.modules[0].container.fields[4].inputParams.fields[0].inputParams.elementType.inputParams.fields;
relationFieldSet[5] = advancedFields;
Array.prototype.remove = function(from, to){
  this.splice(from,
	!to ||
	1 + to - from + (!(to < 0 ^ from >= 0) && (to < 0 || -1) * this.length));
  return this.length;
};
// remove excludeField in first level form
relationFieldSet.remove(2);
// remove Description in first level form
relationFieldSet.remove(2);

