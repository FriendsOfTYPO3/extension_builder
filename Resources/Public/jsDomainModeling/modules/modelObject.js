extbaseModeling_wiringEditorLanguage.modules.push(
	{
		name: "New Model Object",
		container: {	// Configuration according to WireIt.Container.options
			xtype: "WireIt.FormContainer",
			title: "Title",	// this will be overriden in WiteIt.WiringEditor.addModule() with the module name, so it could also be left empty here
			preventSelfWiring: false,
			fields: [
				{
					type: "inplaceedit",
					inputParams: {
						name: "name",
						className: "inputEx-Field extbase-modelTitleEditor",
						editorField:{
							type: "string",
							inputParams: {}
						},
						animColors: {from: "#7a7a7a" , to: "#585858"}
					}
				}, {
					type: "group",
					inputParams: {
						collapsible: true,
						collapsed: true,
						legend: "Domain Object Settings",
						name: "objectsettings",
						fields: [
							{
								type: "select",
								inputParams: {
									label: "Object Type",
									name: "type",
									selectValues: ["Entity", "ValueObject"],
									selectOptions: ["Entity", "Value Object"]
								}
							},
							{
								type: "boolean",
								inputParams: {
									label: "Is aggregate root?",
									name: "aggregateRoot",
									value: false
								}
							},
							{
								type: "string",
								inputParams: {
									label: "Description",
									name: "description",
									required: false
								}
							}
						]
					}
				},
				{
					type: "group",
					inputParams: {
						collapsible: true,
						collapsed: true,
						legend: "Default Actions",
						name: "actionGroup",
						fields: [
							{
								type: "list",
								inputParams: {
									label: "",
									name: "actions",
									sortable: false,
									elementType: {
										type: "select",
										inputParams: {
											label: "Action Type",
											name: "actionType",
											selectValues: ["list", "edit", "create"]
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
						collapsible: true,
						collapsed: true,
						legend: "Properties",
						name: "propertyGroup",
						fields: [
							{
								type: "list",
								inputParams: {
									label: "",
									name: "properties",
									wirable: false,
									sortable: true,
									elementType: {
										type: "group",
										inputParams: {
											name: "property",
											fields: [
												{
													inputParams: {
														label: "Property Name",
														name: "propertyName",
														required: false
													}
												},
												{
													type: "select",
													inputParams: {
														label: "Property Type",
														name: "propertyType",
														selectValues: [
															"String",
															"Text",
															"Integer",
															"Float",
															"Boolean",
															"DateTime",
															"Select"
														],
														selectOptions: [
															"String",
															"Text",
															"Integer",
															"Floating Point",
															"Boolean",
															"Date Time",
															"Select List"
														]
													}
												},
												{
													inputParams: {
														label: "Description",
														name: "propertyDescription"
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: "Is required?",
														name: "propertyIsRequired",
														value: false
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: "Can be excluded?",
														name: "propertyIsExcludeField",
														value: false
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
						collapsible: false,
						collapsed: false,
						legend: "Relations",
						name: "relationGroup",
						fields: [
							{
								type: "list",
								inputParams: {
									label: "",
									name: "relations",
									wirable: false,
									elementType: {
										type: "group",
										inputParams: {
											name: "relation",
											fields: [
												{
													type: "string",
													inputParams: {
														label: "Name",
														name: "relationName",
														required: false
													}
												},
												{
													type: "string",
													inputParams: {
														label: "Related Object",
														name: "relationWire",
														required: false,
														wirable: true,
														ddConfig: {
															type: "input",
															allowedTypes: ["output", "input"]
														}
													}
												},
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
													type: "boolean",
													inputParams: {
														label: "Edit inline?",
														name: "inlineEditing",
														value: false
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
					direction: [0,-1],
					offsetPosition: {
						left: 20,
						top: -15
					},
					ddConfig: {
						type: "output",
						allowedTypes: ["input"]
					}
				}
			]
		}
	}
);