import axios from "axios";
import React, {useState} from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import { v4 as uuidv4 } from 'uuid';

export const ActionButtonsComponent = (props) => {
    const [errors, setErrors] = useState(null);
    const [success, setSuccess] = useState(null);

    const handleClose = () => {
        setErrors(null);
        console.log('Close');
    }

    const handleSave = () => {
        console.log("-Props-")
        console.log(props);
        console.log("----------")

        // modules => nodes from react flow
        let modules = [];

        // For each props.nodes, create a module object
        props.nodes.forEach((node) => {
            console.log("Node");
            console.log(node);
            let customActions = [];
            node.data.customActions.map((action) => {
                customActions.push(action);
            });
            console.log("Custom Actions");
            console.log(customActions);

            let properties = [];
            node.data.properties.map((property) => {
                properties.push(
                    {
                        "allowedFileTypes": "",
                        "maxItems": "1",
                        "propertyDescription": property.description,
                        "propertyIsExcludeField": property.isExcludedField,
                        "propertyIsL10nModeExclude": property.isl10nModeExlude,
                        "propertyIsNullable": property.isNullable,
                        "propertyIsRequired": property.isRequired,
                        "propertyName": property.name,
                        "propertyType": property.type,
                        "uid": uuidv4()
                    }
                );
            });

            let module = {
                "config": {
                    "position": [
                        node.position.x,
                        node.position.y
                    ]
                },
                "name": node.data.label,
                "value": {
                    "actionGroup": {
                        "_default0_index": node.data.actions.actionIndex,
                        "_default1_list": node.data.actions.actionList,
                        "_default2_show": node.data.actions.actionShow,
                        "_default3_new_create": node.data.actions.actionNewCreate,
                        "_default4_edit_update": node.data.actions.actionEditUpdate,
                        "_default5_delete": node.data.actions.actionDelete,
                        "customActions": customActions
                    },
                    "name": node.data.label,
                    "objectsettings": {
                        "addDeletedField": node.data.addDeletedField,
                        "addHiddenField": node.data.addHiddenField,
                        "addStarttimeEndtimeFields": node.data.addStarttimeEndtimeFields,
                        "aggregateRoot": node.data.isAggregateRoot,
                        "categorizable": node.data.enableCategorization,
                        "description": node.data.description,
                        "mapToTable": node.data.mapToExistingTable,
                        "parentClass": node.data.extendExistingModelClass,
                        "sorting": node.data.enableSorting,
                        "type": "Entity",
                        "uid": "1173301976935"
                    },
                    "propertyGroup": {
                        "properties": properties
                    },
                    "relationGroup": {
                        "relations": []
                    }
                }
            };
            modules.push(module);
        });

        console.log("-Nodes-")
        console.log(props.nodes);
        console.log("-Nodes END-")

        let working = {
            "modules": modules,
            "properties": {
                "backendModules": props.modules,
                "description": props.properties.description || "",
                "emConf": {
                    "category": props.properties.emConf.category || "backend",
                    "custom_category": "",
                    "dependsOn": props.properties.emConf.dependsOn || "",
                    "disableLocalization": props.properties.emConf.disableLocalization || false,
                    "disableVersioning": props.properties.emConf.disableVersioning || false,
                    "generateDocumentationTemplate": props.properties.emConf.generateDocumentationTemplate || false,
                    "generateEditorConfig": props.properties.emConf.generateEditorConfig || false,
                    "generateEmptyGitRepository": props.properties.emConf.generateEmptyGitRepository || false,
                    "sourceLanguage": props.properties.emConf.sourceLanguage || "en",
                    "state": props.properties.emConf.state || "alpha",
                    "targetVersion": `${props.properties.emConf.targetVersion}.0-${props.properties.emConf.targetVersion}.99` || "12.4.0",
                    "version": props.properties.emConf.version || "0.0.1"
                },
                "extensionKey": props.properties.extensionKey || "my_ext",
                "name": props.properties.name || "My Ext",
                "originalExtensionKey": "",
                "originalVendorName": "",
                "persons": props.authors,
                "plugins": props.plugins,
                "vendorName": props.properties.vendorName || "MyVendor"
            },
            "wires": []
        };

        let payload = {
            "id": 4,
            "method": "saveWiring",
            "name": props.properties.name || "my_ext",
            "params": {
                "language": "extbaseModeling",
                "working": JSON.stringify(working)
            },
            "version": "json-rpc-2.0"
        };
        console.log("----------")
        console.log("payload");
        console.log(payload);
        console.log("----------")

        // TYPO3 will be available in the global scope
        // eslint-disable-next-line no-undef
        axios.post(TYPO3.settings.ajaxUrls.eb_dispatchRpcAction, JSON.stringify(payload), {
            headers: {
                'Content-Type': 'application/json',
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(function (response) {
                console.log("Successfull saved");
                console.log(response.data.success);
                if(response.data.success === null || response.data.success === undefined) {
                    top.TYPO3.Modal.confirm('Successfull saved but ...', '... Something went wrong on server side');
                } else {
                    top.TYPO3.Modal.confirm('Successfull saved', response.data.success);
                }
                // eslint-disable-next-line no-restricted-globals,no-undef
                setSuccess(response);
            })
            .catch(function (error) {
                console.log("Error");
                console.log(error.message);
                // eslint-disable-next-line no-restricted-globals
                top.TYPO3.Modal.confirm(error.message, error.response.data);
                setErrors(error);
            });
    }

    const handleDemoInput = () => {
        props.handleDemoInput();
    }

	return (
		<div className="mb-2">
            <div className="btn-group w-100" role="group" aria-label="Basic example">
                <button
                    type="button"
                    className="btn btn-success"
                    id="eb-btn-save"
                    onClick={handleSave}
                ><FontAwesomeIcon className="me-1" icon="fa-solid fa-save" />Save</button>
                <button
                    type="button"
                    className="btn btn-light text-dark"
                    id="eb-btn-save"
                ><FontAwesomeIcon className="me-1" icon="fa-solid fa-folder" />Open</button>
            </div>
        </div>
	)
}
