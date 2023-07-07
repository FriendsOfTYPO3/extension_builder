import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SingleAuthorComponent} from "../SingleComponents/SingleAuthorComponent";


export const DebugOutputAccordion = (props) => {
    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title" id="heading-panel-debug">
                        <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-debug" aria-expanded="true" aria-controls="panel-debug">
                            <span className="caret"></span>
                            <strong>Debug output</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-debug" className="accordion-collapse collapse" aria-labelledby="heading-panel-debug"
                     data-bs-parent="#accordion-left-panel-debug">
                    <div className="panel-body">
                        <pre>
                            {JSON.stringify(props, null, 2)}
                        </pre>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
