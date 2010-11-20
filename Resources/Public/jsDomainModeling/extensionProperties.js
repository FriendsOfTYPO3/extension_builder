extbaseModeling_wiringEditorLanguage.propertiesFields =
[
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
	},
	{
		type: "list",
		inputParams: {
			label: "Plugins",
			name: "plugins",
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
								required: true
							}
						},
						{
							inputParams: {
								label: "Key",
								name: "key",
								required: true
							}
//						},
//						{
//							type: "select",
//							inputParams: {
//								label: "Type",
//								name: "type",
//								selectValues: ["list_type", "CType"],
//								selectOptions: ["Frontend plugin", "Content type"],
//							}
						}
					]
				}
			}
		}
	}
];