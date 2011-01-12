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
							inputParams: {
								required	: true
							}
						}, 
						animColors: {from: "#cccccc" , to: "#cccccc"}
					}
				},
				{
					type: "group",
					inputParams: {
						collapsible: true,
						collapsed: true,
						legend: "Domain Object Settings",
						name: "objectsettings",
						fields: [
							{
								inputParams: {
									name: "uid", 
									className:'hiddenField'
								}
							},
							{
								type: "select", 
								inputParams: {
									label: "Object Type", 
									name: "type",
									description: "Value objects are simple objects that are defined by their value and have no 'identity'",
									selectValues: ["Entity", "ValueObject"],
									selectOptions: ["Entity", "Value Object"]
								}
							},
							{
								type: "boolean", 
								inputParams: {
									label: "Is aggregate root?", 
									name: "aggregateRoot",
									description: "If true, the kickstarter will generate a repository and default controller actions for this model",
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
											description: "For each selected action, the kickstarter will generate a template and a action method in the controller of this model. If the model is an aggregate root the default CRUD actions will be generated per default.",
											selectValues: ["show","list","create","update","delete"],
											selectOptions: ["show","list","create/new","edit/update","delete"]
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
											className: "propertyGroup",
											fields: [
												{
													type: "hidden",
														inputParams: {
														name: "uid",
														className:'hiddenField'
													}
												},
												{
													inputParams: {
														label: "Property Name", 
														name: "propertyName",
														description:'The name should be lowercase and unique in this model',
														required: true
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
													type:'text',
													inputParams: {
														label: "Description", 
														name: "propertyDescription",
														cols:20,
														rows:1
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: "Is Required?", 
														name: "propertyIsRequired",
														value: false
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: "Is ExcludeField?", 
														name: "propertyIsExcludeField",
														description: 'If set to true, this field will only be visible for admin users per default',
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
											className: "relationGroup",
											fields: [
												{
													type: "hidden",
														inputParams: {
														name: "uid",
														className:'hiddenField'
													}
												},
												{
													type: "string", 
													inputParams: {
														label: "Name", 
														name: "relationName",
														description:'The name should be lowercase and unique in this model',
														required: true
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
													type: "text", 
													inputParams: {
														label: "Description", 
														name: "relationDescription", 
														required: false, 
														cols:20,
														rows:1
													}
												},
												{
													type: "string", 
													inputParams: {
														label: "", 
														name: "relationWire", 
														required: false, 
														wirable: true,
														className: 'terminalFieldWrap',
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