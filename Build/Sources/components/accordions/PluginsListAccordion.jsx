import {Fragment} from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import {SinglePluginComponent} from "../SingleComponents/SinglePluginComponent";
import {SingleAuthorComponent} from "../SingleComponents/SingleAuthorComponent";


export const PluginsListAccordion = (props) => {
    return (
        <Fragment>
            <div className="panel panel-default">
                <div className="panel-heading">
                    <h3 className="panel-title" id="heading-panel-plugins">
                        <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-plugins" aria-expanded="true" aria-controls="panel-plugins">
                            <span className="caret"></span>
                            <strong>Frontend plugins</strong>
                        </a>
                    </h3>
                </div>
                <div id="panel-plugins" className="accordion-collapse collapse" aria-labelledby="heading-panel-plugins"
                     data-bs-parent="#accordion-left-panel">
                    <div className="panel-body">
                        <ul>
                            {
                                props.plugins.map((plugin, index) => {
                                    return (
                                        <Fragment key={index}>
                                            <SinglePluginComponent
                                                plugin={plugin}
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
                            onClick={props.addPluginsHandler}>
                            <FontAwesomeIcon className="me-1" icon="fa-solid fa-plus" />
                            Add new plugin
                        </button>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
