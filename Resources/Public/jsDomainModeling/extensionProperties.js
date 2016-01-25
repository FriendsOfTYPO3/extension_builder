extbaseModeling_wiringEditorLanguage.propertiesFields =
[
	{
		type: "string",
		inputParams: {
			name: "name",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.name,
			typeInvite: TYPO3.settings.extensionBuilder._LOCAL_LANG.extensionTitle,
			required: true
		}
	},
	{
		type: "string",
		inputParams: {
			name: "vendorName",
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.vendorName,
			placeholder: TYPO3.settings.extensionBuilder._LOCAL_LANG.vendorName,
			value: '',
			ucFirst: true,
            regexp: /^[A-Z]/,
            minLength: 2,
			forceAlphaNumeric: true,
			cols: 30,
			description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_vendorName,
			required: true
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
            regexp: /^[a-z]/,
            minLength: 3,
            maxLength: 30,
			cols: 30,
			description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_extensionKey,
			required: true
		}
	},
	{
		inputParams: {
			name: "originalExtensionKey",
			className:'hiddenField'
		}
	},
    {
        inputParams: {
            name: "originalVendorName",
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
			className: 'emConf mainGroup',
			legend: TYPO3.settings.extensionBuilder._LOCAL_LANG.moreOptions,
			name: "emConf",
			fields: [
					{
						type: "select",
						inputParams: {
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.category,
							name: "category",
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_category,
							selectValues: [ "plugin", "module", "misc", "be", "fe", "services", "templates", "distribution", "example", "doc"],
							selectOptions: [
								TYPO3.settings.extensionBuilder._LOCAL_LANG.plugins,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.backendModules,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.misc,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.backend,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.frontend,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.services,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.templates,
								TYPO3.settings.extensionBuilder._LOCAL_LANG.distribution,
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
							size: 5,
							value: '1.0.0'
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
						type: "boolean",
						inputParams: {
							name: "disableVersioning",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.disableVersioning,
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_disableVersioning,
							value: 0
						}
					},
					{
						type: "boolean",
						inputParams: {
							name: "disableLocalization",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.disableLocalization,
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_disableLocalization,
							value: 0
						}
					},
					{
						type: "boolean",
						inputParams: {
							name: "skipGenerateDocumentationTemplate",
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.skipGenerateDocumentationTemplate,
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_skipGenerateDocumentationTemplate,
							value: 1
						}
					},
					{
						type: "string",
						inputParams: {
							name: "sourceLanguage",
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_sourceLanguage,
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.sourceLanguage,
							value: 'en',
							cols: 30
						}
					},
                    {
                        type: "select",
                        inputParams: {
                            name: "targetVersion",
							id: 'targetVersionSelector',
                            label: TYPO3.settings.extensionBuilder._LOCAL_LANG.target_version,
                            description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_target_version,
                            selectOptions: [
								'TYPO3 v 7.6'
                            ],
                            selectValues: ["7.6.0-7.6.99"],
							value: '7.6.0-7.6.99'
                        }
                    },
					{
						type: "text",
						inputParams: {
							label: TYPO3.settings.extensionBuilder._LOCAL_LANG.dependsOn,
							name: "dependsOn",
							id: 'extensionDependencies',
							description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_dependsOn,
							cols:20,
							rows:6,
							value : "typo3 => 7.6.0-7.6.99\n"
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
			className: 'persons mainGroup',
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
			sortable: true,
			className: 'plugins mainGroup',
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
								forceLowerCase: true,
								forceAlphaNumeric: true,
								noSpaces: true,
								description: TYPO3.settings.extensionBuilder._LOCAL_LANG.uniqueInThisModel
							}
						},
						{
							type: 'group',
							inputParams: {
								collapsible: true,
								collapsed: true,
								legend: TYPO3.settings.extensionBuilder._LOCAL_LANG.advancedOptions,
								name: "actions",
								className:"wideTextfields",
								fields: [
									{
										type: "text",
										inputParams: {
											name: "controllerActionCombinations",
											label: TYPO3.settings.extensionBuilder._LOCAL_LANG.controller_action_combinations,
											description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_controller_action_combinations,
											placeholder: 'ControllerName => action1,action2',
											cols: 38,
											rows: 3
										}
									},
									{
										type: "text",
										inputParams: {
											name: "noncacheableActions",
											label: TYPO3.settings.extensionBuilder._LOCAL_LANG.noncacheable_actions,
											placeholder: 'ControllerName => action1,action2',
											description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_noncacheable_actions,
											cols: 38,
											rows: 3
										}
									},
									{
										type: "text",
										inputParams: {
											name: "switchableActions",
											label: TYPO3.settings.extensionBuilder._LOCAL_LANG.switchableActions,
											description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_switchableActions,
											cols: 38,
											rows: 3
										}
									}
			//						{
			//							type: "select",
			//							inputParams: {
			//								name: "type",
			//								label: TYPO3.settings.extensionBuilder._LOCAL_LANG.type,
			//								selectValues: ["list_type", "CType"],
			//								selectOptions: ["Frontend plugin", "Content type"],
			//							}
								]
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
			label: TYPO3.settings.extensionBuilder._LOCAL_LANG.backendModules,
			name: "backendModules",
			className: 'bottomBorder mainGroup',
			sortable: true,
			elementType: {
				type: "group",
				className: 'smallBottomBorder',
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
								forceLowerCase: true,
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
						},
						{
							type: 'group',
							inputParams: {
								collapsible: true,
								collapsed: true,
								legend: TYPO3.settings.extensionBuilder._LOCAL_LANG.advancedOptions,
								name: "actions",
								className:"wideTextfields",
								fields: [
									{
										type: "text",
										inputParams: {
											name: "controllerActionCombinations",
											label: TYPO3.settings.extensionBuilder._LOCAL_LANG.controller_action_combinations,
											description: TYPO3.settings.extensionBuilder._LOCAL_LANG.descr_controller_action_combinations,
											cols: 38,
											rows: 3
										}
									}
								]
							}
						}
					]
				}
			}
		}
	}

];
