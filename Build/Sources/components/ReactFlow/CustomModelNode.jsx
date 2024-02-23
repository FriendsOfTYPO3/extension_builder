import { useState, useContext} from 'react';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {TYPO3StyledAccordion} from "../accordions/TYPO3StyledAccordion";
import {TYPO3StyledAccordionGroup} from "../accordions/TYPO3StyledAccordionGroup";
import InputComponent from "../forms/input/InputComponent";
import CheckboxComponent from "../forms/input/CheckboxComponent";
import TextareaComponent from "../forms/textarea/TextareaComponent";
import SelectComponent from "../forms/select/SelectComponent";
import { Handle, Position } from 'reactflow';
import propertyTypes from "./customModelNode/propertyTypes";
import relationTypes from "./customModelNode/relationTypes";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'

import {AdvancedOptionsContext, RemoveEdgeContext, EdgesContext } from "../../App";
import { v4 as uuidv4 } from 'uuid';

export const CustomModelNode = (props) => {
    const [properties, setProperties] = useState(props.data.properties);
    const [relations, setRelations] = useState(props.data.relations);
    const [relationIndex, setRelationIndex] = useState(0);
    const [customActions, setCustomActions] = useState([]);

    const {isAdvancedOptionsVisible} = useContext(AdvancedOptionsContext);
    const {removeEdge} = useContext(RemoveEdgeContext);
    const {edges} = useContext(EdgesContext);

    // TODO: create a default property inside an empty js file and set it her.
    const addEmptyProperty = () => {
        setProperties([...properties, {
            name: '',
            type: '',
            description: '',
            isRequired: false,
            isNullable: false,
            isExcludeField: false,
            isl10nModeExlude: false,
            typeSelect: {
                selectboxValues: "",
                renderType: "selectSingle",
                foreignTable: "",
            },
            typeText: {
                enableRichtext: false,
            },
            typeNumber: {
                enableSlider: false,
                steps: 1,
                setRange: false,
                upperRange: 255,
                lowerRange: 0,
            },
            typeColor: {
                setValuesColorPicker: false,
                colorPickerValues: '',
            },
            typeBoolean: {
                renderType: "default",
                booleanValues: "",
            },
            typePassword: {
                renderPasswordGenerator: false,
            },
            typeDateTime: {
                dbTypeDateTime: "",
                formatDateTime: "",
            },
            typeFile: {
                allowedFileTypes: "",
            },
            size: "",
            minItems: "",
            maxItems: "",
        }]);
        props.data.properties.push(
            {
                name: '',
                type: '',
                description: '',
                isRequired: false,
                isNullable: false,
                excludeField: false,
                isl10nModeExlude: false,
                typeSelect: {
                    selectboxValues: "",
                    renderType: "selectSingle",
                    foreignTable: "",
                },
                typeText: {
                    enableRichtext: false,
                },
                typeNumber: {
                    enableSlider: false,
                    steps: 1,
                    setRange: false,
                    upperRange: 255,
                    lowerRange: 0,
                },
                typeColor: {
                    setValuesColorPicker: false,
                    colorPickerValues: '',
                },
                typeBoolean: {
                    renderType: "default",
                    booleanValues: "",
                },
                typePassword: {
                    renderPasswordGenerator: false,
                },
                typeDateTime: {
                    dbTypeDateTime: "",
                    formatDateTime: "",
                },
                typeFile: {
                    allowedFileTypes: "",
                },
                size: "",
                minItems: "",
                maxItems: "",
            }
        );
    }

    const addEmptyRelation = () => {
        const newRelation = {
            "foreignRelationClass": "",
            "lazyLoading": true,
            "excludeField": true,
            "relationDescription": "",
            "relationName": "",
            "relationType": "",
            "relationWire": "",
            "renderType": "",
            // TODO: muss die uuid hier generiert werden?
            "uid": uuidv4() // Hier wird die UUID generiert
        };

        setRelations([...relations, newRelation]);
        props.data.relations.push(newRelation);
        setRelationIndex(prevIndex => prevIndex + 1);
    }

    const addEmptyAction = () => {
        const newAction = {name: ''};

        // Updating customActions
        setCustomActions(prevActions => [...prevActions, newAction]);

        // Adding a new entry to props.data.customActions
        props.data.customActions = [...props.data.customActions, newAction];
    }

    const updateCustomAction = (index, value) => {
        props.data.customActions[index] = value;
    }

    const updateProperty = (index, property, value) => {
        const pathParts = property.split(".");
        let currentProperty = properties[index];

        for (let i = 0; i < pathParts.length - 1; i++) {
            if (!currentProperty[pathParts[i]]) {
                currentProperty[pathParts[i]] = {};
            }
            currentProperty = currentProperty[pathParts[i]];
        }
        currentProperty[pathParts[pathParts.length - 1]] = value;

        setProperties([...properties]);
        props.data.properties = properties;
    }

    const removeProperty = (propertyIndex) => {
        properties.splice(propertyIndex, 1);
        setProperties([...properties]);
        props.data.properties = properties;
    }

    const removeRelation = (relationIndex, handleId) => {
        removeEdge(handleId);

        relations.splice(relationIndex, 1);
        setRelations([...relations]);
        props.data.relations = relations;
    }

    const moveProperty = (propertyIndex, direction) => {
        const newIndex = propertyIndex + direction;
        if (newIndex < 0 || newIndex >= properties.length) {
            return;
        }
        const property = properties[propertyIndex];
        properties.splice(propertyIndex, 1);
        properties.splice(newIndex, 0, property);
        setProperties([...properties]);
        props.data.properties = properties;
    }

    const moveRelation = (relationIndex, direction) => {
        const newIndex = relationIndex + direction;
        if (newIndex < 0 || newIndex >= relations.length) {
            return;
        }
        const relation = relations[relationIndex];
        relations.splice(relationIndex, 1);
        relations.splice(newIndex, 0, relation);
        setRelations([...relations]);
        props.data.relations = relations;
    }

    const updateRelation = (index, property, value) => {
        relations[index][property] = value;
        setRelations([...relations]);
        props.data.relations = relations;
    }

    const updateNode = (path, value) => {
        const pathParts = path.split(".");
        let data = props.data;

        for (let i = 0; i < pathParts.length - 1; i++) {
            if (!data[pathParts[i]]) {
                data[pathParts[i]] = {};
            }
            data = data[pathParts[i]];
        }
        data[pathParts[pathParts.length - 1]] = value;
    }

    const removeNode = (id) => {
        // props.removeNode(id);
        console.log('removeNode', props);
    }

    const getIsRelationConnected = (relationUid) => {
        // check if the relation UID is inside the props.edges
        return edges.some(edge => edge.sourceHandle === relationUid);
    };

    return (
        <div className="custom-model-node">
            <div className="drag-handle"></div>
            <div className="custom-model-node__header">
                <InputComponent
                    initialValue={props.data.label}
                    label="Node title"
                    placeholder="Node title"
                    identifier="nodeTitle"
                    onChange={(value) => {
                        // replace all whitespaces inside the value with an empty string
                        value = value.replace(/\s/g, '');
                        updateNode('label', value.charAt(0).toUpperCase() + value.slice(1));
                    }}
                />
                <button
                    className="btn btn-danger btn-sm btn-delete-node"
                    onClick={() => {
                        removeNode(props.id);
                    }}
                ><FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-trash"/></button>
                <Handle
                    type="target"
                    id={`cmn-${props.id}`}
                    position={Position.Left}
                    onConnect={(params) => console.log('handle onConnect', params)}
                    style={{
                        background: 'rgb(255 135 0 / 33%)',
                        border: '3px solid #ff8700',
                        position: 'relative',
                        top: '-21px',
                        left: '-30px'
                    }}
                />
            </div>
            <TYPO3StyledAccordionGroup id={`accordionCustomModelNode-${props.id}`}>
            <TYPO3StyledAccordion  title="Domain object settings" id={`accordionItemCustomModelNode-settings-${props.id}`} parentId={`accordionCustomModelNode-${props.id}`}>
                    <CheckboxComponent
                        identifier="isAggregateRoot"
                        label="Is aggregate root"
                        checked={props.data.isAggregateRoot}
                        onChange={(value) => {
                            updateNode('isAggregateRoot', value);
                        }}
                    />
                    { isAdvancedOptionsVisible && (<SelectComponent
                                identifier="controllerScope"
                                label="Controller scope"
                                options={['Backend', 'Frontend']}
                                initialValue={props.data.controllerScope}
                                onChange={(value) => {
                                    updateNode('controllerScope', value);
                                }} />
                    )}
                    { isAdvancedOptionsVisible && (
                        <p>This is only used for the templates of the Controller.<br/>
                            In the frontend, there is no ModuleTemplateFactory <br/>
                            available. If you want to use Boostrap classes inside <br/>
                            your Template, you should choose "Backend". <br/>
                            You can always adjust your controllers for your needs.</p>
                    )}
                    { isAdvancedOptionsVisible && (<CheckboxComponent
                        identifier="enableSorting"
                        label="Enable sorting"
                        checked={props.data.enableSorting}
                        onChange={(value) => {
                            updateNode('enableSorting', value);
                        }}
                    /> )}
                    { isAdvancedOptionsVisible && (<CheckboxComponent
                        identifier="addDeletedField"
                        label="Add deleted field"
                        checked={props.data.addDeletedField}
                        onChange={(value) => {
                            updateNode('addDeletedField', value);
                        }}
                    /> )}
                    { isAdvancedOptionsVisible && (<CheckboxComponent
                        identifier="addHiddenField"
                        label="Add hidden field"
                        checked={props.data.addHiddenField}
                        onChange={(value) => {
                            updateNode('addHiddenField', value);
                        }}
                    /> )}
                    { isAdvancedOptionsVisible && (<CheckboxComponent
                        identifier="addStarttimeEndtimeFields"
                        label="Add starttime/endtime fields"
                        checked={props.data.addStarttimeEndtimeFields}
                        onChange={(value) => {
                            updateNode('addStarttimeEndtimeFields', value);
                        }}
                    /> )}
                    { isAdvancedOptionsVisible && (<CheckboxComponent
                        identifier="enableCategorization"
                        label="Enable categorization"
                        checked={props.data.enableCategorization}
                        onChange={(value) => {
                            updateNode('enableCategorization', value);
                        }}
                    /> )}
                    <TextareaComponent
                        placeholder="Description"
                        identifier="description"
                        label="Description"
                        initialValue={props.data.description}
                        onChange={(value) => {
                            updateNode('description', value);
                        }}
                    />
                    { isAdvancedOptionsVisible && (<InputComponent
                        label="Map to existing table:"
                        placeholder="tablename"
                        identifier="mapToExistingTable"
                        initialValue={props.data.mapToExistingTable}
                        onChange={(value) => {
                            updateNode('mapToExistingTable', value);
                        }}
                    /> )}
                    { isAdvancedOptionsVisible && (<InputComponent
                        label="Extend existing model class:"
                        placeholder="\Fully\Qualified\Classname"
                        identifier="extendExistingModelClass"
                        initialValue={props.data.parentClass}
                        onChange={(value) => {
                            updateNode('parentClass', value);
                        }}
                    /> )}
                </TYPO3StyledAccordion>
                <TYPO3StyledAccordion  title="Actions" id={`accordionItemCustomModelNode-actions-${props.id}`} parentId={`accordionCustomModelNode-${props.id}`}>
                    <CheckboxComponent
                        identifier="actionIndex"
                        label="index"
                        checked={props.data.actions?.actionIndex}
                        onChange={(value) => {
                            updateNode('actions.actionIndex', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="actionList"
                        label="list"
                        checked={props.data.actions?.actionList}
                        onChange={(value) => {
                            updateNode('actions.actionList', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="actionShow"
                        label="show"
                        checked={props.data.actions?.actionShow}
                        onChange={(value) => {
                            updateNode('actions.actionShow', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="actionNewCreate"
                        label="new / create"
                        checked={props.data.actions?.actionNewCreate}
                        onChange={(value) => {
                            updateNode('actions.actionNewCreate', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="actionEditUpdate"
                        label="edit / update"
                        checked={props.data.actions?.actionEditUpdate}
                        onChange={(value) => {
                            updateNode('actions.actionEditUpdate', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="actionDelete"
                        label="delete"
                        checked={props.data.actions?.actionDelete}
                        onChange={(value) => {
                            updateNode('actions.actionDelete', value);
                        }}
                    />
                    <div className="d-flex justify-content-between align-items-center mt-2">
                        <span className="text-primary">Custom actions</span>
                        <button className="btn btn-outline btn-sm p-0 text-primary"
                                title="Add action"
                                onClick={addEmptyAction}
                        ><FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" /></button>
                    </div>
                    {
                        props.data.customActions.map((action, index) => (
                            <div className="d-flex align-items-center justify-content-between custom-model-node__action-wrapper">
                                <InputComponent
                                    placeholder="Action name"
                                    identifier={`actionName${index}`}
                                    initialValue={action}
                                    onChange={(value) => {
                                        updateCustomAction(index, value);
                                    }}
                                />
                                <button className="btn btn-outline btn-sm p-0 text-danger"
                                        title="Remove action"
                                        onClick={() => {
                                            props.data.customActions.splice(index, 1);
                                            setCustomActions([...props.data.customActions]);
                                        }}
                                >
                                    <FontAwesomeIcon className="font-awesome-icon text-danger" icon="fa-solid fa-trash" />
                                </button>
                             </div>
                        ))
                    }
                </TYPO3StyledAccordion>
                <TYPO3StyledAccordion  title="Properties" id={`accordionItemCustomModelNode-properties-${props.id}`} parentId={`accordionCustomModelNode-${props.id}`}>
                    <div className="d-flex justify-content-end">
                        <button className="btn btn-success mb-2 btn-sm" title="Add property"
                                onClick={addEmptyProperty}
                        >
                            <FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" />
                        </button>
                    </div>
                    <TYPO3StyledAccordionGroup
                        id="accordionCustomModelNodeProperties"
                    >
                    {
                        props.data.properties.map((property, index) => {
                            return (
                                <TYPO3StyledAccordion
                                    title={`${property.name} ${property.type ? `(${property.type})` : ''}`}
                                    id={`nodeProperty-${props.id}-${index}`}
                                    parentId="accordionCustomModelNodeProperties"
                                >
                                    <div className="custom-model-node__property-wrapper">
                                        <InputComponent
                                            label="Property name"
                                            placeholder="Property name"
                                            identifier="propertyName"
                                            initialValue={property.name}
                                            onChange={(value) => {
                                                updateProperty(index, "name", value.toLowerCase());
                                            }}
                                        />
                                        <SelectComponent
                                            label="Property type"
                                            identifier="propertyType"
                                            options={propertyTypes}
                                            initialValue={property.type}
                                            onChange={(value) => {
                                                updateProperty(index, "type", value);
                                            }}
                                        />
                                        {property.type === 'Text' &&(<CheckboxComponent
                                            label="Enable RichText editor"
                                            identifier="enableRichTextEditor"
                                            initialValue={property.typeText?.enableRichtext}
                                            checked={property.typeText?.enableRichtext}
                                            onChange={(value) => {
                                                updateProperty(index, "typeText.enableRichtext", value);
                                            }}
                                        />)}
                                        {(property.type === 'Integer' || property.type === 'Float') &&(
                                            <div className="d-flex flex-column">
                                                <CheckboxComponent
                                                    label="Enable slider"
                                                    identifier="enableSlider"
                                                    initialValue={property.typeNumber?.enableSlider}
                                                    checked={property.typeNumber?.enableSlider}
                                                    onChange={(value) => {
                                                        updateProperty(index, "typeNumber.enableSlider", value);
                                                    }} />
                                                {property.typeNumber?.enableSlider && (
                                                    <CheckboxComponent
                                                        label="Set range"
                                                        identifier="setRange"
                                                        initialValue={property.typeNumber?.setRange}
                                                        checked={property.typeNumber?.setRange}
                                                        onChange={(value) => {
                                                            updateProperty(index, "typeNumber.setRange", value);
                                                        }} />
                                                )}
                                            </div>
                                        )}
                                        {(property.type === 'Integer' || property.type === 'Float') && (property.typeNumber?.enableSlider) &&
                                            <div className="d-flex">
                                                <InputComponent
                                                    label="Step size"
                                                    placeholder="1"
                                                    identifier="steps"
                                                    initialValue={property.typeNumber?.steps}
                                                    onChange={(value) => {
                                                        updateProperty(index, "typeNumber.steps", value);
                                                    }}
                                                />
                                            </div>
                                        }{((property.type === 'Integer' || property.type === 'Float') && (property.typeNumber?.enableSlider && property.typeNumber?.setRange)) &&
                                            <div className="d-flex flex-column">
                                                <InputComponent
                                                    label="Lower range"
                                                    placeholder="0"
                                                    identifier="lowerRange"
                                                    initialValue={property.typeNumber?.lowerRange}
                                                    onChange={(value) => {
                                                        updateProperty(index, "typeNumber.lowerRange", value);
                                                    }}
                                                />
                                                <InputComponent
                                                    label="Upper range"
                                                    placeholder="42"
                                                    identifier="upperRange"
                                                    initialValue={property.typeNumber?.upperRange}
                                                    onChange={(value) => {
                                                        updateProperty(index, "typeNumber.upperRange", value);
                                                    }}
                                                />
                                            </div>
                                        }
                                        {property.type === 'DateTime' &&(<SelectComponent
                                            label="Format DateTime"
                                            identifier="formatDateTime"
                                            options={['date', 'datetime', 'time', 'timesec']}
                                            initialValue={property.typeDateTime?.formatDateTime}
                                            onChange={(value) => {
                                                updateProperty(index, "typeDateTime.formatDateTime", value);
                                            }}
                                        />)}
                                        {property.type === 'Select' &&(<TextareaComponent
                                            label="Values for Select-Box"
                                            placeholder="label;value separated by new line"
                                            identifier="selectboxValues"
                                            initialValue={property.typeSelect?.selectboxValues}
                                            onChange={(value) => {
                                                updateProperty(index, "typeSelect.selectboxValues", value);
                                            }}
                                        />)}
                                        {property.type === 'Select' &&(<SelectComponent
                                            label="Render Type"
                                            identifier="renderType"
                                            options={['selectSingle','selectSingleBox','selectCheckBox','selectMultipleSideBySide']}
                                            initialValue={property.typeSelect?.renderType}
                                            onChange={(value) => {
                                                updateProperty(index, "typeSelect.renderType", value);
                                            }}
                                        />)}
                                        {property.type === 'Select' &&<InputComponent
                                            label="Foreign table (will override values)"
                                            placeholder="Foreign table"
                                            identifier="foreignTable"
                                            initialValue={property.typeSelect?.foreignTable}
                                            onChange={(value) => {
                                                updateProperty(index, "typeSelect.foreignTable", value);
                                            }}
                                        />}
                                        {property.type === 'Select' &&<InputComponent
                                            label="Where (only for foreign table)"
                                            placeholder="where x = y"
                                            identifier="whereClause"
                                            initialValue={property.typeSelect?.whereClause}
                                            onChange={(value) => {
                                                updateProperty(index, "typeSelect.whereClause", value);
                                            }}
                                        />}
                                        {property.type === 'Select' &&<InputComponent
                                            label="Size"
                                            placeholder="5"
                                            identifier="size"
                                            initialValue={property.size}
                                            onChange={(value) => {
                                                updateProperty(index, "size", value);
                                            }}
                                        />}
                                        {property.type === 'Text' && !property.typeText?.enableRichtext && <InputComponent
                                            label="Rows (not for richtext)"
                                            placeholder="5"
                                            identifier="rows"
                                            initialValue={property.rows}
                                            onChange={(value) => {
                                                updateProperty(index, "rows", value);
                                            }}
                                        />}
                                        {(property.type === 'Select' || property.type === 'File') && (<div className="d-flex">
                                            <InputComponent
                                                label="Min items"
                                                placeholder="2"
                                                identifier="minItems"
                                                initialValue={property.minItems}
                                                onChange={(value) => {
                                                    updateProperty(index, "minItems", value);
                                                }}
                                            />
                                            <InputComponent
                                                label="Max items"
                                                placeholder="20"
                                                identifier="maxItems"
                                                initialValue={property.maxItems}
                                                onChange={(value) => {
                                                    updateProperty(index, "maxItems", value);
                                                }}
                                            />
                                        </div>)}
                                        {(property.type === 'File') && (<div className="d-flex">
                                            <InputComponent
                                                label="Allowed filetypes"
                                                placeholder="2"
                                                identifier="allowedFileTypes"
                                                initialValue={property.typeFile?.allowedFileTypes}
                                                onChange={(value) => {
                                                    updateProperty(index, "typeFile.allowedFileTypes", value);
                                                }}
                                            />
                                        </div>)}
                                        {property.type === 'Boolean' &&(<SelectComponent
                                            label="Render Type"
                                            identifier="renderTypeBoolean"
                                            options={['default','checkboxToggle']}
                                            initialValue={property.typeBoolean?.renderType}
                                            onChange={(value) => {
                                                updateProperty(index, "typeBoolean.renderType", value);
                                            }}
                                        />)}
                                        {property.type === 'Boolean' && (<TextareaComponent
                                            label="Items for checkbox"
                                            placeholder="label;value separated by new line"
                                            identifier="booleanValues"
                                            initialValue={property.typeBoolean?.booleanValues}
                                            onChange={(value) => {
                                                updateProperty(index, "typeBoolean.booleanValues", value);
                                            }}
                                        />)}
                                        {property.type === 'Password' &&(<CheckboxComponent
                                            identifier="renderPasswordGenerator"
                                            label="Render password generator"
                                            initialValue={property.typePassword?.renderPasswordGenerator}
                                            checked={property.typePassword?.renderPasswordGenerator}
                                            onChange={(value) => {
                                                updateProperty(index, "typePassword.renderPasswordGenerator", value);
                                            }} />)
                                        }
                                        {property.type === 'ColorPicker' &&( <CheckboxComponent
                                            label="Set values for color picker"
                                            identifier="setValuesColorPicker"
                                            initialValue={property.typeColor?.setValuesColorPicker}
                                            checked={property.typeColor?.setValuesColorPicker}
                                            onChange={(value) => {
                                                updateProperty(index, "typeColor.setValuesColorPicker", value);
                                            }} />)}
                                        {(property.type === 'ColorPicker' && property.typeColor?.setValuesColorPicker) &&(<TextareaComponent
                                            label="Values for Color Picker"
                                            placeholder="label;value separated by new line"
                                            identifier="colorPickerValues"
                                            initialValue={property.typeColor?.colorPickerValues}
                                            onChange={(value) => {
                                                updateProperty(index, "typeColor.colorPickerValues", value);
                                            }}
                                        />)}
                                        <TextareaComponent
                                            label="Property description"
                                            placeholder="Property description"
                                            identifier="propertyDescription"
                                            initialValue={property.description}
                                            onChange={(value) => {
                                                updateProperty(index, "description", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isRequired"
                                            label="is required?"
                                            checked={property.isRequired}
                                            onChange={(value) => {
                                                updateProperty(index, "isRequired", value);
                                            }}
                                        />
                                        { isAdvancedOptionsVisible && (<CheckboxComponent
                                            identifier="isNullable"
                                            label="is nullable?"
                                            checked={property.isNullable}
                                            onChange={(value) => {
                                                updateProperty(index, "isNullable", value);
                                            }}
                                        /> )}
                                        { isAdvancedOptionsVisible && (<CheckboxComponent
                                            identifier="excludeField"
                                            label="is exclude field?"
                                            checked={property.excludeField}
                                            onChange={(value) => {
                                                updateProperty(index, "excludeField", value);
                                            }}
                                        /> )}
                                        { isAdvancedOptionsVisible && (<CheckboxComponent
                                            identifier="isl10nModeExlude"
                                            label="is l10n_mode = exclude?"
                                            checked={property.isl10nModeExlude}
                                            onChange={(value) => {
                                                updateProperty(index, "isl10nModeExlude", value);
                                            }}
                                        /> )}
                                        <div className="d-flex">
                                            <button
                                                className="btn btn-danger me-auto"
                                                onClick={() => {
                                                    removeProperty(index);
                                                }}
                                            >
                                                <FontAwesomeIcon icon={faTrash}/>
                                            </button>
                                            <button
                                                className="btn btn-info me-1"
                                                onClick={
                                                    () => moveProperty(index, -1)
                                                }
                                                disabled={index === 0}
                                            >
                                                <FontAwesomeIcon icon={faArrowUp}/>
                                            </button>
                                            <button
                                                className="btn btn-info"
                                                onClick={
                                                    () => moveProperty(index, 1)
                                                }
                                                disabled={index === properties.length - 1}
                                            >
                                                <FontAwesomeIcon icon={faArrowDown}/>
                                            </button>
                                        </div>
                                    </div>
                                </TYPO3StyledAccordion>
                            )
                        })
                    }
                    </TYPO3StyledAccordionGroup>
                </TYPO3StyledAccordion>
                <div className="d-flex align-items-center justify-content-between">
                    <h5>Relations</h5>
                    <button
                        className="btn btn-success mb-2 mt-2 btn-sm"
                        title="Add relation"
                        onClick={addEmptyRelation}
                    >
                        <FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus"/>
                    </button>
                </div>
                {
                    props.data.relations.map((relation, index) => {
                        return (
                            <div className="relation" key={relation.uid}>
{/*                                <pre>
                                    {JSON.stringify(index, null, 2)}
                                </pre>
                                <h4>relation</h4>
                                <pre>
                                    {JSON.stringify(relation, null, 2)}
                                </pre>*/}
                                <Handle
                                    type="source"
                                    /*id={`rel-${props.id}-${index}`}*/
                                    id={`rel-${props.id}-${relation.uid}`}
                                    position={Position.Left}
                                    onConnect={(params) => console.log('handle onConnect', params)}
                                    style={{
                                        background: 'rgb(255 135 0 / 33%)',
                                        border: '3px solid #ff8700',
                                        position: 'relative',
                                        top: '42px',
                                        left: '4px',
                                        zIndex: '1000'
                                    }}
                                />
                                <TYPO3StyledAccordion
                                    /*title={`uid: ${relation.uid} - id: ${props.id} - index: ${index} - name: ${relation.relationName} type: ${relation.relationType ? `(${relation.relationType})` : ''}`}*/
                                    title={`${relation.relationName} ${relation.relationType ? `(${relation.relationType})` : ''}`}
                                    id={`nodeRelation-${props.id}-${index}`}
                                    parentId="accordionCustomModelNodeRelations"
                                >
                                    <div className="custom-model-node__relation-wrapper">
                                        <InputComponent
                                            label="Relation name"
                                            placeholder="Relation name"
                                            initialValue={relation.relationName}
                                            identifier="relationName"
                                            onChange={(value) => {
                                                updateRelation(index, "relationName", value);
                                            }}
                                        />
                                        <SelectComponent
                                            label="Relation type"
                                            identifier="relationType"
                                            initialValue={relation.relationType}
                                            options={relationTypes}
                                            showEmptyValue={true}
                                            onChange={(value) => {
                                                updateRelation(index, "relationType", value);
                                            }}
                                        />
                                        <TextareaComponent
                                            placeholder="Description"
                                            label="Relation description"
                                            initialValue={relation.relationDescription}
                                            identifier="relationDescription"
                                            onChange={(value) => {
                                                updateRelation(index, "relationDescription", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isExcludeField"
                                            label="is exclude field?"
                                            checked={relation.isExcludeField}
                                            onChange={(value) => {
                                                updateRelation(index, "isExcludeField", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="lazyLoading"
                                            label="is lazy loading?"
                                            checked={relation.lazyLoading}
                                            onChange={(value) => {
                                                updateRelation(index, "lazyLoading", value);
                                            }}
                                        />
                                        <InputComponent
                                            label="Relation to external class"
                                            placeholder="Fully qualified class name"
                                            identifier="relationToExternalClass"
                                            initialValue={relation.relationToExternalClass}
                                            onChange={(value) => {
                                                updateRelation(index, "relationToExternalClass", value);
                                            }}
                                        />
                                        <div className="d-flex">
                                                <button
                                                    disabled={getIsRelationConnected(`rel-${props.id}-${relation.uid}`)}
                                                    className="btn btn-danger me-auto"
                                                    onClick={() => {
                                                        removeRelation(index, `rel-${props.id}-${relation.uid}`);
                                                    }}
                                                >
                                                    <FontAwesomeIcon icon={faTrash}/>
                                                </button>
                                                <button
                                                    className="btn btn-info me-1"
                                                    onClick={() => {
                                                        // moveRelation(index, -1)
                                                        console.log("move relation up", index);
                                                    }}
                                                    /*disabled={index === 0}*/
                                                    disabled
                                                >
                                                    <FontAwesomeIcon icon={faArrowUp}/>
                                                </button>
                                                <button
                                                    className="btn btn-info"
                                                    onClick={() => {
                                                        // moveRelation(index, 1)
                                                        console.log("move relation down", index);
                                                    }}
                                                    /*disabled={index === relations.length - 1}*/
                                                    disabled
                                                >
                                                    <FontAwesomeIcon icon={faArrowDown}/>
                                                </button>
                                        </div>

                                    </div>
                                </TYPO3StyledAccordion>
                            </div>
                        )
                    })
                }
            </TYPO3StyledAccordionGroup>
        </div>
    );
}
