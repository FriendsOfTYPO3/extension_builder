import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SingleModuleComponent} from "../SingleComponents/SingleModuleComponent";
import {SingleAuthorComponent} from "../SingleComponents/SingleAuthorComponent";


export const ModulesListAccordion = (props) => {
    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title" id="heading-panel-modules">
                        <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-modules" aria-expanded="true" aria-controls="panel-modules">
                            <span className="caret"></span>
                            <strong>Backend modules</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-modules" className="accordion-collapse collapse" aria-labelledby="heading-panel-modules"
                     data-bs-parent="#accordion-left-panel">
                    <div className="panel-body">
                        <ul>
                            {
                                props.modules.map((module, index) => {
                                    return (
                                        <Fragment key={index}>
                                            <SingleModuleComponent
                                                module={module}
                                                index={index}
                                                {...props}
                                            />
                                        </Fragment>
                                    )
                                })
                            }
                        </ul>
                        <button
                            className="btn btn-success w-100"
                            onClick={props.addModulesHandler}>
                            <FontAwesomeIcon className="me-1" icon="fa-solid fa-plus" />
                            Add new module
                        </button>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
