var advancedFields = {
		type: "group",
		inputParams: {
			collapsible: true,
			collapsed: true,
			legend: "More",
			name: "advancedSettings",
			fields: [
					{
						type: "select",
						inputParams: {
							label: "Type",
							name: "relationType",
							selectValues: ["zeroToOne", "zeroToMany", "manyToMany"],
							selectOptions: ["1:1","1:n", "m:n"]
						}
					},
					{
						type: "text", 
						inputParams: {
							label: "Description", 
							name: "relationDescription", 
							cols:20,
							rows:1
						}
					},
					{
						type: "boolean",
						inputParams: {
							label: "Is ExcludeField?", 
							name: "propertyIsExcludeField",
							value: false
						}
					},
					{
						type: "boolean",
						inputParams: {
							label: "Edit inline?",
							name: "inlineEditing",
							value: true
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