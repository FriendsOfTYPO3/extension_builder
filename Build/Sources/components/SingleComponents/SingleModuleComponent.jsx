import { Fragment } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'

export const SingleModuleComponent = (props) => {

    const updateModuleHandler = (field, value) => {
        props.updateModuleHandler(props.module.id, field, value);
    };

    const mainModules = [
        "web",
        "site",
        "file",
        "user",
        "tools",
        "system",
        "help"
    ];

    return (
        <Fragment>
            <div className="mb-5">
                <div className="mb-2 input-group">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Module Name"
                        aria-label="Module Name"
                        aria-describedby="basic-addon1"
                        value={props.module.name}
                        onChange={(e) => {
                            updateModuleHandler('name', e.target.value);
                        }}
                    />
                    <span className="input-group-text" id="basic-addon1">Name</span>
                </div>
                <div className="mb-2 input-group">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Module key"
                        aria-label="Module key"
                        aria-describedby="basic-addon1"
                        value={props.module.key}
                        onChange={(e) => {
                            updateModuleHandler('key', e.target.value);
                        }}
                    />
                    <span className="input-group-text" id="basic-addon1">Key</span>
                </div>
                <div className="mb-2 input-group">
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Please enter the description for this module"
                        value={props.module.description}
                        onChange={(e) => {
                            updateModuleHandler('description', e.target.value);
                        }}
                        rows="5" />
                    <span className="input-group-text" id="basic-addon1">Description</span>
                </div>
                <div className="mb-2 input-group">
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Module tab label"
                        aria-label="Module tab label"
                        aria-describedby="basic-addon1"
                        value={props.module.tabLabel}
                        onChange={(e) => {
                            updateModuleHandler('tabLabel', e.target.value);
                        }}
                    />
                    <span className="input-group-text" id="basic-addon1">Label</span>
                </div>
                <div className="mb-2 input-group">
                    <select
                        className="form-select"
                        aria-label="Mail module"
                        onChange={(e) => {
                            updateModuleHandler('mainModule', e.target.value);
                        }}
                    >
                        <option>Please choose the main module</option>
                        {
                            mainModules.map((module, index) => {
                                return (
                                    <option key={index} value={module}>{module}</option>
                                )
                            })
                        }
                    </select>
                    <span className="input-group-text" id="basic-addon1">Main module</span>
                </div>
                <div className="mb-2 input-group">
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Blog => edit, update, delete"
                        value={props.module.controllerActionsCachable}
                        onChange={(e) => {
                            updateModuleHandler('actions.controllerActionsCachable', e.target.value);
                        }}
                        rows="5" />
                    <span className="input-group-text" id="basic-addon1">Cachable controller actions</span>
                </div>
                <div className="d-flex module-actions">
                    <button
                        className="btn btn-danger me-auto"
                        onClick={() => {
                            props.removeModuleHandler(props.module.id);
                        }}
                    >
                        <FontAwesomeIcon icon={faTrash} />
                    </button>
                    <button
                        className="btn btn-info me-1"
                        onClick={() => props.moveModule(props.index, -1)}
                        disabled={props.index === 0}
                    >
                        <FontAwesomeIcon icon={faArrowUp} />
                    </button>
                    <button
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
