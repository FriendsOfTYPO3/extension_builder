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
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Module Name</label>
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
                </div>
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Module key</label>
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
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Module Description</label>
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
                </div>
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Module tab label</label>
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
                </div>
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Main Module</label>
                    <select
                        className="form-select"
                        aria-label="Role"
                        onChange={(e) => {
                            updateModuleHandler('role', e.target.value);
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
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-signature" /></span>Cachable controller actions</label>
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Blog => edit, update, delete"
                        value={props.module.controllerActionsCachable}
                        onChange={(e) => {
                            updateModuleHandler('controllerActionsCachable', e.target.value);
                        }}
                        rows="5" />
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
