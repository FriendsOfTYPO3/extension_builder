import { memo } from "react";
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { SinglePluginComponent } from "../SingleComponents/SinglePluginComponent";

export const PluginsListAccordion = memo((props) => {
    return (
        <div className="panel panel-default">
            <div className="panel-heading">
                <h3 className="fs-3 panel-title" id="heading-panel-plugins">
                    <a href="#" className="collapsed" data-bs-toggle="collapse" data-bs-target="#panel-plugins"
                       aria-expanded="true" aria-controls="panel-plugins">
                        <span className="caret"></span>
                        <strong>Frontend plugins</strong>
                    </a>
                </h3>
            </div>
            <div id="panel-plugins" className="accordion-collapse collapse" aria-labelledby="heading-panel-plugins"
                 data-bs-parent="#accordion-left-panel">
                <div className="panel-body py-2">
                    <ul>
                        {
                            props.plugins.map((plugin, index) => (
                                <SinglePluginComponent
                                    key={index}
                                    plugin={plugin}
                                    index={index}
                                    {...props}
                                />
                            ))
                        }
                    </ul>
                    {props.plugins.length === 0 &&
                        <div className="alert alert-danger" role="alert">No plugins yet</div>}
                    <button
                        className="fs-3 btn btn-success w-100"
                        onClick={props.addPluginsHandler}>
                        <FontAwesomeIcon className="me-1" icon={['fas', 'plus']}/>
                        Add new plugin
                    </button>
                </div>
            </div>
        </div>
    )
})
