import axios from "axios";
import React, {useState, useContext} from "react";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import { v4 as uuidv4 } from 'uuid';
import {listAvailableExtensions} from "../helper/api/listAvailableExtensions";
import { Modal, Button } from 'react-bootstrap';
import {EdgesContext, NodesContext} from "../App";

export const ActionButtonsComponent = (props) => {
    const {nodes} = useContext(NodesContext);
    const {edges} = useContext(EdgesContext);

    const [errors, setErrors] = useState(null);
    const [success, setSuccess] = useState(null);

    const [show, setShow] = useState(false);
    const [modalTitle, setModalTitle] = useState('');
    const [modalBody, setModalBody] = useState('');

    const [modalBodyHtml, setModalBodyHtml] = useState('');
    const [modalBodyJsx, setModalBodyJsx] = useState(null)

    const handleClose = () => {
        setErrors(null);
    }

    const handleSave = () => {
        // modules => nodes from react flow
        let modules = [];

        // For each nodes, create a module object
        nodes.forEach((node) => {
            let customActions = [];
            node.data.customActions.map((action) => {
                customActions.push(action);
            });

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

            let relations = [];
            node.data.relations.map((relation) => {
                relations.push(
                    {
                        "foreignRelationClass": relation.foreignRelationClass || "",
                        "lazyLoading": relation.lazyLoading || false,
                        "propertyIsExcludeField": relation.propertyIsExcludeField || false,
                        "relationDescription": relation.relationDescription || "",
                        "relationName": relation.relationName || "",
                        "relationType": relation.relationType || "anyToMany",
                        "relationWire": "[wired]",
                        "renderType": "selectSingle",
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
                        "uid": uuidv4()
                    },
                    "propertyGroup": {
                        "properties": properties
                    },
                    "relationGroup": {
                        "relations": relations
                    }
                }
            };
            modules.push(module);
        });

        let sourceRelationCount = new Map();

        let wires = edges.map((edge, index) => {
            // Ermittle die Indizes der Nodes
            let sourceIndex = nodes.findIndex(n => n.id === edge.source);
            let targetIndex = nodes.findIndex(n => n.id === edge.target);

            // Aktualisiere die Zählung für die Source Node
            if (!sourceRelationCount.has(sourceIndex)) {
                sourceRelationCount.set(sourceIndex, 0);
            }
            let relationIndex = sourceRelationCount.get(sourceIndex);
            sourceRelationCount.set(sourceIndex, relationIndex + 1);

            return {
                "src": {
                    "moduleId": modules.findIndex(node => node.name === nodes[sourceIndex].data.label),
                    "moduleName": nodes[sourceIndex].data.label,
                    "terminal": `relationWire_${relationIndex}`,
                    "uid": edge.id
                },
                "tgt": {
                    "moduleId": modules.findIndex(node => node.name === nodes[targetIndex].data.label),
                    "moduleName": nodes[targetIndex].data.label,
                    "terminal": "SOURCES",
                    "uid": edge.source
                }
            }
        });


        let working = {
            "modules": modules,
            "properties": {
                "backendModules": props.modules,
                "description": props.properties.description || "",
                "emConf": {
                    "category": props.properties.emConf.category,
                    "custom_category": "",
                    "dependsOn": props.properties.emConf.dependsOn || "",
                    "disableLocalization": props.properties.emConf.disableLocalization || false,
                    "disableVersioning": props.properties.emConf.disableVersioning || false,
                    "generateDocumentationTemplate": props.properties.emConf.generateDocumentationTemplate || false,
                    "generateEditorConfig": props.properties.emConf.generateEditorConfig || false,
                    "generateEmptyGitRepository": props.properties.emConf.generateEmptyGitRepository || true,
                    "sourceLanguage": props.properties.emConf.sourceLanguage,
                    "state": props.properties.emConf.state,
                    "targetVersion": props.properties.emConf.targetVersion,
                    "version": props.properties.emConf.version
                },
                "extensionKey": props.properties.extensionKey,
                "name": props.properties.name,
                "originalExtensionKey": "",
                "originalVendorName": "",
                "persons": props.authors,
                "plugins": props.plugins,
                "vendorName": props.properties.vendorName
            },
            "wires": wires,
            "nodes": nodes,
            "edges": edges
        };

        let payload = {
            "id": 4,
            "method": "saveWiring",
            "name": props.properties.name,
            "params": {
                "language": "extbaseModeling",
                "working": JSON.stringify(working)
            },
            "version": "json-rpc-2.0"
        };

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
                    setModalTitle('Successfull saved but ...');
                    setModalBodyHtml('... Something went wrong on server side<br><br><code>' + JSON.stringify(response.data) + '</code>');
                    setModalBodyJsx(null)
                    setShow(true);
                } else {
                    setModalTitle('Successfull saved');
                    setModalBodyHtml(response.data.success)
                    setModalBodyJsx(null)
                    setShow(true);
                }
                // eslint-disable-next-line no-restricted-globals,no-undef
                setSuccess(response);
            })
            .catch(function (error) {
                console.log("Error");
                console.log(error.message);
                // eslint-disable-next-line no-restricted-globals
                top.TYPO3.Modal.confirm(
                    error.message,
                    error.response.data,
                    0,
                    [
                        {
                            text: 'Close',
                            trigger: function () {
                                console.log("this should close the modal");
                                top.TYPO3.Modal.dismiss();
                            }
                        }
                    ]
                );
                setErrors(error);
            });
    }

    const handleOpenExtension = async () => {
        // Lists all available extensions built with the extension builder
        // Only if they have the extensionbuilder.json file
        const extensions = await listAvailableExtensions();

        // the extensions are now inside the extensions constant as a json object
        // the modal now lists the extensions and after clicking on one of them, the modal closes and the extension is loaded

        // extensions json has the following structure:
        // error = null, if no error occurs
        // success = true, when the request was successful
        // result with the array of extensions

        if(extensions.error !== null && extensions.success === false) {
            console.log("fetching failed");
            setShow(true);
            setModalTitle('Fetching failed');
            setModalBodyHtml('Fetching the extensions failed. Please check, if you have extensions with a valid extensionbuilder.json file.');
            setModalBodyJsx(null);
        } else if (extensions.error === null && extensions.success === true) {
            setShow(true);
            setModalTitle('Available extensions');
            setModalBodyJsx(
                <>
                    <p>Please select an extension to open</p>
                    <div className="list-group">
                        {extensions.result.map((extension) => (
                            <button
                                type="button"
                                className="list-group-item list-group-item-action"
                                key={extension.name}
                                onClick={() => handleExtensionClick(extension)}
                            >
                                {extension.name}
                            </button>
                        ))}
                    </div>
                </>
            );
            setModalBodyHtml(null);
        }
    }

    const handleExtensionClick = (extension) => {
        setShow(false);
        props.handleOpenExtension(extension);
    };

	return (
        <>
            <Modal show={show} onHide={() => setShow(false)}>
                <Modal.Header closeButton>
                    <Modal.Title>{modalTitle}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {modalBodyHtml && <div dangerouslySetInnerHTML={{ __html: modalBodyHtml }} />}
                    {modalBodyJsx}
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" onClick={() => setShow(false)}>
                        Close
                    </Button>
                </Modal.Footer>
            </Modal>
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
                        id="eb-btn-prefill"
                        onClick={handleOpenExtension}
                    ><FontAwesomeIcon className="me-1" icon="fa-solid fa-file" />Open</button>
                </div>
            </div>
        </>

	)
}
