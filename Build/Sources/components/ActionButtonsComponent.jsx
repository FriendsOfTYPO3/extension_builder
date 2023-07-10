import axios from "axios";
import React, {useState} from "react";
import {Error} from "./errors/Error";
import {Success} from "./errors/Success";
import {BootstrapModal} from "./modals/BootstrapModal";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

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

        let working = {
            "modules": [],
            "properties": {
                "backendModules": props.modules,
                "description": props.properties.description || "",
                "emConf": {
                    "category": props.properties.emConf.category || "backend",
                    "custom_category": "",
                    "dependsOn": props.properties.emConf.dependsOn || "",
                    "disableLocalization": props.properties.emConf.disableLocalization || false,
                    "disableVersioning": props.properties.emConf.disableVersioning || false,
                    "generateDocumentationTemplate": props.properties.emConf.generateDocumentationTemplate || true,
                    "generateEditorConfig": props.properties.emConf.generateEditorConfig || true,
                    "generateEmptyGitRepository": props.properties.emConf.generateEmptyGitRepository || true,
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
                // eslint-disable-next-line no-restricted-globals,no-undef
                top.TYPO3.Modal.confirm('Successfull saved', response.data.success);
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
{/*            <button
                type="button"
                className="btn btn-secondary me-2"
                id="eb-btn-demo"
                onClick={handleDemoInput}
            >Demo Input</button>*/}

{/*            <button
                type="button"
                className="btn btn-danger"
                id="eb-btn-close"
            >Close</button>*/}
        </div>
	)
}
