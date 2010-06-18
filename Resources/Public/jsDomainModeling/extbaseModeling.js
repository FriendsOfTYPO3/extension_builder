var extbaseModeling_wiringEditorLanguage = {
	parentEl: 'domainModelEditor',
	languageName: "extbaseModeling",
	smdUrl: '../typo3conf/ext/extbase_kickstarter/Resources/Public/jsDomainModeling/phpBackend/WiringEditor.smd',
	layoutOptions: {	// Configuration of the whole layout. See documentation of YUI's widget.Layout()
		units: [
			{
				position: 'top', 
				height: 50, 
				body: 'top'
			},
			{
				position: 'left', 
				width: 500, 
				resize: true, 
				body: 'left', 
				gutter: '5px', 
				collapse: true,
				collapseSize: 25, 
				header: 'Extension Configuration', 
				scroll: true, 
				animate: false
			},
			{
				position: 'center', 
				header: 'Domain Modeling',
				body: 'center', 
				gutter: '5px',
				collapse: true, 
				collapseSize: 25
			},
			{
				position: 'right',
				width: 500,
				resize: true,
				body: 'right',
				gutter: '5px',
				collapse: true,
				collapseSize: 25,
				header: 'Code Generator',
				scroll: true,
				animate: false
			},
			{
				position: 'bottom',
				height: 40,
				body: 'bottom'
			}
		]
	},
	layerOptions: {
		// konfiguration der "Arbeitsflaeche", auf der man die Objekte ablegen kann. Doku: WireIt.Layer
	},
	propertiesFields: [
		{
			type: "string",
			inputParams: {
				name: "name",
				label: "Name",
				typeInvite: "Extension title"
			}
		},
		{
			type: "string",
			inputParams: {
				name: "extensionKey", 
				label: "Key",
				typeInvite: "Extension Key",
				cols: 30
			}
		},
		{
			type: "text",
			inputParams: {
				name: "description", 
				label: "Descr.", 
				typeInvite: "Description",
				cols: 30
			}
		},
		{
			type: "select", 
			inputParams: {
				label: "State", 
				name: "state", 
				selectValues: ["alpha","beta","stable","experimental","test"]
			}
		},
		{
			type: "list", 
			inputParams: {
				label: "Persons",
				name: "persons",
				sortable: true,
				elementType: {
					type: "group",
					inputParams: {
						name: "property",
						fields: [
							{
								inputParams: {
									label: "Name", 
									name: "name",
									required: false
								}
							},
							{
								type: "select", 
								inputParams: {
									label: "Role", 
									name: "role", 
									selectValues: ["Developer", "Product Manager"]
								}
							},
							{
								inputParams: {
									label: "Email", 
									name: "email",
									required: false
								}
							},
							{
								inputParams: {
									label: "Company", 
									name: "company",
									required: false
								}
							},
						]
					}
				}
			}
		}
	],
	modules: [
		{
			name: "New Model Object",
			container: {	// Configuration according to WireIt.Container.options
				xtype: "WireIt.FormContainer",
				title: "Title",	// this will be overriden in WiteIt.WiringEditor.addModule() with the module name, so it could also be left empty here
				icon: "../typo3conf/ext/extbase_kickstarter/Resources/Public/jsDomainModeling/typo3-logo.gif",
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
							animColors: {from: "#000000" , to: "#5C85D6"}
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
																"Integer",
																"Float",
																"Boolean",
																"DateTime",
																"Select"
															], 
															selectOptions: [
																"Text String",
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
															label: "Is Required?", 
															name: "propertyIsRequired",
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
															selectOptions: ["0 .. 1","0 .. * (foreign Key)", "0 .. * (association table)"]
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
	]
};
