import {Fragment, useContext} from "react";
import {EdgesContext, NodesContext} from "../../App";

export const DebugOutputAccordion = (props) => {
    const {nodes} = useContext(NodesContext);
    const {edges} = useContext(EdgesContext);

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
                    <div className="panel-body py-2">
                        <h4>Nodes</h4>
                        <pre>
                            {JSON.stringify(nodes, null, 2)}
                        </pre>
                        <h4>Edges</h4>
                        <pre>
                            {JSON.stringify(edges, null, 2)}
                        </pre>
                        <h4>Props</h4>
                        <pre>
                            {JSON.stringify(props, null, 2)}
                        </pre>
                    </div>
                </div>
            </div>
        </Fragment>
    )
}
