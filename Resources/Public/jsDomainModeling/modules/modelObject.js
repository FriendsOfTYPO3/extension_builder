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
						legend: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.domainObjectSettings,
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
									name: "type",
									label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.objectType,
									description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_objectType,
									selectValues: ["Entity", "ValueObject"],
									selectOptions: [
										TYPO3.settings.extbaseKickstarter._LOCAL_LANG.entity,
										TYPO3.settings.extbaseKickstarter._LOCAL_LANG.valueObject
									]
								}
							},
							{
								type: "boolean", 
								inputParams: {
									name: "aggregateRoot",
									label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.isAggregateRoot,
									description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_isAggregateRoot,
									value: false
								}
							},
							{
								type: "string", 
								inputParams: {
									name: "description",
									label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.description,
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
						legend: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.defaultActions,
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
											name: "actionType",
											label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.actionType,
											description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_actionType,
											selectValues: ["show","list","create","update","delete"],
											selectOptions: [
												TYPO3.settings.extbaseKickstarter._LOCAL_LANG.show,
												TYPO3.settings.extbaseKickstarter._LOCAL_LANG.list,
												TYPO3.settings.extbaseKickstarter._LOCAL_LANG.create_new,
												TYPO3.settings.extbaseKickstarter._LOCAL_LANG.edit_update,
												TYPO3.settings.extbaseKickstarter._LOCAL_LANG['delete']
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
						collapsible: true,
						collapsed: true,
						legend: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.properties,
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
														name: "propertyName",
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.propertyName,
														description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_propertyName,
														required: true
													}
												},
												{
													type: "select", 
													inputParams: {
														name: "propertyType",
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.propertyType,
														selectValues: [
															"String",
															"Text",
															"Integer",
															"Float",
															"Boolean",
															"DateTime",
															"Select",
															"File",
															"Image"
														], 
														selectOptions: [
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.string,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.text,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.integer,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.floatingPoint,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.boolean,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.dateTime,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.selectList,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.file,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.image
														]
													}
												},
												{
													type:'text',
													inputParams: {
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.description, 
														name: "propertyDescription",
														cols:20,
														rows:1
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.isRequired, 
														name: "propertyIsRequired",
														value: false
													}
												},
												{
													type: "boolean",
													inputParams: {
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.isExcludeField, 
														name: "propertyIsExcludeField",
														description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_isExcludeField,
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
						legend: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.relations,
						name: "relationGroup",
						fields: [
							{
								type: "list", 
								inputParams: {
									name: "relations",
									label: "",
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
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.relationName, 
														name: "relationName",
														description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_relationName,
														required: true
													}
												},
												{
													type: "boolean",
													inputParams: {
														name: "propertyIsExcludeField",
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.isExcludeField,
														description: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.descr_isExcludeField,
														value: false
													}
												},
												{
													type: "text", 
													inputParams: {
														name: "relationDescription",
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.description,
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
														label: TYPO3.settings.extbaseKickstarter._LOCAL_LANG.type,
														name: "relationType",
														selectValues: ["zeroToOne", "zeroToMany", "manyToMany"],
														selectOptions: [
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.zeroToOne,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.zeroToMany,
															TYPO3.settings.extbaseKickstarter._LOCAL_LANG.manyToMany
														]
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