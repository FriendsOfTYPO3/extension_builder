import { useEffect, useState} from 'react';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {TYPO3StyledAccordion} from "../accordions/TYPO3StyledAccordion";
import {TYPO3StyledAccordionGroup} from "../accordions/TYPO3StyledAccordionGroup";
import InputComponent from "../forms/input/InputComponent";
import CheckboxComponent from "../forms/input/CheckboxComponent";
import TextareaComponent from "../forms/textarea/TextareaComponent";
import SelectComponent from "../forms/select/SelectComponent";
import { Handle, Position } from 'reactflow';

export const CustomModelNode = (props) => {
    const [properties, setProperties] = useState([]);
    const [relations, setRelations] = useState([]);
    const [customActions, setCustomActions] = useState([]);

    const propertyTypes = [
        "String",
        "Text",
        "RichText",
        "Slug",
        "ColorPicker",
        "Password",
        "Email",
        "Integer",
        "Float" ,
        "Boolean",
        "InputLink" ,
        "Date" ,
        "DateTime" ,
        "NativeDate",
        "NativeDateTime" ,
        "NativeTime",
        "Time",
        "TimeSec",
        "Select" ,
        "File",
        "Image" ,
        "PassThrough" ,
        "None"
    ];

    const relationTypes = [
        'zeroToOne',
        'zeroToMany',
        'manyToOne',
        'manyToMany'
    ];

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

    return (
        <div className="custom-model-node">
            <div className="drag-handle"></div>
            <div className="custom-model-node__header">
                <InputComponent
                    label="Node title"
                    placeholder="Node title"
                    identifier="nodeTitle"
                    validation={{ isRequired: true }}
                    onChange={(value) => {
                        updateNode('label', value);
                    }}
                />
                <Handle
                    type="target"
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
                            updateNode('objectsettings.aggregateRoot', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="enableSorting"
                        label="Enable sorting"
                        checked={props.data.enableSorting}
                        onChange={(value) => {
                            updateNode('objectsettings.sorting', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addDeletedField"
                        label="Add deleted field"
                        checked={props.data.addDeletedField}
                        onChange={(value) => {
                            updateNode('objectsettings.addDeletedField', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addHiddenField"
                        label="Add hidden field"
                        checked={props.data.addHiddenField}
                        onChange={(value) => {
                            updateNode('objectsettings.addHiddenField', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="addStarttimeEndtimeFields"
                        label="Add starttime/endtime fields"
                        checked={props.data.addStarttimeEndtimeFields}
                        onChange={(value) => {
                            updateNode('objectsettings.addStarttimeEndtimeFields', value);
                        }}
                    />
                    <CheckboxComponent
                        identifier="enableCategorization"
                        label="Enable categorization"
                        checked={props.data.enableCategorization}
                        onChange={(value) => {
                            updateNode('objectsettings.categorizable', value);
                        }}
                    />
                    <TextareaComponent
                        placeholder="Description"
                        identifier="description"
                        label="Description"
                        onChange={(value) => {
                            updateNode('objectsettings.description', value);
                        }}
                    />
                    <InputComponent
                        label="Map to existing table:"
                        placeholder="tablename"
                        identifier="mapToExistingTable"
                        onChange={(value) => {
                            updateNode('objectsettings.mapToTable', value);
                        }}
                    />
                    <InputComponent
                        label="Extend existing model class:"
                        placeholder="\Fully\Qualified\Classname"
                        identifier="extendExistingModelClass"
                        onChange={(value) => {
                            updateNode('objectsettings.parentClass', value);
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
                        customActions.map((action, index) => {
                            return (
                                <div className="custom-model-node__action-wrapper">
                                    <InputComponent
                                        label="Action name"
                                        placeholder="Action name"
                                        identifier="actionName"
                                        onChange={(value) => {
                                            updateCustomAction(index, value);
                                        }}
                                    />
                                </div>
                            )
                        })
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
                        properties.map((property, index) => {
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
                                            onChange={(value) => {
                                                updateProperty(index, "name", value);
                                            }}
                                        />
                                        <SelectComponent
                                            label="Property type"
                                            identifier="propertyType"
                                            options={propertyTypes}
                                            onChange={(value) => {
                                                updateProperty(index, "type", value);
                                            }}
                                        />
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
                                            initialChecked={false}
                                            onChange={(value) => {
                                                updateProperty(index, "isRequired", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isNullable"
                                            label="is nullable?"
                                            initialChecked={false}
                                            onChange={(value) => {
                                               updateProperty(index, "isNullable", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="propertyIsExcludeField"
                                            label="is exclude field?"
                                            initialChecked={false}
                                            onChange={(value) => {
                                                updateProperty(index, "propertyIsExcludeField", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isl10nModeExlude"
                                            label="is l10n_mode = exclude?"
                                            initialChecked={false}
                                            onChange={(value) => {
                                                updateProperty(index, "isl10nModeExlude", value);
                                            }}
                                        />
                                    </div>
                                </TYPO3StyledAccordion>
                            )
                        })
                    }
                    </TYPO3StyledAccordionGroup>
                </TYPO3StyledAccordion>
                <TYPO3StyledAccordion  title="Relations" id={`accordionItemCustomModelNode-relations-${props.id}`} parentId={`accordionCustomModelNode-${props.id}`}>
                    <div className="d-flex justify-content-between align-items-center">
                        <h5 className="text-primary">Relations</h5>
                        <button
                            className="btn btn-success mb-2 mt-2 btn-sm"
                            title="Add relation"
                            onClick={addEmptyRelation}
                        >
                            <FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" />
                        </button>
                    </div>
                    <TYPO3StyledAccordionGroup
                        id="accordionCustomModelNodeRelations"
                    >
                    {
                        relations.map((relation, index) => {
                            return (
                                <TYPO3StyledAccordion
                                    title={`${relation.relationName} ${relation.relationType ? `(${relation.relationType})` : ''}`}
                                    id={`nodeRelation-${props.id}-${index}`}
                                    parentId="accordionCustomModelNodeRelations"
                                >
                                    <div className="custom-model-node__relation-wrapper">
                                        <InputComponent
                                            label="Relation name"
                                            placeholder="Relation name"
                                            identifier="relationName"
                                            onChange={(value) => {
                                                updateRelation(index, "relationName", value);
                                            }}
                                        />
                                        <SelectComponent
                                            label="Relation type"
                                            identifier="relationType"
                                            options={relationTypes}
                                            onChange={(value) => {
                                                updateRelation(index, "relationType", value);
                                            }}
                                        />
                                        <TextareaComponent
                                            placeholder="Description"
                                            label="Relation description"
                                            identifier="relationDescription"
                                            onChange={(value) => {
                                                updateRelation(index, "relationDescription", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="isExcludeField"
                                            label="is exclude field?"
                                            initialChecked={false}
                                            onChange={(value) => {
                                                updateRelation(index, "isExcludeField", value);
                                            }}
                                        />
                                        <CheckboxComponent
                                            identifier="lazyLoading"
                                            label="is lazy loading?"
                                            initialChecked={false}
                                            onChange={(value) => {
                                                updateRelation(index, "lazyLoading", value);
                                            }}
                                        />
                                        <InputComponent
                                            label="Relation to external class"
                                            placeholder="Fully qualified class name"
                                            identifier="relationToExternalClass"
                                            onChange={(value) => {
                                                updateRelation(index, "relationToExternalClass", value);
                                            }}
                                        />
                                        <Handle
                                            type="source"
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
                                </TYPO3StyledAccordion>
                            )})
                        }
                    </TYPO3StyledAccordionGroup>
                </TYPO3StyledAccordion>
                <TYPO3StyledAccordion  title="Debug" id={`accordionItemCustomModelNode-debug-${props.id}`} parentId={`accordionCustomModelNode-${props.id}`} >
                    <div className="mb-5">
                        <pre>
                            {JSON.stringify(props, null, 2)}
                        </pre>>
                        <pre>
                            {JSON.stringify(properties, null, 2)}
                        </pre>
                    </div>
                </TYPO3StyledAccordion>
            </TYPO3StyledAccordionGroup>
        </div>
    );
}
