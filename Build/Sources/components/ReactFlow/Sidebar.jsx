import React from 'react';

export default (props) => {
    const onDragStart = (event, nodeType) => {
        event.dataTransfer.setData('application/reactflow', nodeType);
        event.dataTransfer.effectAllowed = 'move';
    };

    return (
        <aside className="fs-3 react-flow__sidebar">
            <div className="description">You can drag these nodes to the pane on the left.</div>
            <div className="dndnode custom-model-node" onDragStart={(event) => onDragStart(event, 'customModel')} draggable>
                New Model Object
            </div>
        </aside>
    );
};
