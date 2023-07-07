import { Fragment, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowUp, faArrowDown, faTrash } from '@fortawesome/free-solid-svg-icons'

export const SinglePluginComponent = (props) => {

    const updatePluginHandler = (field, value) => {
       props.updatePluginHandler(props.plugin.id, field, value);
    };

    return (
        <Fragment>
            <div className="mb-5">
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-puzzle-piece" /></span>Plugin Name</label>
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Plugin Name"
                        aria-label="Plugin Name"
                        aria-describedby="basic-addon1"
                        value={props.plugin.name}
                        onChange={(e) => {
                            updatePluginHandler('name', e.target.value);
                        }}
                    />
                </div>
                <div className="mb-3">
                    <label htmlFor="inputGroupSelect01"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Plugin key</label>
                    <input
                        type="text"
                        className="form-control"
                        placeholder="Plugin key"
                        aria-label="Plugin key"
                        aria-describedby="basic-addon1"
                        value={props.plugin.key}
                        onChange={(e) => {
                            updatePluginHandler('key', e.target.value);
                        }}
                    />
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Description</label>
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Please insert a description"
                        value={props.description}
                        onChange={(e) => {
                            updatePluginHandler('description', e.target.value);
                        }}
                        rows="5" />
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Cachable controller actions</label>
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Blog => list, show"
                        value={props.plugin.controllerActionsCachable}
                        onChange={(e) => {
                            updatePluginHandler('actions.controllerActionCombinations', e.target.value);
                        }}
                        rows="5" />
                </div>
                <div className="mb-3">
                    <label htmlFor="exampleFormControlTextarea1" className="form-label"><span className="me-2"><FontAwesomeIcon icon="fa-solid fa-key" /></span>Non cachable controller actions</label>
                    <textarea
                        type="text"
                        className="form-control"
                        id="exampleFormControlTextarea1"
                        placeholder="Blog => edit, update, delete"
                        value={props.plugin.controllerActionsNonCachable}
                        onChange={(e) => {
                            updatePluginHandler('actions.noncacheableActions', e.target.value);
                        }}
                        rows="5" />
                </div>
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
