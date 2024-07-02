import { Fragment, memo } from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { SingleModuleComponent } from "../SingleComponents/SingleModuleComponent";

export const ModulesListAccordion = memo((props) => {
    return (
        <div className="panel panel-default">
            <div className="panel-heading">
                <h3 className="fs-3 panel-title" id="heading-panel-modules">
                    <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-modules"
                       aria-expanded="true" aria-controls="panel-modules">
                        <span className="caret"></span>
                        <strong>Backend modules</strong>
                    </a>
                </h3>
            </div>
            <div id="panel-modules" className="accordion-collapse collapse" aria-labelledby="heading-panel-modules"
                 data-bs-parent="#accordion-left-panel">
                <div className="panel-body py-2">
                    <ul>
                        {
                            props.modules.map((module, index) => (
                                <SingleModuleComponent
                                    key={index}
                                    module={module}
                                    index={index}
                                    {...props}
                                />
                            ))
                        }
                    </ul>
                    {props.modules.length === 0 &&
                        <div className="alert alert-danger" role="alert">No modules yet</div>}
                    <button
                        className="fs-3 btn btn-success w-100"
                        onClick={props.addModulesHandler}>
                        <FontAwesomeIcon className="me-1" icon={['fas', 'plus']}/>
                        Add new module
                    </button>
                </div>
            </div>
        </div>
    )
})
