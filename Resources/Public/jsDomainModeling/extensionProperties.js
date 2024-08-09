extbaseModeling_wiringEditorLanguage.propertiesFields = [
  {
    type: "string",
    inputParams: {
      name: "name",
      label: "name",
      typeInvite: "extensionTitle",
      required: true,
    },
  },
  {
    type: "string",
    inputParams: {
      name: "vendorName",
      label: "vendorName",
      placeholder: "vendorName",
      helpLink:
        "https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Namespaces/#usage-in-extensions",
      ucFirst: true,
      regexp: /^[A-Za-z]/,
      minLength: 2,
      forceAlphaNumeric: true,
      cols: 30,
      description: "descr_vendorName",
      required: true,
    },
  },
  {
    type: "string",
    inputParams: {
      name: "extensionKey",
      label: "key",
      helpLink:
        "https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ExtensionArchitecture/ExtensionKey/",
      typeInvite: "extensionKey",
      forceLowerCase: true,
      forceAlphaNumericUnderscore: true,
      minLength: 3,
      maxLength: 30,
      cols: 30,
      description: "descr_extensionKey",
      required: true,
    },
  },
  {
    inputParams: {
      name: "originalExtensionKey",
      className: "hiddenField",
    },
  },
  {
    inputParams: {
      name: "originalVendorName",
      className: "hiddenField",
    },
  },
  {
    type: "text",
    inputParams: {
      name: "description",
      label: "description",
      typeInvite: "description",
      cols: 30,
    },
  },
  {
    type: "group",
    inputParams: {
      collapsible: true,
      collapsed: true,
      className: "emConf mainGroup",
      legend: "moreOptions",
      name: "emConf",
      fields: [
        {
          type: "select",
          inputParams: {
            label: "category",
            name: "category",
            description: "descr_category",
            selectValues: [
              "plugin",
              "module",
              "misc",
              "be",
              "fe",
              "services",
              "templates",
              "distribution",
              "example",
              "doc",
            ],
            selectOptions: [
              "plugins",
              "backendModules",
              "misc",
              "backend",
              "frontend",
              "services",
              "templates",
              "distribution",
              "examples",
              "documentation",
            ],
          },
        },
        {
          type: "string",
          inputParams: {
            name: "custom_category",
            label: "custom_category",
            description: "descr_custom_category",
            cols: 30,
          },
        },
        {
          type: "string",
          inputParams: {
            name: "version",
            label: "version",
            required: false,
            size: 5,
            value: "1.0.0",
          },
        },
        {
          type: "select",
          inputParams: {
            name: "state",
            label: "state",
            selectValues: ["alpha", "beta", "stable", "experimental", "test"],
            selectOptions: ["alpha", "beta", "stable", "experimental", "test"],
          },
        },
        {
          type: "boolean",
          inputParams: {
            name: "disableVersioning",
            label: "disableVersioning",
            description: "descr_disableVersioning",
            value: 0,
          },
        },
        {
          type: "boolean",
          inputParams: {
            name: "disableLocalization",
            label: "disableLocalization",
            description: "descr_disableLocalization",
            value: 0,
          },
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateDocumentationTemplate",
            label: "generateDocumentationTemplate",
            description: "descr_generateDocumentationTemplate",
            value: 1,
          },
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateEmptyGitRepository",
            label: "generateEmptyGitRepository",
            description: "descr_generateEmptyGitRepository",
            value: 1,
          },
        },
        {
          type: "boolean",
          inputParams: {
            name: "generateEditorConfig",
            label: "generateEditorConfig",
            description: "descr_generateEditorConfig",
            value: 1,
          },
        },
        {
          type: "string",
          inputParams: {
            name: "sourceLanguage",
            description: "descr_sourceLanguage",
            label: "sourceLanguage",
            value: "en",
            cols: 30,
          },
        },
        {
          type: "select",
          inputParams: {
            name: "targetVersion",
            id: "targetVersionSelector",
            label: "target_version",
            description: "descr_target_version",
            selectValues: ["11.5.0-11.5.99"],
            selectOptions: ["TYPO3 v11.5"],
            value: "11.5.0-11.5.99",
          },
        },
        {
          type: "text",
          inputParams: {
            label: "dependsOn",
            name: "dependsOn",
            id: "extensionDependencies",
            description: "descr_dependsOn",
            cols: 20,
            rows: 6,
            value: "typo3 => 11.5.0-11.5.99\n",
          },
        },
      ],
    },
  },
  {
    type: "list",
    inputParams: {
      label: "persons",
      name: "persons",
      sortable: true,
      className: "persons mainGroup",
      elementType: {
        type: "group",
        inputParams: {
          name: "property",
          fields: [
            {
              inputParams: {
                label: "name",
                name: "name",
                required: true,
              },
            },
            {
              type: "select",
              inputParams: {
                name: "role",
                label: "role",
                selectValues: ["Developer", "Product Manager"],
                selectOptions: ["developer", "product_manager"],
              },
            },
            {
              inputParams: {
                name: "email",
                label: "email",
                required: false,
              },
            },
            {
              inputParams: {
                name: "company",
                label: "company",
                required: false,
              },
            },
          ],
        },
      },
    },
  },
  {
    type: "list",
    inputParams: {
      name: "plugins",
      label: "plugins",
      sortable: true,
      className: "plugins mainGroup",
      elementType: {
        type: "group",
        inputParams: {
          name: "property",
          fields: [
            {
              inputParams: {
                name: "name",
                label: "name",
                required: true,
              },
            },
            {
              inputParams: {
                name: "key",
                label: "key",
                required: true,
                forceLowerCase: true,
                forceAlphaNumeric: true,
                noSpaces: true,
                description: "uniqueInThisModel",
              },
            },
            {
              type: "text",
              inputParams: {
                name: "description",
                label: "description",
                required: false,
                cols: 20,
                rows: 6,
              },
            },
            {
              type: "group",
              inputParams: {
                collapsible: true,
                collapsed: true,
                legend: "advancedOptions",
                name: "actions",
                className: "wideTextfields",
                fields: [
                  {
                    type: "text",
                    inputParams: {
                      name: "controllerActionCombinations",
                      label: "controller_action_combinations",
                      description: "descr_controller_action_combinations",
                      placeholder: "ControllerName => action1,action2",
                      cols: 38,
                      rows: 3,
                    },
                  },
                  {
                    type: "text",
                    inputParams: {
                      name: "noncacheableActions",
                      label: "noncacheable_actions",
                      placeholder: "ControllerName => action1,action2",
                      description: "descr_noncacheable_actions",
                      cols: 38,
                      rows: 3,
                    },
                  },
                ],
              },
            },
          ],
        },
      },
    },
  },
  {
    type: "list",
    inputParams: {
      label: "backendModules",
      name: "backendModules",
      className: "bottomBorder mainGroup",
      sortable: true,
      elementType: {
        type: "group",
        className: "smallBottomBorder",
        inputParams: {
          name: "properties",
          fields: [
            {
              inputParams: {
                label: "name",
                name: "name",
                required: true,
              },
            },
            {
              inputParams: {
                label: "key",
                name: "key",
                required: true,
                forceLowerCase: true,
                forceAlphaNumeric: true,
                noSpaces: true,
                description: "uniqueInThisModel",
              },
            },
            {
              type: "text",
              inputParams: {
                label: "short_description",
                name: "description",
                required: false,
                cols: 20,
                rows: 6,
              },
            },
            {
              inputParams: {
                label: "tab_label",
                name: "tabLabel",
              },
            },
            {
              type: "select",
              inputParams: {
                label: "mainModule",
                name: "mainModule",
                required: true,
                selectValues: [
                  "web",
                  "site",
                  "file",
                  "user",
                  "tools",
                  "system",
                  "help",
                ],
              },
            },
            {
              type: "group",
              inputParams: {
                collapsible: true,
                collapsed: true,
                legend: "advancedOptions",
                name: "actions",
                className: "wideTextfields",
                fields: [
                  {
                    type: "text",
                    inputParams: {
                      name: "controllerActionCombinations",
                      label: "controller_action_combinations",
                      placeholder: "ControllerName => action1,action2",
                      description: "descr_controller_action_combinations",
                      cols: 38,
                      rows: 3,
                    },
                  },
                ],
              },
            },
          ],
        },
      },
    },
  },
];
