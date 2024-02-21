import { Fragment } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'
import React from "react";
import SelectComponent from "../forms/select/SelectComponent";
import TextareaComponent from "../forms/textarea/TextareaComponent";
import InputComponent from "../forms/input/InputComponent";
import {mainModules} from "../../initialValues/module";

export const SingleModuleComponent = (props) => {

    const updateModuleHandler = (field, value) => {
        props.updateModuleHandler(props.index, field, value);
    };

    return (
        <Fragment>
            <div className="mb-5">
                <InputComponent
                    label="Name"
                    type="text"
                    identifier={`name-${props.index}`}
                    initialValue={props.module.name}
                    onChange={(value) => {
                        updateModuleHandler('name', value);
                    }}
                />
                <InputComponent
                    label="Key"
                    type="text"
                    identifier={`key-${props.index}`}
                    initialValue={props.module.key}
                    onChange={(value) => {
                        updateModuleHandler('key', value);
                    }}
                />
                <TextareaComponent
                    label="Description"
                    identifier={`description-${props.index}`}
                    initialValue={props.module.description}
                    onChange={(value) => {
                        updateModuleHandler('description', value);
                    }}
                    placeholder="Description"
                />
                <InputComponent
                    label="Label"
                    type="text"
                    identifier={`tabLabel-${props.index}`}
                    initialValue={props.module.tabLabel}
                    onChange={(value) => {
                        updateModuleHandler('tabLabel', value);
                    }}
                />
                <SelectComponent
                    label="Main module"
                    initialValue={props.module.mainModule}
                    identifier={`mainModule-${props.index}`}
                    options={mainModules}
                    defaultValue="Please choose the main module"
                    onChange={(value) => {
                        updateModuleHandler('mainModule', value);
                    }}
                />
                <TextareaComponent
                    placeholder="Blog => edit, update, delete"
                    label="Cachable controller actions"
                    identifier={`cachableControllerActions-${props.index}`}
                    initialValue={props.module.actions.controllerActionCombinations}
                    onChange={(value) => {
                        updateModuleHandler('actions.controllerActionCombinations', value);
                    }}
                />
                <div className="d-flex module-actions">
                    <button
                        role="button"
                        aria-label="Trash"
                        className="btn btn-danger me-auto"
                        onClick={() => {
                            props.removeModuleHandler(props.index);
                        }}
                    >
                        <FontAwesomeIcon icon={faTrash} />
                    </button>
                    <button
                        role="button"
                        aria-label="ArrowUp"
                        className="btn btn-info me-1"
                        onClick={() => props.moveModule(props.index, -1)}
                        disabled={props.index === 0}
                    >
                        <FontAwesomeIcon icon={faArrowUp} />
                    </button>
                    <button
                        role="button"
                        aria-label="ArrowDown"
                        className="btn btn-info"
                        onClick={() => props.moveModule(props.index, 1)}
                        disabled={props.index === props.modules.length - 1}
                    >
                        <FontAwesomeIcon icon={faArrowDown} />
                    </button>
                </div>
            </div>
        </Fragment>
    );
};
