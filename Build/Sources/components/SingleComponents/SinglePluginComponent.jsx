import { Fragment, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'
import InputComponent from "../forms/input/InputComponent";
import TextareaComponent from "../forms/textarea/TextareaComponent";

export const SinglePluginComponent = (props) => {

    const updatePluginHandler = (field, value) => {
       props.updatePluginHandler(props.plugin.id, field, value);
    };

    return (
        <Fragment>
            <div className="mb-5">
                <InputComponent
                    label="Plugin Name"
                    type="text"
                    identifier="name"
                    validation={{ isRequired: true, minLength: 2 }}
                    initialValue={props.plugin.name}
                    onChange={(value) => {
                        updatePluginHandler('name', value);
                    }}
                />
                <InputComponent
                    label="Plugin Key"
                    type="text"
                    identifier="key"
                    initialValue={props.key}
                    onChange={(value) => {
                        updatePluginHandler('key', value);
                    }}
                />
                <TextareaComponent
                    placeholder="Please insert a description"
                    label="Description"
                    identifier="description"
                    initialValue={props.description}
                    onChange={(value) => {
                        updatePluginHandler('description', value);
                    }}
                />
                <TextareaComponent
                    placeholder="Blog => list, show"
                    label="Cachable controller actions"
                    identifier="controllerActionsCachable"
                    initialValue={props.controllerActionsCachable}
                    onChange={(value) => {
                        updatePluginHandler('controllerActionsCachable', value);
                    }}
                />
                <TextareaComponent
                    placeholder="Blog => edit, update, delete"
                    label="Non cachable controller actions"
                    identifier="controllerActionsNonCachable"
                    initialValue={props.controllerActionsNonCachable}
                    onChange={(value) => {
                        updatePluginHandler('controllerActionsNonCachable', value);
                    }}
                />
                <div className="d-flex author-actions">
                    <button
                        className="btn btn-danger me-auto"
                        onClick={() => {
                            props.removePluginHandler(props.plugin.id);
                        }}
                    >
                        <FontAwesomeIcon icon={faTrash} />
                    </button>
                    <button
                        className="btn btn-info me-1"
                        onClick={() => props.movePlugin(props.index, -1)}
                        disabled={props.index === 0}
                    >
                        <FontAwesomeIcon icon={faArrowUp} />
                    </button>
                    <button
                        className="btn btn-info"
                        onClick={() => props.movePlugin(props.index, 1)}
                        disabled={props.index === props.plugins.length - 1}
                    >

                        <FontAwesomeIcon icon={faArrowDown} />
                    </button>
                </div>
            </div>
        </Fragment>
    );
};
