import React from 'react';

export default (props) => {
    const onDragStart = (event, nodeType) => {
        event.dataTransfer.setData('application/reactflow', nodeType);
        event.dataTransfer.effectAllowed = 'move';
    };

    return (
        <aside className="react-flow__sidebar">
            <div className="description">You can drag these nodes to the pane on the left.</div>
            <div className="dndnode custom-model-node" onDragStart={(event) => onDragStart(event, 'customModel')} draggable>
                New Model Object
            </div>
{/*            <div className="debug-output">
                <h5>Debug output</h5>
                <pre>
                    {JSON.stringify(props.nodes, null, 2)}
                </pre>
                <pre>
                    {JSON.stringify(props.edges, null, 2)}
                </pre>
            </div>*/}

            {/*<div className="dndnode" onDragStart={(event) => onDragStart(event, 'default')} draggable>
                Default Node
            </div>*/}
        </aside>
    );
};
