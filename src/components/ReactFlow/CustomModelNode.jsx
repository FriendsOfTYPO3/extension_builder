import { useEffect, useState} from 'react';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";

export const CustomModelNode = (props) => {
    const [properties, setProperties] = useState([]);
    const [customActions, setCustomActions] = useState([]);

    const propertyTypes = [
        { name: "String", value : "string" },
        { name: "Text", value : "text" },
        { name: "Rich Text*", value : "richtext" },
        { name: "Slug", value : "slug" },
        { name: "Color picker", value : "colorpicker" },
        { name: "Password", value : "password" },
        { name: "Email", value : "email" },
        { name: "Integer", value : "integer" },
        { name: "Floating point", value : "floatingpoint" },
        { name: "Boolean", value : "boolean" },
        { name: "Link", value : "link" },
        { name: "Date", value : "date" },
        { name: "DateTime", value : "datetime" },
        { name: "Date (timestamp)", value : "date_timestamp" },
        { name: "DateTime (timestamp)", value : "datetime_timestamp" },
        { name: "Time*", value : "time" },
        { name: "Time (timestamp)", value : "time_timestamp" },
        { name: "Time/Sec", value : "timesec" },
        { name: "Select list", value : "selectlist" },
        { name: "File*", value : "file" },
        { name: "Image*", value : "image" },
        { name: "Pass through", value : "passthrough" },
        { name: "None", value : "none" },
    ];

    const popoverText = {
        'objectType': 'There is another object type called ValueObject, please refer to the documentation for more information.',
    }

    const addEmptyProperty = () => {
        setProperties([...properties, {
            name: '',
            type: '',
            description: '',
            isRequired: false,
            isNullable: false,
            isExcdeField: false,
            isl10nModeExlude: false,
        }]);
    }

    const addEmptyAction = () => {
        setCustomActions([...customActions, {
            name: ''
        }]);

        // add empty array entry to props.data.customActions
        props.data.customActions.push({
            name: ''
        });
    }

    // useEffect(() => {
    //     const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    //     // eslint-disable-next-line no-unused-vars
    //     const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new Popover(popoverTriggerEl, {
//
    //     }));
    // }, []);

    return (
        <div className="custom-model-node">
            <div className="drag-handle"></div>
            <div className="custom-model-node__header">
                <input type="text" name="nodeTitle" placeholder={props.data.label}
                    onChange={(e) => {
                      props.data.label = e.target.value;
                    }}
                />
            </div>
            <div className="accordion" id="accordionCustomModelNode">
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingOne">
                        <button className="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Domain object settings
                        </button>
                    </h2>
                    <div id="collapseOne" className="accordion-collapse collapse show" aria-labelledby="headingOne"
                         data-bs-parent="#accordionCustomModelNode">
                        <div className="accordion-body">
                            <div>
                               {/* <label htmlFor="objectType" title="There is another type 'Value object'. Please refer to the documentation for this object type.">Object type: Entity&nbsp;
                                    <span
                                        style={{cursor: "pointer"}}
                                        data-bs-container="body"
                                            data-bs-toggle="popover" data-bs-placement="top"
                                            data-bs-content={popoverText.objectType}>
                                        <FontAwesomeIcon icon="fa-solid fa-circle-info" />
                                    </span>
                                </label>*/}
                                {/*<select name="objectType" id="objectType" className="nodrag"
                                    onChange={(e) => {
                                        props.data.objectType = e.target.value;
                                    }}
                                >
                                    {objectTypes.map((objectType, index) => <option key={index} value={objectType}>{objectType}</option>)}
                                </select>*/}
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="isAggregateRoot">Is aggregate root:</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.isAggregateRoot}
                                    id="isAggregateRoot"
                                    name="isAggregateRoot"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.isAggregateRoot = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="enableSorting">Enable sorting</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.enableSorting}
                                    id="enableSorting"
                                    name="enableSorting"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.enableSorting = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="addDeletedField">Add deleted field</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.addDeletedField}
                                    id="addDeletedField"
                                    name="addDeletedField"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.addDeletedField = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="addHiddenField">Add hidden field</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.addHiddenField}
                                    id="addHiddenField"
                                    name="addHiddenField"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.addHiddenField = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="addStarttimeEndtimeFields">Add starttime/endtime fields</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.addStarttimeEndtimeFields}
                                    id="addStarttimeEndtimeFields"
                                    name="addStarttimeEndtimeFields"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.addStarttimeEndtimeFields = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="enableCategorization">Enable categorization</label>
                                <input
                                    type="checkbox"
                                    checked={props.data.enableCategorization}
                                    id="enableCategorization"
                                    name="enableCategorization"
                                    className="nodrag"
                                    onChange={(e) => {
                                        props.data.enableCategorization = e.target.checked;
                                    }}
                                />
                            </div>
                            <div>
                                <label htmlFor="description">Description:</label>
                                <textarea rows="3" id="description" name="description" className="nodrag" placeholder="Description"
                                    onChange={(e) => {
                                        props.data.description = e.target.value;
                                    }}
                                />
                            </div>
                            <div>
                                <label htmlFor="mapToExistingTable">Map to existing table:</label>
                                <input type="text" id="mapToExistingTable" name="mapToExistingTable" className="nodrag"
                                    onChange={(e) => {
                                        props.data.mapToExistingTable = e.target.value;
                                    }}
                                />
                            </div>
                            <div>
                                <label htmlFor="extendExistingModelClass">Extend existing model class:</label>
                                <input type="text" id="extendExistingModelClass" name="extendExistingModelClass" className="nodrag" placeholder="\Fully\Qualified\Classname"
                                    onChange={(e) => {
                                        props.data.extendExistingModelClass = e.target.value;
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingTwo">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Actions
                        </button>
                    </h2>
                    <div id="collapseTwo" className="accordion-collapse collapse" aria-labelledby="headingTwo"
                         data-bs-parent="#accordionCustomModelNode">
                        <div className="accordion-body">
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionIndex">index</label>
                                <input type="checkbox" id="actionIndex" name="actionIndex" className="nodrag"
                                    onChange={(e) => {
                                        props.data.actions.actionIndex = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionList">list</label>
                                <input type="checkbox" id="actionList" name="actionList" className="nodrag"
                                    onChange={(e) => {
                                        props.data.actions.actionList = e.target.checked;
                                    }}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionShow">show</label>
                                <input type="checkbox" id="actionShow" name="actionShow" className="nodrag"
                                    onChange={
                                        (e) => {
                                            props.data.actions.actionShow = e.target.checked;
                                        }
                                    }
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionNewCreate">new / create</label>
                                <input type="checkbox" id="actionNewCreate" name="actionNewCreate" className="nodrag"
                                    onChange={(e) => {props.data.actions.actionNewCreate = e.target.checked;}}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionEditUpdate">edit / update</label>
                                <input type="checkbox" id="actionEditUpdate" name="actionEditUpdate" className="nodrag"
                                    onChange={(e) => {props.data.actions.actionEditUpdate = e.target.checked;}}
                                />
                            </div>
                            <div className="d-flex justify-content-between">
                                <label htmlFor="actionDelete">actionDelete</label>
                                <input type="checkbox" id="isAggregateRoot" name="isAggregateRoot" className="nodrag"
                                    onChange={(e) => {props.data.actions.actionDelete = e.target.checked;}}
                                />
                            </div>
                            <div className="d-flex justify-content-between align-items-center mt-2">
                                <span className="text-primary">Custom actions</span>
                                <button className="btn btn-outline btn-sm p-0 text-primary"
                                        title="Add action"
                                        onClick={addEmptyAction}
                                ><FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" /></button>
                            </div>
                            {
                                customActions.map((action) => {
                                    return (
                                        <div className="custom-model-node__action-wrapper">
                                            <input type="text" name="actionName" placeholder="Action name" />
                                        </div>
                                    )
                                })
                            }
                        </div>
                    </div>
                </div>
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingThree">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Properties
                        </button>
                    </h2>
                    <div id="collapseThree" className="accordion-collapse collapse" aria-labelledby="headingThree"
                         data-bs-parent="#accordionCustomModelNode">
                        <div className="accordion-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <h5 className="text-primary">Properties</h5>
                                <button className="btn btn-success mb-2 mt-2 btn-sm" title="Add property"
                                        onClick={addEmptyProperty}
                                >
                                    <FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" />
                                </button>
                            </div>
                            {
                                properties.map((property) => {
                                    return (
                                        <div className="custom-model-node__property-wrapper">
                                            <div className="d-flex justify-content-between">
                                                <input type="text" name="propertyName" placeholder="Property name" />
                                            </div>
                                            <select name="propertyType" id="propertyType" className="nodrag" >
                                                {propertyTypes.map((propertyType, index) => <option key={index} value={propertyType.value}>{propertyType.name}</option>)}
                                            </select>
                                            <input type="text" name="propertyDescription" placeholder="Property description" />
                                            <div className="d-flex justify-content-between">
                                                <label htmlFor="isRequired">is required?</label>
                                                <input type="checkbox" id="isRequired" name="isRequired" className="nodrag" />
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <label htmlFor="isNullable">is nullable?</label>
                                                <input type="checkbox" id="isNullable" name="isNullable" className="nodrag" />
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <label htmlFor="isExcdeField">is exclude field?</label>
                                                <input
                                                    type="checkbox"
                                                    id="isExcdeField"
                                                    name="isExcdeField"
                                                    className="nodrag" />
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <label htmlFor="isl10nModeExlude">is l10n_mode = exclude</label>
                                                <input type="checkbox" id="isl10nModeExlude" name="isl10nModeExlude" className="nodrag" />
                                            </div>
                                            <hr />
                                        </div>
                                    )
                                })
                            }
                        </div>
                    </div>
                </div>
                <div className="accordion-item">
                    <h2 className="accordion-header" id="headingFour">
                        <button className="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Relations
                        </button>
                    </h2>
                    <div id="collapseFour" className="accordion-collapse collapse" aria-labelledby="headingFour"
                         data-bs-parent="#accordionCustomModelNode">
                        <div className="accordion-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <h5 className="text-primary">Relations</h5>
                                <button className="btn btn-success mb-2 mt-2 btn-sm" title="Add relation" disabled>
                                    <FontAwesomeIcon className="font-awesome-icon" icon="fa-solid fa-plus" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
