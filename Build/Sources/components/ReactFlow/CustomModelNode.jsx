import { useEffect, useState} from 'react';
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


export const CustomModelNode = (props) => {
    const [properties, setProperties] = useState(props.data.properties);
    const [relations, setRelations] = useState(props.data.relations);
    const [customActions, setCustomActions] = useState([]);

    const addEmptyProperty = () => {
        setProperties([...properties, {
            name: '',
            type: '',
            description: '',
            isRequired: false,
            isNullable: false,
            isExcludeField: false,
            isl10nModeExlude: false,
        }]);
        props.data.properties.push(
            {
                name: '',
                type: '',
                description: '',
                isRequired: false,
                isNullable: false,
                isExcludeField: false,
                isl10nModeExlude: false,
            }
        );
    }

    // TODO: uuid should not be hard coded ???
    const addEmptyRelation = () => {
        setRelations([...relations, {
            "foreignRelationClass": "",
            "lazyLoading": true,
            "propertyIsExcludeField": true,
            "relationDescription": "",
            "relationName": "",
            "relationType": "",
            "relationWire": "",
            "renderType": "",
            "uid": "905857860343"
        }]);
        props.data.relations.push(
            {
                "foreignRelationClass": "",
                "lazyLoading": true,
                "propertyIsExcludeField": true,
                "relationDescription": "",
                "relationName": "",
                "relationType": "",
                "relationWire": "",
                "renderType": "",
                "uid": "905857860343"
            }
        );
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
        properties[index][property] = value;
        setProperties([...properties]);
        props.data.properties = properties;
    }

    const removeProperty = (propertyIndex) => {
        properties.splice(propertyIndex, 1);
        setProperties([...properties]);
        props.data.properties = properties;
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

    return (
        <div className="custom-model-node">
            <div className="drag-handle"></div>
            <div className="custom-model-node__header">
                <InputComponent
                    initialValue={props.data.label}
                    label="Node title"
                    placeholder="Node title"
                    identifier="nodeTitle"
                    validation={{ isRequired: true }}
                    onChange={(value) => {
                        updateNode('label', value);
                    }}
                />
                <button
                    className="btn btn-danger btn-sm btn-delete-node"
                    onClick={() => {
                        removeNode(props.id);
                    }}
                ><FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-trash" /></button>
                <Handle
                    type="target"
                    id={`customModelNode-${props.id}`}
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
                    <CheckboxComponent
                        identifier="enableSorting"
                        label="Enable sorting"
                        checked={props.data.enableSorting}
                        onChange={(value) => {
                            updateNode('enableSorting', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addDeletedField"
                        label="Add deleted field"
                        checked={props.data.addDeletedField}
                        onChange={(value) => {
                            updateNode('addDeletedField', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addHiddenField"
                        label="Add hidden field"
                        checked={props.data.addHiddenField}
                        onChange={(value) => {
                            updateNode('addHiddenField', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addStarttimeEndtimeFields"
                        label="Add starttime/endtime fields"
                        checked={props.data.addStarttimeEndtimeFields}
                        onChange={(value) => {
                            updateNode('addStarttimeEndtimeFields', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="enableCategorization"
                        label="Enable categorization"
                        checked={props.data.enableCategorization}
                        onChange={(value) => {
                            updateNode('enableCategorization', value);
                        }}
                    />
                    <TextareaComponent
                        placeholder="Description"
                        identifier="description"
                        label="Description"
                        initialValue={props.data.description}
                        onChange={(value) => {
                            updateNode('description', value);
                        }}
                    />
                    <InputComponent
                        label="Map to existing table:"
                        placeholder="tablename"
                        identifier="mapToExistingTable"
                        initialValue={props.data.mapToExistingTable}
                        onChange={(value) => {
                            updateNode('mapToExistingTable', value);
                        }}
                    />
                    <InputComponent
                        label="Extend existing model class:"
                        placeholder="\Fully\Qualified\Classname"
                        identifier="extendExistingModelClass"
                        initialValue={props.data.parentClass}
                        onChange={(value) => {
                            updateNode('parentClass', value);
                        }}
                    />
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
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="text-primary">Properties</h5>
                        <button className="btn btn-success mb-2 mt-2 btn-sm" title="Add property"
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
                                                updateProperty(index, "name", value);
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
                                        {property.type === 'Select' &&(<TextareaComponent
                                            label="Values for Select-Box"
                                            placeholder="label;value separated by new line"
                                            identifier="selectboxValues"
                                            initialValue={property.selectboxValues}
                                            onChange={(value) => {
                                                updateProperty(index, "selectboxValues", value);
                                            }}
                                        />)}
                                        {property.type === 'Select' &&(<SelectComponent
                                            label="Render Type"
                                            identifier="renderType"
                                            options={['selectSingle','selectSingleBox','selectCheckBox','selectMultipleSideBySide']}
                                            initialValue={property.renderType}
                                            onChange={(value) => {
                                                updateProperty(index, "renderType", value);
                                            }}
                                        />)}
                                        {property.type === 'Select' &&<InputComponent
                                            label="Foreign table (will override values)"
                                            placeholder="Foreign table"
                                            identifier="foreignTable"
                                            initialValue={property.foreignTable}
                                            onChange={(value) => {
                                                updateProperty(index, "foreignTable", value);
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
                                        {property.type === 'Select' &&(<div className="d-flex">
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
                                        <CheckboxComponent
                                            identifier="isNullable"
                                            label="is nullable?"
                                            checked={property.isNullable}
                                            onChange={(value) => {
                                                updateProperty(index, "isNullable", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="propertyIsExcludeField"
                                            label="is exclude field?"
                                            checked={property.propertyIsExcludeField}
                                            onChange={(value) => {
                                                updateProperty(index, "propertyIsExcludeField", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isl10nModeExlude"
                                            label="is l10n_mode = exclude?"
                                            checked={property.isl10nModeExlude}
                                            onChange={(value) => {
                                                updateProperty(index, "isl10nModeExlude", value);
                                            }}
                                        />
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
                            <div className="relation">
                                <Handle
                                    type="source"
                                    id={`relation-${props.id}-${index}`}
                                    position={Position.Left}
                                    onConnect={(params) => console.log('handle onConnect', params)}
                                    style={{
                                        background: 'rgb(255 135 0 / 33%)',
                                        border: '3px solid #ff8700',
                                        position: 'relative',
                                        top: '42px',
                                        zIndex: '1000'
                                    }}
                                />
                                <TYPO3StyledAccordion
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
                                            options={relationTypes}
                                            showEmptyValue={false}
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
                                    </div>
                                </TYPO3StyledAccordion>
                            </div>
                        )})
                }
            </TYPO3StyledAccordionGroup>
        </div>
    );
}
