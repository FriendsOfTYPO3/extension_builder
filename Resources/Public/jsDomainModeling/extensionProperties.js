extbaseModeling_wiringEditorLanguage.propertiesFields =
[
	{
		type: "string",
		inputParams: {
			name: "name",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.name,
			typeInvite: TYPO3.settings.extensionBuilder._LOCAL_LANG.extensionTitle
		}
	},
	{
		type: "string",
		inputParams: {
			name: "extensionKey",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.key,
			typeInvite: TYPO3.settings.extensionBuilder._LOCAL_LANG.extensionKey,
			forceLowerCase: true,
			forceAlphaNumericUnderscore: true,
			cols: 30,
			description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_extensionKey
		}
	},
	{
		inputParams: {
			name: "originalExtensionKey",
			className:'hiddenField'
		}
	},
	{
		type: "text",
		inputParams: {
			name: "description",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.short_description,
			typeInvite: TYPO3.settings.extensionBuilder._LOCAL_LANG.description,
			cols: 30
		}
	},
	{
		type: 'group',
		inputParams: {
			collapsible: true,
			collapsed: true,
			legend: TYPO3.settings.extensionBuilder._LOCAL_LANG.more,
			name: "emConf",
			description: 'Test',
			fields: [
					{
						type: "select",
						inputParams: {
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.category,
							name: "category",
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_category,
							selectValues: [ "plugin", "module", "misc", "backend", "frontend", "services","templates", "examples", "doc"],
							selectOptions: [
								TYPO3.settings.extensionBuilder._LOCAL_LANG.plugins,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.backendModules,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.misc,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.backend,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.frontend,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.services,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.templates,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.examples,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.documentation
							]
						}
					},
					{
						type: "string",
						inputParams: {
							name: "custom_category",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.custom_category,
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_custom_category,
							cols: 30
						}
					},
					{
						type: "string",
						inputParams: {
							name: "version",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.version,
							required: false,
							size: 5
						}
					},
					{
						type: "select",
						inputParams: {
							name: "state",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.state,
							selectOptions: [
								TYPO3.settings.extensionBuilder._LOCAL_LANG.alpha,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.beta,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.stable,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.experimental,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.test
							],
							selectValues: ["alpha","beta","stable","experimental","test"]
						}
					},
					{
						type: "string",
						inputParams: {
							name: "priority",
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_priority,
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.priority,
							cols: 30
						}
					},
					{
						type: "boolean",
						inputParams: {
							name: "shy",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.shy,
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_shy,
							value: 0
						}
					}
			]
		}
	},
	{
		type: "list",
		inputParams: {
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.persons,
			name: "persons",
			sortable: true,
			elementType: {
				type: "group",
				inputParams: {
					name: "property",
					fields: [
						{
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.name,
								name: "name",
								required: true
							}
						},
						{
							type: "select",
							inputParams: {
								name: "role",
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.role,
								selectOptions: [
									TYPO3.settings.extensionBuilder._LOCAL_LANG.developer,
									TYPO3.settings.extensionBuilder._LOCAL_LANG.product_manager
								],
								selectValues: ["Developer", "Product Manager"]
							}
						},
						{
							inputParams: {
								name: "email",
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.email,
								required: false
							}
						},
						{
							inputParams: {
								name: "company",
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.company,
								required: false
							}
						}
					]
				}
			}
		}
	},
	{
		type: "list",
		inputParams: {
			name: "plugins",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.plugins,
			description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_plugins,
			sortable: true,
			elementType: {
				type: "group",
				inputParams: {
					name: "property",
					fields: [
						{
							inputParams: {
								name: "name",
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.name,
								required: true
							}
						},
						{
							inputParams: {
								name: "key",
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.key,
								required: true,
								forceAlphaNumeric: true,
								noSpaces: true,
								description: TYPO3.settings.extensionBuilder._LOCAL_LANG.uniqueInThisModel
							}
//						},
//						{
//							type: "select",
//							inputParams: {
//								name: "type",
//								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.type,
//								selectValues: ["list_type", "CType"],
//								selectOptions: ["Frontend plugin", "Content type"],
//							}
						}
					]
				}
			}
		}
	},
	{
		type: "list",
		inputParams: {
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.backendModules,
			name: "backendModules",
			sortable: true,
			elementType: {
				type: "group",
				inputParams: {
					name: "properties",
					fields: [
						{
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.name,
								name: "name",
								required: true
							}
						},
						{
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.key,
								name: "key",
								required: true,
								forceAlphaNumeric: true,
								noSpaces: true,
								description: TYPO3.settings.extensionBuilder._LOCAL_LANG.uniqueInThisModel
							}
						},
						{
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.short_description,
								name: "description"
							}
						},
						{
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.tab_label,
								name: "tabLabel"
							}
						},
						{
							type: 'select',
							inputParams: {
								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.mainModule,
								name: "mainModule",
								required: true,
								selectValues: ["web", "user","tools","help"]
							}
						}
					]
				}
			}
		}
	}

];